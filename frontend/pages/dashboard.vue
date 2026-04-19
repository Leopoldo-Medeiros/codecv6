<template>
  <NuxtLayout name="admin">

    <!-- Welcome header -->
    <div class="mb-6 flex flex-wrap items-start justify-between gap-3">
      <div>
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">
          Welcome back, {{ user?.fullname }} 👋
        </h1>
        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
          {{ isAdmin ? "Here's what's happening on your platform today." : 'Continue where you left off.' }}
        </p>
      </div>
      <UButton v-if="isAdmin" icon="i-heroicons-plus" size="sm" @click="navigateTo('/courses')">
        New Course
      </UButton>
    </div>

    <!-- Stat cards -->
    <div class="mb-6 grid grid-cols-2 gap-4 lg:grid-cols-4">
      <div
        v-for="stat in displayStats"
        :key="stat.label"
        class="flex items-center gap-4 rounded-xl border border-gray-200 bg-white p-5
               dark:border-gray-700 dark:bg-gray-800"
      >
        <div
          class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl text-lg"
          :style="`background:${stat.color}18; color:${stat.color}`"
        >
          <UIcon :name="stat.icon" />
        </div>
        <div>
          <p class="text-2xl font-bold leading-none text-gray-900 dark:text-white">
            <span v-if="statsLoading" class="inline-block h-7 w-12 animate-pulse rounded-md bg-gray-200 dark:bg-gray-700" />
            <span v-else>{{ stat.value }}</span>
          </p>
          <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">{{ stat.label }}</p>
        </div>
      </div>
    </div>

    <!-- Main grid -->
    <div class="grid grid-cols-1 items-start gap-5 lg:grid-cols-3">

      <!-- Left: main content -->
      <div class="lg:col-span-2">
        <div class="rounded-xl border border-gray-200 bg-white dark:border-gray-700 dark:bg-gray-800">

          <!-- Card header -->
          <div class="flex items-center justify-between border-b border-gray-100 px-5 py-4 dark:border-gray-700">
            <div>
              <h2 class="text-sm font-semibold text-gray-900 dark:text-white">
                {{ isAdmin ? 'Recent Users' : 'My Learning Paths' }}
              </h2>
              <p class="mt-0.5 text-xs text-gray-500 dark:text-gray-400">
                {{ isAdmin ? 'Latest registrations on the platform' : 'Your progress across all assigned paths' }}
              </p>
            </div>
            <UButton
              variant="ghost" color="gray" size="xs"
              trailing-icon="i-heroicons-arrow-right"
              @click="navigateTo(isAdmin ? '/users' : '/my-paths')"
            >
              View all
            </UButton>
          </div>

          <!-- Card body -->
          <div class="px-5 py-4">
            <div v-if="statsLoading" class="flex justify-center py-8 text-indigo-500">
              <UIcon name="i-heroicons-arrow-path" class="animate-spin text-xl" />
            </div>

            <!-- Admin: recent users table -->
            <template v-else-if="isAdmin">
              <UTable v-if="recentUsers.length" :rows="recentUsers" :columns="userColumns">
                <template #fullname-data="{ row }">
                  <div class="flex items-center gap-2.5">
                    <UAvatar :src="row.profile?.profile_image_url || '/images/team-13.jpg'" :alt="row.fullname" size="xs" />
                    <div>
                      <p class="text-sm font-medium text-gray-900 dark:text-white">{{ row.fullname }}</p>
                      <p class="text-xs text-gray-500 dark:text-gray-400">{{ row.email }}</p>
                    </div>
                  </div>
                </template>
                <template #role-data="{ row }">
                  <UBadge :color="roleBadgeColor(row.role)" variant="subtle" size="xs">{{ row.role }}</UBadge>
                </template>
                <template #created_at-data="{ row }">
                  <span class="text-xs text-gray-400 dark:text-gray-500">{{ formatDate(row.created_at) }}</span>
                </template>
                <template #actions-data="{ row }">
                  <UDropdown :items="getUserActions(row)" :popper="{ placement: 'bottom-end' }">
                    <UButton icon="i-heroicons-ellipsis-horizontal" color="gray" variant="ghost" size="xs" />
                  </UDropdown>
                </template>
              </UTable>
              <p v-else class="py-8 text-center text-sm text-gray-400 dark:text-gray-500">No users yet.</p>
            </template>

            <!-- Client: learning paths with progress -->
            <template v-else>
              <div v-if="clientPaths.length" class="flex flex-col gap-4">
                <div
                  v-for="path in clientPaths"
                  :key="path.id"
                  class="cursor-pointer rounded-xl border border-gray-100 p-4 transition-colors
                         hover:border-indigo-200 hover:bg-indigo-50/30
                         dark:border-gray-700 dark:hover:border-indigo-800 dark:hover:bg-indigo-950/20"
                  @click="navigateTo('/my-paths')"
                >
                  <div class="flex items-start justify-between gap-3">
                    <div class="flex items-center gap-3">
                      <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-indigo-50 dark:bg-indigo-950">
                        <UIcon name="i-heroicons-map" class="h-5 w-5 text-indigo-500" />
                      </div>
                      <div>
                        <p class="text-sm font-semibold text-gray-900 dark:text-white">{{ path.name }}</p>
                        <p class="text-xs text-gray-400 dark:text-gray-500">
                          {{ path.doneCount }}/{{ path.totalSteps }} steps completed
                        </p>
                      </div>
                    </div>
                    <span class="shrink-0 text-sm font-bold" :class="progressColor(path.pct)">
                      {{ path.pct }}%
                    </span>
                  </div>

                  <!-- Progress bar -->
                  <UProgress :value="path.pct" size="xs" color="indigo" class="mt-3" />

                  <!-- Next step -->
                  <div v-if="path.nextStep" class="mt-3 flex items-center gap-2">
                    <UIcon name="i-heroicons-arrow-right-circle" class="h-4 w-4 shrink-0 text-indigo-400" />
                    <span class="text-xs text-gray-500 dark:text-gray-400">
                      Next: <span class="font-medium text-gray-700 dark:text-gray-300">{{ path.nextStep }}</span>
                    </span>
                  </div>
                  <div v-else-if="path.totalSteps > 0" class="mt-3 flex items-center gap-2">
                    <UIcon name="i-heroicons-check-circle" class="h-4 w-4 shrink-0 text-green-500" />
                    <span class="text-xs font-medium text-green-600 dark:text-green-400">All steps completed!</span>
                  </div>
                </div>
              </div>

              <!-- Empty state -->
              <div v-else class="flex flex-col items-center py-10 text-center">
                <div class="mb-3 flex h-16 w-16 items-center justify-center rounded-full bg-indigo-50 dark:bg-indigo-950">
                  <UIcon name="i-heroicons-map" class="h-8 w-8 text-indigo-400" />
                </div>
                <p class="text-sm font-medium text-gray-700 dark:text-gray-300">No learning paths yet</p>
                <p class="mt-1 max-w-xs text-xs text-gray-400 dark:text-gray-500">
                  Your consultant will assign a personalised learning path based on your goals.
                </p>
                <UButton size="xs" color="indigo" variant="soft" class="mt-4" @click="navigateTo('/my-paths')">
                  Learn more
                </UButton>
              </div>
            </template>
          </div>
        </div>
      </div>

      <!-- Right: quick links + CTA -->
      <div class="flex flex-col gap-5">

        <!-- Quick links -->
        <div class="rounded-xl border border-gray-200 bg-white dark:border-gray-700 dark:bg-gray-800">
          <div class="border-b border-gray-100 px-5 py-4 dark:border-gray-700">
            <h2 class="text-sm font-semibold text-gray-900 dark:text-white">
              {{ isAdmin ? 'Quick Links' : 'Quick Actions' }}
            </h2>
          </div>
          <div class="flex flex-col gap-1 p-3">
            <button
              v-for="link in quickLinks"
              :key="link.label"
              class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-left text-sm font-medium
                     text-gray-700 transition-colors hover:bg-gray-50
                     dark:text-gray-300 dark:hover:bg-gray-700"
              @click="navigateTo(link.to)"
            >
              <span
                class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg text-base"
                :style="`background:${link.color}18;color:${link.color}`"
              >
                <UIcon :name="link.icon" />
              </span>
              <span class="flex-1">{{ link.label }}</span>
              <UIcon name="i-heroicons-chevron-right" class="h-4 w-4 text-gray-400 dark:text-gray-600" />
            </button>
          </div>
        </div>

        <!-- CTA card -->
        <div class="rounded-xl p-5 text-white" style="background:linear-gradient(135deg,#0ea5e9,#0284c7)">
          <p class="text-xs font-semibold uppercase tracking-widest text-sky-200">IT Career Coaching</p>
          <h3 class="mt-2 text-base font-bold">Ready to accelerate your career?</h3>
          <p class="mt-1 text-sm text-sky-100 leading-relaxed">
            Courses, learning paths, and job opportunities tailored for Irish tech roles.
          </p>
          <button
            class="mt-4 rounded-lg bg-white px-4 py-2 text-sm font-semibold text-sky-700
                   transition-opacity hover:opacity-90"
            @click="navigateTo(isAdmin ? '/courses' : '/my-paths')"
          >
            {{ isAdmin ? 'Explore Courses' : 'View My Paths' }}
          </button>
        </div>

      </div>
    </div>

  </NuxtLayout>
