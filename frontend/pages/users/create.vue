<template>
  <NuxtLayout name="admin">
    <div class="create-user-container">
      <div class="container-fluid">
        <div class="px-2">
          <div class="row mb-4">
            <div class="col-12">
              <h1 class="page-title">Create New User</h1>
            </div>
          </div>

          <div class="row">
            <div class="col-lg-8">
              <div class="modern-card">
                <form @submit.prevent="handleSubmit">
                  <!-- Full Name -->
                  <div class="mb-3">
                    <label class="form-label">Full Name *</label>
                    <input 
                      v-model="form.fullname" 
                      type="text" 
                      class="form-control modern-input"
                      required
                    >
                  </div>

                  <!-- Email -->
                  <div class="mb-3">
                    <label class="form-label">Email *</label>
                    <input 
                      v-model="form.email" 
                      type="email" 
                      class="form-control modern-input"
                      required
                    >
                  </div>

                  <!-- Password -->
                  <div class="mb-3">
                    <label class="form-label">Password *</label>
                    <input 
                      v-model="form.password" 
                      type="password" 
                      class="form-control modern-input"
                      required
                      minlength="8"
                    >
                  </div>

                  <!-- Role -->
                  <div class="mb-3">
                    <label class="form-label">Role *</label>
                    <select v-model="form.role" class="form-control modern-input" required>
                      <option value="">Select Role</option>
                      <option v-for="role in roles" :key="role.id" :value="role.id">
                        {{ role.name }}
                      </option>
                    </select>
                  </div>

                  <!-- Error Message -->
                  <div v-if="error" class="alert alert-danger">
                    {{ error }}
                  </div>

                  <!-- Success Message -->
                  <div v-if="success" class="alert alert-success">
                    User created successfully! Redirecting...
                  </div>

                  <!-- Buttons -->
                  <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary" :disabled="loading">
                      <span v-if="loading">Creating...</span>
                      <span v-else>Create User</span>
                    </button>
                    <NuxtLink to="/users" class="btn btn-secondary">
                      Cancel
                    </NuxtLink>
                  </div>
                </form>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </NuxtLayout>
</template>

<script setup lang="ts">
definePageMeta({
  layout: false,
  middleware: 'auth'
})

const api = useApi()
const router = useRouter()

const form = ref({
  fullname: '',
  email: '',
  password: '',
  role: ''
})

const roles = ref([])
const loading = ref(false)
const error = ref('')
const success = ref(false)

// Fetch roles on mount
onMounted(async () => {
  try {
    roles.value = await api.get('/roles')
  } catch (err) {
    console.error('Failed to fetch roles:', err)
    // Fallback to hardcoded roles if API fails
    roles.value = [
      { id: 1, name: 'admin' },
      { id: 2, name: 'client' }
    ]
  }
})

const handleSubmit = async () => {
  loading.value = true
  error.value = ''
  success.value = false

  try {
    // Mock user creation (replace with real API call later)
    console.log('Creating user with data:', form.value)
    
    // Simulate API delay
    await new Promise(resolve => setTimeout(resolve, 500))
    
    // For now, just show success message
    // In production, this would be: await api.post('/users', form.value)
    success.value = true
    
    // Redirect to users list after 1.5 seconds
    setTimeout(() => {
      router.push('/users')
    }, 1500)
  } catch (err: any) {
    error.value = err.data?.message || 'Failed to create user. Please try again.'
    console.error('Create user error:', err)
  } finally {
    loading.value = false
  }
}

useHead({
  title: 'Create User'
})
</script>

<style scoped>
.create-user-container {
  min-height: 100vh;
  background: var(--bg-primary);
  padding: 2rem;
}

.page-title {
  font-size: 2rem;
  font-weight: 700;
  color: var(--text-primary);
  margin-bottom: 0;
}

.modern-card {
  background: var(--bg-card);
  border-radius: 12px;
  padding: 2rem;
  border: 1px solid var(--border-color);
  box-shadow: var(--shadow-md);
}

.form-label {
  color: var(--text-primary);
  font-weight: 600;
  margin-bottom: 0.5rem;
  display: block;
}

.modern-input {
  background: var(--bg-tertiary);
  border: 1px solid var(--border-color);
  color: var(--text-primary);
  padding: 0.75rem;
  border-radius: 8px;
  width: 100%;
  transition: all 0.2s ease;
}

.modern-input:focus {
  outline: none;
  border-color: #3b82f6;
  box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
  background: var(--bg-secondary);
}

.btn {
  padding: 0.75rem 1.5rem;
  border-radius: 8px;
  font-weight: 600;
  transition: all 0.2s ease;
}

.btn-primary {
  background: var(--gradient-blue);
  border: none;
  color: white;
}

.btn-primary:hover:not(:disabled) {
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(59, 130, 246, 0.4);
}

.btn-primary:disabled {
  opacity: 0.6;
  cursor: not-allowed;
}

.btn-secondary {
  background: var(--bg-tertiary);
  border: 1px solid var(--border-color);
  color: var(--text-primary);
  text-decoration: none;
  display: inline-block;
}

.btn-secondary:hover {
  background: var(--bg-hover);
  text-decoration: none;
}

.alert {
  padding: 1rem;
  border-radius: 8px;
  margin-bottom: 1rem;
}

.alert-danger {
  background: rgba(239, 68, 68, 0.1);
  border: 1px solid rgba(239, 68, 68, 0.3);
  color: #ef4444;
}

.alert-success {
  background: rgba(16, 185, 129, 0.1);
  border: 1px solid rgba(16, 185, 129, 0.3);
  color: #10b981;
}
</style>
