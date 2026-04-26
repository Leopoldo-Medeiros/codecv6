<template>
  <div class="mkt">
    <!-- HEADER -->
    <header :class="['mkt-header', { 'mkt-header--scrolled': scrolled, 'mkt-header--dark': isDarkHero && !scrolled }]">
      <div class="mkt-container mkt-header__inner">
        <NuxtLink to="/" class="mkt-logo">
          <span class="mkt-logo__icon">
            <svg width="28" height="34" viewBox="0 0 28 34" fill="none" xmlns="http://www.w3.org/2000/svg">
              <path d="M2 0h16l10 10v22a2 2 0 01-2 2H2a2 2 0 01-2-2V2a2 2 0 012-2z" fill="#059669"/>
              <path d="M18 0l10 10h-8a2 2 0 01-2-2V0z" fill="white" opacity="0.3"/>
              <text x="13" y="25" text-anchor="middle" fill="white" font-size="11" font-weight="800" font-family="SF Mono, Monaco, Consolas, monospace" letter-spacing="0">{ }</text>
            </svg>
          </span>
          <span class="mkt-logo__text">CODE<span class="mkt-logo__cv">CV</span></span>
        </NuxtLink>

        <nav class="mkt-nav">
          <NuxtLink to="/" class="mkt-nav__link">Home</NuxtLink>
          <NuxtLink to="/about" class="mkt-nav__link">About</NuxtLink>
          <NuxtLink to="/pricing" class="mkt-nav__link">Pricing</NuxtLink>
          <NuxtLink to="/faqs" class="mkt-nav__link">FAQs</NuxtLink>
        </nav>

        <div class="mkt-header__actions">
          <template v-if="isAuthenticated">
            <NuxtLink to="/dashboard" class="mkt-btn mkt-btn--ghost">Dashboard</NuxtLink>
            <a href="#" class="mkt-btn mkt-btn--primary" @click.prevent="logout">Logout</a>
          </template>
          <template v-else>
            <NuxtLink to="/login" class="mkt-btn mkt-btn--ghost">Sign In</NuxtLink>
            <NuxtLink to="/login" class="mkt-btn mkt-btn--primary">Get Started</NuxtLink>
          </template>
        </div>

        <button class="mkt-burger" :aria-label="mobileOpen ? 'Close' : 'Menu'" @click="mobileOpen = !mobileOpen">
          <span class="mkt-burger__line" :class="{ open: mobileOpen }" />
        </button>
      </div>

      <Transition name="slide-down">
        <div v-if="mobileOpen" class="mkt-mobile-menu">
          <NuxtLink to="/" class="mkt-mobile-menu__link" @click="mobileOpen = false">Home</NuxtLink>
          <NuxtLink to="/about" class="mkt-mobile-menu__link" @click="mobileOpen = false">About</NuxtLink>
          <NuxtLink to="/pricing" class="mkt-mobile-menu__link" @click="mobileOpen = false">Pricing</NuxtLink>
          <NuxtLink to="/faqs" class="mkt-mobile-menu__link" @click="mobileOpen = false">FAQs</NuxtLink>
          <template v-if="isAuthenticated">
            <NuxtLink to="/dashboard" class="mkt-mobile-menu__link" @click="mobileOpen = false">Dashboard</NuxtLink>
            <a href="#" class="mkt-mobile-menu__link" @click.prevent="() => { logout(); mobileOpen = false }">Logout</a>
          </template>
          <template v-else>
            <NuxtLink to="/login" class="mkt-mobile-menu__link" @click="mobileOpen = false">Sign In</NuxtLink>
            <NuxtLink to="/login" class="mkt-mobile-menu__cta" @click="mobileOpen = false">Get Started</NuxtLink>
          </template>
        </div>
      </Transition>
    </header>

    <main>
      <slot />
    </main>

    <!-- FOOTER -->
    <footer class="mkt-footer">
      <div class="mkt-container">
        <div class="mkt-footer__top">
          <div class="mkt-footer__brand">
            <NuxtLink to="/" class="mkt-logo mkt-logo--footer">
              <span class="mkt-logo__icon">
                <svg width="24" height="30" viewBox="0 0 28 34" fill="none" xmlns="http://www.w3.org/2000/svg">
                  <path d="M2 0h16l10 10v22a2 2 0 01-2 2H2a2 2 0 01-2-2V2a2 2 0 012-2z" fill="#059669"/>
                  <path d="M18 0l10 10h-8a2 2 0 01-2-2V0z" fill="white" opacity="0.3"/>
                  <text x="13" y="25" text-anchor="middle" fill="white" font-size="11" font-weight="800" font-family="SF Mono, Monaco, Consolas, monospace" letter-spacing="0">{ }</text>
                </svg>
              </span>
              <span class="mkt-logo__text">CODE<span class="mkt-logo__cv">CV</span></span>
            </NuxtLink>
            <p class="mkt-footer__tagline">Accelerating IT careers in Ireland.<br>Expert coaching from real practitioners.</p>
            <div class="mkt-footer__socials">
              <a href="https://www.linkedin.com/company/86762280" target="_blank" class="mkt-footer__social" aria-label="LinkedIn">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor"><path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433a2.062 2.062 0 01-2.063-2.065 2.064 2.064 0 112.063 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/></svg>
              </a>
              <a href="https://www.instagram.com/codecv_info/" target="_blank" class="mkt-footer__social" aria-label="Instagram">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838a6.162 6.162 0 100 12.324 6.162 6.162 0 000-12.324zM12 16a4 4 0 110-8 4 4 0 010 8zm6.406-11.845a1.44 1.44 0 100 2.881 1.44 1.44 0 000-2.881z"/></svg>
              </a>
            </div>
          </div>

          <div class="mkt-footer__col">
            <h6 class="mkt-footer__col-title">Company</h6>
            <ul class="mkt-footer__links">
              <li><NuxtLink to="/about">About Us</NuxtLink></li>
              <li><NuxtLink to="/faqs">FAQs</NuxtLink></li>
              <li><a href="mailto:codecvinfo@gmail.com">Contact</a></li>
              <li><NuxtLink to="/privacy">Privacy Policy</NuxtLink></li>
              <li><NuxtLink to="/terms">Terms of Service</NuxtLink></li>
            </ul>
          </div>

          <div class="mkt-footer__col">
            <h6 class="mkt-footer__col-title">Platform</h6>
            <ul class="mkt-footer__links">
              <li><NuxtLink to="/pricing">Pricing</NuxtLink></li>
              <li><NuxtLink to="/login">Sign In</NuxtLink></li>
            </ul>
          </div>

          <div class="mkt-footer__col">
            <h6 class="mkt-footer__col-title">Contact</h6>
            <ul class="mkt-footer__links">
              <li><a href="mailto:codecvinfo@gmail.com">codecvinfo@gmail.com</a></li>
              <li><span>+353 89 405 0730</span></li>
              <li><span>Dublin, Ireland</span></li>
            </ul>
          </div>
        </div>

        <div class="mkt-footer__bottom">
          <p>&copy; {{ new Date().getFullYear() }} CODECV. All Rights Reserved.</p>
          <p>Dublin, Ireland</p>
        </div>
      </div>
    </footer>
  </div>
