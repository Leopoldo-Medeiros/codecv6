<template>
  <div class="min-h-screen bg-gray-50 dark:bg-slate-950" style="font-family:'Inter',system-ui,sans-serif">

    <!-- Mobile backdrop -->
    <Transition enter-from-class="opacity-0" enter-active-class="transition-opacity duration-200"
                leave-to-class="opacity-0" leave-active-class="transition-opacity duration-200">
      <div v-if="sidebarOpen" class="fixed inset-0 z-40 bg-black/40 lg:hidden" @click="sidebarOpen = false" />
    </Transition>

    <!-- ═══════════════════════════════════════ SIDEBAR ═══ -->
    <aside :class="[
      'fixed inset-y-0 left-0 z-50 flex w-60 flex-col border-r border-gray-200 bg-white dark:border-slate-800 dark:bg-slate-900 transition-transform duration-200',
      sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'
    ]">

      <!-- Accent stripe -->
      <div class="h-0.5 w-full shrink-0" style="background:linear-gradient(90deg,#6366f1,#06b6d4,#8b5cf6)"></div>

      <!-- Brand -->
      <div class="flex h-[60px] shrink-0 items-center justify-center border-b border-gray-100 px-5 dark:border-slate-800">
        <NuxtLink to="/dashboard" class="flex items-center gap-2.5">
          <!-- Light mode: real logo (mix-blend hides white bg) -->
          <img src="/images/codecv.png" alt="CODECV"
               class="h-8 w-auto dark:hidden"
               style="mix-blend-mode:multiply"
               onerror="this.style.display='none'" />
          <!-- Dark mode: gradient text wordmark -->
          <span class="hidden dark:inline-block brand-glow text-[15px] font-extrabold tracking-[0.18em] uppercase">
            CODECV
          </span>
        </NuxtLink>
      </div>

      <!-- User card -->
      <div class="flex shrink-0 items-center gap-3 border-b border-gray-100 px-4 py-3 dark:border-slate-800">
        <UAvatar :src="user?.profile?.profile_image_url || '/images/team-13.jpg'" :alt="user?.fullname" size="sm" />
        <div class="min-w-0 flex-1">
          <p class="truncate text-[13px] font-semibold text-gray-800 dark:text-slate-200">{{ user?.fullname }}</p>
          <p class="text-[11px] capitalize text-gray-400 dark:text-slate-500">{{ user?.role }}</p>
        </div>
      </div>

      <!-- Nav -->
      <nav class="flex-1 overflow-y-auto p-3 [scrollbar-width:none] [&::-webkit-scrollbar]:hidden">

        <!-- Main items -->
        <div class="mb-1 flex flex-col gap-0.5">
          <NuxtLink
            v-for="item in mainItems"
            :key="item.to"
            :to="item.to"
            class="nav-item group relative flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium"
            :class="isActive(item.to) ? 'nav-item--active' : 'nav-item--idle'"
            @click="sidebarOpen = false"
          >
            <!-- glow pill (dark mode active only) -->
            <span v-if="isActive(item.to)" class="nav-glow" aria-hidden="true"></span>
            <!-- left indicator -->
            <span
              class="absolute left-0 top-1/2 h-5 w-0.5 -translate-y-1/2 rounded-r transition-all"
              :class="isActive(item.to) ? 'nav-bar--active' : 'bg-transparent'"
            ></span>
            <UIcon :name="item.icon" class="nav-icon h-[18px] w-[18px] shrink-0"
              :class="isActive(item.to) ? 'nav-icon--active' : 'nav-icon--idle'" />
            <span class="flex-1">{{ item.label }}</span>
          </NuxtLink>
        </div>

        <!-- Admin tools -->
        <div v-if="isAdmin" class="mt-4 flex flex-col gap-0.5 border-t border-gray-100 pt-4 dark:border-slate-800">
          <p class="mb-1 px-3 text-[10px] font-semibold uppercase tracking-widest text-gray-400 dark:text-slate-600">
            Admin
          </p>
          <NuxtLink
            to="/settings"
            class="nav-item group relative flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium"
            :class="isActive('/settings') ? 'nav-item--active' : 'nav-item--idle'"
          >
            <span v-if="isActive('/settings')" class="nav-glow" aria-hidden="true"></span>
            <span class="absolute left-0 top-1/2 h-5 w-0.5 -translate-y-1/2 rounded-r transition-all"
              :class="isActive('/settings') ? 'nav-bar--active' : 'bg-transparent'"></span>
            <UIcon name="i-heroicons-cog-6-tooth"
              class="nav-icon h-[18px] w-[18px] shrink-0"
              :class="isActive('/settings') ? 'nav-icon--active' : 'nav-icon--idle'" />
            <span>Settings</span>
          </NuxtLink>
        </div>

      </nav>

      <!-- Footer logout -->
      <div class="shrink-0 border-t border-gray-100 p-3 dark:border-slate-800">
        <button
          class="flex w-full items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium
                 text-gray-500 transition-colors hover:bg-red-50 hover:text-red-600
                 dark:text-slate-500 dark:hover:bg-red-950/40 dark:hover:text-red-400"
          @click="handleLogout"
        >
          <UIcon name="i-heroicons-arrow-right-on-rectangle" class="h-[18px] w-[18px] shrink-0" />
          Logout
        </button>
      </div>
    </aside>

    <!-- ═══════════════════════════════════════ MAIN ═══════ -->
    <div class="flex min-h-screen flex-col lg:pl-60">

      <!-- Topbar -->
      <header class="sticky top-0 z-40 flex h-14 shrink-0 items-center justify-between
                     border-b border-gray-200 bg-white px-4 lg:px-6
                     dark:border-slate-800 dark:bg-slate-900">
        <div class="flex items-center gap-3">
          <!-- Hamburger — mobile only -->
          <button class="flex h-8 w-8 items-center justify-center rounded-lg text-gray-500
                         hover:bg-gray-100 dark:text-slate-400 dark:hover:bg-slate-800 lg:hidden"
            @click="sidebarOpen = !sidebarOpen">
            <UIcon name="i-heroicons-bars-3" class="h-5 w-5" />
          </button>
          <UBreadcrumb :links="breadcrumbLinks" class="hidden sm:flex" />
        </div>

        <div class="flex items-center gap-1">

          <UButton :icon="isDark ? 'i-heroicons-sun' : 'i-heroicons-moon'"
            color="gray" variant="ghost" size="sm" @click="toggleColorMode" />
          <UPopover>
            <UButton icon="i-heroicons-bell" color="gray" variant="ghost" size="sm" />
            <template #panel>
              <div class="w-72 p-4">
                <p class="mb-1 text-sm font-semibold text-gray-900 dark:text-white">Notifications</p>
                <p class="text-sm text-gray-400 dark:text-slate-500">Nothing new</p>
              </div>
            </template>
          </UPopover>
          <UDropdown :items="userMenuItems" :popper="{ placement: 'bottom-end' }">
            <button class="flex items-center gap-2 rounded-lg border border-gray-200 px-3 py-1.5
                           text-sm font-medium text-gray-700 transition-colors hover:bg-gray-50
                           dark:border-slate-700 dark:text-slate-300 dark:hover:bg-slate-800">
              <UAvatar :src="user?.profile?.profile_image_url || '/images/team-13.jpg'" :alt="user?.fullname" size="xs" />
              <span class="max-w-[120px] truncate">{{ user?.fullname }}</span>
              <UIcon name="i-heroicons-chevron-down" class="h-3.5 w-3.5 text-gray-400 dark:text-slate-500" />
            </button>
          </UDropdown>
        </div>
      </header>

      <main class="flex-1 p-4 lg:p-6">
        <slot />
      </main>

      <footer class="border-t border-gray-200 px-6 py-4 text-center text-xs
                     text-gray-400 dark:border-slate-800 dark:text-slate-600">
        &copy; {{ new Date().getFullYear() }}
        <strong class="text-gray-500 dark:text-slate-400">CODECV</strong>.
        All rights reserved.
      </footer>
    </div>

    <UNotifications />
  </div>
