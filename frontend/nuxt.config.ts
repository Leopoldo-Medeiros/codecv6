// https://nuxt.com/docs/api/configuration/nuxt-config
export default defineNuxtConfig({
  devtools: { enabled: false },

  devServer: {
    host: "localhost",
    port: 3000,
  },

  pages: true,

  compatibilityDate: "2025-02-28",

  runtimeConfig: {
    public: {
      apiBase: process.env.NUXT_PUBLIC_API_BASE || 'http://codecv6.lndo.site',
    },
  },

  typescript: {
    strict: true,
  },
})
