<template>
  <NuxtLayout name="admin">
    <!-- Page Header -->
    <div class="mb-8">
      <div class="flex items-center justify-between">
        <div>
          <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">
            Courses Management
          </h1>
          <p class="text-gray-500 dark:text-gray-400 text-lg">
            Manage your course catalog and content
          </p>
        </div>
        <UButton v-if="isAdmin" @click="goToCreateCourse" color="primary">
          <Icon name="heroicons:plus" class="w-4 h-4 mr-2" />
          Create Course
        </UButton>
      </div>
    </div>

    <!-- Search and Filters -->
    <UCard class="mb-6">
      <div class="flex flex-col sm:flex-row gap-4">
        <div class="flex-1">
          <UInput
            v-model="searchQuery"
            placeholder="Search courses..."
            icon="i-heroicons-magnifying-glass"
            size="lg"
            @keyup.enter="handleSearch"
          />
        </div>
        <UButton @click="handleSearch" size="lg" :loading="loading">
          <Icon name="heroicons:magnifying-glass" class="w-4 h-4 mr-2" />
          Search
        </UButton>
      </div>
    </UCard>

    <!-- Courses Table -->
    <UCard class="hover:shadow-lg transition-shadow duration-200">
      <template #header>
        <div class="flex items-center justify-between">
          <div>
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
              All Courses
            </h3>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
              Page {{ currentPage }} of {{ totalPages }}
            </p>
          </div>
          <div class="flex items-center gap-2">
            <span class="text-sm text-gray-500">{{ totalCourses }} total courses</span>
          </div>
        </div>
      </template>

      <!-- Loading State -->
      <div v-if="loading" class="text-center py-12">
        <UIcon name="i-heroicons-arrow-path" class="w-8 h-8 animate-spin text-primary-500 mx-auto mb-4" />
        <p class="text-gray-500 dark:text-gray-400">Loading courses...</p>
      </div>

      <!-- Error State -->
      <div v-else-if="error" class="text-center py-12">
        <UIcon name="i-heroicons-exclamation-triangle" class="w-8 h-8 text-red-500 mx-auto mb-4" />
        <p class="text-red-500 mb-4">{{ error }}</p>
        <UButton @click="loadCourses" variant="outline" color="red">
          <Icon name="heroicons:arrow-path" class="w-4 h-4 mr-2" />
          Retry
        </UButton>
      </div>

      <!-- Empty State -->
      <div v-else-if="courses.length === 0" class="text-center py-12">
        <UIcon name="i-heroicons-inbox" class="w-12 h-12 text-gray-400 mx-auto mb-4" />
        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">No courses found</h3>
        <p class="text-gray-500 dark:text-gray-400 mb-4">
          {{ searchQuery ? 'Try adjusting your search terms' : 'Get started by creating your first course' }}
        </p>
        <UButton v-if="isAdmin" @click="goToCreateCourse" color="primary">
          <Icon name="heroicons:plus" class="w-4 h-4 mr-2" />
          Create Course
        </UButton>
      </div>

      <!-- Courses Table -->
      <UTable
        v-else
        :rows="[...courses]"
        :columns="columns"
        :loading="loading"
        class="w-full"
      >
        <!-- Name Column -->
        <template #name-data="{ row }">
          <div class="py-2">
            <p class="font-medium text-gray-900 dark:text-white">{{ row.name }}</p>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1 line-clamp-2">
              {{ row.description || 'No description available' }}
            </p>
          </div>
        </template>

        <!-- Slug Column -->
        <template #slug-data="{ row }">
          <UKbd>{{ row.slug }}</UKbd>
        </template>

        <!-- Instructor Column -->
        <template #instructor-data="{ row }">
          <div class="flex items-center gap-3">
            <UAvatar
              :src="row.user?.profile_image"
              :alt="row.user?.fullname"
              size="sm"
            >
              {{ row.user?.fullname?.charAt(0).toUpperCase() || 'U' }}
            </UAvatar>
            <div>
              <p class="font-medium text-gray-900 dark:text-white">{{ row.user?.fullname || 'Unknown' }}</p>
              <p class="text-sm text-gray-500 dark:text-gray-400">Instructor</p>
            </div>
          </div>
        </template>

        <!-- Created Column -->
        <template #created-data="{ row }">
          <div class="text-sm">
            <p class="text-gray-900 dark:text-white">{{ formatDate(row.created_at) }}</p>
            <p class="text-gray-500 dark:text-gray-400">{{ getRelativeTime(row.created_at) }}</p>
          </div>
        </template>

        <!-- Actions Column -->
        <template #actions-data="{ row }">
          <div class="flex items-center gap-2">
            <UButton
              :to="`/courses/${row.id}`"
              variant="ghost"
              color="yellow"
              size="sm"
              title="View Course"
            >
              <Icon name="heroicons:eye" class="w-4 h-4" />
            </UButton>
            <template v-if="isAdmin">
              <UButton
                @click="goToEditCourse(row.id)"
                variant="ghost"
                color="blue"
                size="sm"
                title="Edit Course"
              >
                <Icon name="heroicons:pencil-square" class="w-4 h-4" />
              </UButton>
              <UButton
                @click="confirmDelete(row.id)"
                variant="ghost"
                color="red"
                size="sm"
                title="Delete Course"
              >
                <Icon name="heroicons:trash" class="w-4 h-4" />
              </UButton>
            </template>
          </div>
        </template>
      </UTable>

      <!-- Pagination -->
      <template #footer v-if="courses.length > 0">
        <div class="flex items-center justify-between">
          <div class="text-sm text-gray-500 dark:text-gray-400">
            Page {{ currentPage }} of {{ totalPages }} ({{ totalCourses }} courses)
          </div>
          <UPagination
            v-model="currentPage"
            :total="totalCourses"
            :page-count="itemsPerPage"
            show-last
            show-first
          />
        </div>
      </template>
    </UCard>
  </NuxtLayout>