</template>

<script setup lang="ts">
const { isAuthenticated, logout } = useAuth()
const route = useRoute()
const mobileOpen = ref(false)
const scrolled = ref(false)
const isDarkHero = computed(() => route.path === '/')

useHead({
  link: [
    { rel: 'preconnect', href: 'https://fonts.googleapis.com' },
    { rel: 'preconnect', href: 'https://fonts.gstatic.com', crossorigin: '' },
    {
      rel: 'stylesheet',
      href: 'https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;1,400&display=swap',
    },
  ],
})

// Site-wide structured data — applies to every marketing page that uses
// this layout. Search engines use this to enrich SERP listings (sitelinks,
// knowledge panel, brand info).
useSchemaOrg([
  defineOrganization({
    name: 'CODECV',
    logo: '/images/Logo/codecv.png',
    sameAs: [
      'https://www.linkedin.com/company/86762280',
      'https://www.instagram.com/codecv_info/',
    ],
    address: {
      '@type': 'PostalAddress',
      addressCountry: 'IE',
      addressLocality: 'Dublin',
    },
    contactPoint: {
      '@type': 'ContactPoint',
      email: 'codecvinfo@gmail.com',
      telephone: '+353-89-405-0730',
      contactType: 'customer support',
      areaServed: 'IE',
      availableLanguage: ['en'],
    },
  }),
  defineWebSite({
    name: 'CODECV — IT Career Coaching for Ireland',
  }),
])

