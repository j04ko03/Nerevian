<template>
  <div class="dashboard">
    <header class="dashboard-header">
      <div class="header-left">
        <div class="logo">N</div>
        <h1>Nerevian</h1>
      </div>
      <div class="header-right">
        <span class="user-info" v-if="authStore.user">
          {{ authStore.user.nom }} {{ authStore.user.cognoms }} — <span class="role-badge">{{ authStore.user.rol }}</span>
        </span>
        <button @click="handleLogout" class="btn-logout">Cerrar Sesión</button>
      </div>
    </header>
    <main class="dashboard-content">
      <h2>Bienvenido al Dashboard</h2>
      <p>Has iniciado sesión correctamente.</p>
    </main>
  </div>
</template>

<script setup>
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/authStore'

const router    = useRouter()
const authStore = useAuthStore()

async function handleLogout() {
  await authStore.logout()
  router.push({ name: 'login' })
}
</script>

<style scoped>
.dashboard {
  min-height: 100vh;
  background: #F9FAFB;
}

.dashboard-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 1rem 2rem;
  background: white;
  border-bottom: 1px solid #E5E7EB;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
}

.header-left {
  display: flex;
  align-items: center;
  gap: 0.75rem;
}

.logo {
  width: 36px;
  height: 36px;
  background: #1A8A7D;
  color: white;
  border-radius: 8px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-weight: 700;
  font-size: 1.1rem;
}

.header-left h1 {
  font-size: 1.25rem;
  font-weight: 700;
  color: #1A2B2A;
  margin: 0;
}

.header-right {
  display: flex;
  align-items: center;
  gap: 1rem;
}

.user-info {
  font-size: 0.875rem;
  color: #6B7280;
}

.role-badge {
  background: #D1FAE5;
  color: #065F46;
  padding: 0.15rem 0.5rem;
  border-radius: 4px;
  font-size: 0.75rem;
  font-weight: 600;
  text-transform: uppercase;
}

.btn-logout {
  background: transparent;
  color: #EF4444;
  border: 1.5px solid #EF4444;
  border-radius: 8px;
  padding: 0.4rem 1rem;
  font-size: 0.875rem;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.2s;
}

.btn-logout:hover {
  background: #EF4444;
  color: white;
}

.dashboard-content {
  padding: 3rem 2rem;
  max-width: 800px;
  margin: 0 auto;
}

.dashboard-content h2 {
  font-size: 1.5rem;
  font-weight: 700;
  color: #1A2B2A;
  margin: 0 0 0.5rem;
}

.dashboard-content p {
  color: #6B7280;
}
</style>
