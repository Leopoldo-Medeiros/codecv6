<template>
  <NuxtLayout name="admin">
    <!-- Page Header -->
    <div class="mb-8">
      <div class="flex items-center justify-between">
        <div>
          <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">
            User Profile
          </h1>
          <p class="text-gray-500 dark:text-gray-400 text-lg">
            Viewing details for {{ selectedUser?.fullname }}
          </p>
        </div>
        <UButton @click="navigateTo('/users')" variant="ghost" color="gray">
          <Icon name="heroicons:arrow-left" class="w-4 h-4 mr-2" />
          Back to Users
        </UButton>
      </div>
    </div>

    <!-- Loading -->
    <div v-if="loading" class="flex justify-center p-12">
      <UIcon name="i-heroicons-arrow-path" class="w-8 h-8 animate-spin text-primary-500" />
    </div>

    <!-- Error -->
    <UAlert v-else-if="fetchError" color="red" variant="subtle" :description="fetchError" />

    <!-- User Details -->
    <div v-else-if="selectedUser" class="grid grid-cols-1 lg:grid-cols-3 gap-6">
      <!-- Profile Card -->
      <UCard class="lg:col-span-1">
        <div class="text-center">
          <div class="relative mb-4">
            <div class="absolute inset-0 rounded-full bg-primary-500 blur-md opacity-20 animate-pulse"></div>
            <UAvatar
              :src="selectedUser.profile?.profile_image_url || '/images/team-13.jpg'"
              :alt="selectedUser.fullname"
              size="3xl"
              class="ring-2 ring-primary-500 ring-offset-2 dark:ring-offset-gray-900"
            />
          </div>
          <h2 class="text-xl font-bold text-gray-900 dark:text-white">{{ selectedUser.fullname }}</h2>
          <p class="text-gray-500 dark:text-gray-400 mb-4">{{ selectedUser.profile?.profession || '—' }}</p>
          <UBadge :color="roleBadgeColor(selectedUser.role)" variant="subtle">
            {{ selectedUser.role }}
          </UBadge>
        </div>

        <UDivider class="my-6" />

        <!-- Social Links -->
        <div class="space-y-3">
          <div class="flex items-center justify-between text-sm">
            <div class="flex items-center gap-2 text-gray-500">
              <Icon name="mdi:github" class="w-5 h-5" />
              <span>GitHub</span>
            </div>
            <a v-if="selectedUser.profile?.github" :href="selectedUser.profile.github" target="_blank" class="text-primary-500 truncate max-w-[150px]">View Profile</a>
            <span v-else class="text-gray-400">Not available</span>
          </div>
          <div class="flex items-center justify-between text-sm">
            <div class="flex items-center gap-2 text-gray-500">
              <Icon name="mdi:linkedin" class="w-5 h-5 text-blue-600" />
              <span>LinkedIn</span>
            </div>
            <a v-if="selectedUser.profile?.linkedin" :href="selectedUser.profile.linkedin" target="_blank" class="text-primary-500 truncate max-w-[150px]">View Profile</a>
            <span v-else class="text-gray-400">Not available</span>
          </div>
        </div>
      </UCard>

      <!-- Details -->
      <div class="lg:col-span-2 space-y-6">

        <!-- Account Details -->
        <UCard>
          <template #header>
            <h3 class="font-semibold text-gray-900 dark:text-white">Account Details</h3>
          </template>
          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
              <label class="text-xs font-bold text-gray-400 uppercase tracking-widest">Full Name</label>
              <p class="text-gray-900 dark:text-white font-medium border-b dark:border-gray-800 pb-2 mt-1">{{ selectedUser.fullname }}</p>
            </div>
            <div>
              <label class="text-xs font-bold text-gray-400 uppercase tracking-widest">Email</label>
              <p class="text-gray-900 dark:text-white font-medium border-b dark:border-gray-800 pb-2 mt-1">{{ selectedUser.email }}</p>
            </div>
            <div>
              <label class="text-xs font-bold text-gray-400 uppercase tracking-widest">Birth Date</label>
              <p class="text-gray-900 dark:text-white font-medium border-b dark:border-gray-800 pb-2 mt-1">{{ selectedUser.profile?.birth_date || 'N/A' }}</p>
            </div>
            <div>
              <label class="text-xs font-bold text-gray-400 uppercase tracking-widest">Member Since</label>
              <p class="text-gray-900 dark:text-white font-medium border-b dark:border-gray-800 pb-2 mt-1">{{ formatDate(selectedUser.created_at) }}</p>
            </div>
          </div>
        </UCard>

        <!-- Career Profile -->
        <UCard v-if="hasOnboarding">
          <template #header>
            <h3 class="font-semibold text-gray-900 dark:text-white">Career Profile</h3>
          </template>
          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
              <label class="text-xs font-bold text-gray-400 uppercase tracking-widest">Role</label>
              <p class="text-gray-900 dark:text-white font-medium border-b dark:border-gray-800 pb-2 mt-1">
                {{ selectedUser.profile?.profession || 'N/A' }}
              </p>
            </div>
            <div>
              <label class="text-xs font-bold text-gray-400 uppercase tracking-widest">Level</label>
              <p class="mt-1 pb-2 border-b dark:border-gray-800">
                <UBadge :color="levelColor(selectedUser.profile?.level)" variant="subtle" class="capitalize">
                  {{ selectedUser.profile?.level || 'N/A' }}
                </UBadge>
              </p>
            </div>
            <div>
              <label class="text-xs font-bold text-gray-400 uppercase tracking-widest">Product Interest</label>
              <p class="mt-1 pb-2 border-b dark:border-gray-800">
                <UBadge :color="productColor(selectedUser.profile?.product_interest)" variant="subtle">
                  {{ productLabel(selectedUser.profile?.product_interest) }}
                </UBadge>
              </p>
            </div>
            <div>
              <label class="text-xs font-bold text-gray-400 uppercase tracking-widest">Timeline</label>
              <p class="text-gray-900 dark:text-white font-medium border-b dark:border-gray-800 pb-2 mt-1">
                {{ timelineLabel(selectedUser.profile?.timeline) }}
              </p>
            </div>
            <div>
              <label class="text-xs font-bold text-gray-400 uppercase tracking-widest">Availability</label>
              <p class="text-gray-900 dark:text-white font-medium border-b dark:border-gray-800 pb-2 mt-1">
                {{ selectedUser.profile?.availability_hours ? `${selectedUser.profile.availability_hours}h / week` : 'N/A' }}
              </p>
            </div>
            <div v-if="selectedUser.profile?.goal">
              <label class="text-xs font-bold text-gray-400 uppercase tracking-widest">Goal</label>
              <p class="text-gray-900 dark:text-white font-medium border-b dark:border-gray-800 pb-2 mt-1">
                {{ selectedUser.profile.goal }}
              </p>
            </div>
            <div class="md:col-span-2" v-if="selectedUser.profile?.stack?.length">
              <label class="text-xs font-bold text-gray-400 uppercase tracking-widest">Stack</label>
              <div class="flex flex-wrap gap-2 mt-2">
                <UBadge v-for="s in selectedUser.profile.stack" :key="s" color="indigo" variant="subtle">
                  {{ s }}
                </UBadge>
              </div>
            </div>
          </div>
        </UCard>

        <!-- Onboarding pending notice -->
        <UAlert v-else color="amber" variant="subtle"
          icon="i-heroicons-clock"
          title="Onboarding not completed"
          description="This user has not filled in their career profile yet." />

        <!-- Assign Consultant (admin only) -->
        <UCard v-if="isAdmin">
          <template #header>
            <h3 class="font-semibold text-gray-900 dark:text-white">Assigned Consultant</h3>
          </template>

          <div v-if="selectedUser.consultant" class="flex items-center gap-3 mb-4">
            <UAvatar :alt="selectedUser.consultant.fullname" size="sm" />
            <div>
              <p class="text-sm font-semibold text-gray-900 dark:text-white">{{ selectedUser.consultant.fullname }}</p>
              <p class="text-xs text-gray-400">{{ selectedUser.consultant.email }}</p>
            </div>
          </div>
          <p v-else class="text-sm text-gray-400 mb-4">No consultant assigned yet.</p>

          <div class="flex gap-3">
            <select v-model="selectedConsultantId" class="flex-1 rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-slate-800 px-3 py-2 text-sm text-gray-900 dark:text-white">
              <option :value="null">— Remove assignment —</option>
              <option v-for="c in consultants" :key="c.id" :value="c.id">{{ c.fullname }}</option>
            </select>
            <UButton
              :loading="assigningConsultant"
              :disabled="selectedConsultantId === selectedUser.consultant_id"
              @click="handleAssignConsultant"
            >
              Save
            </UButton>
          </div>
        </UCard>

      </div>
    </div>
  </NuxtLayout>
