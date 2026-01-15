<template>
  <NuxtLayout name="admin">
    <section>
      <div class="container bootstrap snippets bootdey">
        <div class="px-2">
          <div class="row mb-3">
            <div class="col-lg-6">
              <h1 class="text-xl fw-bold large-text">Courses List</h1>
            </div>
            <div class="col-lg-6 text-end">
              <button class="btn btn-primary btn-sm mb-4">Create Course</button>
            </div>
          </div>

          <div class="row">
            <div class="col">
              <div class="card shadow mb-4">
                <div class="card-body">
                  <!-- Search Bar -->
                  <div class="input-group mb-3">
                    <input 
                      v-model="searchQuery" 
                      type="text" 
                      placeholder="Search by name or slug" 
                      class="form-control"
                      @keypress.enter="handleSearch"
                    >
                    <button @click="handleSearch" class="btn btn-primary" type="button">Search</button>
                  </div>

                  <div class="table-responsive">
                    <table class="table course-list mt-3">
                      <thead>
                        <tr>
                          <th><span>Course Name</span></th>
                          <th><span>Slug</span></th>
                          <th class="text-center"><span>Action</span></th>
                        </tr>
                      </thead>
                      <tbody>
                        <tr v-for="course in paginatedCourses" :key="course.id">
                          <td>{{ course.name }}</td>
                          <td>{{ course.slug }}</td>
                          <td class="text-center" style="width: 20%;">
                            <NuxtLink :to="`/courses/${course.id}`" class="table-link text-warning me-2" title="View">
                              <i class="fas fa-eye"></i>
                            </NuxtLink>
                            <a href="#" class="table-link text-info me-2" title="Edit">
                              <i class="fas fa-edit"></i>
                            </a>
                            <button 
                              @click="confirmDelete(course.id)" 
                              class="table-link text-danger" 
                              style="border: none; background: none;" 
                              title="Delete"
                            >
                              <i class="fas fa-trash"></i>
                            </button>
                          </td>
                        </tr>
                      </tbody>
                    </table>
                  </div>

                  <!-- Pagination -->
                  <div class="d-flex justify-content-center mt-3">
                    <nav>
                      <ul class="pagination">
                        <li class="page-item" :class="{ disabled: currentPage === 1 }">
                          <a class="page-link" href="#" @click.prevent="changePage(currentPage - 1)">‹</a>
                        </li>
                        <li 
                          v-for="page in totalPages" 
                          :key="page" 
                          class="page-item" 
                          :class="{ active: page === currentPage }"
                        >
                          <a class="page-link" href="#" @click.prevent="changePage(page)">{{ page }}</a>
                        </li>
                        <li class="page-item" :class="{ disabled: currentPage === totalPages }">
                          <a class="page-link" href="#" @click.prevent="changePage(currentPage + 1)">›</a>
                        </li>
                      </ul>
                    </nav>
                  </div>
                  <div class="text-center text-muted small">
                    Showing {{ startIndex + 1 }} to {{ endIndex }} of {{ filteredCourses.length }} results
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>
  </NuxtLayout>
</template>

<script setup lang="ts">
definePageMeta({
  layout: false,
  middleware: 'auth'
})

const searchQuery = ref('')
const currentPage = ref(1)
const itemsPerPage = 10

// Mock courses data
const courses = ref([
  { id: 1, name: 'Introduction to Vue.js', slug: 'intro-to-vuejs' },
  { id: 2, name: 'Advanced JavaScript', slug: 'advanced-javascript' },
  { id: 3, name: 'React Fundamentals', slug: 'react-fundamentals' },
  { id: 4, name: 'Node.js Backend Development', slug: 'nodejs-backend' },
  { id: 5, name: 'Full Stack Web Development', slug: 'full-stack-web-dev' },
  { id: 6, name: 'Python for Beginners', slug: 'python-beginners' },
  { id: 7, name: 'Database Design with SQL', slug: 'database-design-sql' },
  { id: 8, name: 'Mobile App Development', slug: 'mobile-app-dev' },
  { id: 9, name: 'DevOps Essentials', slug: 'devops-essentials' },
  { id: 10, name: 'Cloud Computing with AWS', slug: 'cloud-computing-aws' },
  { id: 11, name: 'Machine Learning Basics', slug: 'machine-learning-basics' },
  { id: 12, name: 'Cybersecurity Fundamentals', slug: 'cybersecurity-fundamentals' }
])

const filteredCourses = computed(() => {
  if (!searchQuery.value) {
    return courses.value
  }
  const query = searchQuery.value.toLowerCase()
  return courses.value.filter(course => 
    course.name.toLowerCase().includes(query) || 
    course.slug.toLowerCase().includes(query)
  )
})

const totalPages = computed(() => {
  return Math.ceil(filteredCourses.value.length / itemsPerPage)
})

const paginatedCourses = computed(() => {
  const start = (currentPage.value - 1) * itemsPerPage
  const end = start + itemsPerPage
  return filteredCourses.value.slice(start, end)
})

const startIndex = computed(() => {
  return (currentPage.value - 1) * itemsPerPage
})

const endIndex = computed(() => {
  return Math.min(startIndex.value + itemsPerPage, filteredCourses.value.length)
})

const handleSearch = () => {
  currentPage.value = 1
}

const changePage = (page: number) => {
  if (page >= 1 && page <= totalPages.value) {
    currentPage.value = page
  }
}

const confirmDelete = (courseId: number) => {
  if (confirm('Are you sure you want to delete this course?')) {
    courses.value = courses.value.filter(c => c.id !== courseId)
  }
}

useHead({
  title: 'Courses List'
})
</script>

<style scoped>
.large-text {
  font-size: 1.5rem;
}

.table-link {
  font-size: 1.2rem;
  text-decoration: none;
  cursor: pointer;
}

.table-link:hover {
  opacity: 0.7;
}
</style>