</template>

<script setup lang="ts">
import type { Path } from '~/composables/usePaths'

definePageMeta({ layout: false, middleware: 'auth' })

const { user } = useAuth()
const isAdmin  = computed(() => user.value?.role === 'admin')

const { fetchUsers }                           = useUsers()
const { fetchCourses }                         = useCourses()
const { fetchPaths, fetchMyPaths, fetchSteps } = usePaths()

const statsLoading = ref(false)

// Admin state
const totalUsers   = ref(0)
const totalCourses = ref(0)
const totalPaths   = ref(0)
const recentUsers  = ref<any[]>([])

// Client state
interface EnrichedPath {
  id: number; name: string; pct: number
  doneCount: number; totalSteps: number; nextStep: string | null
}
const clientPaths = ref<EnrichedPath[]>([])
const totalDone   = ref(0)
const totalSteps  = ref(0)

const overallPct = computed(() =>
  totalSteps.value ? Math.round((totalDone.value / totalSteps.value) * 100) : 0
)

const displayStats = computed(() => isAdmin.value
  ? [
      { label: 'Total Users',    value: totalUsers.value,   icon: 'i-heroicons-users',            color: '#6366f1' },
      { label: 'Active Courses', value: totalCourses.value, icon: 'i-heroicons-book-open',         color: '#0ea5e9' },
      { label: 'Learning Paths', value: totalPaths.value,   icon: 'i-heroicons-map',               color: '#10b981' },
      { label: 'Revenue',        value: '—',                icon: 'i-heroicons-currency-dollar',   color: '#f59e0b' },
    ]
  : [
      { label: 'My Paths',    value: clientPaths.value.length, icon: 'i-heroicons-map',            color: '#10b981' },
      { label: 'Steps Done',  value: totalDone.value,          icon: 'i-heroicons-check-circle',   color: '#0ea5e9' },
      { label: 'Total Steps', value: totalSteps.value,         icon: 'i-heroicons-list-bullet',    color: '#6366f1' },
      { label: 'Progress',    value: `${overallPct.value}%`,   icon: 'i-heroicons-chart-bar',      color: '#f59e0b' },
    ]
)