onMounted(() => {
  if (!import.meta.client) return
  const onScroll = () => { scrolled.value = window.scrollY > 10 }
  window.addEventListener('scroll', onScroll, { passive: true })
  onUnmounted(() => window.removeEventListener('scroll', onScroll))
})
</script>

<style>
/* ============================================================
   CODECV MARKETING — Light Design System
   ============================================================ */

.mkt, .mkt *, .mkt *::before, .mkt *::after {
  box-sizing: border-box;
  margin: 0;
  padding: 0;
}

.mkt {
  --bg: #ffffff;
  --bg-soft: #ECFDF5;
  --bg-card: #ffffff;
  --border: rgba(0, 0, 0, 0.07);
  --text: #111827;
  --text-body: #374151;
  --muted: #9ca3af;
  --accent: #059669;
  --accent-hover: #047857;
  --accent-light: rgba(5, 150, 105, 0.08);
  --accent-mid: rgba(5, 150, 105, 0.15);
  --accent-glow: rgba(5, 150, 105, 0.25);
  --ff: 'Plus Jakarta Sans', sans-serif;
  --radius-pill: 50px;
  --radius-card: 16px;
  --shadow-sm: 0 1px 4px rgba(0,0,0,0.06), 0 4px 16px rgba(0,0,0,0.04);
  --shadow-md: 0 4px 12px rgba(0,0,0,0.08), 0 12px 40px rgba(0,0,0,0.06);

  background: var(--bg);
  color: var(--text);
  font-family: var(--ff);
  font-size: 16px;
  line-height: 1.6;
  min-height: 100vh;
  -webkit-font-smoothing: antialiased;
}

/* Container */
.mkt-container {
  max-width: 1160px;
  margin: 0 auto;
  padding: 0 28px;
}

/* ===== BUTTONS ===== */
.mkt-btn {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  gap: 7px;
  padding: 11px 24px;
  border-radius: var(--radius-pill);
  font-family: var(--ff);
  font-size: 14px;
  font-weight: 600;
  text-decoration: none;
  cursor: pointer;
  transition: all 0.2s ease;
  border: 2px solid transparent;
  white-space: nowrap;
  letter-spacing: -0.01em;
}
.mkt-btn--primary {
  background: var(--accent);
  color: #fff;
  border-color: var(--accent);
  box-shadow: 0 4px 16px var(--accent-glow);
}
.mkt-btn--primary:hover {
  background: var(--accent-hover);
  border-color: var(--accent-hover);
  transform: translateY(-1px);
  box-shadow: 0 8px 24px var(--accent-glow);
}
.mkt-btn--outline {
  background: transparent;
  color: var(--accent);
  border-color: rgba(5, 150, 105, 0.3);
}
.mkt-btn--outline:hover {
  background: var(--accent-light);
  border-color: var(--accent);
}
.mkt-btn--ghost {
  background: transparent;
  color: var(--text-body);
  border-color: transparent;
}
.mkt-btn--ghost:hover {
  color: var(--accent);
  background: var(--accent-light);
}
.mkt-btn--lg {
  padding: 14px 32px;
  font-size: 15px;
}
.mkt-btn--white {
  background: #fff;
  color: var(--accent);
  border-color: #fff;
}
.mkt-btn--white:hover {
  background: #f0edff;
}

