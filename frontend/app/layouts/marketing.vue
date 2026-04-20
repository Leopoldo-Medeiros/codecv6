<template>
  <div id="page" class="page font--jakarta">
    <!-- HEADER -->
    <header id="header" class="tra-menu navbar-dark light-hero-header white-scroll">
      <div class="header-wrapper">

        <!-- MOBILE HEADER -->
        <div class="wsmobileheader clearfix">
          <span class="smllogo">
            <NuxtLink to="/"><img src="/images/codecv.png" alt="CODECV" style="height:48px;width:auto;mix-blend-mode:multiply;"></NuxtLink>
          </span>
          <a id="wsnavtoggle" class="wsanimated-arrow"><span></span></a>
        </div>

        <!-- NAVIGATION MENU -->
        <div class="wsmainfull menu clearfix">
          <div class="wsmainwp clearfix">

            <div class="desktoplogo">
              <NuxtLink to="/" class="logo-black"><img src="/images/codecv.png" alt="CODECV" style="height:56px;width:auto;mix-blend-mode:multiply;"></NuxtLink>
            </div>
            <div class="desktoplogo">
              <NuxtLink to="/" class="logo-white"><img src="/images/codecv.png" alt="CODECV" style="height:56px;width:auto;mix-blend-mode:multiply;"></NuxtLink>
            </div>

            <nav class="wsmenu clearfix">
              <ul class="wsmenu-list nav-theme">

                <li class="nl-simple" aria-haspopup="true">
                  <NuxtLink to="/about" class="h-link">About</NuxtLink>
                </li>

                <li class="nl-simple" aria-haspopup="true">
                  <NuxtLink to="/pricing" class="h-link">Pricing</NuxtLink>
                </li>

                <li class="nl-simple" aria-haspopup="true">
                  <NuxtLink to="/faqs" class="h-link">FAQs</NuxtLink>
                </li>

                <template v-if="isAuthenticated">
                  <li class="nl-simple" aria-haspopup="true">
                    <NuxtLink to="/dashboard" class="h-link">Dashboard</NuxtLink>
                  </li>
                  <li class="nl-simple" aria-haspopup="true">
                    <a href="#" class="h-link" @click.prevent="logout">Logout</a>
                  </li>
                </template>
                <template v-else>
                  <li class="nl-simple reg-fst-link mobile-last-link" aria-haspopup="true">
                    <NuxtLink to="/login" class="h-link">Sign in</NuxtLink>
                  </li>
                  <li class="nl-simple" aria-haspopup="true">
                    <NuxtLink to="/login" class="btn r-04 btn--theme hover--theme last-link">Get Started</NuxtLink>
                  </li>
                </template>

              </ul>
            </nav>

          </div>
        </div>

      </div>
    </header>

    <main>
      <slot />
    </main>

    <!-- FOOTER -->
    <footer id="footer-3" class="pt-100 footer">
      <div class="container">

        <div class="row">

          <div class="col-xl-3">
            <div class="footer-info">
              <img class="footer-logo" src="/images/codecv.png" alt="CODECV" style="height:60px;width:auto;mix-blend-mode:multiply;">
            </div>
          </div>

          <div class="col-sm-4 col-md-3 col-xl-2">
            <div class="footer-links fl-1">
              <h6 class="s-17 w-700">Company</h6>
              <ul class="foo-links clearfix">
                <li><p><NuxtLink to="/about">About Us</NuxtLink></p></li>
                <li><p><NuxtLink to="/faqs">FAQs</NuxtLink></p></li>
                <li><p><a href="mailto:codecvinfo@gmail.com">Contact Us</a></p></li>
                <li><p><NuxtLink to="/privacy">Privacy Policy</NuxtLink></p></li>
                <li><p><NuxtLink to="/terms">Terms of Service</NuxtLink></p></li>
              </ul>
            </div>
          </div>

          <div class="col-sm-4 col-md-3 col-xl-2">
            <div class="footer-links fl-2">
              <h6 class="s-17 w-700">Platform</h6>
              <ul class="foo-links clearfix">
                <li><p><NuxtLink to="/pricing">Pricing</NuxtLink></p></li>
                <li><p><NuxtLink to="/login">Sign In</NuxtLink></p></li>
              </ul>
            </div>
          </div>

          <div class="col-sm-6 col-md-3">
            <div class="footer-links fl-4">
              <h6 class="s-17 w-700">Connect With Us</h6>
              <p class="footer-mail-link ico-25">
                <a href="mailto:codecvinfo@gmail.com">codecvinfo@gmail.com</a>
              </p>
              <ul class="footer-socials ico-25 text-center clearfix">
                <li><a href="https://www.linkedin.com/company/86762280" target="_blank"><span class="flaticon-linkedin"></span></a></li>
                <li><a href="https://www.instagram.com/codecv_info/" target="_blank"><span class="flaticon-instagram"></span></a></li>
              </ul>
            </div>
          </div>

        </div>

        <hr>

        <div class="bottom-footer">
          <div class="row row-cols-1 row-cols-md-2 d-flex align-items-center">
            <div class="col">
              <div class="footer-copyright">
                <p class="p-sm">&copy; {{ new Date().getFullYear() }} CODECV. <span>All Rights Reserved</span></p>
              </div>
            </div>
            <div class="col">
              <div class="bottom-secondary-link ico-15 text-end">
                <p class="p-sm">Dublin, Ireland &bull; +353 89 405 0730</p>
              </div>
            </div>
          </div>
        </div>

      </div>
    </footer>
  </div>
</template>

<script setup lang="ts">
const { isAuthenticated, logout } = useAuth()

useHead({
  link: [
    { rel: 'stylesheet', href: 'https://fonts.googleapis.com/css2?family=Rubik:wght@300;400;500;600;700&display=swap' },
    { rel: 'stylesheet', href: 'https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap' },
    { rel: 'stylesheet', href: '/css/bootstrap.min.css' },
    { rel: 'stylesheet', href: '/css/flaticon.css' },
    { rel: 'stylesheet', href: '/css/menu.css' },
    { rel: 'stylesheet', href: '/css/magnific-popup.css' },
    { rel: 'stylesheet', href: '/css/owl.carousel.min.css' },
    { rel: 'stylesheet', href: '/css/owl.theme.default.min.css' },
    { rel: 'stylesheet', href: '/css/animate.css' },
    { rel: 'stylesheet', href: '/css/blue-theme.css' },
    { rel: 'stylesheet', href: '/css/responsive.css' },
  ],
})

onMounted(() => {
  // Load scripts sequentially, initialise plugins after jQuery is ready
  const loadScript = (src: string): Promise<void> =>
    new Promise((resolve, reject) => {
      if (document.querySelector(`script[src="${src}"]`)) { resolve(); return }
      const s = document.createElement('script')
      s.src = src
      s.onload = () => resolve()
      s.onerror = () => reject(new Error(`Failed to load ${src}`))
      document.body.appendChild(s)
    })

  ;(async () => {
    await loadScript('/js/jquery-3.7.0.min.js')
    await loadScript('/js/bootstrap.min.js')
    await loadScript('/js/modernizr.custom.js')
    await loadScript('/js/jquery.easing.js')
    await loadScript('/js/jquery.appear.js')
    await loadScript('/js/menu.js')
    await loadScript('/js/owl.carousel.min.js')
    await loadScript('/js/jquery.magnific-popup.min.js')
    await loadScript('/js/wow.js')
    await loadScript('/js/custom.js')
  })()
})
</script>
