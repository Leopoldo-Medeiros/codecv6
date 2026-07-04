<template>
  <NuxtLayout name="admin">

    <div v-if="loading" class="flex justify-center py-24">
      <UIcon name="i-heroicons-arrow-path" class="animate-spin text-3xl text-emerald-500" />
    </div>

    <div v-else-if="notFound" class="flex flex-col items-center py-24 text-center">
      <UIcon name="i-heroicons-exclamation-triangle" class="mb-3 h-12 w-12 text-red-400" />
      <p class="text-gray-500 dark:text-gray-400">Course not found.</p>
      <UButton class="mt-4" size="sm" color="gray" @click="router.back()">Go Back</UButton>
    </div>

    <template v-else>

      <div class="mb-6 flex items-center gap-3">
        <UButton icon="i-heroicons-arrow-left" size="sm" color="gray" variant="ghost" @click="router.back()" />
        <div>
          <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Edit Course</h1>
          <p class="mt-0.5 text-sm text-gray-500 dark:text-gray-400">Update course details</p>
        </div>
      </div>

      <div class="max-w-xl">
        <div class="rounded-xl border border-gray-200 bg-white dark:border-gray-700 dark:bg-gray-800">
          <div class="border-b border-gray-100 px-6 py-4 dark:border-gray-700">
            <h2 class="text-sm font-semibold text-gray-900 dark:text-white">Course Details</h2>
          </div>

          <form class="p-6" @submit.prevent="handleSubmit">

            <div class="flex flex-col gap-4">
              <div>
                <label class="mb-1.5 block text-xs font-semibold text-gray-700 dark:text-gray-300">
                  Name <span class="text-red-400">*</span>
                </label>
                <UInput v-model="form.name" placeholder="e.g. Laravel Fundamentals"
                  :disabled="saving" size="lg" />
              </div>
              <div>
                <label class="mb-1.5 block text-xs font-semibold text-gray-700 dark:text-gray-300">
                  Description
                </label>
                <UTextarea v-model="form.description" :rows="4"
                  placeholder="What will students learn in this course?"
                  :disabled="saving" />
              </div>
            </div>

            <!-- Error -->
            <div v-if="error" class="mt-4 flex items-center gap-2 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700 dark:border-red-800/40 dark:bg-red-950/20 dark:text-red-400">
              <UIcon name="i-heroicons-exclamation-circle" class="h-4 w-4 shrink-0" />
              {{ error }}
            </div>

            <div class="mt-6 flex items-center gap-3">
              <UButton type="submit" color="primary" size="md" :loading="saving"
                :disabled="!form.name.trim() || saving">
                Save Changes
              </UButton>
              <UButton color="gray" variant="outline" size="md" @click="router.back()">
                Cancel
              </UButton>
            </div>

          </form>
        </div>
      </div>

    </template>

  </NuxtLayout>
</template>

<script setup lang="ts">
definePageMeta({ layout: false, middleware: 'auth' })

const route  = useRoute()
const router = useRouter()
const toast  = useToast()
const { user, isAdmin, isConsultant } = useAuth()
const { fetchCourse, updateCourse } = useCourses()

const courseId = computed(() => Number(route.params.id))
const loading  = ref(true)
const notFound = ref(false)
const saving   = ref(false)
const error    = ref('')
const form     = reactive({ name: '', description: '' })

onMounted(async () => {
  try {
    const course = await fetchCourse(courseId.value) as any
    if (!course) {
      notFound.value = true
      return
    }

    // Admin can edit any course; consultant only their own (mirrors the backend 403 in CourseController::update)
    const canEdit = isAdmin.value || (isConsultant.value && course.user?.id === user.value?.id)
    if (!canEdit) {
      toast.add({ title: 'Not allowed', description: 'You can only edit courses you created.', color: 'red' })
      router.replace('/courses')
      return
    }

    form.name        = course.name
    form.description = course.description ?? ''
  } catch {
    notFound.value = true
  } finally {
    loading.value = false
  }
})

useHead({ title: 'Edit Course — CODECV' })

async function handleSubmit() {
  if (!form.name.trim()) return
  saving.value = true
  error.value  = ''
  try {
    await updateCourse(courseId.value, { name: form.name, description: form.description || undefined })
    toast.add({ title: 'Course updated', color: 'green' })
    navigateTo(`/courses/${courseId.value}`)
  } catch (err: any) {
    error.value = err?.data?.message || err?.message || 'Failed to update course.'
  } finally {
    saving.value = false
  }
}
</script>