/* ===== HEADER ===== */
.mkt-header {
  position: fixed;
  top: 0; left: 0; right: 0;
  z-index: 1000;
  padding: 16px 24px;
  transition: background 0.3s, box-shadow 0.3s;
  background: rgba(255, 255, 255, 0);
}
.mkt-header--scrolled {
  background: rgba(255, 255, 255, 0.9);
  backdrop-filter: blur(20px);
  -webkit-backdrop-filter: blur(20px);
  box-shadow: 0 1px 0 rgba(0,0,0,0.06);
  padding: 12px 24px;
}

/* Dark hero variant (homepage) — white text when transparent */
.mkt-header--dark .mkt-logo__text { color: #fff; }
.mkt-header--dark .mkt-nav__link { color: rgba(255,255,255,0.8); }
.mkt-header--dark .mkt-nav__link:hover {
  color: #fff;
  background: rgba(255,255,255,0.12);
}
.mkt-header--dark .mkt-nav__link.router-link-active {
  color: #fff;
  background: rgba(255,255,255,0.14);
}
.mkt-header--dark .mkt-btn--ghost {
  color: rgba(255,255,255,0.85);
}
.mkt-header--dark .mkt-btn--ghost:hover {
  color: #fff;
  background: rgba(255,255,255,0.12);
}
.mkt-header__inner {
  display: flex;
  align-items: center;
  gap: 40px;
}

/* Logo */
.mkt-logo {
  display: flex;
  align-items: center;
  gap: 9px;
  text-decoration: none;
  flex-shrink: 0;
}
.mkt-logo__text {
  font-size: 17px;
  font-weight: 800;
  color: var(--text);
  letter-spacing: -0.03em;
}
.mkt-logo__cv {
  color: var(--accent);
}
.mkt-header--dark .mkt-logo__cv {
  color: #a78bfa;
}
.mkt-logo--footer .mkt-logo__text { color: var(--text); }

/* Nav */
.mkt-nav {
  display: flex;
  align-items: center;
  gap: 2px;
  flex: 1;
}
.mkt-nav__link {
  color: var(--text-body);
  text-decoration: none;
  font-size: 14px;
  font-weight: 500;
  padding: 7px 14px;
  border-radius: 30px;
  transition: color 0.2s, background 0.2s;
}
.mkt-nav__link:hover,
.mkt-nav__link.router-link-active {
  color: var(--accent);
  background: var(--accent-light);
}

.mkt-header__actions {
  display: flex;
  align-items: center;
  gap: 8px;
  margin-left: auto;
}

/* Burger */
.mkt-burger {
  display: none;
  background: none;
  border: none;
  cursor: pointer;
  padding: 8px;
  margin-left: auto;
}
.mkt-burger__line,
.mkt-burger__line::before,
.mkt-burger__line::after {
  display: block;
  width: 22px;
  height: 2px;
  background: var(--text);
  border-radius: 2px;
  transition: transform 0.3s, opacity 0.3s;
}
.mkt-burger__line { position: relative; }
.mkt-burger__line::before,
.mkt-burger__line::after { content: ''; position: absolute; left: 0; }
.mkt-burger__line::before { top: -7px; }
.mkt-burger__line::after { top: 7px; }
.mkt-burger__line.open { background: transparent; }
.mkt-burger__line.open::before { transform: rotate(45deg) translate(5px, 5px); }
.mkt-burger__line.open::after { transform: rotate(-45deg) translate(5px, -5px); }

/* Mobile Menu */
.mkt-mobile-menu {
  position: fixed;
  top: 60px; left: 0; right: 0;
  background: rgba(255,255,255,0.97);
  backdrop-filter: blur(20px);
  border-bottom: 1px solid var(--border);
  padding: 16px 24px 24px;
  z-index: 999;
  display: flex;
  flex-direction: column;
  gap: 2px;
}
.mkt-mobile-menu__link {
  display: block;
  color: var(--text);
  text-decoration: none;
  font-size: 16px;
  font-weight: 500;
  padding: 12px 16px;
  border-radius: 10px;
  transition: background 0.2s;
}
.mkt-mobile-menu__link:hover { background: var(--accent-light); color: var(--accent); }
.mkt-mobile-menu__cta {
  display: block;
  margin-top: 12px;
  padding: 14px 20px;
  background: var(--accent);
  color: #fff;
  text-decoration: none;
  font-size: 15px;
  font-weight: 600;
  border-radius: var(--radius-pill);
  text-align: center;
}

.slide-down-enter-active, .slide-down-leave-active { transition: opacity .2s, transform .2s; }
.slide-down-enter-from, .slide-down-leave-to { opacity: 0; transform: translateY(-8px); }

/* ===== FOOTER ===== */
.mkt-footer {
  background: var(--bg-soft);
  border-top: 1px solid var(--border);
  padding: 80px 0 0;
}
.mkt-footer__top {
  display: grid;
  grid-template-columns: 2fr 1fr 1fr 1.5fr;
  gap: 56px;
  padding-bottom: 56px;
}
.mkt-footer__tagline {
  color: var(--muted);
  font-size: 14px;
  line-height: 1.6;
  margin: 14px 0 24px;
}
.mkt-footer__socials {
  display: flex;
  gap: 8px;
}
.mkt-footer__social {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  width: 34px; height: 34px;
  border-radius: 10px;
  border: 1px solid var(--border);
  color: var(--muted);
  text-decoration: none;
  transition: all 0.2s;
  background: var(--bg);
}
.mkt-footer__social:hover {
  border-color: var(--accent);
  color: var(--accent);
  background: var(--accent-light);
}
.mkt-footer__col-title {
  font-size: 11px;
  font-weight: 700;
  letter-spacing: 0.1em;
  text-transform: uppercase;
  color: var(--text);
  margin-bottom: 18px;
}
.mkt-footer__links {
  list-style: none;
  display: flex;
  flex-direction: column;
  gap: 12px;
}
.mkt-footer__links li a,
.mkt-footer__links li span {
  color: var(--muted);
  text-decoration: none;
  font-size: 14px;
  transition: color 0.2s;
}
.mkt-footer__links li a:hover { color: var(--accent); }
.mkt-footer__bottom {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 20px 0;
  border-top: 1px solid var(--border);
  color: var(--muted);
  font-size: 13px;
}

/* ===== SHARED UTILITIES ===== */
.section-badge {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  background: var(--accent-light);
  color: var(--accent);
  font-size: 12px;
  font-weight: 700;
  letter-spacing: 0.06em;
  text-transform: uppercase;
  padding: 5px 14px;
  border-radius: var(--radius-pill);
  margin-bottom: 18px;
}
.section-header {
  text-align: center;
  max-width: 600px;
  margin: 0 auto 60px;
}
.section-title {
  font-size: clamp(28px, 3.5vw, 42px);
  font-weight: 800;
  color: var(--text);
  line-height: 1.2;
  letter-spacing: -0.03em;
  margin-bottom: 14px;
}
.section-sub {
  font-size: 16px;
  color: var(--muted);
  line-height: 1.7;
}

/* ===== RESPONSIVE ===== */
@media (max-width: 768px) {
  .mkt-nav, .mkt-header__actions { display: none; }
  .mkt-burger { display: flex; }
  .mkt-footer__top { grid-template-columns: 1fr 1fr; gap: 32px; }
  .mkt-footer__brand { grid-column: 1 / -1; }
  .mkt-footer__bottom { flex-direction: column; gap: 8px; text-align: center; }
}
@media (max-width: 480px) {
  .mkt-footer__top { grid-template-columns: 1fr; }
  .mkt-container { padding: 0 20px; }
}
</style>
