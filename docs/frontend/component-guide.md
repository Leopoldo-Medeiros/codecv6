# Frontend Component Guide (codecv6)

How components, layouts, and design systems work in the codecv6 frontend.

## The Two Design Systems

| Area | UI library | Notes |
|---|---|---|
| Authenticated / admin pages | `@nuxt/ui` v2 | `UCard`, `UButton`, `UTable`, `UBadge`, `UAvatar`, `UProgress`, `UIcon`, `UDropdown`, `UForm`, `UInput`, ... |
| Marketing pages | Custom `.mkt-*` CSS | Defined in `app/layouts/marketing.vue`, no `@nuxt/ui` |

> Marketing pages (`/`, `/about`, `/pricing`, `/faqs`, `/terms`, `/privacy`) explicitly declare `definePageMeta({ layout: false })` and wrap content in `<NuxtLayout name="marketing">`.

## Tailwind Pinning

`@nuxt/ui` v2 is **incompatible with Tailwind v4**. Tailwind v3 is pinned via `frontend/package.json` `overrides`:

```json
"overrides": { "tailwindcss": "^3.4.0" }
```

Do not bump.

## Authenticated Component Pattern

```vue
<!-- frontend/app/components/CourseCard.vue -->
<script setup lang="ts">
import type { Course } from '~/types/models'

interface Props {
  course: Course
}

const props = defineProps<Props>()
const emit = defineEmits<{ click: [course: Course] }>()
</script>

<template>
  <UCard
    :ui="{ body: { padding: 'p-4' } }"
    class="cursor-pointer hover:ring-2 hover:ring-primary-500 transition"
    @click="emit('click', props.course)"
  >
    <div class="flex gap-4">
      <img
        v-if="course.image_url"
        :src="course.image_url"
        :alt="course.title"
        class="w-24 h-24 object-cover rounded-md"
      />
      <div class="flex-1">
        <h3 class="font-semibold">{{ course.title }}</h3>
        <p class="text-sm text-gray-600 line-clamp-2">{{ course.description }}</p>
        <UBadge v-if="course.is_published" color="success" variant="subtle">Published</UBadge>
      </div>
    </div>
  </UCard>
</template>
```

### Conventions

- `<script setup lang="ts">` (Composition API) — no Options API
- Props typed via `defineProps<Props>()`
- Emits typed via `defineEmits<{ ... }>()`
- Component file in `frontend/app/components/`, PascalCase filename
- Auto-imports work for components in `app/components/` — no explicit `import` needed for sibling components

## Marketing Component Pattern

Marketing components are plain Vue + custom CSS classes. No `@nuxt/ui`.

```vue
<!-- inside pages/index.vue or a marketing-only component -->
<template>
  <section class="hero">
    <div class="hero__inner">
      <h1 class="hero__title">Land a tech job in Ireland.</h1>
      <p class="hero__sub">AI-powered CV reviews, structured learning paths, and 1-on-1 coaching.</p>
      <NuxtLink class="mkt-cta" to="/register">Get started</NuxtLink>
    </div>
  </section>
</template>

<style scoped>
.hero {
  --pad: clamp(56px, 7vw, 96px);
  padding: var(--pad) 24px;
}
.hero__title {
  font-family: 'Plus Jakarta Sans', system-ui, sans-serif;
  font-weight: 800;
  font-size: clamp(2.5rem, 5vw, 4rem);
  letter-spacing: -0.02em;
}
.hero__sub { color: var(--slate); }
.mkt-cta {
  display: inline-block;
  background: var(--accent);
  color: white;
  padding: 14px 32px;
  border-radius: 999px;
}
.mkt-cta:hover { background: var(--accent-hover); }
</style>
```

### Scoped CSS `:root` Gotcha

CSS tokens defined as `:root { --token: ... }` inside a Vue `<style scoped>` block get rewritten to `:root[data-v-xxx]` and **never match the `<html>` element**. The tokens won't apply.

**Fix**: define tokens on a section-level selector (e.g. `.mkt`, `.hero, .features, ...`) inside the scoped block, OR put them in an unscoped block (`<style>`) in the layout file.

### Emerald Palette

```css
:root /* — only inside layouts/marketing.vue (unscoped) */ {
  --accent: #059669;
  --accent-hover: #047857;
  --bg-soft: #ECFDF5;
  --ink: #0F172A;
  --slate: #475569;
  --muted: #64748B;
  --border: #E2E8F0;
}
```

**Never use purple** (`#6b46e5`, `#7c3aed`, `#a78bfa`). Purple was deliberately removed brand-wide to avoid AmigosCode visual collision.

## Layouts

| Layout | Path | Used by |
|---|---|---|
| `default` | `app/layouts/default.vue` | Fallback |
| `admin`   | `app/layouts/admin.vue` | All authenticated app pages |
| `auth`    | `app/layouts/auth.vue` | Login, register, forgot-password, reset-password |
| `marketing` | `app/layouts/marketing.vue` | `/`, `/about`, `/pricing`, `/faqs`, `/terms`, `/privacy` |

### Selecting a Layout

```vue
<script setup lang="ts">
definePageMeta({ layout: 'admin', middleware: 'auth' })
</script>
```

### Marketing Layout Setup

Marketing pages use `layout: false` and wrap content in `<NuxtLayout>` explicitly:

