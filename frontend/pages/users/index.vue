<template>
  <NuxtLayout name="admin">
    <section class="users-section">
      <div class="container-fluid">
        <div class="px-2">
          <div class="row mb-3">
            <div class="col-lg-6 col-12">
              <h1 class="page-title">Users List</h1>
            </div>
            <div class="col-lg-6 col-12 text-end">
              <NuxtLink to="/users/create" class="btn btn-primary btn-sm mb-4">
                Create User
              </NuxtLink>
            </div>
          </div>

          <div class="row">
            <div class="col">
              <div class="modern-table">
                <!-- Search Bar -->
                <div class="table-search-bar p-3">
                  <div class="input-group">
                    <input 
                      v-model="searchQuery" 
                      type="text" 
                      placeholder="Search by name or email" 
                      class="form-control modern-input"
                      @keypress.enter="handleSearch"
                    >
                    <button @click="handleSearch" class="btn btn-primary" type="button">Search</button>
                  </div>
                </div>

                <div class="table-responsive">
                  <table class="table mb-0">
                    <thead>
                      <tr>
                        <th>USER</th>
                        <th>CREATED</th>
                        <th class="text-center">EMAIL</th>
                        <th class="text-center">ACTION</th>
                      </tr>
                    </thead>
                    <tbody>
                      <tr v-for="user in paginatedUsers" :key="user.id">
                        <td class="align-middle">
                          <div class="d-flex align-items-center">
                            <img 
                              :src="user.profile_image || '/images/team-13.jpg'" 
                              alt="Profile Image" 
                              class="rounded-circle user-avatar me-3"
                            >
                            <div>
                              <NuxtLink :to="`/users/${user.id}`" class="user-name-link">
                                {{ user.name }}
                              </NuxtLink>
                              <div class="user-role">{{ user.role }}</div>
                            </div>
                          </div>
                        </td>
                        <td class="align-middle">{{ user.created_at }}</td>
                        <td class="text-center align-middle">
                          <a :href="`mailto:${user.email}`" class="email-link">{{ user.email }}</a>
                        </td>
                        <td class="align-middle text-center action-cell">
                          <NuxtLink :to="`/users/${user.id}`" class="action-icon text-warning" title="View">
                            <i class="fas fa-eye"></i>
                          </NuxtLink>
                          <a href="#" class="action-icon text-info" title="Edit">
                            <i class="fas fa-edit"></i>
                          </a>
                          <button 
                            @click="confirmDelete(user.id)" 
                            class="action-icon text-danger" 
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
                <div class="table-footer p-3">
                  <div class="d-flex justify-content-between align-items-center">
                    <div class="pagination-info">
                      Showing {{ startIndex + 1 }} to {{ endIndex }} of {{ filteredUsers.length }} results
                    </div>
                    <nav>
                      <ul class="pagination mb-0">
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

// Mock users data - in a real app, this would come from an API
const users = ref([
  {
    id: 1,
    name: 'Leo Medeiros',
    email: 'admin@admin.com',
    role: 'Admin',
    profile_image: '/images/team-13.jpg',
    created_at: '2025/02/10'
  },
  {
    id: 2,
    name: 'Client User',
    email: 'client@client.com',
    role: 'Client',
    profile_image: null,
    created_at: '2025/02/10'
  },
  {
    id: 3,
    name: 'Emelia Kub',
    email: 'vschmitt@example.com',
    role: 'Client',
    profile_image: null,
    created_at: '2025/02/10'
  },
  {
    id: 4,
    name: 'Ms. Sibyl White',
    email: 'dickens.dasha@example.org',
    role: 'Client',
    profile_image: null,
    created_at: '2025/02/10'
  },
  {
    id: 5,
    name: 'Prof. Vesta Ullrich DDS',
    email: 'kristofer.jones@example.com',
    role: 'Client',
    profile_image: null,
    created_at: '2025/02/10'
  },
  {
    id: 6,
    name: 'Heber Teurel II',
    email: 'winnifred.kutch@example.com',
    role: 'Client',
    profile_image: null,
    created_at: '2025/02/10'
  },
  {
    id: 7,
    name: 'Arnoldo Dooley',
    email: 'polisson@example.org',
    role: 'Client',
    profile_image: null,
    created_at: '2025/02/10'
  },
  {
    id: 8,
    name: 'Ellen Schopp',
    email: 'flopez@example.com',
    role: 'Client',
    profile_image: null,
    created_at: '2025/02/10'
  },
  {
    id: 9,
    name: 'Ewald King',
    email: 'lkirlin@example.org',
    role: 'Client',
    profile_image: null,
    created_at: '2025/02/10'
  },
  {
    id: 10,
    name: 'Ms. Mary Lowe',
    email: 'isaac.pouros@example.org',
    role: 'Client',
    profile_image: null,
    created_at: '2025/02/10'
  }
])

