# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

> See the root `CLAUDE.md` for project-wide context (auth flow, API conventions, backend architecture, seeded users, and environment setup).

## Commands

```bash
npm run dev        # Dev server at http://localhost:3001 (host — native binaries; port set in nuxt.config.ts devServer)
npm run build      # Production build
npm run generate   # Static build → frontend/dist/

node scripts/generate-world-map.mjs   # regenerate app/utils/world-map-data.ts
                                      # (homepage dotted world map + city pins)
```

Required env (`frontend/.env`):
```
NUXT_PUBLIC_API_BASE=http://codecv6.ddev.site
```

## Architecture

**Nuxt 4** — application code lives in `app/` (not `src/`). Auto-imports are active for composables, components, and Vue/Nuxt primitives (`ref`, `computed`, `useRoute`, etc.) — no explicit imports needed.

**Hybrid rendering:** `ssr: true` globally; marketing/auth-public pages are `prerender: true` in `routeRules`; all authenticated routes are `ssr: false` (SPA). Do not add authenticated routes to the sitemap.

**Tailwind v3** pinned via `overrides` — incompatible with v4. Do not upgrade.

## Component Patterns

### MarkdownContent — segment rendering

`app/components/MarkdownContent.vue` splits step descriptions into typed segments before rendering. Special fenced code blocks are extracted as non-HTML segments:

| Block type | Renders as |
|---|---|
| ` ```mermaid ` | `<FlowDiagram>` (modern pipeline renderer — parses the mermaid flowchart subset; replaced the raw-mermaid box) |
| ` ```lifecycle-diagram ` | `<LaravelLifecycleDiagramCard>` |
| Everything else | custom `renderMarkdown()` → `v-html` |

To embed a new interactive component in step content, add its block type to the `blockPattern` regex and the segment type union in `MarkdownContent.vue`, then handle it in the template.

### ChallengeEditor (Exercism-style workspace)

Full-screen coding challenge interface (`app/components/ChallengeEditor.vue`) rendered as a fixed overlay on top of the admin layout — mounted by `pages/step/[step_id].vue` (path context) and `pages/challenges/[slug].vue` (standalone play). The admin layout stays mounted underneath so navigation is preserved.

Layout: instructions panel LEFT (tabs: Instructions / Hints / Results / Iterations), Monaco RIGHT. `utils/challenge-content.ts` (`parseChallengeDescription`) splits the description's `## Hints` section out of the instructions; `ProgressiveHints.vue` renders them behind one-at-a-time reveal. Results tab: per-test cards with staggered entrance, pass-ratio bar, running skeleton, celebration banner. Iterations tab: the caller's submission history (`GET /challenges/{slug}/submissions`, lazy-loaded, refreshed after runs) — expandable code via `CopyableCode` + restore-into-editor. Instructions render through `InstructionsPanel.vue` (structured `##` sections with icons, justified/hyphenated prose, `CopyableCode` for fences). ⌘/Ctrl+Enter runs tests. `completed` emits `(challenge, progress)` — `progress` is the gamification delta from the run response (null on repeat solves).

### Challenge catalog + completion flow