const quickLinks = computed(() => isAdmin.value
  ? [
      { label: 'Manage Users',   icon: 'i-heroicons-users',     to: '/users',   color: '#6366f1' },
      { label: 'Manage Courses', icon: 'i-heroicons-book-open', to: '/courses', color: '#0ea5e9' },
      { label: 'Learning Paths', icon: 'i-heroicons-map',       to: '/paths',   color: '#10b981' },
      { label: 'Job Listings',   icon: 'i-heroicons-briefcase', to: '/jobs',    color: '#f59e0b' },
    ]
  : [
      { label: 'My Learning Paths', icon: 'i-heroicons-map',                       to: '/my-paths',  color: '#6366f1' },
      { label: 'My Courses',        icon: 'i-heroicons-book-open',                 to: '/my-courses', color: '#0ea5e9' },
      { label: 'My CV',             icon: 'i-heroicons-document-magnifying-glass', to: '/my-cv',     color: '#10b981' },
      { label: 'Edit Profile',      icon: 'i-heroicons-user',                      to: '/profile',   color: '#8b5cf6' },
    ]
)

const userColumns = [
  { key: 'fullname',   label: 'User' },
  { key: 'role',       label: 'Role' },
  { key: 'created_at', label: 'Joined' },
  { key: 'actions',    label: '' },
]

const roleBadgeColor = (r?: string) => ({ admin: 'red', consultant: 'purple' }[r ?? ''] ?? 'blue')
const formatDate     = (d: string)  => new Date(d).toLocaleDateString()
const getUserActions = (u: any) => [[
  { label: 'View', icon: 'i-heroicons-user',   click: () => navigateTo(`/users/${u.id}`) },
  { label: 'Edit', icon: 'i-heroicons-pencil', click: () => navigateTo(`/users/${u.id}-edit`) },
]]

function progressColor(pct: number) {
  if (pct >= 75) return 'text-green-600 dark:text-green-400'
  if (pct >= 40) return 'text-indigo-600 dark:text-indigo-400'
  return 'text-gray-500 dark:text-gray-400'
}

onMounted(async () => {
  statsLoading.value = true
  try {
    if (isAdmin.value) {
      const [usersRes, coursesRes, pathsRes] = await Promise.all([
        fetchUsers({ per_page: 5 }),
        fetchCourses({ per_page: 1 }),
        fetchPaths({ per_page: 1 }),
      ])
      if (usersRes?.meta)   totalUsers.value   = usersRes.meta.total
      if (coursesRes?.meta) totalCourses.value = coursesRes.meta.total
      if (pathsRes?.meta)   totalPaths.value   = pathsRes.meta.total
      recentUsers.value = usersRes?.data ?? []
    } else {
      const myPaths    = await fetchMyPaths()
      const stepArrays = await Promise.all(myPaths.map((p: Path) => fetchSteps(p.id)))

      let done = 0, total = 0

      clientPaths.value = myPaths.map((p: Path, i: number) => {
        const steps     = stepArrays[i] ?? []
        const doneCount = steps.filter((s: any) => s.user_status === 'done').length
        const pct       = steps.length ? Math.round((doneCount / steps.length) * 100) : 0
        const nextStep  = steps.find((s: any) => s.user_status !== 'done')?.title ?? null
        done  += doneCount
        total += steps.length
        return { id: p.id, name: p.name, pct, doneCount, totalSteps: steps.length, nextStep }
      })

      totalDone.value  = done
      totalSteps.value = total
    }
  } finally {
    statsLoading.value = false
  }
})

useHead(() => ({ title: isAdmin.value ? 'Admin Dashboard — CODECV' : 'My Dashboard — CODECV' }))
</script>
