<template>
  <div>
    <!-- Navbar -->
    <Navbar />
    
    <!-- Page Content -->
    <div class="min-h-screen">
      <slot />
    </div>

    <!-- Footer -->
    <footer class="bg-white border-t border-gray-200">
      <div class="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
        <p class="text-center text-gray-500 text-sm">
          &copy; 2025 CodeCV. All rights reserved.
        </p>
      </div>
    </footer>
  </div>
</template>

<script setup>
import Navbar from '~/components/Navbar.vue';
</script>
