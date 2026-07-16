<?php

namespace App\Console\Commands;

use App\Enums\ChallengeDifficulty;
use App\Models\Challenge;
use App\Models\Path;
use App\Models\PathStep;
use App\Models\User;
use Illuminate\Console\Command;

/**
 * Idempotent content import: reads the versioned curriculum under
 * database/content/ and upserts it into the database. Safe to run on a live
 * database — it is upsert-only (never deletes user data or content), and
 * re-running with no file changes reports every row as "unchanged".
 *
 * Layout:
 *   database/content/challenges/<slug>/{meta.json, description.md, boilerplate.php, tests.php}
 *   database/content/paths/<path-slug>/meta.json
 *   database/content/paths/<path-slug>/steps/NN-<step-slug>.md   (---json front-matter + concept_content body)
 *
 * Upsert keys: challenges by slug, paths by name, steps by (path_id, order).
 * Authorship (challenge.created_by, path.consultant_id) is preserved on
 * existing rows and defaults to the platform admin on first import — a sync
 * never reassigns it. See docs/architecture-review.md Phase A.
 */
class ContentSyncCommand extends Command
{
    protected $signature = 'content:sync {--dry-run : Report what would change without writing}';

    protected $description = 'Sync versioned curriculum files (database/content) into the database';

    /** Step columns stored in the JSON front-matter (concept_content is the Markdown body). */
    private const STEP_FIELDS = [
        'order', 'title', 'type', 'description', 'tldr', 'difficulty',
        'estimated_minutes', 'challenge_slug', 'lab_url', 'has_playground',
        'playground_starter_code', 'challenge_prompt',
        'resources', 'instructions', 'prerequisites', 'concepts', 'quiz', 'evidence',
    ];

    public function handle(): int
    {
        $author = User::role('admin')->first() ?? User::find(1);

        if (! $author) {
            $this->error('No admin user found to own imported content. Seed users first.');

            return self::FAILURE;
        }

        $dryRun = (bool) $this->option('dry-run');

        // Challenges first: path steps reference challenges by slug.
        $this->syncChallenges($author->id, $dryRun);
        $this->syncPaths($author->id, $dryRun);

        return self::SUCCESS;
    }

    private function syncChallenges(int $authorId, bool $dryRun): void
    {
        $root = database_path('content/challenges');

        if (! is_dir($root)) {
            $this->warn('No challenge content directory found; skipping.');

            return;
        }

        $created = $updated = $unchanged = 0;

        foreach (glob($root.'/*', GLOB_ONLYDIR) as $dir) {
            $slug = basename($dir);
            $meta = json_decode(file_get_contents($dir.'/meta.json'), true);

            if (! is_array($meta) || ($meta['slug'] ?? null) !== $slug) {
                $this->error("  {$slug}: meta.json missing or slug mismatch — skipped.");

                continue;
            }

            // created_by is intentionally NOT file-managed: preserved on update,
            // defaults to the platform admin on first import, never reassigned.
            $challenge = Challenge::withTrashed()->firstOrNew(['slug' => $slug]);
            $isNew = ! $challenge->exists;

            if ($isNew) {
                $challenge->created_by = $authorId;
            }

            $challenge->fill([
                'title' => $meta['title'],
                'difficulty' => ChallengeDifficulty::from($meta['difficulty']),
                'description' => rtrim(file_get_contents($dir.'/description.md'), "\n"),
                'boilerplate_code' => rtrim(file_get_contents($dir.'/boilerplate.php'), "\n"),
                'tests_code' => rtrim(file_get_contents($dir.'/tests.php'), "\n"),
                'is_teaser' => (bool) ($meta['is_teaser'] ?? false),
                'is_premium' => (bool) ($meta['is_premium'] ?? false),
                'price_eur' => $meta['price_eur'] ?? null,
            ]);

            if (! $isNew && ! $challenge->isDirty()) {
                $unchanged++;

                continue;
            }

            $verb = $isNew ? 'create' : 'update';

            if (! $dryRun) {
                $challenge->save();
            }

            $this->line(sprintf('  %-8s challenge %s', $verb, $slug));
            $isNew ? $created++ : $updated++;
        }

        $prefix = $dryRun ? '[dry-run] ' : '';
        $this->info("{$prefix}Challenges — created: {$created}, updated: {$updated}, unchanged: {$unchanged}");
    }

