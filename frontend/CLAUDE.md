# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

> See the root `CLAUDE.md` for project-wide context (auth flow, API conventions, backend architecture, seeded users, and environment setup).

## Commands

```bash
npm run dev        # Dev server at http://localhost:3000 (host — native binaries)
npm run build      # Production build
npm run generate   # Static build → frontend/dist/
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
| ` ```mermaid ` | `<MermaidDiagram>` |
| ` ```lifecycle-diagram ` | `<LaravelLifecycleDiagramCard>` |
| Everything else | custom `renderMarkdown()` → `v-html` |

To embed a new interactive component in step content, add its block type to the `blockPattern` regex and the segment type union in `MarkdownContent.vue`, then handle it in the template.

### ChallengeEditor

Full-screen coding challenge interface (`app/components/ChallengeEditor.vue`) rendered as a fixed overlay on top of the admin layout (see `pages/step/[step_id].vue`). The admin layout stays mounted underneath so navigation is preserved.

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
| `auth` | Login, register, etc. |
| `marketing` | Public pages (`/`, `/about`, `/pricing`, `/faqs`, `/terms`, `/privacy`) |
| `default` | Fallback |

Marketing pages declare `definePageMeta({ layout: false })` and wrap with `<NuxtLayout name="marketing">` explicitly. They do not use `@nuxt/ui` components — only `.mkt-*` CSS classes defined in `app/layouts/marketing.vue`.

## Types

All shared types are in `app/types/models.ts`. `PathStep` and its `user_status` union are defined in `app/composables/usePaths.ts` (co-located with the composable, not in `models.ts`).

## PathStep page (`pages/step/[step_id].vue`)

Step type drives the rendered UI:
- `reading` (or no type) → two-column layout: `MarkdownContent` left, progress/resources sidebar right
- `challenge` with a linked `Challenge` → `ChallengeEditor` full-screen overlay
- `challenge`/`lab` with no linked exercise → placeholder card
- anything else → fallback card

Progress update calls `updateStepProgress(stepId, status)` which catches `{ data: { blocking_step } }` errors and shows a blocking modal.
