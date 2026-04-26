// https://nuxt.com/docs/api/configuration/nuxt-config

const ddevHostname = process.env.DDEV_HOSTNAME || 'codecv6.ddev.site'

export default defineNuxtConfig({
  devtools: { enabled: false },
  // SSR enabled globally so marketing/auth-public pages can be prerendered
  // with full HTML + structured data (JSON-LD) for SEO. Authenticated pages
  // explicitly opt back into SPA via routeRules below.
  ssr: true,

  devServer: {
    host: "0.0.0.0",
    port: 3000,
  },

  compatibilityDate: "2025-04-15",

  modules: ['@nuxt/ui', '@nuxtjs/seo'],

  ui: {
    primary: 'emerald',
    gray: 'slate',
  },

  // @nuxtjs/seo — site-wide config consumed by sitemap, robots,
  // schema.org, and canonical URLs.
  site: {
    url: 'https://codecv.ie',
    name: 'CODECV',
    description: 'AI-powered CV analysis, LinkedIn optimisation, structured learning paths, and 1-on-1 coaching for IT professionals in Ireland.',
    defaultLocale: 'en-IE',
    // TODO: replace with a proper 1200x630 og-image at /public/og-image.png.
    // Logo is just a fallback so social previews don't 404.
    image: '/images/Logo/codecv.png',
  },

  // og-image module needs SSR; we run as SPA, so we'll provide a static
  // og:image at /og-image.png and disable the dynamic generator.
  ogImage: { enabled: false },

  // Restrict the sitemap to public marketing/auth pages only. Without this,
  // @nuxtjs/sitemap auto-discovers every page (including authenticated UI
  // like /dashboard, /jobs, /courses) and pollutes search indexing.
  sitemap: {
    urls: [
      '/',
      '/about',
      '/pricing',
      '/faqs',
      '/terms',
      '/privacy',
      '/login',
      '/register',
      '/forgot-password',
      '/reset-password',
    ],
    excludeAppSources: true,
  },

  // Hybrid rendering:
  //   - Marketing & auth-public pages: prerendered HTML at build time (SEO).
  //   - Everything else (auth-required UI, dynamic data): SPA (ssr: false).
  routeRules: {
    // Prerendered (full HTML + structured data in SERP)
    '/': { prerender: true },
    '/about': { prerender: true },
    '/pricing': { prerender: true },
    '/faqs': { prerender: true },
    '/terms': { prerender: true },
    '/privacy': { prerender: true },
    '/login': { prerender: true },
    '/register': { prerender: true },
    '/forgot-password': { prerender: true },
    '/reset-password': { prerender: true },

    // SPA — depends on auth state and/or runtime API data
    '/dashboard': { ssr: false },
    '/onboarding': { ssr: false },
    '/payment': { ssr: false },
    '/profile': { ssr: false },
    '/settings': { ssr: false },
    '/my-cv': { ssr: false },
    '/my-paths': { ssr: false },
    '/my-courses': { ssr: false },
    '/linkedin-analyser': { ssr: false },
    '/auth/**': { ssr: false },
    '/courses/**': { ssr: false },
    '/jobs/**': { ssr: false },
    '/paths/**': { ssr: false },
    '/plans/**': { ssr: false },
    '/my-clients/**': { ssr: false },
    '/users/**': { ssr: false },
    '/labs/**': { ssr: false },
  },

  colorMode: {
    preference: 'dark'
  },

  app: {
    head: {
      htmlAttrs: { lang: 'en-IE' },
      titleTemplate: '%s | CODECV',
      meta: [
        { name: 'robots', content: 'index, follow' },
        { name: 'viewport', content: 'width=device-width, initial-scale=1' },
        { property: 'og:site_name', content: 'CODECV' },
        { property: 'og:type', content: 'website' },
        { name: 'twitter:card', content: 'summary_large_image' },
        { name: 'theme-color', content: '#070C18' },
      ],
      link: [
        { rel: 'icon', type: 'image/png', href: '/images/codecv.png' },
      ],
    },
  },

  runtimeConfig: {
    public: {
      // Override with NUXT_PUBLIC_API_BASE env var (loaded via --dotenv from root .env).
      apiBase: `https://${ddevHostname}`,
    },
  },

  typescript: {
    strict: true,
  },

  vite: {
    server: {
      hmr: {
        protocol: 'ws',
        host: 'localhost',
        port: 24678,
        clientPort: 24678,
      },
    },
  },
})
