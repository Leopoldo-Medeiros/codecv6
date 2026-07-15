<template>
  <NuxtLayout name="admin">

    <!-- Welcome header -->
    <div class="mb-6 flex flex-wrap items-end justify-between gap-3">
      <div>
        <h1 class="text-[1.7rem] font-bold tracking-tight text-gray-900 dark:text-white">
          Welcome back, {{ user?.fullname?.split(' ')[0] }} 👋
        </h1>
        <p class="mt-1 text-sm text-gray-500 dark:text-neutral-400">
          {{ isAdmin ? "Here's what's happening on your platform today." : 'Here’s your practice at a glance.' }}
        </p>
      </div>
      <UButton v-if="isAdmin" icon="i-heroicons-plus" color="emerald" size="sm" @click="navigateTo('/courses')">
        New Course
      </UButton>
    </div>

    <!-- KPI row -->
    <div class="mb-6 grid grid-cols-2 gap-4 lg:grid-cols-4">
      <div
        v-for="stat in displayStats"
        :key="stat.label"
        class="rounded-2xl border border-gray-200/80 bg-white p-5 dark:border-neutral-800 dark:bg-neutral-900"
      >
        <div class="flex items-center gap-2.5">
          <span
            class="flex h-9 w-9 items-center justify-center rounded-xl text-lg"
            :style="`background:${stat.color}1a;color:${stat.color}`"
          >
            <UIcon :name="stat.icon" />
          </span>
          <p class="text-[13px] font-medium text-gray-500 dark:text-neutral-400">{{ stat.label }}</p>
        </div>

        <p class="mt-3 text-[1.9rem] font-bold leading-none tracking-tight text-gray-900 dark:text-white">
          <span v-if="statsLoading" class="inline-block h-8 w-14 animate-pulse rounded-md bg-gray-200 dark:bg-neutral-800" />
          <span v-else>{{ stat.value }}</span>
        </p>

        <!-- Per-KPI mini viz -->
        <div v-if="stat.viz" class="mt-3 h-[38px]">
          <AreaChart v-if="stat.viz === 'area' && (stat.series?.length ?? 0) >= 2" :values="stat.series!" :aria-label="stat.label" />
          <MiniBars v-else-if="stat.viz === 'bars'" :values="stat.bars ?? []" :color="stat.color" :height="38" />
          <div v-else-if="stat.viz === 'progress'" class="flex h-full items-end">
            <div class="h-2 w-full overflow-hidden rounded-full bg-gray-100 dark:bg-neutral-800">
              <div class="h-full rounded-full" :style="`width:${stat.ratio ?? 0}%;background:${stat.color}`" />
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="grid grid-cols-1 items-start gap-5 lg:grid-cols-3">

      <!-- ══ Left / main column ══ -->
      <div class="flex flex-col gap-5 lg:col-span-2">

        <!-- Admin: platform-wide practice activity -->
        <section v-if="isAdmin" class="rounded-2xl border border-gray-200/80 bg-white p-5 dark:border-neutral-800 dark:bg-neutral-900">
          <div class="mb-4">
            <h2 class="text-sm font-semibold text-gray-900 dark:text-white">Platform activity</h2>
            <p class="mt-0.5 text-xs text-gray-500 dark:text-neutral-400">All learners' challenges & incidents solved, last 26 weeks</p>
          </div>
          <ActivityHeatmap :activity="adminActivity" />
        </section>

        <!-- Admin: revenue over time -->
        <section v-if="isAdmin" class="rounded-2xl border border-gray-200/80 bg-white p-5 dark:border-neutral-800 dark:bg-neutral-900">
          <div class="mb-1 flex items-center justify-between">
            <div>
              <h2 class="text-sm font-semibold text-gray-900 dark:text-white">Revenue over time</h2>
              <p class="mt-0.5 text-xs text-gray-500 dark:text-neutral-400">Cumulative PAID payments (EUR)</p>
            </div>
            <span class="text-2xl font-bold tracking-tight text-gray-900 dark:text-white">€{{ revenueEur.toLocaleString() }}</span>
          </div>
          <div v-if="revenueCumulative.length >= 2" class="-mx-1 h-[160px]">
            <AreaChart :values="revenueCumulative" aria-label="Revenue over time" />
          </div>
          <div v-else class="flex h-[140px] flex-col items-center justify-center text-center">
            <UIcon name="i-heroicons-currency-euro" class="mb-2 h-8 w-8 text-gray-300 dark:text-neutral-700" />
            <p class="text-sm text-gray-400 dark:text-neutral-500">Revenue will chart here as payments come in.</p>
          </div>
        </section>

        <!-- Client: practice activity heatmap -->
        <section v-if="!isAdmin" class="rounded-2xl border border-gray-200/80 bg-white p-5 dark:border-neutral-800 dark:bg-neutral-900">
          <div class="mb-4 flex items-center justify-between">
            <div>
              <h2 class="text-sm font-semibold text-gray-900 dark:text-white">Practice activity</h2>
              <p class="mt-0.5 text-xs text-gray-500 dark:text-neutral-400">Challenges & incidents solved, last 26 weeks</p>
            </div>
            <span class="rounded-full bg-emerald-50 px-2.5 py-1 text-xs font-semibold text-emerald-600 dark:bg-emerald-950/50 dark:text-emerald-400">
              🔥 {{ progress?.current_streak ?? 0 }} day streak
            </span>
          </div>
          <ActivityHeatmap :activity="activity" />
        </section>

        <!-- Client: practice over time -->
        <section v-if="!isAdmin" class="rounded-2xl border border-gray-200/80 bg-white p-5 dark:border-neutral-800 dark:bg-neutral-900">
          <div class="mb-1 flex items-center justify-between">
            <div>
              <h2 class="text-sm font-semibold text-gray-900 dark:text-white">Practice over time</h2>
              <p class="mt-0.5 text-xs text-gray-500 dark:text-neutral-400">Cumulative challenges & incidents solved</p>
            </div>
            <span class="text-2xl font-bold tracking-tight text-gray-900 dark:text-white">{{ totalSolved }}</span>
          </div>
          <div v-if="cumulative.length >= 2" class="-mx-1 h-[160px]">
            <AreaChart :values="cumulative" aria-label="Practice over time" />
          </div>
          <div v-else class="flex h-[140px] flex-col items-center justify-center text-center">
            <UIcon name="i-heroicons-chart-bar" class="mb-2 h-8 w-8 text-gray-300 dark:text-neutral-700" />
            <p class="text-sm text-gray-400 dark:text-neutral-500">Solve a few challenges to see your progress curve.</p>
          </div>
        </section>

        <!-- Main list card (recent users / learning paths) -->
        <section class="rounded-2xl border border-gray-200/80 bg-white dark:border-neutral-800 dark:bg-neutral-900">
          <div class="flex items-center justify-between border-b border-gray-100 px-5 py-4 dark:border-neutral-800">
            <div>
              <h2 class="text-sm font-semibold text-gray-900 dark:text-white">
                {{ isAdmin ? 'Recent Users' : 'Continue learning' }}
              </h2>
              <p class="mt-0.5 text-xs text-gray-500 dark:text-neutral-400">
                {{ isAdmin ? 'Latest registrations on the platform' : 'Pick up where you left off' }}
              </p>
            </div>
            <UButton variant="ghost" color="gray" size="xs" trailing-icon="i-heroicons-arrow-right"
              @click="navigateTo(isAdmin ? '/users' : '/my-paths')">View all</UButton>
          </div>

          <div class="px-5 py-4">
            <div v-if="statsLoading" class="flex justify-center py-8 text-emerald-500">
              <UIcon name="i-heroicons-arrow-path" class="animate-spin text-xl" />
            </div>

            <!-- Admin: recent users -->
            <template v-else-if="isAdmin">
              <UTable v-if="recentUsers.length" :rows="recentUsers" :columns="userColumns">
                <template #fullname-data="{ row }">
                  <div class="flex items-center gap-2.5">
                    <UAvatar :src="row.profile?.profile_image_url || '/images/team-13.jpg'" :alt="row.fullname" size="xs" />
                    <div>
                      <p class="text-sm font-medium text-gray-900 dark:text-white">{{ row.fullname }}</p>
                      <p class="text-xs text-gray-500 dark:text-neutral-400">{{ row.email }}</p>
                    </div>
                  </div>
                </template>
                <template #role-data="{ row }">
                  <UBadge :color="roleBadgeColor(row.role)" variant="subtle" size="xs">{{ row.role }}</UBadge>
                </template>
                <template #created_at-data="{ row }">
                  <span class="text-xs text-gray-400 dark:text-neutral-500">{{ formatDate(row.created_at) }}</span>
                </template>
                <template #actions-data="{ row }">
                  <UDropdown :items="getUserActions(row)" :popper="{ placement: 'bottom-end' }">
                    <UButton icon="i-heroicons-ellipsis-horizontal" color="gray" variant="ghost" size="xs" />
                  </UDropdown>
                </template>
              </UTable>
              <p v-else class="py-8 text-center text-sm text-gray-400 dark:text-neutral-500">No users yet.</p>
            </template>

            <!-- Client: learning paths -->
            <template v-else>
              <div v-if="clientPaths.length" class="flex flex-col gap-3">
                <button
                  v-for="path in clientPaths"
                  :key="path.id"
                  class="rounded-xl border border-gray-100 p-4 text-left transition-colors
                         hover:border-emerald-200 hover:bg-emerald-50/40
                         dark:border-neutral-800 dark:hover:border-emerald-800/70 dark:hover:bg-emerald-950/20"
                  @click="navigateTo('/my-paths')"
                >
                  <div class="flex items-start justify-between gap-3">
                    <div class="flex items-center gap-3">
                      <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-emerald-50 dark:bg-emerald-950/60">
                        <UIcon name="i-heroicons-map" class="h-5 w-5 text-emerald-500" />
                      </div>
                      <div>
                        <p class="text-sm font-semibold text-gray-900 dark:text-white">{{ path.name }}</p>
                        <p class="text-xs text-gray-400 dark:text-neutral-500">{{ path.doneCount }}/{{ path.totalSteps }} steps completed</p>
                      </div>
                    </div>
                    <span class="shrink-0 text-sm font-bold" :class="progressColor(path.pct)">{{ path.pct }}%</span>
                  </div>
                  <UProgress :value="path.pct" size="xs" color="emerald" class="mt-3" />
                  <div v-if="path.nextStep" class="mt-3 flex items-center gap-2">
                    <UIcon name="i-heroicons-arrow-right-circle" class="h-4 w-4 shrink-0 text-emerald-400" />
                    <span class="text-xs text-gray-500 dark:text-neutral-400">
                      Next: <span class="font-medium text-gray-700 dark:text-neutral-300">{{ path.nextStep }}</span>
                    </span>
                  </div>
                  <div v-else-if="path.totalSteps > 0" class="mt-3 flex items-center gap-2">
                    <UIcon name="i-heroicons-check-circle" class="h-4 w-4 shrink-0 text-green-500" />
                    <span class="text-xs font-medium text-green-600 dark:text-green-400">All steps completed!</span>
                  </div>
                </button>
              </div>
              <div v-else class="flex flex-col items-center py-10 text-center">
                <div class="mb-3 flex h-16 w-16 items-center justify-center rounded-full bg-emerald-50 dark:bg-emerald-950/60">
                  <UIcon name="i-heroicons-map" class="h-8 w-8 text-emerald-400" />
                </div>
                <p class="text-sm font-medium text-gray-700 dark:text-neutral-300">No learning paths yet</p>
                <p class="mt-1 max-w-xs text-xs text-gray-400 dark:text-neutral-500">
                  Your consultant will assign a personalised learning path based on your goals.
                </p>
                <UButton size="xs" color="emerald" variant="soft" class="mt-4" @click="navigateTo('/my-paths')">Learn more</UButton>
              </div>
            </template>
          </div>
        </section>
      </div>

      <!-- ══ Right rail ══ -->
      <div class="flex flex-col gap-5">
        <ProgressWidget v-if="!isAdmin" />
        <CoachingNudge v-if="isClient && coaching" :recommendation="coaching" />

        <!-- Quick actions -->
        <div class="rounded-2xl border border-gray-200/80 bg-white dark:border-neutral-800 dark:bg-neutral-900">
          <div class="border-b border-gray-100 px-5 py-4 dark:border-neutral-800">
            <h2 class="text-sm font-semibold text-gray-900 dark:text-white">{{ isAdmin ? 'Quick Links' : 'Quick Actions' }}</h2>
          </div>
          <div class="flex flex-col gap-1 p-3">
            <button
              v-for="link in quickLinks"
              :key="link.label"
              class="flex items-center gap-3 rounded-xl px-3 py-2.5 text-left text-sm font-medium
                     text-gray-700 transition-colors hover:bg-gray-50
                     dark:text-neutral-300 dark:hover:bg-neutral-800"
              @click="navigateTo(link.to)"
            >
              <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg text-base"
                :style="`background:${link.color}1a;color:${link.color}`">
                <UIcon :name="link.icon" />
              </span>
              <span class="flex-1">{{ link.label }}</span>
              <UIcon name="i-heroicons-chevron-right" class="h-4 w-4 text-gray-400 dark:text-neutral-600" />
            </button>
          </div>
        </div>

        <!-- CTA — learner upsell, clients only (not for admin/consultant) -->
        <div v-if="isClient" class="rounded-2xl p-5 text-white" style="background:linear-gradient(135deg,#059669,#047857)">
          <p class="text-xs font-semibold uppercase tracking-widest text-emerald-100/90">IT Career Coaching</p>
          <h3 class="mt-2 text-base font-bold">Ready to accelerate your career?</h3>
          <p class="mt-1 text-sm leading-relaxed text-emerald-50/90">
            Courses, learning paths, and job opportunities tailored for Irish tech roles.
          </p>
          <button class="mt-4 rounded-lg bg-white px-4 py-2 text-sm font-semibold text-emerald-700 transition-opacity hover:opacity-90"
            @click="navigateTo('/my-paths')">
            View My Paths
          </button>
        </div>
      </div>
    </div>

  </NuxtLayout>
