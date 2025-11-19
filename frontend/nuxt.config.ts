// https://nuxt.com/docs/api/configuration/nuxt-config
export default defineNuxtConfig({
  devtools: { enabled: false },

  devServer: {
      host: "localhost",
      port: 3000,
  },

  // Ensure you have the pages directory and a default page
  pages: true,

  compatibilityDate: "2025-02-28",
})