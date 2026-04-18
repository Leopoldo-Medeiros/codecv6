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

      <!-- Left: recent users / courses -->
      <div class="lg:col-span-2">
        <div class="rounded-xl border border-gray-200 bg-white dark:border-gray-700 dark:bg-gray-800">

          <!-- Card header -->
          <div class="flex items-center justify-between border-b border-gray-100 px-5 py-4
                      dark:border-gray-700">
            <div>
              <h2 class="text-sm font-semibold text-gray-900 dark:text-white">
                {{ isAdmin ? 'Recent Users' : 'Available Courses' }}
              </h2>
              <p class="mt-0.5 text-xs text-gray-500 dark:text-gray-400">
                {{ isAdmin ? 'Latest registrations on the platform' : 'Browse and enroll' }}
              </p>
            </div>
            <UButton
              v-if="isAdmin"
              variant="ghost" color="gray" size="xs"
              trailing-icon="i-heroicons-arrow-right"
              @click="navigateTo('/users')"
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

            <!-- Client: available courses list -->
            <template v-else>
              <div v-if="recentCourses.length" class="-mx-5 divide-y divide-gray-100 dark:divide-gray-700">
                <div
                  v-for="course in recentCourses"
                  :key="course.id"
                  class="flex cursor-pointer items-center gap-3 px-5 py-3 transition-colors
                         hover:bg-gray-50 dark:hover:bg-gray-700/50"
                  @click="navigateTo(`/courses/${course.id}`)"
                >
                  <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg
                              bg-sky-50 dark:bg-sky-900/20">
                    <UIcon name="i-heroicons-book-open" class="text-sky-500 text-base" />
                  </div>
                  <div class="min-w-0 flex-1">
                    <p class="truncate text-sm font-medium text-gray-900 dark:text-white">{{ course.name }}</p>
                    <p v-if="course.description" class="truncate text-xs text-gray-400 dark:text-gray-500">
                      {{ course.description }}
                    </p>
                  </div>
                  <UIcon name="i-heroicons-chevron-right" class="h-4 w-4 shrink-0 text-gray-300 dark:text-gray-600" />
                </div>
              </div>
              <p v-else class="py-8 text-center text-sm text-gray-400 dark:text-gray-500">
                No courses available yet.
              </p>
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
            <template v-if="isAdmin">
              <button
                v-for="link in adminLinks"
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
            </template>
            <template v-else>
              <button
                v-for="link in clientLinks"
                :key="link.label"
                class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-left text-sm font-medium
                       text-gray-700 transition-colors hover:bg-gray-50
                       dark:text-gray-300 dark:hover:bg-gray-700"
                @click="link.to ? navigateTo(link.to) : null"
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
            </template>
          </div>
        </div>

        <!-- CTA card -->
        <div class="rounded-xl p-5 text-white" style="background:linear-gradient(135deg,#4f46e5,#7c3aed)">
          <p class="text-xs font-semibold uppercase tracking-widest text-indigo-200">IT Career Coaching</p>
          <h3 class="mt-2 text-base font-bold">Ready to accelerate your career?</h3>
          <p class="mt-1 text-sm text-indigo-100 leading-relaxed">
            Courses, learning paths, and job opportunities tailored for Irish tech roles.
          </p>
          <button
            class="mt-4 rounded-lg bg-white px-4 py-2 text-sm font-semibold text-indigo-700
                   transition-opacity hover:opacity-90"
            @click="navigateTo('/courses')"
          >
            Explore Courses
          </button>
        </div>

      </div>
    </div>

  </NuxtLayout>
</template>

<script setup lang="ts">
definePageMeta({ layout: false, middleware: 'auth' })

const { user } = useAuth()
const isAdmin  = computed(() => user.value?.role === 'admin')

const { fetchUsers }             = useUsers()
const { fetchCourses }           = useCourses()
const { fetchPaths, fetchSteps } = usePaths()

const statsLoading   = ref(false)
// Admin
const totalUsers     = ref(0)
const totalCourses   = ref(0)
const totalPaths     = ref(0)
const recentUsers    = ref<any[]>([])
// Client
const myPathsCount   = ref(0)
const myCoursesCount = ref(0)
const progressPct    = ref('—')
const recentCourses  = ref<any[]>([])

const displayStats = computed(() => isAdmin.value
  ? [
      { label: 'Total Users',    value: totalUsers.value,   icon: 'i-heroicons-users',          color: '#6366f1' },
      { label: 'Active Courses', value: totalCourses.value, icon: 'i-heroicons-book-open',       color: '#0ea5e9' },
      { label: 'Learning Paths', value: totalPaths.value,   icon: 'i-heroicons-map',             color: '#10b981' },
      { label: 'Revenue',        value: '—',                icon: 'i-heroicons-currency-dollar', color: '#f59e0b' },
    ]
  : [
      { label: 'My Paths',       value: myPathsCount.value,   icon: 'i-heroicons-map',        color: '#10b981' },
      { label: 'My Courses',     value: myCoursesCount.value, icon: 'i-heroicons-book-open',  color: '#0ea5e9' },
      { label: 'Path Progress',  value: progressPct.value,    icon: 'i-heroicons-chart-bar',  color: '#6366f1' },
    ]
)

const adminLinks = [
  { label: 'Manage Users',   icon: 'i-heroicons-users',     to: '/users',   color: '#6366f1' },
  { label: 'Manage Courses', icon: 'i-heroicons-book-open', to: '/courses', color: '#0ea5e9' },
  { label: 'Learning Paths', icon: 'i-heroicons-map',       to: '/paths',   color: '#10b981' },
  { label: 'Job Listings',   icon: 'i-heroicons-briefcase', to: '/jobs',    color: '#f59e0b' },
]

const clientLinks = [
  { label: 'Browse Courses',    icon: 'i-heroicons-book-open',     to: '/courses', color: '#0ea5e9' },
  { label: 'My Learning Path',  icon: 'i-heroicons-map',           to: null,       color: '#10b981' },
  { label: 'Edit Profile',      icon: 'i-heroicons-user',          to: '/profile', color: '#8b5cf6' },
]

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
      // Client: load paths, courses, and compute step progress
      const [pathsRes, coursesRes] = await Promise.all([
        fetchPaths({ per_page: 100 }),
        fetchCourses({ per_page: 5 }),
      ])
      const allPaths = (pathsRes as any)?.data ?? []
      myPathsCount.value   = (pathsRes as any)?.meta?.total ?? allPaths.length
      myCoursesCount.value = (coursesRes as any)?.meta?.total ?? 0
      recentCourses.value  = (coursesRes as any)?.data ?? []

      if (allPaths.length > 0) {
        const stepArrays  = await Promise.all(allPaths.map((p: any) => fetchSteps(p.id)))
        const allSteps    = stepArrays.flat()
        if (allSteps.length > 0) {
          const done = allSteps.filter((s: any) => s.user_status === 'done').length
          progressPct.value = `${Math.round((done / allSteps.length) * 100)}%`
        } else {
          progressPct.value = '0%'
        }
      }
    }
  } finally {
    statsLoading.value = false
  }
})

useHead(() => ({ title: isAdmin.value ? 'Admin Dashboard — CODECV' : 'My Dashboard — CODECV' }))
</script>