</template>

<script setup lang="ts">
definePageMeta({
  layout: false,
  middleware: 'auth'
})

const router = useRouter()
const toast  = useToast()
const { user } = useAuth()
const isAdmin = computed(() => user.value?.role === 'admin')

// Clients have no business on the management page
onMounted(() => {
  if (user.value?.role === 'client') router.replace('/my-courses')
})

const searchQuery = ref('')
const currentPage = ref(1)
const totalCourses = ref(0)
const itemsPerPage = 10

const { courses, loading, error, fetchCourses, deleteCourse } = useCourses()

const columns = [
  { key: 'name', label: 'Course Name', sortable: true },
  { key: 'slug', label: 'Slug' },
  { key: 'instructor', label: 'Instructor' },
  { key: 'created', label: 'Created' },
  { key: 'actions', label: 'Actions', class: 'w-32' }
]

const totalPages = computed(() => Math.max(1, Math.ceil(totalCourses.value / itemsPerPage)))

onMounted(() => loadCourses())

const loadCourses = async () => {
  const res = await fetchCourses({
    search: searchQuery.value || undefined,
    page: currentPage.value,
    per_page: itemsPerPage,
  })
  if (res?.meta) totalCourses.value = res.meta.total
}

watch(currentPage, loadCourses)

const handleSearch = () => {
  currentPage.value = 1
  loadCourses()
}

const goToCreateCourse = () => {
  router.push('/courses/create')
}

const goToEditCourse = (courseId: number) => {
  router.push(`/courses/${courseId}/edit`)
}

const confirmDelete = async (courseId: number) => {
  if (confirm('Are you sure you want to delete this course?')) {
    try {
      await deleteCourse(courseId)
      toast.add({
        title: 'Success',
        description: 'Course deleted successfully',
        color: 'green'
      })
      await loadCourses()
    } catch (err) {
      toast.add({
        title: 'Error',
        description: 'Failed to delete course',
        color: 'red'
      })
    }
  }
}

const formatDate = (dateString: string) => {
  if (!dateString) return 'N/A'
  const date = new Date(dateString)
  return date.toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'short',
    day: 'numeric'
  })
}

const getRelativeTime = (dateString: string) => {
  if (!dateString) return ''
  const date = new Date(dateString)
  const now = new Date()
  const diffTime = Math.abs(now.getTime() - date.getTime())
  const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24))
  
  if (diffDays === 1) return 'Yesterday'
  if (diffDays < 7) return `${diffDays} days ago`
  if (diffDays < 30) return `${Math.floor(diffDays / 7)} weeks ago`
  return `${Math.floor(diffDays / 30)} months ago`
}

useHead({
  title: 'Courses Management'
})
</script>

