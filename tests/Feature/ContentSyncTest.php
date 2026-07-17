<?php

namespace Tests\Feature;

use App\Models\Challenge;
use App\Models\Path;
use App\Models\PathStep;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

/**
 * Acceptance for `content:sync` (docs/architecture-review.md Phase A): the
 * curriculum lives as versioned files under database/content and is imported
 * idempotently — re-running never duplicates, drift is corrected, and content
 * authorship is preserved rather than reassigned.
 */
class ContentSyncTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleSeeder::class);

        // The command resolves the content author via the admin role.
        User::factory()->create()->assignRole('admin');
    }

    private function challengeDirCount(): int
    {
        return count(glob(database_path('content/challenges/*'), GLOB_ONLYDIR));
    }

    public function test_sync_imports_every_challenge_content_directory(): void
    {
        $expected = $this->challengeDirCount();
        $this->assertGreaterThan(0, $expected, 'No challenge content directories found.');

        Artisan::call('content:sync');

        $this->assertSame($expected, Challenge::count());
        $this->assertDatabaseHas('challenges', ['slug' => 'null-safe-property-chain']);
    }

    public function test_running_twice_does_not_duplicate(): void
    {
        Artisan::call('content:sync');
        $afterFirst = Challenge::count();

        Artisan::call('content:sync');

        $this->assertSame($afterFirst, Challenge::count());
    }

    public function test_second_run_reports_everything_unchanged(): void
    {
        Artisan::call('content:sync');
        Artisan::call('content:sync');

        $this->assertStringContainsString('created: 0, updated: 0', Artisan::output());
    }

    public function test_sync_corrects_drifted_content(): void
    {
        Artisan::call('content:sync');

        Challenge::where('slug', 'null-safe-property-chain')->update(['title' => 'DRIFTED']);

        Artisan::call('content:sync');

        $this->assertSame(
            'Null-Safe Property Chain',
            Challenge::where('slug', 'null-safe-property-chain')->value('title'),
        );
    }

    public function test_sync_preserves_existing_content_author(): void
    {
        Artisan::call('content:sync');

        $other = User::factory()->create();
        Challenge::where('slug', 'null-safe-property-chain')->update(['created_by' => $other->id]);

        Artisan::call('content:sync');

        $this->assertSame(
            $other->id,
            Challenge::where('slug', 'null-safe-property-chain')->value('created_by'),
        );
    }

    private function pathDirCount(): int
    {
        return count(glob(database_path('content/paths/*'), GLOB_ONLYDIR));
    }

    public function test_sync_imports_every_path_and_its_steps(): void
    {
        $expectedPaths = $this->pathDirCount();
        $this->assertGreaterThan(0, $expectedPaths, 'No path content directories found.');

        Artisan::call('content:sync');

        $this->assertSame($expectedPaths, Path::count());
        $this->assertGreaterThan(0, PathStep::count());

        // Every step that links a challenge resolves to a real challenge.
        $orphans = PathStep::whereNotNull('challenge_slug')
            ->whereNotIn('challenge_slug', Challenge::pluck('slug'))
            ->count();
        $this->assertSame(0, $orphans, 'A path step references a missing challenge slug.');
    }

    public function test_running_twice_does_not_duplicate_paths_or_steps(): void
    {
        Artisan::call('content:sync');
        $paths = Path::count();
        $steps = PathStep::count();

        Artisan::call('content:sync');

        $this->assertSame($paths, Path::count());
        $this->assertSame($steps, PathStep::count());
    }

    public function test_second_run_reports_paths_and_steps_unchanged(): void
    {
        Artisan::call('content:sync');
        Artisan::call('content:sync');

        $output = Artisan::output();
        $this->assertStringContainsString('Paths — created: 0, updated: 0', $output);
        $this->assertStringContainsString('Steps — created: 0, updated: 0', $output);
    }

    public function test_step_nested_json_round_trips(): void
    {
        Artisan::call('content:sync');

        // A quiz step keeps its question array; an incident step keeps its evidence.
        $quiz = PathStep::where('type', 'quiz')->whereNotNull('quiz')->first();
        $this->assertNotNull($quiz, 'Expected at least one quiz step.');
        $this->assertIsArray($quiz->quiz);
        $this->assertArrayHasKey('question', $quiz->quiz[0]);

        $incident = PathStep::where('type', 'incident')->whereNotNull('evidence')->first();
        $this->assertNotNull($incident, 'Expected at least one incident step.');
        $this->assertIsArray($incident->evidence);
        $this->assertArrayHasKey('scenario', $incident->evidence);
    }

    public function test_sync_preserves_existing_path_consultant(): void
    {
        Artisan::call('content:sync');

        $path = Path::first();
        $other = User::factory()->create();
        Path::where('id', $path->id)->update(['consultant_id' => $other->id]);

        Artisan::call('content:sync');

        $this->assertSame($other->id, Path::find($path->id)->consultant_id);
    }

    public function test_no_vendor_branded_content_remains(): void
    {
        Artisan::call('content:sync');

        $this->assertSame(0, Path::where('name', 'like', '%New Relic%')->count());
        $this->assertSame(0, PathStep::where('concept_content', 'like', '%New Relic%')->count());
        $this->assertSame(0, Challenge::where('description', 'like', '%New Relic%')->count());
    }
}
