<template>
  <div class="wsmenucontainer">
    <!-- PRELOADER SPINNER -->
    <div v-if="isLoading" id="loading" class="loading--theme">
      <div id="loading-center"><span class="loader"></span></div>
    </div>

    <slot />
  </div>
</template>

<script setup>
const isLoading = ref(true)

onMounted(() => {
  // Simulate loading time or wait for assets
  setTimeout(() => {
    isLoading.value = false
  }, 500)
})
</script>