</template>

<script setup lang="ts">
definePageMeta({
  layout: false,
  middleware: 'auth'
})

const route = useRoute()
const userId = computed(() => Number(route.params.id))
const { user: selectedUser, loading, error: fetchError, fetchUser } = useUsers()
const { get, patch } = useApi()
const { isAdmin } = useAuth()
const toast = useToast()

const consultants          = ref<any[]>([])
const selectedConsultantId = ref<number | null>(null)
const assigningConsultant  = ref(false)

async function loadConsultants() {
  try {
    const res = await get<{ data: any[] }>('/consultants')
    consultants.value = res.data
  } catch {}
}

async function handleAssignConsultant() {
  assigningConsultant.value = true
  try {
    const res = await patch<{ user: any }>(`/users/${userId.value}/consultant`, {
      consultant_id: selectedConsultantId.value,
    })
    if (selectedUser.value) selectedUser.value = res.user
    selectedConsultantId.value = res.user.consultant_id ?? null
    toast.add({ title: 'Consultant assigned', color: 'green' })
  } catch (e: any) {
    toast.add({ title: e?.data?.message || 'Failed to assign consultant', color: 'red' })
  } finally {
    assigningConsultant.value = false
  }
}

const hasOnboarding = computed(() => !!selectedUser.value?.profile?.level)

