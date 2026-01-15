<template>
  <header id="header" class="tra-menu navbar-light white-scroll">
    <div class="header-wrapper">

      <!-- NAVIGATION MENU -->
      <div class="wsmainfull menu clearfix">
        <div class="wsmainwp clearfix">

          <!-- MOBILE HEADER -->
          <div class="wsmobileheader clearfix">
            <NuxtLink to="/" class="smll-logo">
              <img src="/images/codecv.png" alt="mobile-logo">
            </NuxtLink>
            <a id="wsnavtoggle" class="wsanimated-arrow" @click="toggleMenu"><span></span></a>
          </div>

          <!-- DESKTOP HEADER -->
          <div class="wsdesktopheader clearfix float-start ps-sm-5 mt-20">
            <NuxtLink to="/" class="smll-logo">
              <img src="/images/codecv.png" alt="desktop-logo">
            </NuxtLink>
          </div>

          <!-- MAIN MENU -->
          <nav class="wsmenu clearfix">
            <div class="overlapblackbg" @click="closeMenu"></div>
            <ul class="wsmenu-list nav-theme">

              <!-- DROPDOWN SUB MENU -->
              <li aria-haspopup="true">
                <NuxtLink to="/about" class="h-link">About</NuxtLink>
              </li>

              <!-- SIMPLE NAVIGATION LINK -->
              <li class="nl-simple" aria-haspopup="true">
                <NuxtLink to="/pricing" class="h-link">Pricing</NuxtLink>
              </li>

              <!-- SIMPLE NAVIGATION LINK -->
              <li class="nl-simple" aria-haspopup="true">
                <NuxtLink to="/faqs" class="h-link">FAQs</NuxtLink>
              </li>

              <!-- AUTH LINKS -->
              <li v-if="!isAuthenticated" class="nl-simple reg-fst-link mobile-last-link" aria-haspopup="true">
                <NuxtLink to="/login" class="h-link">Login</NuxtLink>
              </li>

              <li v-if="!isAuthenticated" class="nl-simple" aria-haspopup="true">
                <NuxtLink to="/register" class="btn r-04 btn--theme hover--tra-white last-link">Sign up</NuxtLink>
              </li>

              <!-- LOGGED IN USER -->
              <li v-if="isAuthenticated" class="nl-simple" aria-haspopup="true">
                <NuxtLink to="/dashboard" class="h-link">
                  {{ user?.name }} <i class="fas fa-user"></i>
                </NuxtLink>
              </li>
              
              <li v-if="isAuthenticated" class="nl-simple" aria-haspopup="true">
                <a href="#" @click.prevent="logout" class="h-link">Logout</a>
              </li>

            </ul>
          </nav> <!-- END MAIN MENU -->

        </div>
      </div> <!-- END NAVIGATION MENU -->

    </div> <!-- End header-wrapper -->
  </header>
</template>

<script setup>
const isMenuOpen = ref(false)
const { isAuthenticated, user, logout } = useAuth()

const toggleMenu = () => {
  isMenuOpen.value = !isMenuOpen.value
  if (isMenuOpen.value) {
    document.body.classList.add('wsactive')
  } else {
    document.body.classList.remove('wsactive')
  }
}

const closeMenu = () => {
  isMenuOpen.value = false
  document.body.classList.remove('wsactive')
}

// Sticky Header Logic
const handleScroll = () => {
  if (window.scrollY > 80) {
    document.querySelector('.wsmainfull')?.classList.add('scroll')
  } else {
    document.querySelector('.wsmainfull')?.classList.remove('scroll')
  }
}

onMounted(() => {
  window.addEventListener('scroll', handleScroll)
})

onUnmounted(() => {
  document.body.classList.remove('wsactive')
  window.removeEventListener('scroll', handleScroll)
})
</script>