</template>

<script setup lang="ts">
useHead({
  link: [
    { rel: 'preconnect', href: 'https://fonts.googleapis.com' },
    { rel: 'preconnect', href: 'https://fonts.gstatic.com', crossorigin: '' },
    { rel: 'stylesheet', href: 'https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap' },
  ]
})

const { user, logout } = useAuth()
const route            = useRoute()
const colorMode        = useColorMode()
const sidebarOpen      = ref(false)

// Close sidebar on route change
watch(() => route.path, () => { sidebarOpen.value = false })

const isDark   = computed(() => colorMode.value === 'dark')
const isAdmin  = computed(() => user.value?.role === 'admin')
const isActive = (p: string) => route.path === p

const toggleColorMode = () => { colorMode.preference = colorMode.value === 'dark' ? 'light' : 'dark' }
const handleLogout    = () => logout()

// ── Neon values injected into CSS via v-bind() ──────────────
// Active item background
const navActiveBg = computed(() =>
  isDark.value ? 'rgba(99,102,241,0.12)' : 'rgb(238,242,255)'
)
// Active item text colour
const navActiveTxt = computed(() =>
  isDark.value ? 'rgb(165,180,252)' : 'rgb(79,70,229)'
)
// Active left-bar colour
const navBarColor = computed(() =>
  isDark.value ? '#818cf8' : '#6366f1'
)
// Neon glow (dark mode only)
const navGlowColor = computed(() =>
  isDark.value
    ? 'rgba(99,102,241,0.35)'
    : 'transparent'
)
// Icon active colour
const navIconActive = computed(() =>
  isDark.value ? 'rgb(129,140,248)' : 'rgb(99,102,241)'
)
// Icon idle colour
const navIconIdle = computed(() =>
  isDark.value ? 'rgb(148,163,184)' : 'rgb(156,163,175)'
)
// Idle text
const navIdleTxt = computed(() =>
  isDark.value ? 'rgb(148,163,184)' : 'rgb(75,85,99)'
)
// Idle hover bg
const navHoverBg = computed(() =>
  isDark.value ? 'rgba(255,255,255,0.04)' : 'rgb(249,250,251)'
)

