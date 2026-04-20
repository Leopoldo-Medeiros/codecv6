<template>
  <div class="min-h-screen flex flex-col bg-white dark:bg-gray-900">
    <Navbar />

    <main class="flex-1">
      <slot />
    </main>

    <footer class="bg-gray-900 border-t border-gray-800 py-10 px-6">
      <div class="max-w-5xl mx-auto flex flex-col md:flex-row items-center justify-between gap-4">
        <div class="flex items-center gap-2">
          <img src="/images/codecv.png" alt="CodeCV" class="h-7 w-auto opacity-80" onerror="this.style.display='none'" />
          <span class="text-gray-400 text-sm font-medium">CODECV</span>
        </div>
        <div class="flex gap-6">
          <NuxtLink to="/about" class="text-sm text-gray-500 hover:text-gray-300 transition-colors">About</NuxtLink>
          <NuxtLink to="/pricing" class="text-sm text-gray-500 hover:text-gray-300 transition-colors">Pricing</NuxtLink>
          <NuxtLink to="/faqs" class="text-sm text-gray-500 hover:text-gray-300 transition-colors">FAQs</NuxtLink>
        </div>
        <p class="text-xs text-gray-600">&copy; {{ new Date().getFullYear() }} CODECV. All rights reserved.</p>
      </div>
    </footer>
  </div>
</template>
