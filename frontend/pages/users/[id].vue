<template>
  <NuxtLayout name="admin">
    <section class="profile-section">
      <div class="container py-5">
        <div class="row">
          <div class="col">
            <nav aria-label="breadcrumb" class="bg-body-tertiary rounded-3 p-3 mb-4">
              <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item">
                  <NuxtLink to="/dashboard">Dashboard</NuxtLink>
                </li>
                <li class="breadcrumb-item">
                  <NuxtLink to="/users">User</NuxtLink>
                </li>
                <li class="breadcrumb-item active" aria-current="page">User Profile</li>
              </ol>
            </nav>
          </div>
        </div>

        <div class="row">
          <div class="col-lg-4">
            <div class="card mb-4">
              <div class="card-body text-center">
                <div class="profile-image-wrapper mb-3 mx-auto">
                  <div class="glowing-circle"></div>
                  <img 
                    :src="selectedUser?.profile_image || '/images/team-13.jpg'" 
                    alt="avatar" 
                    class="rounded-circle img-fluid profile-image position-relative"
                  >
                </div>
                <h5 class="mb-0">{{ selectedUser?.name || 'User' }}</h5>
                <p class="text-muted mb-1">{{ selectedUser?.profession || 'Software Engineer' }}</p>
                <p class="text-muted mb-4">Dublin, Ireland</p>
                <div class="d-flex justify-content-center mb-2">
                  <button type="button" class="btn btn-primary">Follow</button>
                  <button type="button" class="btn btn-outline-primary ms-1">Message</button>
                </div>
              </div>
            </div>
            
            <div class="card mb-4 mb-lg-0">
              <div class="card-body p-0">
                <ul class="list-group list-group-flush rounded-3">
                  <li class="list-group-item d-flex justify-content-between align-items-center p-3">
                    <i class="fab fa-github fa-lg text-body"></i>
                    <a v-if="selectedUser?.github" :href="selectedUser.github" target="_blank" class="mb-0 text-decoration-none text-truncate" style="max-width: 200px;">
                      {{ selectedUser.github }}
                    </a>
                    <p v-else class="mb-0"></p>
                  </li>
                  <li class="list-group-item d-flex justify-content-between align-items-center p-3">
                    <i class="fab fa-linkedin fa-lg" style="color: #0077b5;"></i>
                    <a v-if="selectedUser?.linkedin" :href="selectedUser.linkedin" target="_blank" class="mb-0 text-decoration-none text-truncate" style="max-width: 200px;">
                      {{ selectedUser.linkedin }}
                    </a>
                    <p v-else class="mb-0"></p>
                  </li>
                  <li class="list-group-item d-flex justify-content-between align-items-center p-3">
                    <i class="fab fa-instagram fa-lg" style="color: #ac2bac;"></i>
                    <p class="mb-0"></p>
                  </li>
                  <li class="list-group-item d-flex justify-content-between align-items-center p-3">
                    <i class="fab fa-facebook-f fa-lg" style="color: #3b5998;"></i>
                    <p class="mb-0"></p>
                  </li>
                </ul>
              </div>
            </div>
            
            <div class="col-12">
              <button @click="$router.back()" class="btn btn-primary btn-sm fw-bold mt-4">
                <i class="fas fa-arrow-left"></i> Back
              </button>
            </div>
          </div>
          
          <div class="col-lg-8">
            <div class="card mb-4">
              <div class="card-body">
                <div class="row">
                  <div class="col-sm-3">
                    <p class="mb-0 fw-bold"><i class="fas fa-user me-2"></i>Full Name</p>
                  </div>
                  <div class="col-sm-9">
                    <p class="text-muted mb-0">{{ selectedUser?.name || 'N/A' }}</p>
                  </div>
                </div>
                <hr>
                <div class="row">
                  <div class="col-sm-3">
                    <p class="mb-0 fw-bold"><i class="fas fa-envelope me-2"></i>Email</p>
                  </div>
                  <div class="col-sm-9">
                    <p class="text-muted mb-0">{{ selectedUser?.email || 'N/A' }}</p>
                  </div>
                </div>
                <hr>
                <div class="row">
                  <div class="col-sm-3">
                    <p class="mb-0 fw-bold"><i class="fas fa-birthday-cake me-2"></i>Birth Date</p>
                  </div>
                  <div class="col-sm-9">
                    <p class="text-muted mb-0">{{ selectedUser?.birth_date || 'N/A' }}</p>
                  </div>
                </div>
                <hr>
                <div class="row">
                  <div class="col-sm-3">
                    <p class="mb-0 fw-bold"><i class="fas fa-briefcase me-2"></i>Profession</p>
                  </div>
                  <div class="col-sm-9">
                    <p class="text-muted mb-0">{{ selectedUser?.profession || 'Software Engineer' }}</p>
                  </div>
                </div>
              </div>
            </div>
            
            <div class="row">
              <div class="col-md-6">
                <div class="card mb-4 mb-md-0">
                  <div class="card-body">
                    <p class="mb-4"><span class="text-primary font-italic me-1">assignment</span> Project Status</p>
                    <p class="mb-1" style="font-size: .77rem;">Web Design</p>
                    <div class="progress rounded" style="height: 5px;">
                      <div class="progress-bar" role="progressbar" style="width: 80%" aria-valuenow="80" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                    <p class="mt-4 mb-1" style="font-size: .77rem;">Website Markup</p>
                    <div class="progress rounded" style="height: 5px;">
                      <div class="progress-bar" role="progressbar" style="width: 72%" aria-valuenow="72" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                    <p class="mt-4 mb-1" style="font-size: .77rem;">One Page</p>
                    <div class="progress rounded" style="height: 5px;">
                      <div class="progress-bar" role="progressbar" style="width: 89%" aria-valuenow="89" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                    <p class="mt-4 mb-1" style="font-size: .77rem;">Mobile Template</p>
                    <div class="progress rounded" style="height: 5px;">
                      <div class="progress-bar" role="progressbar" style="width: 55%" aria-valuenow="55" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                    <p class="mt-4 mb-1" style="font-size: .77rem;">Backend API</p>
                    <div class="progress rounded mb-2" style="height: 5px;">
                      <div class="progress-bar" role="progressbar" style="width: 66%" aria-valuenow="66" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                  </div>
                </div>
              </div>
              <div class="col-md-6">
                <div class="card mb-4 mb-md-0">
                  <div class="card-body">
                    <p class="mb-4"><span class="text-primary font-italic me-1">assignment</span> Project Status</p>
                    <p class="mb-1" style="font-size: .77rem;">Web Design</p>
                    <div class="progress rounded" style="height: 5px;">
                      <div class="progress-bar" role="progressbar" style="width: 80%" aria-valuenow="80" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                    <p class="mt-4 mb-1" style="font-size: .77rem;">Website Markup</p>
                    <div class="progress rounded" style="height: 5px;">
                      <div class="progress-bar" role="progressbar" style="width: 72%" aria-valuenow="72" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                    <p class="mt-4 mb-1" style="font-size: .77rem;">One Page</p>
                    <div class="progress rounded" style="height: 5px;">
                      <div class="progress-bar" role="progressbar" style="width: 89%" aria-valuenow="89" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                    <p class="mt-4 mb-1" style="font-size: .77rem;">Mobile Template</p>
                    <div class="progress rounded" style="height: 5px;">
                      <div class="progress-bar" role="progressbar" style="width: 55%" aria-valuenow="55" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                    <p class="mt-4 mb-1" style="font-size: .77rem;">Backend API</p>
                    <div class="progress rounded mb-2" style="height: 5px;">
                      <div class="progress-bar" role="progressbar" style="width: 66%" aria-valuenow="66" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
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

