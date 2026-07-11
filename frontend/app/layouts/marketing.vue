<template>
  <div class="mkt">
    <!-- TOP BAR (NorthWest-style contact strip) -->
    <div class="mkt-topbar">
      <div class="mkt-container mkt-topbar__inner">
        <div class="mkt-topbar__contacts">
          <a href="mailto:codecvinfo@gmail.com" class="mkt-topbar__item">
            <svg width="13" height="13" viewBox="0 0 24 24" fill="none"><path d="M4 6h16v12H4z" stroke="currentColor" stroke-width="1.8"/><path d="M4 7l8 6 8-6" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
            codecvinfo@gmail.com
          </a>
          <span class="mkt-topbar__item">
            <svg width="13" height="13" viewBox="0 0 24 24" fill="none"><path d="M5 4h4l2 5-2.5 1.5a11 11 0 005 5L15 13l5 2v4a2 2 0 01-2 2A16 16 0 013 6a2 2 0 012-2z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/></svg>
            Call us: +353 89 405 0730
          </span>
        </div>
        <div class="mkt-topbar__socials">
          <span>Follow:</span>
          <a href="https://www.linkedin.com/company/86762280" target="_blank" aria-label="LinkedIn">
            <svg width="12" height="12" viewBox="0 0 24 24" fill="currentColor"><path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433a2.062 2.062 0 01-2.063-2.065 2.064 2.064 0 112.063 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/></svg>
          </a>
          <a href="https://www.instagram.com/codecv_info/" target="_blank" aria-label="Instagram">
            <svg width="12" height="12" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838a6.162 6.162 0 100 12.324 6.162 6.162 0 000-12.324zM12 16a4 4 0 110-8 4 4 0 010 8zm6.406-11.845a1.44 1.44 0 100 2.881 1.44 1.44 0 000-2.881z"/></svg>
          </a>
        </div>
      </div>
    </div>

    <!-- HEADER -->
    <header :class="['mkt-header', { 'mkt-header--scrolled': scrolled }]">
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
            <h6 class="mkt-footer__col-title">Get in Touch</h6>
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
const mobileOpen = ref(false)
const scrolled = ref(false)

// Force light color mode for every marketing page. The global default in
// nuxt.config.ts is 'dark' (for the authenticated dashboard), which adds
// `.dark` on <html> and tints @nuxt/ui defaults — we don't want that here.
const colorMode = useColorMode()
colorMode.preference = 'light'

