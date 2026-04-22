// https://nuxt.com/docs/api/configuration/nuxt-config

const ddevHostname = process.env.DDEV_HOSTNAME || 'codecv6.localhost.ddev.site'

export default defineNuxtConfig({
  devtools: { enabled: false },
  ssr: false,

  devServer: {
    host: "0.0.0.0",
    port: 3000,
  },

  compatibilityDate: "2025-04-15",

  modules: ['@nuxt/ui'],

  ui: {
    primary: 'emerald',
    gray: 'slate',
  },

  colorMode: {
    preference: 'dark'
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
        clientPort: 3000,
      },
      proxy: {
        '/api': {
          target: 'http://codecv6.ddev.site:33000',
          changeOrigin: true,
        },
        '/sanctum': {
          target: 'http://codecv6.ddev.site:33000',
          changeOrigin: true,
        },
        '/storage': {
          target: 'http://codecv6.ddev.site:33000',
          changeOrigin: true,
        },
      },
    },
  },
})
