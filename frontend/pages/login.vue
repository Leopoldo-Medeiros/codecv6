<template>
  <div>


    <!-- PAGE CONTENT -->
    <div id="page" class="page font--jakarta">

      <!-- LOGIN PAGE -->
      <div id="login" class="bg--fixed login-1 login-section division">
        <div class="container">
          <div class="row">
            <div class="col-md-8 col-lg-6 offset-md-2 offset-lg-3">
              <div class="register-page-form">


                <!-- TITLE -->
                <div class="col-md-12">
                  <div class="register-form-title">
                    <h3 class="s-32 w-700">Log in to CODECV</h3>
                  </div>
                </div>


                <!-- LOGIN FORM -->
                <form name="signinform" class="row sign-in-form" @submit.prevent="handleLogin">

                  <!-- Google Button -->
                  <div class="col-md-12">
                    <a href="#" class="btn btn-google ico-left">
                      <img src="/images/png_icons/google.png" alt="google-icon"> Sign in with Google
                    </a>
                  </div>

                  <!-- Login Separator -->
                  <div class="col-md-12 text-center">
                    <div class="separator-line">Or, sign in with your email</div>
                  </div>

                  <!-- Form Input -->
                  <div class="col-md-12">
                    <label for="email" class="block text-sm font-medium text-gray-700">Email address</label>
                    <input class="form-control email" type="email" name="email" placeholder="example@example.com">
                  </div>

                  <!-- Form Input -->
                  <div class="col-md-12">
                    <p class="p-sm input-header">Password</p>
                    <div class="wrap-input">
                      <span class="btn-show-pass ico-20" @click="togglePassword">
                        <span class="flaticon-visibility eye-pass" :class="{ 'flaticon-invisible': showPassword }"></span>
                      </span>
                      <input class="form-control password" :type="showPassword ? 'text' : 'password'" name="password"
                        placeholder="* * * * * * * * *">
                    </div>
                  </div>

                  <!-- Error Message -->
                  <div v-if="errorMessage" class="col-md-12">
                    <div class="alert alert-danger">
                      {{ errorMessage }}
                    </div>
                  </div>

                  <!-- Form Submit Button -->
                  <div class="col-md-12">
                    <button
                      type="submit"
                      class="btn btn--theme hover--theme submit"
                      :disabled="isLoading"
                    >
                      <span v-if="isLoading">Logging in...</span>
                      <span v-else>Log In</span>
                    </button>
                  </div>

                </form> <!-- END LOGIN FORM -->


              </div>
            </div>
          </div> <!-- End row -->
        </div> <!-- End container -->
      </div> <!-- END LOGIN PAGE -->
    </div> <!-- END PAGE CONTENT -->
  </div>
</template>

<script setup lang="ts">
definePageMeta({
  layout: 'auth'
})

useHead({
  title: 'Login'
})

const { login, user } = useAuth()
const router = useRouter()
const showPassword = ref(false)
const errorMessage = ref('')
const isLoading = ref(false)

// Debug function to check localStorage
const debugAuth = () => {
  console.log('Current user in localStorage:', localStorage.getItem('user'))
  console.log('Current user in state:', user?.value)
}

// Expose debug function to window for testing
onMounted(() => {
  if (process.client) {
    window.debugAuth = debugAuth
  }
})

const togglePassword = () => {
  showPassword.value = !showPassword.value
}

const handleLogin = async (event: Event) => {
  event.preventDefault()

  try {
    isLoading.value = true
    errorMessage.value = ''

    const formData = new FormData(event.target as HTMLFormElement)
    const email = formData.get('email')?.toString().trim()
    const password = formData.get('password')?.toString().trim()

    console.log('Login attempt with:', { email, password })

    if (!email || !password) {
      errorMessage.value = 'Please enter both email and password'
      return
    }

    const { login } = useAuth()
    const result = await login(email, password)
    console.log('Login result:', result)

    if (result.success) {
      console.log('Login successful, redirecting to dashboard...')
      // Use router.push for SPA navigation instead of full page reload
      navigateTo('/dashboard')
    } else {
      console.error('Login failed:', result.error)
      errorMessage.value = result.error || 'Login failed. Please check your credentials.'

      // Clear password field on error
      const passwordInput = document.querySelector('input[name="password"]') as HTMLInputElement
      if (passwordInput) passwordInput.value = ''
    }
  } catch (error: any) {
    console.error('Login error:', error)

    // More specific error handling
    if (error.response) {
      // The request was made and the server responded with a status code
      // that falls out of the range of 2xx
      console.error('Error response data:', error.response.data)
      console.error('Error status:', error.response.status)
      errorMessage.value = error.response.data?.message || 'An error occurred during login.'
    } else if (error.request) {
      // The request was made but no response was received
      console.error('No response received:', error.request)
      errorMessage.value = 'No response from server. Please check your connection.'
    } else {
      // Something happened in setting up the request that triggered an Error
      console.error('Error setting up request:', error.message)
      errorMessage.value = 'An error occurred while setting up the login request.'
    }
  } finally {
    isLoading.value = false
  }
}
</script>

<style scoped>
.alert-danger {
  color: #721c24;
  background-color: #f8d7da;
  border: 1px solid #f5c6cb;
  padding: 10px 15px;
  border-radius: 4px;
  margin-bottom: 15px;
  font-size: 14px;
}

.btn-google {
  display: flex;
  align-items: center;
  justify-content: center;
  width: 100%;
  border: 1px solid #e0e0e0;
  background-color: #fff;
  color: #333;
  font-weight: 500;
  padding: 12px;
  border-radius: 5px;
  transition: all 0.3s ease;
}

.btn-google:hover {
  background-color: #f5f5f5;
  border-color: #d0d0d0;
}

.btn-google img {
  width: 20px;
  height: 20px;
  margin-right: 10px;
}

.form-control {
  padding: 8px 12px;
  height: auto;
  min-height: 40px;
}

.btn--theme {
  padding: 8px 20px !important;
  min-height: 40px !important;
  height: 40px !important;
  line-height: 1 !important;
  display: flex !important;
  align-items: center !important;
  justify-content: center !important;
}
</style>
