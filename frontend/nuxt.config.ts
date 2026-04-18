// https://nuxt.com/docs/api/configuration/nuxt-config
export default defineNuxtConfig({
  devtools: { enabled: false },
  ssr: false,

  devServer: {
    host: "localhost",
    port: 3000,
  },

  pages: true,

  compatibilityDate: "2025-04-15",

  modules: ['@nuxt/ui'],

  colorMode: {
    preference: 'dark'
  },

  runtimeConfig: {
    public: {
      // Override with NUXT_PUBLIC_API_BASE env var for local dev.
      // Default points to the stable Lando hostname (never changes on restart).
      apiBase: 'http://codecv6.lndo.site',
    },
  },

  typescript: {
    strict: true,
  },
})