```vue
<script setup lang="ts">
definePageMeta({ layout: false })

useSeoMeta({
  title: 'About — CODECV',
  description: '...',
  ogTitle: 'About — CODECV',
  ogDescription: '...',
})
</script>

<template>
  <NuxtLayout name="marketing">
    <!-- page content -->
  </NuxtLayout>
</template>
```

The marketing layout inlines its own header (`{ }` SVG logo) and footer — it does NOT use the `Navbar.vue` / `Footer.vue` components used by `default` / `admin` layouts.

## Middleware

| Middleware | Path | Behaviour |
|---|---|---|
| `auth` | `app/middleware/auth.ts` | Redirects unauthenticated users to `/login` |
| `guest` | `app/middleware/guest.ts` | Redirects authenticated users to `/dashboard` |

Apply per-page:

```vue
<script setup lang="ts">
definePageMeta({ middleware: 'auth' })       // require login
definePageMeta({ middleware: 'guest' })      // require logout
</script>
```

## SSR / SPA Configuration

`nuxt.config.ts` enables SSR globally and opts authenticated pages out:

```ts
ssr: true,

routeRules: {
  '/': { prerender: true },
  '/about': { prerender: true },
  '/pricing': { prerender: true },
  '/faqs': { prerender: true },
  '/terms': { prerender: true },
  '/privacy': { prerender: true },
  '/login': { prerender: true },
  '/register': { prerender: true },
  '/forgot-password': { prerender: true },
  '/reset-password': { prerender: true },

  '/dashboard': { ssr: false },
  '/auth/**': { ssr: false },
  '/courses/**': { ssr: false },
  '/paths/**': { ssr: false },
  // ... all auth-required app routes
},
```

- **Prerendered pages** are generated as static HTML at build time → fast load, full SEO
- **SPA pages** (`ssr: false`) render client-side → safe to use `localStorage`, browser-only APIs, auth state

## SEO

`@nuxtjs/seo` is configured (`nuxt.config.ts`). Marketing pages use `useSeoMeta()`:

```vue
<script setup lang="ts">
useSeoMeta({
  title: 'Pricing — CODECV',
  description: 'Three tiers of IT career coaching: Accelerator, Bootcamp, Mentorship.',
  ogTitle: 'Pricing — CODECV',
  ogDescription: 'Plans for every stage of your IT career.',
  twitterCard: 'summary_large_image',
  robots: 'index, follow',
})
</script>
```

Static `og-image` lives at `/frontend/public/og-image.png` (1200×630). The dynamic `og-image` module is disabled because SSR isn't on for the auth routes — and the marketing pages are prerendered, not SSR.

## Specific Components

### `MarkdownContent`

`app/components/MarkdownContent.vue` splits step descriptions into segments. Fenced code blocks with special language tags render as interactive components:

| Block | Renders as |
|---|---|
| ` ```mermaid ` | `<MermaidDiagram>` |
| ` ```lifecycle-diagram ` | `<LaravelLifecycleDiagramCard>` |
| Everything else | `renderMarkdown()` → `v-html` |

To embed a new interactive component:
1. Add the block type to the `blockPattern` regex
2. Add it to the segment type union
3. Handle it in the template

### `ChallengeEditor`

`app/components/ChallengeEditor.vue` — full-screen fixed overlay rendered on top of the `admin` layout. The layout stays mounted underneath so the user can navigate away.

### `DiagramCanvas` / `RoadmapFlow`

- `DiagramCanvas.vue` — interactive flowcharts via `@vue-flow/core`
- `RoadmapFlow.vue` + `RoadmapStepNode.vue` — learning path visualisation
- `LaravelLifecycleDiagram.vue` — plain HTML/SVG, no Vue Flow

### `PathStep` Page

`pages/step/[step_id].vue` routes based on `step.type`:
- `reading` (or no type) → two-column with `MarkdownContent`
- `challenge` + linked `Challenge` → `ChallengeEditor` overlay
- `challenge` / `lab` with no linked exercise → placeholder
- Anything else → fallback card

Progress updates catch `{ data: { blocking_step } }` errors and show a modal.

## Auto-Imports

These are auto-imported by Nuxt — no `import` statement needed:

- All composables in `app/composables/` (`useAuth`, `useApi`, `useCourses`, ...)
- All components in `app/components/` (`CourseCard`, `MarkdownContent`, ...)
- Vue primitives (`ref`, `computed`, `reactive`, `watch`, `onMounted`, ...)
- Nuxt primitives (`useRoute`, `useRouter`, `navigateTo`, `useState`, `useCookie`, `useRuntimeConfig`, `useSeoMeta`, ...)
- Nuxt UI components (`UCard`, `UButton`, ...)

Types and standard library still need explicit `import`.

## Dark Mode

Color mode is `dark` by default for authenticated pages. Marketing pages force light mode via `useColorMode()` in the marketing layout.

## Important Don'ts

- **Don't use `@nuxt/ui` components on marketing pages**
- **Don't introduce purple** — emerald palette only
- **Don't put CSS tokens in `:root` inside `<style scoped>`** — they won't match
- **Don't bump Tailwind to v4** — incompatible with `@nuxt/ui` v2
- **Don't import `$fetch`** — use `useApi()` via a composable
- **Don't add Pinia** — `useState` + composables is the project's pattern

## See Also

- API usage: `docs/frontend/api-usage.md`
- CRUD page patterns: `docs/frontend/crud-patterns.md`
- Form patterns: `.claude/agents/frontend-forms-agent.md`