useHead({
  link: [
    { rel: 'preconnect', href: 'https://fonts.googleapis.com' },
    { rel: 'preconnect', href: 'https://fonts.gstatic.com', crossorigin: '' },
    {
      rel: 'stylesheet',
      href: 'https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap',
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
   CODECV MARKETING — Corporate Consulting Design System
   (NorthWest-inspired: white surfaces, emerald accents, bold
   geometric sans, kicker + underline section headers)
   ============================================================ */

.mkt, .mkt *, .mkt *::before, .mkt *::after {
  box-sizing: border-box;
  margin: 0;
  padding: 0;
}

.mkt {
  --bg: #ffffff;
  --bg-soft: #F8FAFC;
  --bg-card: #ffffff;
  --border: #E9EDF2;
  --text: #17212B;
  --text-body: #45505C;
  --muted: #8B95A1;
  --ink: #111A22;             /* top bar / dark sections */
  --accent: #059669;
  --accent-hover: #047857;
  --accent-light: rgba(5, 150, 105, 0.08);
  --accent-mid: rgba(5, 150, 105, 0.15);
  --accent-glow: rgba(5, 150, 105, 0.25);
  --ff: 'Poppins', sans-serif;
  --radius-btn: 3px;
  --radius-card: 6px;
  --shadow-sm: 0 2px 10px rgba(23, 33, 43, 0.06);
  --shadow-md: 0 14px 40px rgba(23, 33, 43, 0.10);

  background: var(--bg);
  color: var(--text);
  font-family: var(--ff);
  font-size: 15.5px;
  line-height: 1.7;
  min-height: 100vh;
  -webkit-font-smoothing: antialiased;
}

/* Container */
.mkt-container {
  max-width: 1180px;
  margin: 0 auto;
  padding: 0 28px;
}

/* ===== TOP BAR ===== */
.mkt-topbar {
  background: var(--ink);
  color: rgba(255, 255, 255, 0.75);
  font-size: 12.5px;
}
.mkt-topbar__inner {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 16px;
  min-height: 38px;
}
.mkt-topbar__contacts { display: flex; align-items: center; gap: 24px; }
.mkt-topbar__item {
  display: inline-flex;
  align-items: center;
  gap: 7px;
  color: rgba(255, 255, 255, 0.75);
  text-decoration: none;
  transition: color 0.2s;
}
.mkt-topbar__item:hover { color: #fff; }
.mkt-topbar__item svg { color: var(--accent); }
.mkt-topbar__socials { display: flex; align-items: center; gap: 12px; }
.mkt-topbar__socials span { color: rgba(255, 255, 255, 0.5); }
.mkt-topbar__socials a {
  color: rgba(255, 255, 255, 0.65);
  display: inline-flex;
  transition: color 0.2s;
}
.mkt-topbar__socials a:hover { color: #fff; }

/* ===== BUTTONS =====
   Rectangular, bold, uppercase — consulting style. */
.mkt-btn {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
  padding: 12px 26px;
  border-radius: var(--radius-btn);
  font-family: var(--ff);
  font-size: 13px;
  font-weight: 600;
  letter-spacing: 0.04em;
  text-transform: uppercase;
  text-decoration: none;
  cursor: pointer;
  transition: background 0.22s ease, color 0.22s ease, border-color 0.22s ease, box-shadow 0.22s ease, transform 0.18s ease;
  border: 2px solid transparent;
  white-space: nowrap;
}
.mkt-btn--primary {
  background: var(--accent);
  color: #fff;
  border-color: var(--accent);
}
.mkt-btn--primary:hover {
  background: var(--accent-hover);
  border-color: var(--accent-hover);
  transform: translateY(-1px);
  box-shadow: 0 8px 20px var(--accent-glow);
}
.mkt-btn--outline {
  background: transparent;
  color: var(--accent);
  border-color: rgba(5, 150, 105, 0.4);
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
.mkt-btn--ghost:hover { color: var(--accent); }
.mkt-btn--lg {
  padding: 15px 34px;
  font-size: 13.5px;
}
.mkt-btn--white {
  background: #fff;
  color: var(--accent);
  border-color: #fff;
}
.mkt-btn--white:hover {
  background: transparent;
  color: #fff;
  border-color: #fff;
}

/* ===== HEADER ===== */
.mkt-header {
  position: sticky;
  top: 0;
  z-index: 1000;
  padding: 14px 0;
  background: #fff;
  border-bottom: 1px solid var(--border);
  transition: box-shadow 0.3s, padding 0.3s;
}
.mkt-header--scrolled {
  box-shadow: 0 6px 24px rgba(23, 33, 43, 0.08);
  padding: 10px 0;
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
  font-size: 18px;
  font-weight: 800;
  color: var(--text);
  letter-spacing: -0.02em;
}
.mkt-logo__cv { color: var(--accent); }
.mkt-logo--footer .mkt-logo__text { color: var(--text); }

/* Nav */
.mkt-nav {
  display: flex;
  align-items: center;
  gap: 30px;
  flex: 1;
  justify-content: center;
}
.mkt-nav__link {
  position: relative;
  color: var(--text);
  text-decoration: none;
  font-size: 14.5px;
  font-weight: 600;
  padding: 6px 0;
  transition: color 0.2s;
}
.mkt-nav__link::after {
  content: '';
  position: absolute;
  left: 0; bottom: 0;
  width: 100%;
  height: 2px;
  background: var(--accent);
  transform: scaleX(0);
  transform-origin: right;
  transition: transform 0.28s ease;
}
.mkt-nav__link:hover,
.mkt-nav__link.router-link-active { color: var(--accent); }
.mkt-nav__link:hover::after,
.mkt-nav__link.router-link-active::after {
  transform: scaleX(1);
  transform-origin: left;
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
  position: absolute;
  top: 100%; left: 0; right: 0;
  background: #fff;
  border-bottom: 1px solid var(--border);
  box-shadow: 0 20px 40px rgba(23, 33, 43, 0.10);
  padding: 14px 24px 24px;
  display: flex;
  flex-direction: column;
  gap: 2px;
}
.mkt-mobile-menu__link {
  display: block;
  color: var(--text);
  text-decoration: none;
  font-size: 15.5px;
  font-weight: 600;
  padding: 12px 4px;
  border-bottom: 1px solid var(--border);
  transition: color 0.2s;
}
.mkt-mobile-menu__link:hover { color: var(--accent); }
.mkt-mobile-menu__cta {
  display: block;
  margin-top: 16px;
  padding: 14px 20px;
  background: var(--accent);
  color: #fff;
  text-decoration: none;
  font-size: 13.5px;
  font-weight: 600;
  letter-spacing: 0.04em;
  text-transform: uppercase;
  border-radius: var(--radius-btn);
  text-align: center;
  transition: background 0.2s;
}
.mkt-mobile-menu__cta:hover { background: var(--accent-hover); }

.slide-down-enter-active, .slide-down-leave-active { transition: opacity .2s, transform .2s; }
.slide-down-enter-from, .slide-down-leave-to { opacity: 0; transform: translateY(-8px); }

/* ===== FOOTER (light, corporate) ===== */
.mkt-footer {
  background: var(--bg-soft);
  border-top: 1px solid var(--border);
  padding: 76px 0 0;
}
.mkt-footer__top {
  display: grid;
  grid-template-columns: 2fr 1fr 1fr 1.5fr;
  gap: 56px;
  padding-bottom: 52px;
}
.mkt-footer__tagline {
  color: var(--muted);
  font-size: 14px;
  line-height: 1.7;
  margin: 14px 0 22px;
}
.mkt-footer__socials { display: flex; gap: 8px; }
.mkt-footer__social {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  width: 34px; height: 34px;
  border-radius: var(--radius-btn);
  border: 1px solid var(--border);
  color: var(--muted);
  text-decoration: none;
  transition: all 0.2s;
  background: #fff;
}
.mkt-footer__social:hover {
  border-color: var(--accent);
  color: #fff;
  background: var(--accent);
}
.mkt-footer__col-title {
  font-size: 15px;
  font-weight: 700;
  color: var(--text);
  margin-bottom: 8px;
  position: relative;
  padding-bottom: 12px;
}
.mkt-footer__col-title::after {
  content: '';
  position: absolute;
  left: 0; bottom: 0;
  width: 32px; height: 3px;
  background: var(--accent);
}
.mkt-footer__links {
  list-style: none;
  display: flex;
  flex-direction: column;
  gap: 11px;
  margin-top: 12px;
}
.mkt-footer__links li a,
.mkt-footer__links li span {
  color: var(--text-body);
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

/* ===== SHARED UTILITIES =====
   Same class API as before (other marketing pages depend on these).
   Kicker + bold heading + short green underline — the NorthWest motif. */
.section-badge {
  display: inline-block;
  background: transparent;
  color: var(--muted);
  font-size: 12.5px;
  font-weight: 600;
  letter-spacing: 0.16em;
  text-transform: uppercase;
  padding: 0;
  border-radius: 0;
  margin-bottom: 12px;
}
.section-header {
  text-align: center;
  max-width: 640px;
  margin: 0 auto 56px;
}
.section-title {
  font-size: clamp(26px, 3.4vw, 38px);
  font-weight: 700;
  color: var(--text);
  line-height: 1.22;
  letter-spacing: -0.01em;
  margin-bottom: 0;
  position: relative;
  padding-bottom: 22px;
}
.section-title::after {
  content: '';
  position: absolute;
  left: 50%; bottom: 0;
  transform: translateX(-50%);
  width: 54px; height: 3px;
  background: var(--accent);
}
.section-sub {
  font-size: 15.5px;
  color: var(--muted);
  line-height: 1.75;
  margin-top: 20px;
}

/* ===== RESPONSIVE ===== */
@media (max-width: 860px) {
  .mkt-topbar__socials { display: none; }
}
@media (max-width: 768px) {
  .mkt-nav, .mkt-header__actions { display: none; }
  .mkt-burger { display: flex; }
  .mkt-topbar { display: none; }
  .mkt-footer__top { grid-template-columns: 1fr 1fr; gap: 32px; }
  .mkt-footer__brand { grid-column: 1 / -1; }
  .mkt-footer__bottom { flex-direction: column; gap: 8px; text-align: center; }
}
@media (max-width: 480px) {
  .mkt-footer__top { grid-template-columns: 1fr; }
  .mkt-container { padding: 0 20px; }
}
@media (prefers-reduced-motion: reduce) {
  .mkt *, .mkt *::before, .mkt *::after {
    animation-duration: 0.01ms !important;
    animation-iteration-count: 1 !important;
    transition-duration: 0.01ms !important;
  }
}
</style>
