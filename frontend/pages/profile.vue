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
                  <a href="#">User</a>
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
                    :src="user?.profile_image || '/images/team-13.jpg'" 
                    alt="avatar" 
                    class="rounded-circle img-fluid profile-image position-relative"
                  >
                </div>
                <h5 class="mb-0">{{ user?.name || 'User' }}</h5>
                <p class="text-muted mb-1">{{ userProfession }}</p>
                <p class="text-muted mb-4">{{ userLocation }}</p>
                <div class="d-flex justify-content-center mb-2">
                  <button type="button" class="btn btn-outline-primary">
                    <i class="fas fa-envelope me-1"></i> Message
                  </button>
                  <button type="button" class="btn btn-warning ms-2">
                    <i class="fas fa-edit me-1"></i> Edit
                  </button>
                </div>
              </div>
            </div>
            
            <div class="card mb-4 mb-lg-0">
              <div class="card-body p-0">
                <ul class="list-group list-group-flush rounded-3">
                  <li class="list-group-item d-flex justify-content-between align-items-center p-3">
                    <i class="fab fa-github fa-lg text-body"></i>
                    <a href="https://github.com/Leopol..." target="_blank" class="mb-0 text-decoration-none text-truncate" style="max-width: 200px;">
                      https://github.com/Leopol...
                    </a>
                  </li>
                  <li class="list-group-item d-flex justify-content-between align-items-center p-3">
                    <i class="fab fa-linkedin fa-lg" style="color: #0077b5;"></i>
                    <a href="https://www.linkedin.com/..." target="_blank" class="mb-0 text-decoration-none text-truncate" style="max-width: 200px;">
                      https://www.linkedin.com/...
                    </a>
                  </li>
                  <li class="list-group-item d-flex justify-content-between align-items-center p-3">
                    <i class="fab fa-instagram fa-lg" style="color: #ac2bac;"></i>
                    <p class="mb-0">
                      <a href="#" class="text-muted small">
                        <i class="fas fa-plus-circle me-1"></i>Add your Instagram
                      </a>
                    </p>
                  </li>
                  <li class="list-group-item d-flex justify-content-between align-items-center p-3">
                    <i class="fab fa-facebook-f fa-lg" style="color: #3b5998;"></i>
                    <p class="mb-0">
                      <a href="#" class="text-muted small">
                        <i class="fas fa-plus-circle me-1"></i>Add your Facebook
                      </a>
                    </p>
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
                    <p class="text-muted mb-0">{{ user?.name || 'Leo Medeiros' }}</p>
                  </div>
                </div>
                <hr>
                <div class="row">
                  <div class="col-sm-3">
                    <p class="mb-0 fw-bold"><i class="fas fa-envelope me-2"></i>Email</p>
                  </div>
                  <div class="col-sm-9">
                    <p class="text-muted mb-0">{{ user?.email || 'admin@admin.com' }}</p>
                  </div>
                </div>
                <hr>
                <div class="row">
                  <div class="col-sm-3">
                    <p class="mb-0 fw-bold"><i class="fas fa-birthday-cake me-2"></i>Birth Date</p>
                  </div>
                  <div class="col-sm-9">
                    <p class="text-muted mb-0">1995-05-06</p>
                  </div>
                </div>
                <hr>
                <div class="row">
                  <div class="col-sm-3">
                    <p class="mb-0 fw-bold"><i class="fas fa-briefcase me-2"></i>Profession</p>
                  </div>
                  <div class="col-sm-9">
                    <p class="text-muted mb-0">{{ userProfession }}</p>
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

const { user } = useAuth()

const userProfession = computed(() => {
  return 'Software Engineer'
})

const userLocation = computed(() => {
  return 'Dublin, Ireland'
})

useHead({
  title: 'User Profile'
})
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
