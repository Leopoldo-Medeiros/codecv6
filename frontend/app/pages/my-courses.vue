<template>
  <NuxtLayout name="admin">

    <!-- Header -->
    <div class="mb-6 flex flex-wrap items-start justify-between gap-3">
      <div>
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">My Courses</h1>
        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Track your learning progress and continue where you left off.</p>
      </div>
      <UButton icon="i-heroicons-magnifying-glass" size="sm" color="gray" variant="outline"
        @click="activeTab = 'browse'">
        Browse Courses
      </UButton>
    </div>

    <!-- Overall progress summary -->
    <div class="mb-6 grid grid-cols-2 gap-4 lg:grid-cols-4">
      <div v-for="stat in progressStats" :key="stat.label"
        class="flex items-center gap-4 rounded-xl border border-gray-200 bg-white p-5
               dark:border-gray-700 dark:bg-gray-800">
        <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl text-lg"
          :style="`background:${stat.color}18; color:${stat.color}`">
          <UIcon :name="stat.icon" />
        </div>
        <div>
          <p class="text-2xl font-bold leading-none text-gray-900 dark:text-white">
            <span v-if="loading" class="inline-block h-7 w-12 animate-pulse rounded-md bg-gray-200 dark:bg-gray-700" />
            <span v-else>{{ stat.value }}</span>
          </p>
          <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">{{ stat.label }}</p>
        </div>
      </div>
    </div>

    <!-- Tabs -->
    <div class="mb-5 flex gap-1 border-b border-gray-200 dark:border-gray-700">
      <button v-for="t in tabs" :key="t.value"
        class="px-4 py-2.5 text-sm font-medium transition-colors border-b-2 -mb-px"
        :class="activeTab === t.value
          ? 'border-emerald-600 text-emerald-600 dark:text-emerald-400 dark:border-emerald-400'
          : 'border-transparent text-gray-500 hover:text-gray-700 dark:text-gray-400'"
        @click="activeTab = t.value">
        {{ t.label }}
      </button>
    </div>

    <div class="grid grid-cols-1 gap-5 lg:grid-cols-3">

      <!-- Course list -->
      <div class="lg:col-span-2">

        <!-- Browse tab -->
        <div v-if="activeTab === 'browse'"
          class="rounded-xl border border-gray-200 bg-white dark:border-gray-700 dark:bg-gray-800">
          <div class="border-b border-gray-100 px-5 py-4 dark:border-gray-700">
            <h2 class="text-sm font-semibold text-gray-900 dark:text-white">Available Courses</h2>
            <p class="mt-0.5 text-xs text-gray-500 dark:text-gray-400">All courses on the platform</p>
          </div>
          <div class="p-5">
            <div v-if="catalogLoading" class="flex justify-center py-12 text-emerald-500">
              <UIcon name="i-heroicons-arrow-path" class="animate-spin text-2xl" />
            </div>
            <div v-else-if="!catalog.length" class="py-12 text-center text-sm text-gray-400">
              No courses available yet.
            </div>
            <div v-else class="flex flex-col gap-3">
              <div v-for="course in catalog" :key="course.id"
                class="flex items-start gap-4 rounded-lg border border-gray-100 p-4
                       dark:border-gray-700 hover:border-emerald-200 dark:hover:border-emerald-800 transition-colors">
                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-emerald-50 dark:bg-emerald-950">
                  <UIcon name="i-heroicons-book-open" class="h-5 w-5 text-emerald-500" />
                </div>
                <div class="min-w-0 flex-1">
                  <p class="text-sm font-semibold text-gray-900 dark:text-white">{{ course.name }}</p>
                  <p class="mt-0.5 text-xs text-gray-500 dark:text-gray-400 line-clamp-2">
                    {{ course.description || 'No description available.' }}
                  </p>
                  <p class="mt-1 text-xs text-gray-400 dark:text-gray-500">
                    By {{ course.user?.fullname || 'CODECV' }}
                  </p>
                </div>
                <UButton size="xs" color="emerald" variant="soft"
                  icon="i-heroicons-arrow-right"
                  trailing
                  @click="navigateTo(`/courses/${course.id}`)">
                  View
                </UButton>
              </div>
            </div>
          </div>
        </div>

        <!-- My Courses tab -->
        <div v-else
          class="rounded-xl border border-gray-200 bg-white dark:border-gray-700 dark:bg-gray-800">
          <div class="flex items-center justify-between border-b border-gray-100 px-5 py-4 dark:border-gray-700">
            <div>
              <h2 class="text-sm font-semibold text-gray-900 dark:text-white">My Enrolled Courses</h2>
              <p class="mt-0.5 text-xs text-gray-500 dark:text-gray-400">Your progress and active courses</p>
            </div>
            <div class="flex gap-1">
              <UButton
                v-for="f in filters" :key="f.value"
                size="xs" :color="activeFilter === f.value ? 'emerald' : 'gray'"
                :variant="activeFilter === f.value ? 'solid' : 'ghost'"
                @click="activeFilter = f.value"
              >{{ f.label }}</UButton>
            </div>
          </div>
          <div class="p-5">
            <div v-if="loading" class="flex justify-center py-12 text-emerald-500">
              <UIcon name="i-heroicons-arrow-path" class="animate-spin text-2xl" />
            </div>
            <div v-else class="flex flex-col items-center py-12 text-center">
              <div class="mb-4 flex h-16 w-16 items-center justify-center rounded-full bg-emerald-50 dark:bg-emerald-950">
                <UIcon name="i-heroicons-book-open" class="h-8 w-8 text-emerald-400" />
              </div>
              <h3 class="text-sm font-semibold text-gray-900 dark:text-white">No enrollments yet</h3>
              <p class="mt-1 max-w-xs text-sm text-gray-500 dark:text-gray-400">
                Your consultant will assign courses to your learning path.
              </p>
              <UButton class="mt-4" size="sm" icon="i-heroicons-arrow-right" trailing
                @click="activeTab = 'browse'">
                Browse Courses
              </UButton>
            </div>
          </div>
        </div>
      </div>

      <!-- Right column -->
      <div class="flex flex-col gap-5">

        <!-- Learning path progress -->
        <div class="rounded-xl border border-gray-200 bg-white dark:border-gray-700 dark:bg-gray-800">
          <div class="border-b border-gray-100 px-5 py-4 dark:border-gray-700">
            <h2 class="text-sm font-semibold text-gray-900 dark:text-white">My Learning Path</h2>
            <p class="mt-0.5 text-xs text-gray-500 dark:text-gray-400">Assigned by your consultant</p>
          </div>
          <div class="p-5">
            <div v-if="!activePath" class="flex flex-col items-center py-6 text-center">
              <UIcon name="i-heroicons-map" class="mb-2 h-10 w-10 text-gray-300 dark:text-gray-600" />
              <p class="text-sm text-gray-500 dark:text-gray-400">No learning path assigned yet.</p>
              <p class="mt-1 text-xs text-gray-400 dark:text-gray-500">Your consultant will set this up.</p>
            </div>
            <div v-else>
              <p class="mb-3 text-sm font-medium text-gray-900 dark:text-white">{{ activePath.name }}</p>
              <div class="mb-3 flex items-center gap-2">
                <UProgress :value="activePath.progress" size="md" color="emerald" class="flex-1" />
                <span class="text-sm font-semibold text-emerald-600 dark:text-emerald-400">{{ activePath.progress }}%</span>
              </div>
              <p class="text-xs text-gray-500 dark:text-gray-400">
                {{ activePath.completed }} of {{ activePath.total }} milestones completed
              </p>
            </div>
          </div>
        </div>

        <!-- Recent achievements -->
        <div class="rounded-xl border border-gray-200 bg-white dark:border-gray-700 dark:bg-gray-800">
          <div class="border-b border-gray-100 px-5 py-4 dark:border-gray-700">
            <h2 class="text-sm font-semibold text-gray-900 dark:text-white">Achievements</h2>
          </div>
          <div class="p-5">
            <div v-if="!achievements.length" class="flex flex-col items-center py-6 text-center">
              <UIcon name="i-heroicons-trophy" class="mb-2 h-10 w-10 text-gray-300 dark:text-gray-600" />
              <p class="text-sm text-gray-500 dark:text-gray-400">Complete your first course to earn a badge.</p>
            </div>
            <div v-else class="flex flex-col gap-3">
              <div v-for="a in achievements" :key="a.label" class="flex items-center gap-3">
                <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg"
                  :style="`background:${a.color}18;color:${a.color}`">
                  <UIcon :name="a.icon" class="h-5 w-5" />
                </div>
                <div>
                  <p class="text-sm font-medium text-gray-900 dark:text-white">{{ a.label }}</p>
                  <p class="text-xs text-gray-400 dark:text-gray-500">{{ a.date }}</p>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- CTA -->
        <div class="rounded-xl p-5 text-white" style="background:linear-gradient(135deg,#047857,#059669)">
          <p class="text-xs font-semibold uppercase tracking-widest text-emerald-200">1-on-1 Mentorship</p>
          <h3 class="mt-2 text-base font-bold">Need guidance from an expert?</h3>
          <p class="mt-1 text-sm text-emerald-100 leading-relaxed">
            Book a session with your consultant and accelerate your progress.
          </p>
          <button
            class="mt-4 rounded-lg bg-white px-4 py-2 text-sm font-semibold text-emerald-700
                   transition-opacity hover:opacity-90"
            @click="navigateTo('/pricing')"
          >
            Book a Session
          </button>
        </div>

      </div>
    </div>

  </NuxtLayout>
