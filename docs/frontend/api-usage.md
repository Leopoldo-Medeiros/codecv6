# Frontend API Usage (codecv6)

How the codecv6 frontend talks to the Laravel API.

## The Rule

**ALWAYS** use composables in `frontend/app/composables/` for API calls. **NEVER** use `$fetch` or `fetch` directly in components or pages.

## The Base Wrapper: `useApi()`

`useApi()` is the only place that touches `$fetch` / `ofetch` directly. It handles:

- **Base URL** from `useRuntimeConfig().public.apiBase`
- **Bearer token** from `localStorage.getItem('auth_token')` → `Authorization: Bearer <token>` header
- **CSRF token** from the `XSRF-TOKEN` cookie → `X-XSRF-TOKEN` header
- **Credentials**: `include` (so cookies travel with requests)
- **Timeout**: 90s
- **401 handler**: clears local auth state and redirects to `/login`

```ts
// frontend/app/composables/useApi.ts (simplified)
export const useApi = () => {
  const config = useRuntimeConfig()

  return $fetch.create({
    baseURL: `${config.public.apiBase}/api`,
    credentials: 'include',
    timeout: 90_000,
    onRequest({ options }) {
      const token = import.meta.client ? localStorage.getItem('auth_token') : null
      if (token) options.headers.set('Authorization', `Bearer ${token}`)

      const xsrf = useCookie('XSRF-TOKEN').value
      if (xsrf) options.headers.set('X-XSRF-TOKEN', decodeURIComponent(xsrf))
    },
    onResponseError({ response }) {
      if (response.status === 401 && import.meta.client) {
        localStorage.removeItem('auth_token')
        localStorage.removeItem('auth_user')
        navigateTo('/login')
      }
    },
  })
}
```

## Domain Composables

Each domain has its own composable that wraps `useApi()` and exposes reactive state.

```ts
// frontend/app/composables/useCourses.ts (canonical shape)
import type { Course, CreateCoursePayload, PaginatedResponse } from '~/types/models'

export const useCourses = () => {
  const data = ref<Course[]>([])
  const meta = ref<PaginatedResponse<Course>['meta'] | null>(null)
  const loading = ref(false)
  const error = ref<string | null>(null)

  async function fetchCourses(page = 1) {
    loading.value = true
    error.value = null
    try {
      const res = await useApi()<PaginatedResponse<Course>>('/courses', {
        params: { page },
      })
      data.value = res.data
      meta.value = res.meta
    } catch (e: unknown) {
      error.value = (e as Error).message
    } finally {
      loading.value = false
    }
  }

  async function createCourse(payload: CreateCoursePayload): Promise<Course | null> {
    loading.value = true
    error.value = null
    try {
      const res = await useApi()<{ course: Course; message: string }>('/courses', {
        method: 'POST',
        body: payload,
      })
      data.value.unshift(res.course)
      return res.course
    } catch (e: unknown) {
      error.value = (e as Error).message
      return null
    } finally {
      loading.value = false
    }
  }

  async function deleteCourse(id: number): Promise<boolean> {
    loading.value = true
    error.value = null
    try {
      await useApi()(`/courses/${id}`, { method: 'DELETE' })
      data.value = data.value.filter(c => c.id !== id)
      return true
    } catch (e: unknown) {
      error.value = (e as Error).message
      return false
    } finally {
      loading.value = false
    }
  }

  return {
    data: readonly(data),
    meta: readonly(meta),
    loading: readonly(loading),
    error: readonly(error),
    fetchCourses,
    createCourse,
    deleteCourse,
  }
}
```

### Conventions

- **Return `readonly()` refs** for `data`, `loading`, `error` so consumers can't mutate them
- **Standard try/catch/finally** shape — `loading.value = false` in `finally`
- **Error extraction**: `(e as Error).message`
- **Method names**: `fetch<Plural>`, `create<Singular>`, `update<Singular>`, `delete<Singular>`
- **Optimistic local updates** (push/unshift/filter) are fine — but always refetch on uncertain state

## Using a Composable