</template>

<script setup lang="ts">
import type { Path } from '~/composables/usePaths'

definePageMeta({ layout: false, middleware: 'auth' })

const { user, isClient } = useAuth()
const isAdmin  = computed(() => user.value?.role === 'admin')

const { fetchUsers }                           = useUsers()
const { fetchCourses }                         = useCourses()
const { fetchPaths, fetchMyPaths, fetchSteps } = usePaths()
const { recommendation: coaching, fetchRecommendation } = useCoaching()
const { progress, fetchProgress } = usePracticeProgress()

const statsLoading = ref(false)

// Admin state
const totalUsers   = ref(0)
const totalCourses = ref(0)
const totalPaths   = ref(0)
const recentUsers  = ref<any[]>([])
const adminActivity   = ref<Array<{ date: string, count: number }>>([])
const signups         = ref<Array<{ date: string, count: number }>>([])
const revenueSeries   = ref<Array<{ date: string, amount: number }>>([])
const revenueTotal    = ref(0)

// Client state
interface EnrichedPath { id: number; name: string; pct: number; doneCount: number; totalSteps: number; nextStep: string | null }
const clientPaths = ref<EnrichedPath[]>([])
const totalDone   = ref(0)
const totalSteps  = ref(0)
const activity    = ref<Array<{ date: string, count: number }>>([])

