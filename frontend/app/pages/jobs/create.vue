<template>
  <NuxtLayout name="admin">

    <div class="mb-6 flex items-center gap-3">
      <UButton icon="i-heroicons-arrow-left" size="sm" color="gray" variant="ghost" @click="router.back()" />
      <div>
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">New Job Listing</h1>
        <p class="mt-0.5 text-sm text-gray-500 dark:text-gray-400">Post a job opportunity for clients</p>
      </div>
    </div>

    <div class="max-w-xl">
      <div class="rounded-xl border border-gray-200 bg-white dark:border-gray-700 dark:bg-gray-800">
        <div class="border-b border-gray-100 px-6 py-4 dark:border-gray-700">
          <h2 class="text-sm font-semibold text-gray-900 dark:text-white">Job Details</h2>
        </div>

        <form class="p-6" @submit.prevent="handleSubmit">

          <div class="flex flex-col gap-4">

            <!-- Title -->
            <div>
              <label class="mb-1.5 block text-xs font-semibold text-gray-700 dark:text-gray-300">
                Title <span class="text-red-400">*</span>
              </label>
              <UInput v-model="form.title" placeholder="e.g. Junior Backend Developer" :disabled="saving" size="lg" />
            </div>

            <!-- Company -->
            <div>
              <label class="mb-1.5 block text-xs font-semibold text-gray-700 dark:text-gray-300">
                Company <span class="text-red-400">*</span>
              </label>
              <UInput v-model="form.company" placeholder="e.g. Acme Corp" :disabled="saving" size="lg" />
            </div>

            <!-- Location / Salary -->
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
              <div>
                <label class="mb-1.5 block text-xs font-semibold text-gray-700 dark:text-gray-300">Location</label>
                <UInput v-model="form.location" placeholder="e.g. Remote / Dublin" :disabled="saving" />
              </div>
              <div>
                <label class="mb-1.5 block text-xs font-semibold text-gray-700 dark:text-gray-300">Salary</label>
                <UInput v-model="form.salary" placeholder="e.g. €45,000 - €55,000" :disabled="saving" />
              </div>
            </div>

            <!-- Description -->
            <div>
              <label class="mb-1.5 block text-xs font-semibold text-gray-700 dark:text-gray-300">Description</label>
              <UTextarea v-model="form.description" :rows="5"
                placeholder="Responsibilities, requirements, benefits…" :disabled="saving" />
            </div>

          </div>

          <!-- Error -->
          <div v-if="error" class="mt-4 flex items-center gap-2 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700 dark:border-red-800/40 dark:bg-red-950/20 dark:text-red-400">
            <UIcon name="i-heroicons-exclamation-circle" class="h-4 w-4 shrink-0" />
            {{ error }}
          </div>

          <div class="mt-6 flex items-center gap-3">
            <UButton type="submit" color="primary" size="md" :loading="saving"
              :disabled="!form.title.trim() || !form.company.trim() || saving">
              Create Job
            </UButton>
            <UButton color="gray" variant="outline" size="md" @click="router.back()">
              Cancel
            </UButton>
          </div>

        </form>
      </div>
    </div>

  </NuxtLayout>
</template>

<script setup lang="ts">
definePageMeta({ layout: false, middleware: 'auth' })
useHead({ title: 'New Job — CODECV' })

const router = useRouter()
const toast  = useToast()
const { isAdmin, isConsultant } = useAuth()
const { createJob } = useJobs()

// Only admin/consultant may post jobs (mirrors the `role:admin|consultant` route middleware)
onMounted(() => {
  if (!isAdmin.value && !isConsultant.value) {
    router.replace('/jobs')
  }
})

const form = reactive({ title: '', company: '', location: '', salary: '', description: '' })
const saving = ref(false)
const error  = ref('')

async function handleSubmit() {
  if (!form.title.trim() || !form.company.trim()) return
  saving.value = true
  error.value  = ''
  try {
    await createJob({
      title: form.title,
      company: form.company,
      location: form.location || undefined,
      salary: form.salary || undefined,
      description: form.description || undefined,
    })
    toast.add({ title: 'Job created', color: 'green' })
    navigateTo('/jobs')
  } catch (err: any) {
    error.value = err?.data?.message || err?.message || 'Failed to create job.'
  } finally {
    saving.value = false
  }
}
</script>