const mainItems = computed(() => isAdmin.value
  ? [
      { label: 'Dashboard', icon: 'i-heroicons-home',                       to: '/dashboard' },
      { label: 'Users',     icon: 'i-heroicons-users',                    to: '/users' },
      { label: 'Courses',   icon: 'i-heroicons-book-open',                to: '/courses' },
      { label: 'Paths',     icon: 'i-heroicons-map',                      to: '/paths' },
      { label: 'Plans',     icon: 'i-heroicons-clipboard-document-list',  to: '/plans' },
      { label: 'Jobs',      icon: 'i-heroicons-briefcase',                to: '/jobs' },
    ]
  : [
      { label: 'Dashboard',  icon: 'i-heroicons-home',             to: '/dashboard' },
      { label: 'My Courses', icon: 'i-heroicons-book-open',      to: '/my-courses' },
      { label: 'My Paths',   icon: 'i-heroicons-map',            to: '/my-paths' },
      { label: 'My CV',        icon: 'i-heroicons-document-magnifying-glass', to: '/my-cv' },
      { label: 'LinkedIn',    icon: 'i-heroicons-identification',            to: '/linkedin-analyser' },
    ]
)

const userMenuItems = [
  [
    { label: 'Profile',  icon: 'i-heroicons-user',                    click: () => navigateTo('/profile')  },
    { label: 'Settings', icon: 'i-heroicons-cog-6-tooth',             click: () => navigateTo('/settings') },
  ],
  [
    { label: 'Logout',   icon: 'i-heroicons-arrow-right-on-rectangle', click: () => handleLogout() },
  ],
]

const breadcrumbLinks = computed(() => {
  const path  = route.path
  const links: Array<{ label: string; to?: string; icon?: string }> = [
    { label: 'Home', to: '/dashboard', icon: 'i-heroicons-home' },
  ]
  if (path !== '/dashboard') {
    path.split('/').filter(Boolean).forEach((seg, i, arr) => {
      const to    = '/' + arr.slice(0, i + 1).join('/')
      const label = seg.charAt(0).toUpperCase() + seg.slice(1)
      links.push(i === arr.length - 1 ? { label } : { label, to })
    })
  }
  return links
})
</script>

<style scoped>
/* ── Nav item states — colours come from v-bind(), no :global needed ── */

.nav-item--active {
  background: v-bind(navActiveBg);
  color: v-bind(navActiveTxt);
}
.nav-item--idle {
  color: v-bind(navIdleTxt);
}
.nav-item--idle:hover {
  background: v-bind(navHoverBg);
}

/* Left indicator bar */
.nav-bar--active {
  background: v-bind(navBarColor);
  box-shadow: 0 0 8px v-bind(navBarColor);
}

/* Neon glow overlay (visible in dark mode via navGlowColor) */
.nav-glow {
  position: absolute;
  inset: 0;
  border-radius: 8px;
  pointer-events: none;
  box-shadow: 0 0 18px v-bind(navGlowColor), inset 0 0 12px v-bind(navGlowColor);
}

/* Icons */
.nav-icon--active { color: v-bind(navIconActive); }
.nav-icon--idle   { color: v-bind(navIconIdle); }
.nav-item--idle:hover .nav-icon--idle {
  color: v-bind(navActiveTxt);
}

/* Text glow on active label in dark mode */
.nav-item--active span {
  text-shadow: 0 0 12px v-bind(navGlowColor);
}

/* Brand wordmark — dark mode gradient + glow */
.brand-glow {
  background: linear-gradient(90deg, #818cf8, #38bdf8, #a78bfa);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  background-clip: text;
  filter: drop-shadow(0 0 8px rgba(129, 140, 248, 0.6));
}
</style>
