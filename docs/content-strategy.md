# Content Strategy — closing the depth gap

> Written 2026-07-19 from the product owner's direction: the training content is
> too thin compared to LabEx, Exercism, and the catalogs on Class Central —
> **matching their completeness is the goal for this project.** The platform
> features (Judge0 runner, tiered reading, quizzes, incident readers,
> gamification, content pipeline) are done; the catalog is not. Content is now
> the product's bottleneck and its main authoring workstream.

## 1. Where we stand (measured 2026-07-19)

| Surface | Today | Benchmark |
|---------|-------|-----------|
| Challenges | 34 total, ~10 lines of description each (several have 1–5 lines) | Exercism: 100+ exercises *per track*, each with rich intro, progressive hints, and a "dig deeper" layer |
| Path steps | 62 steps across 8 paths; 6 of 8 paths have only ~50 lines of content per step | LabEx: every lab is a guided, multi-part, hands-on sequence with verification at each part |
| Paths (tracks) | 8, several feel like outlines | Class Central catalogs win on *perceived* completeness: a track page must look like a curriculum, not a list |

Two of our paths already meet the bar and prove the platform can deliver it:
**`php-for-the-real-world`** (1,567 lines / 7 steps) and **`observability-101`**
(1,085 lines / 7 steps). The other six are outlines.

## 2. The canonical exemplars (the quality bar)

Do not invent a new format — copy these:

- **Reading/lab step:** `database/content/paths/php-for-the-real-world/steps/01-modern-php-types-nullables-and-enums.md`
  (~260 lines). Front-matter with `tldr`, `estimated_minutes`, 3+ `resources`,
  `has_playground` + `playground_starter_code`, and multi-part `instructions`
  each with its own `starter_code`. Body uses the tiered headings
  (`## Core — …`, `## Deeper dive — …`, `## Senior insights — …`).
- **Challenge:** `database/content/challenges/api-paginated-resource/`
  (upgraded 2026-07-19 as the reference). Description anatomy:
  1. **The situation** — a real workplace scenario with a voice (a ticket, a
     Slack message, an on-call page). Never "write a function that…" cold.
  2. **Your task** — explicit signature + envelope/output, requirements as
     bullets, exactly matching what `tests.php` asserts.
  3. **Examples** — a small table of input → output including the edge cases.
  4. **Hints** — 2–4, progressive (nudge → concept → near-spoiler).
  5. **In the real world** — how this maps to actual Laravel/production work.
- **Incident step:** any file in `database/content/paths/observability-101/steps/`
  (evidence JSON + diagnostic quiz with cross-incident distractors).

**Definition of done for any step:** a learner who knows nothing about the topic
can go from zero to doing it hands-on without leaving the page — concept (tiered
for junior/mid/senior), at least one runnable playground exercise or linked
challenge, resources for depth, and an honest `estimated_minutes` (15–30).
A step under ~120 lines of body content is almost certainly an outline, not a lesson.

**Definition of done for any challenge:** description follows the 5-part anatomy
above; `tests.php` covers happy path + at least two edge cases; a reference
solution has been validated locally against the runner's supported assertions
(see `RestApiTrackSeeder` history for the precedent); difficulty tag honest.

**Product rule (unchanged, non-negotiable):** every scenario is a real-world
dev situation. No fictional puzzles, no Exercism-style toy domains — we copy
their *completeness*, not their fiction.

## 3. Targets

Volume alone isn't the goal — a track must feel *complete*: a ramp from guided
first steps to independent mastery, with practice at every rung.