</template>

<script setup lang="ts">
definePageMeta({ layout: false, middleware: 'auth' })

useHead({ title: 'My Courses — CODECV' })

const loading        = ref(false)
const catalogLoading = ref(false)
const enrolledCourses = ref<any[]>([])
const activePath      = ref<any>(null)
const achievements    = ref<any[]>([])

const activeTab = ref('browse')
const tabs = [
  { label: 'Browse Courses', value: 'browse' },
  { label: 'My Enrolled',    value: 'enrolled' },
]

const activeFilter = ref('all')
const filters = [
  { label: 'All',         value: 'all' },
  { label: 'In Progress', value: 'in_progress' },
  { label: 'Completed',   value: 'completed' },
]

const filteredCourses = computed(() =>
  activeFilter.value === 'all'
    ? enrolledCourses.value
    : enrolledCourses.value.filter((c: any) => c.status === activeFilter.value)
)

const { courses: catalog, fetchCourses } = useCourses()

onMounted(async () => {
  catalogLoading.value = true
  try {
    await fetchCourses({ per_page: 50 })
  } finally {
    catalogLoading.value = false
  }
})

const progressStats = computed(() => [
  { label: 'Available',    value: catalog.value?.length ?? 0,                                        icon: 'i-heroicons-book-open',    color: '#6366f1' },
  { label: 'Enrolled',     value: enrolledCourses.value.length,                                      icon: 'i-heroicons-check-circle', color: '#10b981' },
  { label: 'In Progress',  value: enrolledCourses.value.filter((c: any) => c.status === 'in_progress').length, icon: 'i-heroicons-arrow-path', color: '#0ea5e9' },
  { label: 'Achievements', value: achievements.value.length,                                         icon: 'i-heroicons-trophy',       color: '#f59e0b' },
])

const statusColor = (s: string) => ({ completed: 'green', in_progress: 'blue', not_started: 'gray' }[s] ?? 'gray')
</script>
