# Frontend CRUD Patterns (codecv6)

Standard patterns for list / detail / create / edit / delete pages in the codecv6 frontend.

## The Composable Pattern (recap)

Every domain has a composable in `frontend/app/composables/use<Domain>.ts` that exposes:

```ts
const {
  data,                  // readonly reactive list or single resource
  meta,                  // pagination meta (lists only)
  loading,               // readonly boolean
  error,                 // readonly string | null
  fetchItems,            // load list
  fetchItem,             // load single
  createItem,            // POST
  updateItem,            // PUT/PATCH
  deleteItem,            // DELETE
} = useCourses()
```

Pages call these methods; pages never call `useApi()` directly.

## List Page

```vue
<!-- frontend/app/pages/courses/index.vue -->
<script setup lang="ts">
definePageMeta({ middleware: 'auth', layout: 'admin' })

const { data: courses, meta, loading, error, fetchCourses, deleteCourse } = useCourses()
const router = useRouter()
const page = ref(1)

onMounted(() => fetchCourses(page.value))

async function onDelete(id: number) {
  if (!confirm('Delete this course?')) return
  const ok = await deleteCourse(id)
  if (ok) {
    const toast = useToast()
    toast.add({ title: 'Course deleted', color: 'success' })
  }
}

function goToEdit(id: number) {
  router.push(`/courses/${id}/edit`)
}
</script>

<template>
  <div class="space-y-6">
    <div class="flex items-center justify-between">
      <h1 class="text-2xl font-bold">Courses</h1>
      <UButton to="/courses/new" color="primary">New course</UButton>
    </div>

    <UAlert v-if="error" color="error" variant="soft" :title="error" />

    <UCard v-if="loading && courses.length === 0">
      <USkeleton class="h-32 w-full" />
    </UCard>

    <UCard v-else>
      <UTable :rows="courses" :columns="columns">
        <template #actions-data="{ row }">
          <UDropdown
            :items="[[
              { label: 'Edit', icon: 'i-heroicons-pencil', click: () => goToEdit(row.id) },
              { label: 'Delete', icon: 'i-heroicons-trash', click: () => onDelete(row.id) },
            ]]"
          >
            <UButton icon="i-heroicons-ellipsis-vertical" variant="ghost" size="xs" />
          </UDropdown>
        </template>
      </UTable>

      <UPagination
        v-if="meta && meta.last_page > 1"
        v-model="page"
        :total="meta.total"
        :page-count="meta.per_page"
        class="mt-6"
        @update:model-value="(p: number) => fetchCourses(p)"
      />
    </UCard>
  </div>
</template>

<script setup lang="ts">
const columns = [
  { key: 'id', label: '#' },
  { key: 'title', label: 'Title' },
  { key: 'is_published', label: 'Published' },
  { key: 'actions', label: '' },
]
</script>
```

## Detail Page

```vue
<!-- frontend/app/pages/courses/[id].vue -->
<script setup lang="ts">
definePageMeta({ middleware: 'auth', layout: 'admin' })

const route = useRoute()
const { current: course, loading, error, fetchCourse } = useCourses()

onMounted(() => fetchCourse(Number(route.params.id)))
</script>

<template>
  <div>
    <UAlert v-if="error" color="error" variant="soft" :title="error" />

    <USkeleton v-if="loading" class="h-64 w-full" />

    <UCard v-else-if="course">
      <template #header>
        <div class="flex items-center justify-between">
          <h1 class="text-2xl font-bold">{{ course.title }}</h1>
          <UButton :to="`/courses/${course.id}/edit`" color="primary" variant="outline">
            Edit
          </UButton>
        </div>
      </template>

      <img v-if="course.image_url" :src="course.image_url" :alt="course.title" class="w-full rounded-lg" />
      <p class="mt-4 whitespace-pre-wrap">{{ course.description }}</p>
    </UCard>
  </div>
</template>
```

## Create Page