const route = useRoute()
const userId = computed(() => parseInt(route.params.id as string))

// Mock users data - same as in index.vue
const users = [
  {
    id: 1,
    name: 'Leo Medeiros',
    email: 'admin@admin.com',
    role: 'Admin',
    profession: 'Software Engineer',
    profile_image: '/images/team-13.jpg',
    created_at: '2025/02/10',
    birth_date: '1995-05-06',
    github: 'https://github.com/leopol...',
    linkedin: 'https://www.linkedin.com/...'
  },
  {
    id: 2,
    name: 'Client User',
    email: 'client@client.com',
    role: 'Client',
    profession: 'Designer',
    profile_image: null,
    created_at: '2025/02/10',
    birth_date: '1990-03-15',
    github: null,
    linkedin: null
  },
  {
    id: 3,
    name: 'Emelia Kub',
    email: 'vschmitt@example.com',
    role: 'Client',
    profession: 'Developer',
    profile_image: null,
    created_at: '2025/02/10',
    birth_date: '1988-07-22',
    github: null,
    linkedin: null
  },
  {
    id: 4,
    name: 'Ms. Sibyl White',
    email: 'dickens.dasha@example.org',
    role: 'Client',
    profession: 'Product Manager',
    profile_image: null,
    created_at: '2025/02/10',
    birth_date: '1992-11-30',
    github: null,
    linkedin: null
  },
  {
    id: 5,
    name: 'Prof. Vesta Ullrich DDS',
    email: 'kristofer.jones@example.com',
    role: 'Client',
    profession: 'Consultant',
    profile_image: null,
    created_at: '2025/02/10',
    birth_date: '1985-02-18',
    github: null,
    linkedin: null
  },
  {
    id: 6,
    name: 'Heber Teurel II',
    email: 'winnifred.kutch@example.com',
    role: 'Client',
    profession: 'Analyst',
    profile_image: null,
    created_at: '2025/02/10',
    birth_date: '1993-09-05',
    github: null,
    linkedin: null
  },
  {
    id: 7,
    name: 'Arnoldo Dooley',
    email: 'polisson@example.org',
    role: 'Client',
    profession: 'Engineer',
    profile_image: null,
    created_at: '2025/02/10',
    birth_date: '1991-06-12',
    github: null,
    linkedin: null
  },
  {
    id: 8,
    name: 'Ellen Schopp',
    email: 'flopez@example.com',
    role: 'Client',
    profession: 'Marketing Specialist',
    profile_image: null,
    created_at: '2025/02/10',
    birth_date: '1994-04-28',
    github: null,
    linkedin: null
  },
  {
    id: 9,
    name: 'Ewald King',
    email: 'lkirlin@example.org',
    role: 'Client',
    profession: 'Sales Manager',
    profile_image: null,
    created_at: '2025/02/10',
    birth_date: '1987-12-03',
    github: null,
    linkedin: null
  },
  {
    id: 10,
    name: 'Ms. Mary Lowe',
    email: 'isaac.pouros@example.org',
    role: 'Client',
    profession: 'HR Specialist',
    profile_image: null,
    created_at: '2025/02/10',
    birth_date: '1996-08-19',
    github: null,
    linkedin: null
  }
]

const selectedUser = computed(() => {
  return users.find(u => u.id === userId.value)
})

useHead(() => ({
  title: selectedUser.value ? `${selectedUser.value.name} - Profile` : 'User Profile'
}))
</script>

<style scoped>
.profile-section {
  background-color: #eee;
}

.profile-image-wrapper {
  position: relative;
  width: 150px;
  height: 150px;
  display: inline-block;
}

.profile-image {
  width: 150px;
  height: 150px;
  object-fit: cover;
  z-index: 2;
}

.glowing-circle {
  position: absolute;
  width: 100%;
  height: 100%;
  border-radius: 50%;
  background: conic-gradient(from 0deg, #00d2ff, #3a7bd5, #00d2ff);
  filter: blur(8px);
  z-index: 1;
  animation: spin 4s linear infinite;
}

@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}
</style>