const overallPct = computed(() => totalSteps.value ? Math.round((totalDone.value / totalSteps.value) * 100) : 0)
const totalSolved = computed(() => activity.value.reduce((s, a) => s + a.count, 0))
const cumulative = computed(() => {
  const sorted = [...activity.value].sort((a, b) => a.date.localeCompare(b.date))
  let sum = 0
  return sorted.map((a) => { sum += a.count; return sum })
})

function cumulativeOf<T>(rows: T[], key: (r: T) => number, dateOf: (r: T) => string): number[] {
  let sum = 0
  return [...rows].sort((a, b) => dateOf(a).localeCompare(dateOf(b))).map((r) => { sum += key(r); return sum })
}
const signupsCumulative = computed(() => cumulativeOf(signups.value, r => r.count, r => r.date))
const revenueCumulative = computed(() => cumulativeOf(revenueSeries.value, r => r.amount / 100, r => r.date))
const revenueEur = computed(() => Math.round(revenueTotal.value / 100))

// Last 14 days as a dense daily-count series (zeros included) for KPI mini-bars.
const recentDaily = computed(() => {
  const map: Record<string, number> = {}
  for (const a of activity.value) map[a.date] = a.count
  const out: number[] = []
  const d = new Date()
  d.setHours(0, 0, 0, 0)
  for (let i = 13; i >= 0; i--) {
    const day = new Date(d)
    day.setDate(day.getDate() - i)
    const k = `${day.getFullYear()}-${String(day.getMonth() + 1).padStart(2, '0')}-${String(day.getDate()).padStart(2, '0')}`
    out.push(map[k] ?? 0)
  }
  return out
})

