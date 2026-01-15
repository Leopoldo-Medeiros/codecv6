<template>
  <div class="dark-theme-wrapper">
    <div class="container-fluid p-0 flex-shrink-0">
      <div class="row flex-nowrap">
        <!-- SIDEBAR -->
        <div class="col-auto col-md-3 col-xl-2 pr-sm-2 px-0 pl-0 dark-sidebar">
          <div id="sidebar"
            class="dark-sidebar d-flex flex-column align-items-center align-items-sm-start px-3 pt-4 vh-100">

          <!-- USER PROFILE -->
          <div v-if="user" class="user-profile-card my-4 w-100">
            <div class="profile-header text-center">
              <div class="profile-image-wrapper mb-3 mx-auto">
                <div class="profile-glow"></div>
                <img :src="user.profile_image || '/images/team-13.jpg'" :alt="user.name" class="profile-image rounded-circle">
              </div>
              <h6 class="profile-name mb-1">{{ user.name }}</h6>
              <div class="profile-role mb-3">
                <span v-if="user.role === 'admin'" class="role-badge admin-badge">
                  <i class="fas fa-crown me-1"></i> Admin
                </span>
                <span v-else class="role-badge member-badge">
                  <i class="fas fa-user me-1"></i> Member
                </span>
              </div>
            </div>
            <div class="profile-actions d-flex justify-content-center gap-2">
              <NuxtLink to="/profile" class="action-btn" title="View Profile">
                <i class="fas fa-user"></i>
              </NuxtLink>
              <a href="#" class="action-btn" title="Settings">
                <i class="fas fa-cog"></i>
              </a>
              <a href="#" @click.prevent="handleLogout" class="action-btn logout-btn" title="Logout">
                <i class="fas fa-sign-out-alt"></i>
              </a>
            </div>
          </div>
          <hr class="w-100 my-2 opacity-25">

          <!-- NAVIGATION -->
          <ul class="nav flex-column w-100">

            <!-- Admin Menu -->
            <template v-if="user?.role === 'admin'">
              <li class="nav-item">
                <NuxtLink to="/users" class="nav-link large-font">
                  <i class="fas fa-users me-2"></i> Clients
                </NuxtLink>
              </li>
              <li class="nav-item">
                <NuxtLink to="/courses" class="nav-link large-font">
                  <i class="fas fa-book me-2"></i> Courses
                </NuxtLink>
              </li>
              <li class="nav-item">
                <a href="#" class="nav-link large-font">
                  <i class="fas fa-map-signs me-2"></i> Paths
                </a>
              </li>
              <li class="nav-item">
                <a href="#" class="nav-link large-font">
                  <i class="fas fa-tasks me-2"></i> Steps
                </a>
              </li>
              <li class="nav-item">
                <NuxtLink to="/dashboard" class="nav-link large-font">
                  <i class="fas fa-tachometer-alt me-2"></i> Dashboard
                </NuxtLink>
              </li>
            </template>

            <!-- Client Menu -->
            <template v-else>
              <li class="nav-item">
                <a href="#" class="nav-link py-2 px-4 rounded hover:bg-blue-500 hover:text-white flex items-center">
                  <i class="fas fa-graduation-cap me-2"></i> My Courses
                </a>
              </li>
              <li class="nav-item">
                <a href="#" class="nav-link py-2 px-4 rounded hover:bg-blue-500 hover:text-white flex items-center">
                  <i class="fas fa-route me-2"></i> My Paths
                </a>
              </li>
              <li class="nav-item">
                <a href="#" class="nav-link py-2 px-4 rounded hover:bg-blue-500 hover:text-white flex items-center">
                  <i class="fas fa-id-card me-2"></i> My CV
                </a>
              </li>
              <li class="nav-item">
                <a href="#" class="nav-link py-2 px-4 rounded hover:bg-blue-500 hover:text-white flex items-center">
                  <i class="fas fa-folder-open me-2"></i> My Files
                </a>
              </li>
            </template>
             <li class="nav-item mt-3">
                <a href="#" @click.prevent="handleLogout" class="nav-link large-font">
                  <i class="fas fa-sign-out-alt me-2"></i> Logout
                </a>
              </li>
              <li class="nav-item mt-2">
                 <a href="#" class="nav-link large-font position-relative">
                    <i class="fas fa-bell"></i>
                    <span class="position-absolute top-0 start-100 translate-middle p-1 bg-danger border border-light rounded-circle">
                        <span class="visually-hidden">New alerts</span>
                    </span>
                 </a>
              </li>
          </ul>
        </div>
      </div>

      <!-- Main Content -->
      <div class="col py-3">
        <!-- Navbar -->
        <nav class="navbar dark-navbar topbar border-bottom shadow-sm py-2 mb-4 rounded">
          <div class="container-fluid">
             <button type="button" id="sidebarCollapse" class="btn d-md-none">
                <i class="fas fa-bars"></i>
            </button>

            <div class="navbar-brand py-0 ms-md-5 mx-auto mx-md-0">
               <NuxtLink to="/dashboard" class="smll-logo">
                  <img src="/images/codecv.png" alt="Logo" class="navbar-logo" style="max-height: 100px;">
               </NuxtLink>
            </div>

            <div class="d-flex align-items-center gap-3">
              <!-- Theme Toggle -->
              <button @click="toggleTheme" class="btn btn-sm theme-toggle-btn" title="Toggle theme">
                <i :class="isDark ? 'fas fa-sun' : 'fas fa-moon'"></i>
              </button>

              <!-- User Dropdown -->
              <div v-if="user" class="dropdown">
                 <a href="#" class="d-block link-dark text-decoration-none dropdown-toggle" id="dropdownUser2" data-bs-toggle="dropdown" aria-expanded="false">
                     <img :src="user.profile_image || '/images/team-13.jpg'" alt="avatar" width="32" height="32" class="rounded-circle">
                 </a>
                 <ul class="dropdown-menu dropdown-menu-end text-small shadow" aria-labelledby="dropdownUser2">
                     <li><NuxtLink class="dropdown-item" to="/profile"><i class="fas fa-user me-2"></i> Profile</NuxtLink></li>
                     <li><hr class="dropdown-divider"></li>
                     <li><a class="dropdown-item text-danger" href="#" @click.prevent="handleLogout"><i class="fas fa-sign-out-alt me-2"></i> Sign out</a></li>
                 </ul>
              </div>
            </div>
          </div>
        </nav>

        <slot />
        
        <!-- Footer -->
        <footer class="mt-auto py-3 bg-body-tertiary m-0 text-center">
            <span class="text-body-secondary"> Copyright © {{ new Date().getFullYear() }} CODECV</span>
        </footer>
      </div>
    </div>
  </div>
  </div>
