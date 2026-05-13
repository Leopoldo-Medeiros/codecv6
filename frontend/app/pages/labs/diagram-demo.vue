<template>
  <NuxtLayout name="admin">
    <div class="py-8">
      <div class="mb-6 px-4">
        <p class="mb-1 text-xs font-semibold uppercase tracking-widest text-emerald-500">Labs · Framework Internals</p>
        <h1 class="text-2xl font-bold text-white">Laravel Request Lifecycle</h1>
        <p class="mt-1 text-sm text-slate-400">The full lifecycle of an HTTP request — with observability injection.</p>
      </div>
      <LaravelLifecycleDiagram />
    </div>
  </NuxtLayout>
</template>

<script setup lang="ts">
definePageMeta({ middleware: 'auth' })
</script>