interface StatCard {
  label: string; value: string | number; icon: string; color: string
  viz?: 'area' | 'bars' | 'progress'; series?: number[]; bars?: number[]; ratio?: number
}

const displayStats = computed<StatCard[]>(() => isAdmin.value
  ? [
      { label: 'Total Users',    value: totalUsers.value,   icon: 'i-heroicons-users',        color: '#6366f1', viz: 'area', series: signupsCumulative.value },
      { label: 'Active Courses', value: totalCourses.value, icon: 'i-heroicons-book-open',     color: '#0ea5e9' },
      { label: 'Learning Paths', value: totalPaths.value,   icon: 'i-heroicons-map',           color: '#10b981' },
      { label: 'Revenue (EUR)',  value: `€${revenueEur.value.toLocaleString()}`, icon: 'i-heroicons-currency-euro', color: '#f59e0b', viz: 'area', series: revenueCumulative.value },
    ]
  : [
      { label: 'XP earned',        value: progress.value?.xp_points ?? 0,      icon: 'i-heroicons-bolt',         color: '#10b981', viz: 'area', series: cumulative.value },
      { label: 'Day streak',       value: progress.value?.current_streak ?? 0, icon: 'i-heroicons-fire',         color: '#f59e0b', viz: 'bars', bars: recentDaily.value },
      { label: 'Steps completed',  value: totalDone.value,                     icon: 'i-heroicons-check-circle', color: '#38bdf8', viz: 'bars', bars: recentDaily.value },
      { label: 'Overall progress', value: `${overallPct.value}%`,              icon: 'i-heroicons-chart-bar',    color: '#8b5cf6', viz: 'progress', ratio: overallPct.value },
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
      { label: 'My Learning Paths', icon: 'i-heroicons-map',                       to: '/my-paths',   color: '#6366f1' },
      { label: 'My Courses',        icon: 'i-heroicons-book-open',                 to: '/my-courses', color: '#0ea5e9' },
      { label: 'My CV',             icon: 'i-heroicons-document-magnifying-glass', to: '/my-cv',      color: '#10b981' },
      { label: 'Edit Profile',      icon: 'i-heroicons-user',                      to: '/profile',    color: '#8b5cf6' },
    ]
)