Per track (path):
- **10–15 steps** minimum (today's median: 7), mixing reading → playground →
  challenge → quiz checkpoint → (where relevant) incident.
- Every reading step at the exemplar bar; every 2–3 steps end in something
  *graded* (challenge or quiz) so progress is earned, not scrolled.
- A capstone: one bigger challenge or incident sequence that certifies the
  track (wire `badge_key` — the certification-seal machinery already exists).

Challenge catalog:
- **From 34 → 80+** within the existing tracks (Exercism's density, our
  real-world rule). Each new challenge born at the anatomy bar — no more
  one-line descriptions ever.
- Difficulty ramp per topic: at least one `beginner`, two `intermediate`, one
  `advanced` per concept cluster, so the F4 free tier (beginner) always has a
  taste of every topic.

## 4. Authoring workflow (human or AI)

Authoring is now cheap by design — use it:

1. Copy the exemplar's structure into
   `database/content/challenges/<slug>/` or `paths/<slug>/steps/NN-<slug>.md`.
2. `ddev artisan content:sync --dry-run` → check the diff report.
3. `ddev artisan content:sync` → verify in the UI (step page / challenge editor).
4. For challenges: write the reference solution, run it against `tests.php`
   locally (the runner supports plain PHPUnit assertions), then delete the
   solution — only boilerplate ships.
5. `ddev artisan test --compact` (ContentSyncTest guards the pipeline) → commit
   the content files. One track-slice per commit.

An AI session can mass-produce drafts, but **every piece goes through the
anatomy checklist** and the real-world rule before sync. Thin content is worse
than missing content — it's the thing the product owner flagged.

## 5. Prioritized authoring backlog

Ordered by funnel impact (what a prospective payer sees first):

| # | Work | Why first |
|---|------|-----------|
| 1 | ~~**Bring all 34 existing challenge descriptions to the anatomy bar**~~ **DONE 2026-07-20** — all 34 rewritten to the 5-part anatomy (median went from ~10 lines to ~50; every one faithful to its `tests.php`, synced and verified idempotent) | Cheapest, highest-visibility fix — teasers and the challenge list are the funnel's front door. ~1–5-line descriptions were the "thin" feeling, concentrated. |
| 2 | **Deepen `rest-apis-auth`** (9 steps, 476 lines → exemplar bar, 12+ steps) | It's the flagship Option-A track and the most job-relevant; it's also newest, so structure is already right. |
| 3 | **Deepen `database-performance` + `debugging-like-a-pro`** | Highest-demand senior topics; pair naturally with new challenges (EXPLAIN plans, N+1 hunts — real incidents already exist to link to). |
| 4 | **Deepen `git-professional-workflow` + `php-code-katas`** | Beginner-facing; these are what free-tier users judge us by. |
| 5 | **`debug-katas-find-the-bug`: 13 steps / 372 lines** | Format is fine (katas are short by nature) but each kata needs the situation/hints/real-world wrapper. |
| 6 | **New challenges to reach 80+** | Interleave with 2–4: every deepened step should mint 1–2 challenges. |
| 7 | **Next new track = waitlist winner** | Check `GET /admin/waitlist` before authoring anything new from scratch. Born at the bar, 10–15 steps. |

**Gate for "done":** no challenge description under 40 lines (debug katas:
under 30 — the kata format is deliberately tighter, but still carries the full
anatomy); no path under 10 steps except `debug-katas-find-the-bug` (kata
format); every path ends in a graded capstone; a cold visitor comparing us to
a LabEx course page doesn't feel the difference.

**Renderer constraints (learned 2026-07-20, respect them when authoring):**
challenge descriptions render through the custom `renderMarkdown()` in
`frontend/app/utils/markdown.ts`, which supports headings, fenced code, bold,
inline code, `-`/`*` bullets, and tables — but NOT blockquotes (`>`), NOT
numbered lists, and NOT the `|---|` table separator row (write tables as
plain `| a | b |` rows with a bold first row as header).

## 6. What NOT to do

- Don't build new platform features to "fix" content — the platform is ahead of
  the catalog. Feature work resumes per `docs/roadmap.md` after launch items.
- Don't pad with filler prose to hit line counts — depth = more *doing*
  (playground parts, graded checkpoints, edge-case tests), not longer paragraphs.
- Don't author in seeders or the DB — `database/content/` + `content:sync` only.
- Don't break the F4 economics: keep `beginner` challenges genuinely free-tier
  worthy, and price-gate depth, not breadth.