```vue
<!-- frontend/app/pages/courses/new.vue -->
<script setup lang="ts">
definePageMeta({ middleware: 'auth', layout: 'admin' })

const { loading, error, createCourse } = useCourses()
const router = useRouter()
const toast = useToast()

const state = reactive({
  title: '',
  description: '',
  image: null as File | null,
  is_published: false,
})

const canSubmit = computed(() => state.title.trim().length > 0)

function onFile(event: Event) {
  state.image = (event.target as HTMLInputElement).files?.[0] ?? null
}

async function onSubmit() {
  const fd = new FormData()
  fd.append('title', state.title)
  if (state.description) fd.append('description', state.description)
  if (state.image) fd.append('image', state.image)
  fd.append('is_published', state.is_published ? '1' : '0')

  const created = await createCourse(fd)
  if (created) {
    toast.add({ title: 'Course created', color: 'success' })
    router.push(`/courses/${created.id}`)
  }
}
</script>

<template>
  <UCard>
    <template #header>
      <h1 class="text-2xl font-bold">New course</h1>
    </template>

    <UForm :state="state" class="space-y-6" @submit="onSubmit">
      <UFormField label="Title" name="title" required>
        <UInput v-model.trim="state.title" placeholder="Course title" size="xl" />
      </UFormField>

      <UFormField label="Description" name="description">
        <UTextarea v-model="state.description" rows="6" placeholder="Short description" />
      </UFormField>

      <UFormField label="Cover image" name="image">
        <UInput type="file" accept="image/*" @change="onFile" />
      </UFormField>

      <UFormField name="is_published">
        <USwitch v-model="state.is_published" label="Publish immediately" />
      </UFormField>

      <UButton
        type="submit"
        color="primary"
        :loading="loading"
        :disabled="!canSubmit"
        class="justify-center"
      >
        Create course
      </UButton>

      <UAlert v-if="error" color="error" variant="soft" :title="error" />
    </UForm>
  </UCard>
</template>
```

## Edit Page

```vue
<!-- frontend/app/pages/courses/[id]/edit.vue -->
<script setup lang="ts">
definePageMeta({ middleware: 'auth', layout: 'admin' })

const route = useRoute()
const router = useRouter()
const toast = useToast()

const { current: course, loading, error, fetchCourse, updateCourse } = useCourses()

const state = reactive({
  title: '',
  description: '',
  image: null as File | null,
  is_published: false,
})

onMounted(async () => {
  await fetchCourse(Number(route.params.id))
  if (course.value) {
    state.title = course.value.title
    state.description = course.value.description ?? ''
    state.is_published = course.value.is_published
  }
})

function onFile(event: Event) {
  state.image = (event.target as HTMLInputElement).files?.[0] ?? null
}

async function onSubmit() {
  const fd = new FormData()
  fd.append('_method', 'PUT') // Laravel form-method spoofing for multipart
  fd.append('title', state.title)
  if (state.description) fd.append('description', state.description)
  if (state.image) fd.append('image', state.image)
  fd.append('is_published', state.is_published ? '1' : '0')

  const updated = await updateCourse(Number(route.params.id), fd)
  if (updated) {
    toast.add({ title: 'Course updated', color: 'success' })
    router.push(`/courses/${updated.id}`)
  }
}
</script>

<template>
  <UCard>
    <template #header>
      <h1 class="text-2xl font-bold">Edit course</h1>
    </template>

    <UAlert v-if="error" color="error" variant="soft" :title="error" class="mb-4" />
    <USkeleton v-if="loading && !course" class="h-32 w-full" />

    <UForm v-if="course" :state="state" class="space-y-6" @submit="onSubmit">
      <UFormField label="Title" name="title" required>
        <UInput v-model.trim="state.title" size="xl" />
      </UFormField>

      <UFormField label="Description" name="description">
        <UTextarea v-model="state.description" rows="6" />
      </UFormField>

      <UFormField label="Replace image" name="image">
        <UInput type="file" accept="image/*" @change="onFile" />
      </UFormField>

      <UFormField name="is_published">
        <USwitch v-model="state.is_published" label="Published" />
      </UFormField>

      <UButton type="submit" color="primary" :loading="loading" class="justify-center">
        Save changes
      </UButton>
    </UForm>
  </UCard>
</template>
```