    private function syncPaths(int $authorId, bool $dryRun): void
    {
        $root = database_path('content/paths');

        if (! is_dir($root)) {
            $this->warn('No path content directory found; skipping.');

            return;
        }

        $pathsCreated = $pathsUpdated = $pathsUnchanged = 0;
        $stepsCreated = $stepsUpdated = $stepsUnchanged = 0;

        foreach (glob($root.'/*', GLOB_ONLYDIR) as $dir) {
            $meta = json_decode(file_get_contents($dir.'/meta.json'), true);

            if (! is_array($meta) || empty($meta['name'])) {
                $this->error('  '.basename($dir).': meta.json missing or has no name — skipped.');

                continue;
            }

            // consultant_id is authorship: preserved on update, admin on create.
            $path = Path::withTrashed()->firstOrNew(['name' => $meta['name']]);
            $isNew = ! $path->exists;

            if ($isNew) {
                $path->consultant_id = $authorId;
            }

            $path->fill([
                'description' => $meta['description'] ?? null,
                'badge_key' => $meta['badge_key'] ?? null,
            ]);

            if ($isNew || $path->isDirty()) {
                $verb = $isNew ? 'create' : 'update';
                if (! $dryRun) {
                    $path->save();
                }
                $this->line(sprintf('  %-8s path %s', $verb, $meta['name']));
                $isNew ? $pathsCreated++ : $pathsUpdated++;
            } else {
                $pathsUnchanged++;
            }

            // Steps need a persisted path id. In dry-run on a brand-new path the
            // path has no id yet, so its steps can't be keyed — report and skip.
            if (! $path->exists) {
                continue;
            }

            [$c, $u, $n] = $this->syncSteps($path, $dir.'/steps', $dryRun);
            $stepsCreated += $c;
            $stepsUpdated += $u;
            $stepsUnchanged += $n;
        }

        $prefix = $dryRun ? '[dry-run] ' : '';
        $this->info("{$prefix}Paths — created: {$pathsCreated}, updated: {$pathsUpdated}, unchanged: {$pathsUnchanged}");
        $this->info("{$prefix}Steps — created: {$stepsCreated}, updated: {$stepsUpdated}, unchanged: {$stepsUnchanged}");
    }

    /**
     * Sync one path's steps from its steps/ directory. Each step file is a
     * `---json` front-matter block followed by the concept_content body.
     *
     * @return array{0:int,1:int,2:int} [created, updated, unchanged]
     */
    private function syncSteps(Path $path, string $stepsDir, bool $dryRun): array
    {
        if (! is_dir($stepsDir)) {
            return [0, 0, 0];
        }

        $created = $updated = $unchanged = 0;

        foreach (glob($stepsDir.'/*.md') as $file) {
            [$front, $body] = $this->parseStepFile($file);

            if ($front === null) {
                $this->error('  '.basename($file).': malformed front-matter — skipped.');

                continue;
            }

            $step = PathStep::firstOrNew([
                'path_id' => $path->id,
                'order' => $front['order'],
            ]);
            $isNew = ! $step->exists;

            $fill = [];
            foreach (self::STEP_FIELDS as $f) {
                $fill[$f] = $front[$f] ?? null;
            }
            // Steps without prose (lab/challenge/quiz/incident) store NULL, not
            // an empty string — an empty body must round-trip back to NULL.
            $fill['concept_content'] = $body === '' ? null : $body;
            $step->fill($fill);

            if (! $isNew && ! $step->isDirty()) {
                $unchanged++;

                continue;
            }

            if (! $dryRun) {
                $step->save();
            }

            $isNew ? $created++ : $updated++;
        }

        return [$created, $updated, $unchanged];
    }

    /**
     * Split a step file into its decoded JSON front-matter and Markdown body.
     *
     * @return array{0:?array,1:string} [front-matter or null on error, body]
     */
    private function parseStepFile(string $file): array
    {
        $raw = file_get_contents($file);

        if (! preg_match('/^---json\s*\n(.*?)\n---\n(.*)$/s', $raw, $m)) {
            return [null, ''];
        }

        $front = json_decode($m[1], true);

        if (! is_array($front) || ! isset($front['order'])) {
            return [null, ''];
        }

        return [$front, rtrim($m[2], "\n")];
    }
}
