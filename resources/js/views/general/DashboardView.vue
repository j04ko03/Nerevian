<template>
    <!-- Si no es Admin, mostramos una página en blanco -->
    <div v-if="!authStore.isAdmin" class="blank-page"></div>

    <AdminLayout v-else>
        <template #header-title>Dashboard general</template>
        
        <div class="dashboard-wrapper">
            <div v-if="loading" class="loading-state">
                <div class="spinner"></div>
                <p>Cargando datos...</p>
            </div>
            
            <div v-else class="dashboard-grid">
                <!-- 1. STATS (KPIs) -->
                <section class="kpi-section">
                    <div class="kpi-card glass">
                        <div class="icon-wrapper bg-blue">
                            <svg class="kpi-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                                <circle cx="9" cy="7" r="4"></circle>
                                <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                                <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                            </svg>
                        </div>
                        <div class="kpi-info">
                            <p class="kpi-label">Total Usuarios</p>
                            <h3 class="kpi-value">{{ stats?.totalUsuarios || 0 }}</h3>
                        </div>
                    </div>

                    <div class="kpi-card glass">
                        <div class="icon-wrapper bg-orange">
                            <svg class="kpi-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                                <circle cx="8.5" cy="7" r="4"></circle>
                                <line x1="20" y1="8" x2="20" y2="14"></line>
                                <line x1="23" y1="11" x2="17" y2="11"></line>
                            </svg>
                        </div>
                        <div class="kpi-info">
                            <p class="kpi-label">Peticiones Pendientes</p>
                            <h3 class="kpi-value">{{ stats?.peticionesPendientes || 0 }}</h3>
                        </div>
                    </div>

                    <div class="kpi-card glass">
                        <div class="icon-wrapper bg-teal">
                            <svg class="kpi-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path>
                            </svg>
                        </div>
                        <div class="kpi-info">
                            <p class="kpi-label">Total Operadores</p>
                            <h3 class="kpi-value">{{ stats?.totalOperadores || 0 }}</h3>
                        </div>
                    </div>
                </section>

                <!-- 2. DATA TABLES -->
                <section class="data-section">
                    
                    <!-- Monitor de Carga -->
                    <div class="data-card glass">
                        <div class="card-header">
                            <h3>Monitor de Carga</h3>
                            <button class="btn-more">Ver todos</button>
                        </div>
                        <div class="card-body">
                            <ul class="advanced-list">
                                <li v-for="(item, index) in monitorCarga" :key="index" class="list-item">
                                    <div class="item-icon-box">
                                        <svg class="item-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <polyline points="22 12 18 12 15 21 9 3 6 12 2 12"></polyline>
                                        </svg>
                                    </div>
                                    <div class="item-details">
                                        <p class="item-title">{{ item.registro }}</p>
                                        <p class="item-subtitle">{{ item.entidad }} &bull; {{ item.accion }}</p>
                                    </div>
                                    <div class="item-date">{{ item.fecha }}</div>
                                </li>
                                <li v-if="monitorCarga.length === 0" class="empty-state">
                                    No hay modificaciones recientes
                                </li>
                            </ul>
                        </div>
                    </div>

                    <!-- Últimos Usuarios -->
                    <div class="data-card glass">
                        <div class="card-header">
                            <h3>Últimos Usuarios Registrados</h3>
                            <button class="btn-more">Administrar</button>
                        </div>
                        <div class="card-body">
                            <ul class="advanced-list">
                                <li v-for="(usuario, index) in ultimosUsuarios" :key="index" class="list-item">
                                    <div class="item-avatar">{{ usuario.nombre.substring(0, 2).toUpperCase() }}</div>
                                    <div class="item-details">
                                        <p class="item-title">{{ usuario.nombre }}</p>
                                        <span :class="['role-badge', `role-${usuario.rol.toLowerCase()}`]">{{ usuario.rol }}</span>
                                    </div>
                                    <div class="item-date">{{ usuario.fecha }}</div>
                                </li>
                                <li v-if="ultimosUsuarios.length === 0" class="empty-state">
                                    No hay usuarios recientes
                                </li>
                            </ul>
                        </div>
                    </div>

                </section>
            </div>
        </div>
    </AdminLayout>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useAuthStore } from '@/stores/authStore'
import AdminLayout from '@/layouts/AdminLayout.vue'
import api from '@/plugins/axios'

const authStore = useAuthStore()

// Estado reactivo
const loading = ref(true)
const stats = ref(null)
const monitorCarga = ref([])
const ultimosUsuarios = ref([])

onMounted(async () => {
    // Si no es admin, no llamamos a la API y la vista queda en blanco gracias al v-if de arriba.
    if (!authStore.isAdmin) return;

    try {
        const response = await api.get('/dashboard')
        if (response.data.success) {
            stats.value = response.data.stats
            monitorCarga.value = response.data.monitorCarga
            ultimosUsuarios.value = response.data.ultimosUsuarios
        }
    } catch (error) {
        console.error("Error al cargar el dashboard:", error)
    } finally {
        loading.value = false
    }
})
</script>

<style scoped>
/* Contenedor principal con animaciones */
.dashboard-wrapper {
    animation: fadeIn 0.4s ease-out forwards;
    padding-bottom: 2rem;
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
}

