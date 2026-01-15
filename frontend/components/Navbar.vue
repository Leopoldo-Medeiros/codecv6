<template>
  <nav class="bg-white shadow-sm fixed w-full z-10">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="flex justify-between h-16">
        <!-- Logo -->
        <div class="flex-shrink-0 flex items-center">
          <NuxtLink to="/">
            <img class="h-8 w-auto" src="/images/codecv.png" alt="CodeCV">
          </NuxtLink>
        </div>

        <!-- Desktop Navigation -->
        <div class="hidden sm:ml-6 sm:flex sm:space-x-8">
          <NuxtLink 
            to="/about" 
            class="border-transparent text-gray-700 hover:border-gray-300 hover:text-gray-900 inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium"
          >
            About
          </NuxtLink>
          <NuxtLink 
            to="/pricing" 
            class="border-transparent text-gray-700 hover:border-gray-300 hover:text-gray-900 inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium"
          >
            Pricing
          </NuxtLink>
          <NuxtLink 
            to="/faqs" 
            class="border-transparent text-gray-700 hover:border-gray-300 hover:text-gray-900 inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium"
          >
            FAQs
          </NuxtLink>
        </div>

        <!-- Right side items -->
        <div class="hidden sm:ml-6 sm:flex sm:items-center">
          <NuxtLink 
            v-if="!isAuthenticated"
            to="/login" 
            class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md text-sm font-medium"
          >
            Login
          </NuxtLink>
          <NuxtLink 
            v-else
            to="/dashboard" 
            class="text-gray-700 hover:text-gray-900 px-3 py-2 rounded-md text-sm font-medium"
          >
            Dashboard
          </NuxtLink>
        </div>

        <!-- Mobile menu button -->
        <div class="-mr-2 flex items-center sm:hidden">
          <button 
            @click="isOpen = !isOpen"
            class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none"
          >
            <span class="sr-only">Open main menu</span>
            <svg 
              class="h-6 w-6" 
              xmlns="http://www.w3.org/2000/svg" 
              fill="none" 
              viewBox="0 0 24 24" 
              stroke="currentColor"
            >
              <path v-if="!isOpen" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
              <path v-else stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
          </button>
        </div>
      </div>
    </div>

    <!-- Mobile menu -->
    <div v-if="isOpen" class="sm:hidden">
      <div class="pt-2 pb-3 space-y-1 bg-white">
        <NuxtLink 
          to="/about" 
          class="block pl-3 pr-4 py-2 border-l-4 text-base font-medium border-transparent text-gray-600 hover:bg-gray-50 hover:border-gray-300 hover:text-gray-800"
        >
          About
        </NuxtLink>
        <NuxtLink 
          to="/pricing" 
          class="block pl-3 pr-4 py-2 border-l-4 text-base font-medium border-transparent text-gray-600 hover:bg-gray-50 hover:border-gray-300 hover:text-gray-800"
        >
          Pricing
        </NuxtLink>
        <NuxtLink 
          to="/faqs" 
          class="block pl-3 pr-4 py-2 border-l-4 text-base font-medium border-transparent text-gray-600 hover:bg-gray-50 hover:border-gray-300 hover:text-gray-800"
        >
          FAQs
        </NuxtLink>
        <div class="pt-4 pb-3 border-t border-gray-200">
          <div class="space-y-1">
            <NuxtLink 
              v-if="!isAuthenticated"
              to="/login" 
              class="block px-4 py-2 text-base font-medium text-gray-500 hover:text-gray-800 hover:bg-gray-100"
            >
              Login
            </NuxtLink>
            <NuxtLink 
              v-else
              to="/dashboard" 
              class="block px-4 py-2 text-base font-medium text-gray-500 hover:text-gray-800 hover:bg-gray-100"
            >
              Dashboard
            </NuxtLink>
          </div>
        </div>
      </div>
    </div>
  </nav>
  <!-- Add padding to the top of the page content to account for the fixed navbar -->
  <div class="h-16"></div>
</template>

<script setup>
// You'll need to implement your authentication logic here
const isAuthenticated = false;
const isOpen = ref(false);
</script>
