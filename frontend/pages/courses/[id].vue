<template>
  <NuxtLayout name="admin">

    <!-- Loading -->
    <div v-if="loading" class="flex justify-center py-24">
      <UIcon name="i-heroicons-arrow-path" class="animate-spin text-3xl text-emerald-500" />
    </div>

    <!-- Error -->
    <div v-else-if="error" class="flex flex-col items-center py-24 text-center">
      <UIcon name="i-heroicons-exclamation-triangle" class="mb-3 h-12 w-12 text-red-400" />
      <p class="text-gray-500 dark:text-gray-400">Course not found.</p>
      <UButton class="mt-4" size="sm" color="gray" @click="router.back()">Go Back</UButton>
    </div>

    <template v-else-if="course">

      <!-- Header -->
      <div class="mb-6 flex flex-wrap items-start justify-between gap-3">
        <div class="flex items-center gap-3">
          <UButton icon="i-heroicons-arrow-left" size="sm" color="gray" variant="ghost"
            @click="router.back()" />
          <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ course.name }}</h1>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
              By {{ course.user?.fullname || 'CODECV' }}
            </p>
          </div>
        </div>
        <!-- Admin actions -->
        <div v-if="isAdmin" class="flex gap-2">
          <UButton size="sm" color="gray" variant="outline" icon="i-heroicons-pencil-square"
            @click="navigateTo(`/courses/${course.id}/edit`)">
            Edit
          </UButton>
          <UButton size="sm" color="red" variant="outline" icon="i-heroicons-trash"
            @click="handleDelete">
            Delete
          </UButton>
        </div>
      </div>

      <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">

        <!-- Main content -->
        <div class="lg:col-span-2 flex flex-col gap-5">

          <!-- Course info card -->
          <div class="rounded-xl border border-gray-200 bg-white dark:border-gray-700 dark:bg-gray-800">
            <div class="flex items-center gap-4 border-b border-gray-100 px-6 py-5 dark:border-gray-700">
              <div class="flex h-14 w-14 shrink-0 items-center justify-center rounded-xl bg-emerald-50 dark:bg-emerald-950">
                <UIcon name="i-heroicons-book-open" class="h-7 w-7 text-emerald-500" />
              </div>
              <div>
                <h2 class="text-base font-bold text-gray-900 dark:text-white">{{ course.name }}</h2>
                <p class="text-xs text-gray-500 dark:text-gray-400">{{ course.slug }}</p>
              </div>
            </div>
            <div class="px-6 py-5">
              <h3 class="mb-2 text-sm font-semibold text-gray-900 dark:text-white">About this course</h3>
              <p class="text-sm text-gray-600 leading-relaxed dark:text-gray-400">
                {{ course.description || 'No description provided for this course yet.' }}
              </p>
            </div>
          </div>

          <!-- What you'll learn (placeholder) -->
          <div class="rounded-xl border border-gray-200 bg-white dark:border-gray-700 dark:bg-gray-800">
            <div class="border-b border-gray-100 px-6 py-4 dark:border-gray-700">
              <h3 class="text-sm font-semibold text-gray-900 dark:text-white">What you'll learn</h3>
            </div>
            <div class="grid grid-cols-1 gap-2 px-6 py-5 sm:grid-cols-2">
              <div v-for="item in whatYoullLearn" :key="item"
                class="flex items-start gap-2 text-sm text-gray-600 dark:text-gray-400">
                <UIcon name="i-heroicons-check-circle" class="mt-0.5 h-4 w-4 shrink-0 text-green-500" />
                {{ item }}
              </div>
            </div>
          </div>

        </div>

        <!-- Sidebar -->
        <div class="flex flex-col gap-5">

          <!-- Enroll / Start card -->
          <div class="rounded-xl border border-gray-200 bg-white dark:border-gray-700 dark:bg-gray-800">
            <div class="p-6">
              <div class="mb-4 text-center">
                <p class="text-xs font-semibold uppercase tracking-widest text-emerald-500 dark:text-emerald-400">
                  Ready to start?
                </p>
                <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                  Contact your consultant to get access to this course and begin your learning journey.
                </p>
              </div>
              <a
                :href="`https://wa.me/353894050730?text=Hi%2C+I%27d+like+to+start+the+course+%22${encodeURIComponent(course.name)}%22.+Can+you+help+me+get+access%3F`"
                target="_blank"
                class="block w-full rounded-lg bg-emerald-600 px-4 py-3 text-center text-sm font-semibold
                       text-white transition-opacity hover:opacity-90"
              >
                Request Access via WhatsApp
              </a>
              <p class="mt-3 text-center text-xs text-gray-400 dark:text-gray-500">
                We'll get back to you within 24 hours
              </p>
            </div>
          </div>

          <!-- Course meta -->
          <div class="rounded-xl border border-gray-200 bg-white dark:border-gray-700 dark:bg-gray-800">
            <div class="border-b border-gray-100 px-5 py-4 dark:border-gray-700">
              <h3 class="text-sm font-semibold text-gray-900 dark:text-white">Course Details</h3>
            </div>
            <div class="divide-y divide-gray-100 dark:divide-gray-700">
              <div class="flex items-center justify-between px-5 py-3">
                <span class="text-xs text-gray-500 dark:text-gray-400">Instructor</span>
                <div class="flex items-center gap-2">
                  <UAvatar :alt="course.user?.fullname" size="xs" />
                  <span class="text-xs font-medium text-gray-800 dark:text-gray-200">
                    {{ course.user?.fullname || 'CODECV' }}
                  </span>
                </div>
              </div>
              <div class="flex items-center justify-between px-5 py-3">
                <span class="text-xs text-gray-500 dark:text-gray-400">Created</span>
                <span class="text-xs font-medium text-gray-800 dark:text-gray-200">
                  {{ formatDate(course.created_at) }}
                </span>
              </div>
              <div class="flex items-center justify-between px-5 py-3">
                <span class="text-xs text-gray-500 dark:text-gray-400">Language</span>
                <span class="text-xs font-medium text-gray-800 dark:text-gray-200">English / Portuguese</span>
              </div>
            </div>
          </div>

        </div>
      </div>
    </template>

  </NuxtLayout>