const filteredUsers = computed(() => {
  if (!searchQuery.value) {
    return users.value
  }
  const query = searchQuery.value.toLowerCase()
  return users.value.filter(user => 
    user.name.toLowerCase().includes(query) || 
    user.email.toLowerCase().includes(query)
  )
})

const totalPages = computed(() => {
  return Math.ceil(filteredUsers.value.length / itemsPerPage)
})

const paginatedUsers = computed(() => {
  const start = (currentPage.value - 1) * itemsPerPage
  const end = start + itemsPerPage
  return filteredUsers.value.slice(start, end)
})

const startIndex = computed(() => {
  return (currentPage.value - 1) * itemsPerPage
})

const endIndex = computed(() => {
  return Math.min(startIndex.value + itemsPerPage, filteredUsers.value.length)
})

const handleSearch = () => {
  currentPage.value = 1
}

const changePage = (page: number) => {
  if (page >= 1 && page <= totalPages.value) {
    currentPage.value = page
  }
}

const confirmDelete = (userId: number) => {
  if (confirm('Are you sure you want to delete this user?')) {
    // In a real app, this would call an API to delete the user
    users.value = users.value.filter(u => u.id !== userId)
  }
}

useHead({
  title: 'Users List'
})
</script>

<style scoped>
.users-section {
  min-height: 100vh;
  background: var(--bg-primary);
  padding: 2rem;
}

.page-title {
  font-size: 2rem;
  font-weight: 700;
  color: var(--text-primary);
}

.table-search-bar {
  background: var(--bg-tertiary);
  border-bottom: 1px solid var(--border-color);
}

.modern-table thead th {
  color: var(--text-primary) !important;
  font-weight: 700;
  font-size: 0.75rem;
  letter-spacing: 0.05em;
  text-transform: uppercase;
  padding: 1rem;
  background: var(--bg-tertiary);
}

.modern-table tbody td {
  color: #2d3748 !important;
  font-size: 0.95rem;
  padding: 1rem;
  font-weight: 500;
}

/* Ensure all table text is readable, but exclude action icons */
.modern-table tbody td:not(.action-cell),
.modern-table tbody td:not(.action-cell) * {
  color: #2d3748 !important;
}

.user-avatar {
  width: 40px;
  height: 40px;
  object-fit: cover;
}

.user-name-link {
  font-weight: 600;
  text-decoration: none;
  color: #3b82f6 !important;
  display: block;
  font-size: 1rem;
}

.user-name-link:hover {
  text-decoration: underline;
}

.user-role {
  font-size: 0.875rem;
  color: var(--text-muted) !important;
  font-style: italic;
}

.email-link {
  color: #3b82f6 !important;
  text-decoration: none;
  font-size: 0.95rem;
}

.email-link:hover {
  text-decoration: underline;
}

.action-cell {
  width: 150px;
}

.action-icon {
  font-size: 1.2rem;
  text-decoration: none;
  cursor: pointer;
  border: none;
  background: none;
  padding: 0.5rem;
  transition: opacity 0.2s ease;
}

.action-icon:hover {
  opacity: 0.7;
}

.table-footer {
  background: var(--bg-tertiary);
  border-top: 1px solid var(--border-color);
}

.pagination-info {
  color: var(--text-primary) !important;
  font-size: 0.875rem;
  font-weight: 500;
}

.pagination .page-link {
  background: var(--bg-card);
  border-color: var(--border-color);
  color: var(--text-primary) !important;
  font-weight: 500;
}

.pagination .page-link:hover {
  background: var(--bg-hover);
  border-color: var(--border-color);
  color: var(--text-primary) !important;
}

.pagination .page-item.active .page-link {
  background: var(--gradient-blue);
  border-color: #3b82f6;
  color: white !important;
}

.pagination .page-item.disabled .page-link {
  background: var(--bg-tertiary);
  color: var(--text-muted) !important;
}
</style>