- `pages/challenges/index.vue` — learner catalog: search, difficulty + solved/unsolved filter pills, cards with `solved`/`locked` state (both flags from `GET /challenges`). In the learner sidebar nav.
- `pages/challenges/[slug].vue` — standalone workspace (no path step needed); 403 → `LockedUpsell`; on a fresh solve shows `CompletionCelebration` fed by the run response's `progress`.
- `CompletionCelebration.vue` — teleported overlay (`z-[70]`, above the editor's `z-50`): XP count-up, CSS confetti (deterministic, no `Math.random`), streak, fresh badges, "Next up" CTA. On `/step/[id]` it fires for any fresh step completion (`progress !== null`) except path completion, which keeps the F6 coaching modal. `/step/[id]` sets `key: route => route.fullPath` so the next-step navigation remounts the page.

### DiagramCanvas / RoadmapFlow

`DiagramCanvas.vue` uses `@vue-flow/core` for interactive flowcharts. `RoadmapFlow.vue` + `RoadmapStepNode.vue` are the learning path visualisation. These are separate from `LaravelLifecycleDiagram.vue` which is plain HTML/SVG (no Vue Flow).

## Composable Pattern

All domain composables follow this shape:

```ts
export const useFoo = () => {
  const data = ref<Foo[]>([])
  const loading = ref(false)
  const error = ref<string | null>(null)

  async function fetchFoo() {
    loading.value = true
    try {
      data.value = await useApi().get<Foo[]>('/foos')
    } catch (e: unknown) {
      error.value = (e as Error).message
    } finally {
      loading.value = false
    }
  }

  return { data: readonly(data), loading: readonly(loading), error: readonly(error), fetchFoo }
}
```

`useApi()` handles: Bearer token from `localStorage`, CSRF token from cookie, `credentials: 'include'`, 90s timeout.

## useAuth internals

- Auth state lives in `useState('user')` — shared across all composable calls.
- Token in `localStorage` as `auth_token`, user object as `auth_user`.
- `isAdmin` / `isClient` / `isConsultant` are `computed` from `user.value?.role`.
- `updateUser(patch)` merges into stored state and re-serialises to localStorage.

## Layouts

| Layout | Used by |
|---|---|
| `admin` | All authenticated pages (dashboard, paths, users, etc.) |
| `auth` | Forgot/reset password |
| `marketing` | Public pages (`/`, `/about`, `/pricing`, `/faqs`, `/terms`, `/privacy`) plus the practice-funnel public pages `/try/[slug]` (F2 teaser runner, Monaco editor + public run endpoint) and `/u/[slug]` (F3 public skill profile). These two are `ssr: false` (per-slug runtime data), not prerendered. |
| `default` | Fallback |
| — (none) | Login and register use `layout: false` + the standalone `TerminalShell` component (terminal-style auth with `TerminalPrompt` fields and the `TerminalMascot` reactive robot); shared `.term-*` classes live in `TerminalShell.vue`'s unscoped style block |

Marketing pages declare `definePageMeta({ layout: false })` and wrap with `<NuxtLayout name="marketing">` explicitly. They do not use `@nuxt/ui` components — only `.mkt-*` CSS classes defined in `app/layouts/marketing.vue`.

## Types

All shared types are in `app/types/models.ts`. `PathStep` and its `user_status` union are defined in `app/composables/usePaths.ts` (co-located with the composable, not in `models.ts`).

## PathStep page (`pages/step/[step_id].vue`)

Step type drives the rendered UI:
- `reading` (or no type) → two-column layout: `MarkdownContent` left, progress/resources sidebar right
- `challenge` with a linked `Challenge` → `ChallengeEditor` full-screen overlay
- `challenge`/`lab` with no linked exercise → placeholder card
- `quiz` with questions → `QuizRunner` (grades server-side, marks the step done on a perfect score)
- `incident` with `evidence` → `IncidentRunner` (observability track): renders trace/metrics/logs then reuses `QuizRunner` to diagnose
- anything else → fallback card

Progress update calls `updateStepProgress(stepId, status)` which catches `{ data: { blocking_step } }` errors and shows a blocking modal.

## Practice funnel (frontend)

- `renderMarkdown()` lives in `app/utils/markdown.ts` (auto-imported), shared by `MarkdownContent.vue` and the public teaser page.
- `usePracticeProgress()` — the F1 gamification snapshot (`GET /me/progress`) plus the F3 public-profile visibility toggle (`PATCH /me/public-profile`; reads current state from `/me`).
- `ProgressWidget.vue` — dashboard right-rail card (clients only) showing XP, streak, badges, and the public-profile toggle with a copy-link control. Slug links resolve to `/u/{slug}`.
- `/try/[slug].vue` — public teaser runner: fetches the teaser list, loads Monaco with the boilerplate, POSTs to `/public/challenges/{slug}/run`, shows per-test results and a signup CTA on pass. Homepage has a lightweight teaser section (`#try`) that only lists challenges and links here, so Monaco isn't loaded on the prerendered landing page.
- `/u/[slug].vue` — public skill profile: XP/streak stats, completed challenges, stack, badges, and a signup CTA.
- `useCoaching()` + `CoachingNudge.vue` (F6) — the coaching upsell. `useCoaching().fetchRecommendation()` hits `GET /me/coaching-recommendation`; the backend decides whether a nudge is earned and which tier. `CoachingNudge.vue` renders it (emerald card, tier icon, price in the viewer's currency, CTA → `/pricing#plans`). Shown on the dashboard right-rail (clients only, below `ProgressWidget`) and inside a path-completion celebration `UModal` on `/step/[id]` — that modal fires when `updateStepProgress`'s response carries a freshly-earned `path_completed` badge (see the `StepProgressResponse` type in `usePaths.ts`).
- **Observability track — incident reader** (`IncidentRunner.vue` + `TraceWaterfall.vue`, `MetricChart.vue`, `LogStream.vue`) — a `type=incident` step renders curated telemetry from `step.evidence` (`{ scenario, trace, metrics, logs }`), all client-side from static JSON (no live backend). `TraceWaterfall` is the hero: a span waterfall with per-service colors, `repeat`/N+1 collapsing (effective duration = `dur × repeat`), and click-to-inspect. `IncidentRunner` composes the evidence + reuses `<QuizRunner>` for the graded diagnosis, emitting `passed` so the step page marks it done. Types live in `usePaths.ts` (`IncidentEvidence`, `TraceSpan`).
- `QuizRunner.vue` (F5) — renders a `quiz`-type step's questions (from `step.quiz`, answer key already stripped by the API), POSTs the answer map to `/path-steps/{stepId}/quiz` for server-side grading, then shows per-question correct/incorrect highlights + explanations. Emits `passed` on a perfect score; the step page marks the step done in response (earning XP through the normal gamification path). "Try again" resets local state without another fetch.
- **Waitlist (demand-sensing)** — `WaitlistSection.vue` (homepage "coming soon" cards, inline email capture → `POST /public/waitlist`), `WaitlistVote.vue` (client dashboard right rail, one-tap vote → authenticated `POST /waitlist`), `WaitlistAdminCard.vue` (admin dashboard right rail, per-track signup counts from `GET /admin/waitlist`). Candidate tracks come from `config/waitlist.php` on the backend.
- `LockedUpsell.vue` — the F4 gate's shared lock/upsell panel (amber lock, "Unlock with Practice Pro" → `/pricing#plans`). The steps list marks gated rows with `step.locked` (from the API); `my-paths.vue` shows a lock icon + "Pro" badge and swaps the drawer body for `<LockedUpsell compact>`, and `/step/[step_id]` catches the API's 403 and renders `<LockedUpsell>` instead of a generic error. NOTE: `/step/**` is in `routeRules` as `ssr: false` — like every other authenticated page, it must render client-side so the localStorage bearer token is available to the `auth` guard (it was missing before F4's lock UI and would SSR-redirect to /login).