</template>

<script setup lang="ts">
const { user, logout } = useAuth();
const isDark = ref(true); // Default to dark theme

const handleLogout = () => {
  logout();
};

const toggleTheme = () => {
  isDark.value = !isDark.value;
  
  // Toggle dark theme class on body
  if (process.client) {
    if (isDark.value) {
      document.body.classList.add('dark-theme');
      localStorage.setItem('theme', 'dark');
    } else {
      document.body.classList.remove('dark-theme');
      localStorage.setItem('theme', 'light');
    }
  }
};

onMounted(() => {
  // Check local storage for saved theme preference
  if (process.client) {
    const savedTheme = localStorage.getItem('theme');
    if (savedTheme === 'light') {
      isDark.value = false;
      document.body.classList.remove('dark-theme');
    } else {
      isDark.value = true;
      document.body.classList.add('dark-theme');
    }
  }
});
</script>

<style scoped>
.custom-menu-bg {
  background-color: #a8cbf7; /* Light blue matching screenshot */
}
.custom-sidebar-width {
  min-height: 100vh;
}
.nav-link {
    color: #333; /* Dark text */
    font-weight: 500;
}

/* Dark Theme Styles */
.dark-theme-wrapper {
  background: var(--bg-primary);
  min-height: 100vh;
}

.dark-sidebar {
  background: var(--bg-secondary) !important;
  border-right: 1px solid var(--border-color);
}