/* Base UI - Glassmorphism */
.glass {
    background: rgba(255, 255, 255, 0.7);
    backdrop-filter: blur(12px);
    -webkit-backdrop-filter: blur(12px);
    border: 1px solid rgba(255, 255, 255, 0.3);
    box-shadow: 0 8px 32px rgba(31, 38, 135, 0.04);
    border-radius: 16px;
    transition: transform 0.25s ease, box-shadow 0.25s ease;
}

.glass:hover {
    transform: translateY(-4px);
    box-shadow: 0 12px 40px rgba(31, 38, 135, 0.08);
}

/* Loading State */
.loading-state {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    height: 400px;
    color: #64748b;
    font-weight: 500;
}

.spinner {
    width: 40px;
    height: 40px;
    border: 4px solid #e2e8f0;
    border-top: 4px solid #1A8A7D;
    border-radius: 50%;
    animation: spin 1s linear infinite;
    margin-bottom: 1rem;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

/* Grid General */
.dashboard-grid {
    display: flex;
    flex-direction: column;
    gap: 2rem;
}

/* 1. KPIs Section */
.kpi-section {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
    gap: 1.5rem;
}

.kpi-card {
    display: flex;
    align-items: center;
    padding: 1.5rem;
    gap: 1.25rem;
}

.icon-wrapper {
    width: 60px;
    height: 60px;
    border-radius: 14px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    flex-shrink: 0;
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
}

.bg-blue {
    background: linear-gradient(135deg, #3B82F6 0%, #2563EB 100%);
}

.bg-orange {
    background: linear-gradient(135deg, #F97316 0%, #EA580C 100%);
}

.bg-teal {
    background: linear-gradient(135deg, #14B8A6 0%, #0D9488 100%);
}

.kpi-icon {
    width: 28px;
    height: 28px;
}

.kpi-info {
    display: flex;
    flex-direction: column;
}

.kpi-label {
    font-size: 0.875rem;
    color: #64748b;
    margin: 0 0 0.25rem 0;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.kpi-value {
    font-size: 1.875rem;
    font-weight: 800;
    color: #0F172A;
    margin: 0;
    line-height: 1;
}

/* 2. Data Section */
.data-section {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
    gap: 1.5rem;
}

.data-card {
    display: flex;
    flex-direction: column;
    overflow: hidden;
}

.card-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 1.5rem 1.5rem 1rem 1.5rem;
    border-bottom: 1px solid rgba(0, 0, 0, 0.05);
}

.card-header h3 {
    margin: 0;
    font-size: 1.125rem;
    font-weight: 700;
    color: #1E293B;
}

.btn-more {
    background: none;
    border: none;
    color: #1A8A7D;
    font-size: 0.875rem;
    font-weight: 600;
    cursor: pointer;
    transition: color 0.2s;
}

.btn-more:hover {
    color: #115E59;
}

.card-body {
    padding: 0;
}

/* Lists */
.advanced-list {
    list-style: none;
    padding: 0;
    margin: 0;
}

.list-item {
    display: flex;
    align-items: center;
    padding: 1rem 1.5rem;
    border-bottom: 1px solid rgba(0,0,0,0.03);
    transition: background-color 0.2s;
}

.list-item:hover {
    background-color: rgba(255,255,255,0.6);
}

.list-item:last-child {
    border-bottom: none;
}

.item-icon-box {
    width: 40px;
    height: 40px;
    background-color: #F1F5F9;
    color: #64748b;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-right: 1rem;
    flex-shrink: 0;
}

.item-avatar {
    width: 40px;
    height: 40px;
    background: linear-gradient(135deg, #1A8A7D 0%, #062E2B 100%);
    color: white;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 700;
    font-size: 0.9rem;
    margin-right: 1rem;
    flex-shrink: 0;
}

.item-icon {
    width: 20px;
    height: 20px;
}

.item-details {
    flex: 1;
    display: flex;
    flex-direction: column;
}

.item-title {
    margin: 0 0 0.25rem 0;
    font-size: 0.95rem;
    font-weight: 600;
    color: #1E293B;
}

.item-subtitle {
    margin: 0;
    font-size: 0.8rem;
    color: #64748b;
}

.item-date {
    font-size: 0.8rem;
    color: #94A3B8;
    font-weight: 500;
    white-space: nowrap;
    margin-left: 1rem;
}

/* Badges */
.role-badge {
    display: inline-block;
    padding: 0.2rem 0.6rem;
    border-radius: 6px;
    font-size: 0.7rem;
    font-weight: 700;
    text-transform: uppercase;
    width: fit-content;
}

.role-admin {
    background-color: #FEF2F2;
    color: #EF4444;
}

.role-operador {
    background-color: #F0FDF4;
    color: #22C55E;
}

.role-usuario {
    background-color: #EFF6FF;
    color: #3B82F6;
}

.empty-state {
    padding: 2rem;
    text-align: center;
    color: #94a3b8;
    font-size: 0.9rem;
    font-weight: 500;
}

.blank-page {
    width: 100vw;
    height: 100vh;
    background-color: #F9FAFB;
}

/* Responsiveness */
@media (max-width: 768px) {
    .data-section {
        grid-template-columns: 1fr;
    }
}
</style>