</template>

<script setup lang="ts">
definePageMeta({ layout: false, middleware: 'auth' })

const route  = useRoute()
const router = useRouter()
const toast  = useToast()
const { user } = useAuth()
const isAdmin  = computed(() => user.value?.role === 'admin')

const { fetchCourses, deleteCourse } = useCourses()
const course  = ref<any>(null)
const loading = ref(true)
const error   = ref(false)

onMounted(async () => {
  try {
    const res = await fetchCourses({ per_page: 100 })
    course.value = res?.data?.find((c: any) => c.id === Number(route.params.id)) ?? null
    if (!course.value) error.value = true
  } catch {
    error.value = true
  } finally {
    loading.value = false
  }
})

const whatYoullLearn = [
  'Core concepts and best practices',
  'Hands-on projects and exercises',
  'Real-world application techniques',
  'Industry-relevant skills for the Irish market',
  'Code review and feedback sessions',
  'Certificate of completion',
]

const formatDate = (d: string) => d ? new Date(d).toLocaleDateString('en-IE', { year: 'numeric', month: 'long', day: 'numeric' }) : '—'

async function handleDelete() {
  if (!confirm('Delete this course? This cannot be undone.')) return
  try {
    await deleteCourse(Number(route.params.id))
    toast.add({ title: 'Course deleted', color: 'green' })
    navigateTo('/courses')
  } catch {
    toast.add({ title: 'Failed to delete', color: 'red' })
  }
}

useHead(() => ({ title: course.value ? `${course.value.name} — CODECV` : 'Course — CODECV' }))
</script>