.nav-link {
  color: var(--text-secondary) !important;
  transition: all 0.2s ease;
  border-radius: 8px;
  margin: 0.25rem 0;
  padding: 0.75rem 1rem !important;
}

.nav-link:hover {
  background: var(--bg-hover) !important;
  color: var(--text-primary) !important;
}

.nav-link.router-link-active {
  background: var(--gradient-blue) !important;
  color: white !important;
}

.profile-image {
  width: 80px;
  height: 80px;
  border-radius: 50%;
  object-fit: cover;
  border: 3px solid var(--border-color);
}

.user-profile h6 {
  color: var(--text-primary);
}

.user-profile .text-muted {
  color: var(--text-muted) !important;
}

.large-font {
  font-size: 0.9rem;
}

.dark-navbar {
  background: var(--bg-card) !important;
  border-color: var(--border-color) !important;
}

.dark-footer {
  background: var(--bg-card);
  border-top: 1px solid var(--border-color);
  color: var(--text-muted);
}

.modern-btn-primary {
  background: var(--gradient-blue);
  color: white;
  border: none;
  position: relative;
}

.modern-btn-primary .badge {
  position: absolute;
  top: -5px;
  right: -5px;
}

.theme-toggle-btn {
  background: var(--bg-tertiary);
  color: var(--text-primary);
  border: 1px solid var(--border-color);
  border-radius: 8px;
  padding: 0.5rem 0.75rem;
  transition: all 0.2s ease;
}

.theme-toggle-btn:hover {
  background: var(--bg-hover);
  transform: scale(1.05);
}

.theme-toggle-btn i {
  font-size: 1.1rem;
}

/* Modern Profile Card */
.user-profile-card {
  background: linear-gradient(135deg, rgba(59, 130, 246, 0.1) 0%, rgba(139, 92, 246, 0.1) 100%);
  border-radius: 16px;
  padding: 1.5rem 1rem;
  border: 1px solid rgba(59, 130, 246, 0.2);
}

.profile-header {
  margin-bottom: 1rem;
}

.profile-image-wrapper {
  position: relative;
  width: 90px;
  height: 90px;
  display: inline-block;
}

.profile-glow {
  position: absolute;
  width: 100%;
  height: 100%;
  border-radius: 50%;
  background: conic-gradient(from 0deg, #3b82f6, #8b5cf6, #3b82f6);
  filter: blur(8px);
  z-index: 0;
  animation: spin 4s linear infinite;
}

@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}

.profile-image {
  width: 90px;
  height: 90px;
  object-fit: cover;
  position: relative;
  z-index: 1;
  border: 3px solid var(--bg-secondary);
}

.profile-name {
  color: var(--text-primary);
  font-weight: 700;
  font-size: 1.1rem;
  margin-bottom: 0.5rem;
}

.profile-role {
  margin-bottom: 1rem;
}

.role-badge {
  display: inline-flex;
  align-items: center;
  padding: 0.375rem 0.875rem;
  border-radius: 20px;
  font-size: 0.75rem;
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: 0.05em;
}

.admin-badge {
  background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
  color: white;
  box-shadow: 0 4px 12px rgba(245, 158, 11, 0.3);
}

.member-badge {
  background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
  color: white;
  box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
}

.profile-actions {
  padding-top: 1rem;
  border-top: 1px solid rgba(255, 255, 255, 0.1);
}

.action-btn {
  width: 40px;
  height: 40px;
  display: flex;
  align-items: center;
  justify-content: center;
  background: var(--bg-tertiary);
  border: 1px solid var(--border-color);
  border-radius: 10px;
  color: var(--text-primary);
  text-decoration: none;
  transition: all 0.2s ease;
}

.action-btn:hover {
  background: var(--bg-hover);
  transform: translateY(-2px);
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
}

.logout-btn:hover {
  background: rgba(239, 68, 68, 0.1);
  border-color: #ef4444;
  color: #ef4444;
}

/* Navigation Icons */
.nav-link i {
  font-size: 1.1rem;
  margin-right: 0.75rem;
  width: 20px;
  text-align: center;
}
</style>