const userColumns = [
  { key: 'fullname', label: 'User' },
  { key: 'role', label: 'Role' },
  { key: 'created_at', label: 'Joined' },
  { key: 'actions', label: '' },
]

const roleBadgeColor = (r?: string): 'red' | 'purple' | 'blue' =>
  ({ admin: 'red', consultant: 'purple' } as Record<string, 'red' | 'purple'>)[r ?? ''] ?? 'blue'
const formatDate = (d: string) => new Date(d).toLocaleDateString()
const getUserActions = (u: any) => [[
  { label: 'View', icon: 'i-heroicons-user',   click: () => navigateTo(`/users/${u.id}`) },
  { label: 'Edit', icon: 'i-heroicons-pencil', click: () => navigateTo(`/users/${u.id}-edit`) },
]]

function progressColor(pct: number) {
  if (pct >= 75) return 'text-green-600 dark:text-green-400'
  if (pct >= 40) return 'text-emerald-600 dark:text-emerald-400'
  return 'text-gray-500 dark:text-neutral-400'
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

      useApi().get<{
        activity: Array<{ date: string, count: number }>
        signups: Array<{ date: string, count: number }>
        revenue: { total: number, currency: string, series: Array<{ date: string, amount: number }> }
      }>('/admin/dashboard')
        .then((r) => {
          adminActivity.value = r.activity
          signups.value = r.signups
          revenueSeries.value = r.revenue.series
          revenueTotal.value = r.revenue.total
        })
        .catch(() => {})
    } else {
      fetchRecommendation()
      fetchProgress()
      useApi().get<{ activity: Array<{ date: string, count: number }> }>('/me/activity')
        .then((r) => { activity.value = r.activity })
        .catch(() => {})

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
