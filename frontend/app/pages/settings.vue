<script setup lang="ts">
// /settings is folded into /profile until there is real account-level
// state to manage (billing, 2FA, sessions). Permanent redirect preserves
// any external links to the old URL.
definePageMeta({
  middleware: 'auth',
})

await navigateTo('/profile', { replace: true, redirectCode: 301 })
</script>

<template><span /></template>
