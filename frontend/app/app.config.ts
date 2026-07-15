export default defineAppConfig({
  ui: {
    // Emerald brand primary + neutral (near-black) grays so every @nuxt/ui
    // component (UCard, UTable, UInput, …) matches the "Lotech" dashboard's
    // near-black surfaces instead of the default bluish slate.
    primary: 'emerald',
    gray: 'neutral',
  },
})