```vue
<!-- frontend/app/pages/courses/index.vue -->
<script setup lang="ts">
definePageMeta({ middleware: 'auth', layout: 'admin' })

const { data: courses, meta, loading, error, fetchCourses } = useCourses()

onMounted(() => fetchCourses())
</script>

<template>
  <div>
    <h1 class="text-2xl font-bold">Courses</h1>

    <UAlert v-if="error" color="error" variant="soft" :title="error" />

    <USkeleton v-if="loading && courses.length === 0" class="h-64 w-full" />

    <UCard v-for="course in courses" :key="course.id">
      <h3>{{ course.title }}</h3>
      <p>{{ course.description }}</p>
    </UCard>

    <UPagination
      v-if="meta"
      v-model="page"
      :total="meta.total"
      :page-count="meta.per_page"
      @update:model-value="fetchCourses"
    />
  </div>
</template>
```

## The Existing Composables

| Composable | Domain |
|---|---|
| `useAuth` | Login, logout, user state, role checks (`isAdmin`, `isClient`, `isConsultant`) |
| `useApi` | Base `$fetch` wrapper |
| `useUsers` | Admin CRUD on users |
| `useCourses` | Course list / create / update / delete |
| `usePlans` | Plan CRUD |
| `usePaths` | Path CRUD + step progress updates |
| `useJobs` | Job listings |
| `useMyClients` | Consultant client management (assign/remove paths) |
| `useNotifications` | Notification fetch + mark-as-read |
| `useCheckout` | Stripe Checkout (`startCheckout`, `getStatus`, `detectCurrency`) |
| `useAuthTheme` | Theme switching between auth/marketing pages |

## When to Create a New Composable

- A new domain area emerges (e.g. `useReports`, `useChallenges`)
- Multiple components need the same network call + state
- A page-level fetch grows past ~30 lines

When in doubt: create one. They're cheap and they keep components clean.

## CSRF Cookie Flow

For stateful (cookie-based) endpoints (Sanctum), call `/sanctum/csrf-cookie` once at app boot. The frontend `useAuth.login()` does this before posting credentials.

```ts
// Before any POST/PUT/PATCH/DELETE on a cookie-session endpoint
await $fetch('/sanctum/csrf-cookie', {
  baseURL: useRuntimeConfig().public.apiBase,
  credentials: 'include',
})
```

For token-only flows (most of the codecv6 API), the Bearer token is enough and the CSRF call is decorative — but doing it once is harmless.

## Error Handling

The composable swallows the error into `error.value` (a string message). Consumers render that:

```vue
<UAlert v-if="error" color="error" variant="soft" :title="error" />
```

For finer-grained per-field errors (Laravel 422 with `errors: { title: ['Required'] }`), the composable can extract the validation object:

```ts
catch (e: unknown) {
  const err = e as { data?: { message?: string; errors?: Record<string, string[]> } }
  error.value = err.data?.message ?? 'Request failed'
  fieldErrors.value = err.data?.errors ?? {}
}
```

## File Uploads

Use `FormData` and let the wrapper not stringify it.

```ts
async function uploadCv(file: File, jobDescription: string) {
  const fd = new FormData()
  fd.append('pdf', file)
  fd.append('job_description', jobDescription)

  return await useApi()('/cv/analyse', {
    method: 'POST',
    body: fd,
  })
}
```

`$fetch` auto-detects `FormData` and sets the correct `Content-Type: multipart/form-data; boundary=...` header.

## Tips & Gotchas

- **Don't import `$fetch` / `ofetch` in components** — use `useApi()`
- **Don't store tokens in `useState`** — `localStorage` is the source of truth; `useState` syncs the user object only
- **Don't add try/catch in components** — let the composable handle errors and expose them via `error.value`
- **Refresh local state after mutations** — either optimistically update the ref or call the `fetch*` method
- **No Pinia** — `useState` + composables are the pattern. Don't introduce a store layer

## Type Safety

All API payloads and responses should be typed via `frontend/app/types/models.ts`:

```ts
export interface Course {
  id: number
  title: string
  description: string | null
  image_url: string | null
  is_published: boolean
  created_at: string
  creator?: User // optional — only present when eager-loaded
}

export interface PaginatedResponse<T> {
  data: T[]
  meta: {
    current_page: number
    last_page: number
    per_page: number
    total: number
  }
  links: {
    first: string
    last: string
    prev: string | null
    next: string | null
  }
}

export interface ApiResponse<T> {
  message: string
  [key: string]: T | string
}
```

For types tied to a single composable (like `PathStep.user_status`), co-locate them in the composable file rather than in `models.ts`.

## See Also

- Components & layouts: `docs/frontend/component-guide.md`
- CRUD page patterns: `docs/frontend/crud-patterns.md`
- Auth flow: `docs/authentication.md`