const roleBadgeColor = (role?: string) => {
  switch (role) {
    case 'admin': return 'red'
    case 'consultant': return 'purple'
    default: return 'blue'
  }
}

const levelColor = (level?: string) => {
  switch (level) {
    case 'junior':  return 'green'
    case 'mid':     return 'blue'
    case 'senior':  return 'purple'
    case 'manager': return 'orange'
    default: return 'gray'
  }
}

const productColor = (p?: string) => {
  switch (p) {
    case 'self-serve':  return 'green'
    case 'bootcamp':    return 'blue'
    case 'mentorship':  return 'purple'
    default: return 'gray'
  }
}

const productLabel = (p?: string) => {
  switch (p) {
    case 'self-serve':  return 'Career Accelerator'
    case 'bootcamp':    return 'Laravel Bootcamp'
    case 'mentorship':  return '1-on-1 Mentorship'
    default: return 'N/A'
  }
}

const timelineLabel = (t?: string) => {
  switch (t) {
    case '1-3m':     return '1–3 months'
    case '3-6m':     return '3–6 months'
    case '6-12m':    return '6–12 months'
    case 'flexible': return 'No rush'
    default: return 'N/A'
  }
}

const formatDate = (date: string) => new Date(date).toLocaleDateString()

onMounted(async () => {
  await fetchUser(userId.value)
  selectedConsultantId.value = selectedUser.value?.consultant_id ?? null
  if (isAdmin.value) loadConsultants()
})
</script>
