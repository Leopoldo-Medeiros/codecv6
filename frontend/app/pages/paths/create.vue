<template>
  <NuxtLayout name="admin">

    <div class="mb-6 flex items-center gap-3">
      <UButton icon="i-heroicons-arrow-left" size="sm" color="gray" variant="ghost" @click="router.back()" />
      <div>
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">New Learning Path</h1>
        <p class="mt-0.5 text-sm text-gray-500 dark:text-gray-400">Create a roadmap for your clients</p>
      </div>
    </div>

    <div class="max-w-xl">
      <div class="rounded-xl border border-gray-200 bg-white dark:border-gray-700 dark:bg-gray-800">
        <div class="border-b border-gray-100 px-6 py-4 dark:border-gray-700">
          <h2 class="text-sm font-semibold text-gray-900 dark:text-white">Path Details</h2>
        </div>

        <form class="p-6" @submit.prevent="handleSubmit">

          <div class="flex flex-col gap-4">

            <!-- Name -->
            <div>
              <label class="mb-1.5 block text-xs font-semibold text-gray-700 dark:text-gray-300">
                Name <span class="text-red-400">*</span>
              </label>
              <UInput v-model="form.name" placeholder="e.g. Laravel Developer Roadmap"
                :disabled="saving" size="lg" />
            </div>

            <!-- Description -->
            <div>
              <label class="mb-1.5 block text-xs font-semibold text-gray-700 dark:text-gray-300">
                Description
              </label>
              <UTextarea v-model="form.description" :rows="4"
                placeholder="What career goal does this path help achieve?"
                :disabled="saving" />
            </div>

          </div>

          <!-- Error -->
          <div v-if="error" class="mt-4 flex items-center gap-2 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700 dark:border-red-800/40 dark:bg-red-950/20 dark:text-red-400">
            <UIcon name="i-heroicons-exclamation-circle" class="h-4 w-4 shrink-0" />
            {{ error }}
          </div>

          <div class="mt-6 flex items-center gap-3">
            <UButton type="submit" color="emerald" size="md" :loading="saving"
              :disabled="!form.name.trim() || saving">
              Create Path
            </UButton>
            <UButton color="gray" variant="outline" size="md" @click="router.back()">
              Cancel
            </UButton>
          </div>

        </form>
      </div>

      <p class="mt-4 text-xs text-gray-400 dark:text-gray-500">
        After creating the path you'll be taken directly to the step editor to build your roadmap.
      </p>
    </div>

  </NuxtLayout>
</template>

<script setup lang="ts">
definePageMeta({ layout: false, middleware: 'auth' })
useHead({ title: 'New Path — CODECV' })

const router = useRouter()
const toast  = useToast()
const { createPath } = usePaths()

const form = reactive({ name: '', description: '' })
const saving = ref(false)
const error  = ref('')

async function handleSubmit() {
  if (!form.name.trim()) return
  saving.value = true
  error.value  = ''
  try {
    const res = await createPath({ name: form.name, description: form.description || undefined }) as any
    const id  = res?.path?.id ?? res?.data?.id
    toast.add({ title: 'Path created', description: 'Now add steps to build your roadmap.', color: 'green' })
    navigateTo(id ? `/paths/${id}` : '/paths')
  } catch (err: any) {
    error.value = err?.data?.message || err?.message || 'Failed to create path.'
  } finally {
    saving.value = false
  }
}
</script>
