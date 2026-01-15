<template>
  <NuxtLayout name="admin">
    <div class="dashboard-container">
      <div v-if="user">
        <!-- Admin Dashboard -->
        <div v-if="user.role === 'admin'">
          <div class="mb-4">
            <h1 class="dashboard-title">Admin Dashboard</h1>
          </div>

          <!-- Stat Cards -->
          <div class="row g-4 mb-4">
            <div class="col-md-6">
              <div class="stat-card stat-card-primary">
                <div class="stat-card-header">
                  <b>Total Users</b>
                </div>
                <div class="stat-card-body">
                  <h5 class="stat-card-value">53</h5>
                  <p class="stat-card-text">Total registered users on the platform</p>
                </div>
              </div>
            </div>
            <div class="col-md-6">
              <div class="stat-card stat-card-success">
                <div class="stat-card-header">
                  <b>Total Admins</b>
                </div>
                <div class="stat-card-body">
                  <h5 class="stat-card-value">1</h5>
                  <p class="stat-card-text">Total active admins</p>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Client Dashboard -->
        <div v-else>
          <div class="modern-card">
            <h2 class="mb-4">Client Dashboard</h2>
            <div class="metric-card">
              <h5 class="mb-3">Welcome, {{ user.name }}!</h5>
              <p class="text-muted mb-0">This is your client dashboard</p>
            </div>
          </div>
        </div>
      </div>
      <div v-else>
        <p>Loading...</p>
      </div>
    </div>
  </NuxtLayout>
</template>

<script setup lang="ts">
definePageMeta({
  layout: false,
  middleware: 'auth'
})

const { user } = useAuth()

useHead(() => ({
  title: user.value?.role === 'admin' ? 'Admin Dashboard' : 'Client Dashboard'
}))
</script>

<style scoped>
.dashboard-container {
  min-height: 100vh;
  background: var(--bg-primary);
  padding: 2rem;
}

.dashboard-title {
  font-size: 2rem;
  font-weight: 700;
  color: var(--text-primary);
  margin-bottom: 0.5rem;
}

.stat-card {
  border-radius: 8px;
  overflow: hidden;
  box-shadow: var(--shadow-md);
  transition: transform 0.2s ease;
}

.stat-card:hover {
  transform: translateY(-2px);
  box-shadow: var(--shadow-lg);
}

.stat-card-primary {
  background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
  color: white;
}

.stat-card-success {
  background: linear-gradient(135deg, #10b981 0%, #059669 100%);
  color: white;
}

.stat-card-header {
  padding: 1rem 1.5rem;
  font-size: 1.125rem;
  border-bottom: 1px solid rgba(255, 255, 255, 0.2);
}

.stat-card-body {
  padding: 1.5rem;
}

.stat-card-value {
  font-size: 2.5rem;
  font-weight: 700;
  margin-bottom: 0.5rem;
}

.stat-card-text {
  font-size: 1rem;
  opacity: 0.9;
  margin-bottom: 0;
}
</style>