## Delete Pattern

Always confirm before destructive actions, and provide undo when possible:

```ts
async function onDelete(id: number) {
  if (!confirm('Delete this course? This cannot be undone.')) return

  const ok = await deleteCourse(id)
  if (ok) {
    toast.add({ title: 'Course deleted', color: 'success' })
  }
}
```

For more elaborate confirmation, use `UModal`:

```vue
<UModal v-model="confirmOpen">
  <UCard>
    <template #header>
      <h3>Delete "{{ targetCourse?.title }}"?</h3>
    </template>
    <p>This will permanently delete the course. Are you sure?</p>
    <template #footer>
      <div class="flex gap-2 justify-end">
        <UButton color="gray" variant="ghost" @click="confirmOpen = false">Cancel</UButton>
        <UButton color="error" :loading="loading" @click="confirmDelete">Delete</UButton>
      </div>
    </template>
  </UCard>
</UModal>
```

## File Uploads

Use `FormData` and `_method=PUT` for multipart updates (Laravel doesn't parse multipart for native PUT/PATCH).

```ts
const fd = new FormData()
fd.append('_method', 'PUT')
fd.append('title', state.title)
fd.append('image', state.image)

await useApi()(`/courses/${id}`, { method: 'POST', body: fd })
```

## Pagination

The Laravel Resource Collection response wraps pagination meta:

```json
{
  "data": [...],
  "meta": {
    "current_page": 1,
    "last_page": 5,
    "per_page": 20,
    "total": 96
  },
  "links": {
    "first": "...",
    "last": "...",
    "prev": null,
    "next": "..."
  }
}
```

Use `UPagination` from `@nuxt/ui`:

```vue
<UPagination
  v-model="page"
  :total="meta.total"
  :page-count="meta.per_page"
  @update:model-value="(p: number) => fetchCourses(p)"
/>
```

## Loading & Error States

Three rendering branches per page:

1. **Initial loading** (no data yet): `USkeleton` or spinner
2. **Error**: `UAlert` with the error message
3. **Empty state**: friendly message + CTA
4. **Data**: the actual UI

```vue
<template>
  <UAlert v-if="error" color="error" variant="soft" :title="error" />

  <USkeleton v-else-if="loading && data.length === 0" class="h-64 w-full" />

  <UCard v-else-if="data.length === 0">
    <div class="text-center py-12">
      <UIcon name="i-heroicons-inbox" class="mx-auto text-5xl text-gray-400" />
      <p class="mt-4 text-gray-600">No courses yet.</p>
      <UButton to="/courses/new" color="primary" class="mt-4">Create your first course</UButton>
    </div>
  </UCard>

  <div v-else>
    <!-- list rendering -->
  </div>
</template>
```

## Toast Notifications

```ts
const toast = useToast()
toast.add({ title: 'Course created', color: 'success' })
toast.add({ title: 'Could not save', description: error.value, color: 'error' })
```

## Search / Filter

Add query params and a debounced watcher:

```ts
const search = ref('')

watchDebounced(
  search,
  () => fetchCourses(1, { search: search.value }),
  { debounce: 300 },
)
```

Update the composable to forward `params`:

```ts
async function fetchCourses(page = 1, filters: Record<string, unknown> = {}) {
  const res = await useApi()<PaginatedResponse<Course>>('/courses', {
    params: { page, ...filters },
  })
  // ...
}
```

## Common Don'ts

- **Don't fetch in `setup()` for SPA routes** — use `onMounted()` so it runs client-side
- **Don't store the entire list in `localStorage`** — refetch on mount
- **Don't reach into another page's state via `provide/inject`** — each page owns its own state via its composable
- **Don't show stale data after a mutation** — composables optimistically update; if uncertain, refetch
- **Don't render hidden buttons** for actions the user can't perform — check `isAdmin` / `isConsultant` from `useAuth`

## See Also

- API usage: `docs/frontend/api-usage.md`
- Components & layouts: `docs/frontend/component-guide.md`
- Forms: `.claude/agents/frontend-forms-agent.md`
