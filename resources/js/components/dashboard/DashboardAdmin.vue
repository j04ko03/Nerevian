<template>
    <AppLayout>
        <Header
            title="Dashboard Administradora"
            :subtitle="`Benvinguda, ${user?.nom}`"
        />

        <!-- ── Welcome card ──────────────────────── -->
        <div class="welcome-card">
            <div class="welcome-bg-circle welcome-bg-circle--1" />
            <div class="welcome-bg-circle welcome-bg-circle--2" />

            <div class="welcome-left">
                <span class="welcome-eyebrow">
                    <ShieldCheck :size="13" />
                    Panell d'administració
                </span>
                <h2 class="welcome-title">Benvinguda, {{ user?.nom }}!</h2>
                <p class="welcome-subtitle">
                    Supervisa els usuaris, gestiona les peticions de registre
                    i mantén el control total del sistema des d'aquí.
                </p>
            </div>

            <div class="welcome-actions">
                <RouterLink to="/admin/usuaris" class="cta cta--primary">
                    <Users :size="15" />
                    Gestionar usuaris
                </RouterLink>
                <div class="cta-row">
                    <RouterLink to="/admin/peticions" class="cta cta--glass">
                        <ClipboardList :size="14" />
                        Peticions
                    </RouterLink>
                    <RouterLink to="/admin/configuracio" class="cta cta--glass">
                        <Settings :size="14" />
                        Configuració
                    </RouterLink>
                </div>
            </div>
        </div>

        <!-- ── Stat cards ────────────────────────── -->
        <StatsGrid :columns="3">
            <StatCard
                label="Total Usuaris"
                :value="stats.totalUsuarios"
                compare="registrats al sistema"
                :loading="loading"
            >
                <template #icon><Users :size="18" /></template>
            </StatCard>
            <StatCard
                label="Peticions pendents"
                :value="stats.peticionesPendientes"
                compare="per revisar"
                color="yellow"
                :loading="loading"
            >
                <template #icon><UserPlus :size="18" /></template>
            </StatCard>
            <StatCard
                label="Total Operadors"
                :value="stats.totalOperadores"
                compare="actius"
                color="green"
                :loading="loading"
            >
                <template #icon><Monitor :size="18" /></template>
            </StatCard>
        </StatsGrid>

        <!-- ── Skeleton ──────────────────────────── -->
        <div v-if="loading" class="dashboard">
            <div class="card-skeleton">
                <div class="skeleton-card-header">
                    <Skeleton width="45%" height="0.95rem" borderRadius="6px" />
                </div>
                <div class="skeleton-timeline">
                    <div v-for="i in 5" :key="i" class="skeleton-timeline-item">
                        <div class="skeleton-timeline-left">
                            <Skeleton shape="circle" width="2.2rem" height="2.2rem" />
                            <div v-if="i < 5" class="skeleton-line" />
                        </div>
                        <div class="skeleton-timeline-content">
                            <Skeleton width="55%" height="0.85rem" borderRadius="6px" />
                            <Skeleton width="35%" height="0.7rem" borderRadius="6px" class="mt-1" />
                        </div>
                        <Skeleton width="3.5rem" height="1.5rem" borderRadius="20px" />
                    </div>
                </div>
            </div>
            <div class="card-skeleton">
                <div class="skeleton-card-header">
                    <Skeleton width="38%" height="0.95rem" borderRadius="6px" />
                </div>
                <div class="skeleton-feed">
                    <div v-for="i in 5" :key="i" class="skeleton-feed-item">
                        <div class="skeleton-feed-left">
                            <Skeleton width="2rem" height="2rem" borderRadius="8px" />
                            <div v-if="i < 5" class="skeleton-feed-line" />
                        </div>
                        <div class="skeleton-feed-content">
                            <div class="skeleton-feed-top">
                                <Skeleton width="50%" height="0.8rem" borderRadius="6px" />
                                <Skeleton width="3rem" height="1.3rem" borderRadius="20px" />
                            </div>
                            <Skeleton width="70%" height="0.7rem" borderRadius="6px" class="mt-1" />
                            <Skeleton width="30%" height="0.65rem" borderRadius="6px" class="mt-1" />
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- ── Content ───────────────────────────── -->
        <div v-else class="dashboard">
            <UltimosUsuarios :usuarios="ultimosUsuarios" />
            <ActividadSistema :actividad="monitorCarga" />
        </div>
    </AppLayout>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { Users, UserPlus, Monitor, ShieldCheck, ClipboardList, Settings } from 'lucide-vue-next';
import { RouterLink } from 'vue-router';
import Skeleton from 'primevue/skeleton';
import AppLayout from '@/layout/AppLayout.vue';
import Header from '@/layout/Header.vue';
import { useAuthStore } from '@/stores/authStore';
import StatsGrid from '@/components/dashboard/components/StatsGrid.vue';
import StatCard from '@/components/dashboard/components/StatCard.vue';
import UltimosUsuarios from '@/components/dashboard/components/UltimosUsuarios.vue';
import ActividadSistema from '@/components/dashboard/components/ActividadSistema.vue';
import api from '@/plugins/axios';

const { user } = useAuthStore();

const loading = ref(true);
const stats = ref({ totalUsuarios: 0, peticionesPendientes: 0, totalOperadores: 0 });
const monitorCarga = ref([]);
const ultimosUsuarios = ref([]);

onMounted(async () => {
    try {
        const { data } = await api.get('/dashboard');
        if (data.success) {
            stats.value           = data.stats;
            monitorCarga.value    = data.monitorCarga;
            ultimosUsuarios.value = data.ultimosUsuarios;
        }
    } catch (e) {
        console.error('Error cargando dashboard admin', e);
    } finally {
        loading.value = false;
    }
});
</script>

<style scoped>
/* ── Main grid ───────────────────────────── */
.dashboard {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 1.5rem;
}

/* ── Skeleton ────────────────────────────── */
.card-skeleton {
    background: white;
    border: 1px solid #e5e7eb;
    border-radius: 12px;
    padding: 1.25rem 1.5rem;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02);
}

.skeleton-card-header {
    margin-bottom: 1.25rem;
    padding-bottom: 0.75rem;
    border-bottom: 1px solid #f3f4f6;
}

/* Timeline skeleton */
.skeleton-timeline { display: flex; flex-direction: column; }

.skeleton-timeline-item {
    display: flex;
    align-items: flex-start;
    gap: 0.75rem;
    padding: 0.5rem 0;
}

.skeleton-timeline-left {
    display: flex;
    flex-direction: column;
    align-items: center;
    flex-shrink: 0;
}

.skeleton-line {
    width: 2px;
    flex: 1;
    min-height: 1.2rem;
    background: #f3f4f6;
    border-radius: 2px;
    margin-top: 4px;
}

.skeleton-timeline-content {
    flex: 1;
    display: flex;
    flex-direction: column;
    gap: 0.35rem;
    padding-top: 0.35rem;
}

/* Feed skeleton */
.skeleton-feed { display: flex; flex-direction: column; }

.skeleton-feed-item {
    display: flex;
    align-items: flex-start;
    gap: 0.75rem;
    padding: 0.5rem 0;
}

.skeleton-feed-left {
    display: flex;
    flex-direction: column;
    align-items: center;
    flex-shrink: 0;
}

.skeleton-feed-line {
    width: 2px;
    flex: 1;
    min-height: 1.2rem;
    background: #f3f4f6;
    border-radius: 2px;
    margin-top: 4px;
}

.skeleton-feed-content {
    flex: 1;
    display: flex;
    flex-direction: column;
    gap: 0.3rem;
    padding-top: 0.25rem;
}

.skeleton-feed-top {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 0.5rem;
}

.mt-1 { margin-top: 0.25rem; }

/* ── Welcome card ────────────────────────── */
.welcome-card {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 2rem;
    background: linear-gradient(135deg, #2a1a08 0%, #6b4c24 45%, #8a6e3e 100%);
    border-radius: 16px;
    padding: 1.75rem 2rem;
    margin-bottom: 1rem;
    box-shadow: 0 8px 28px -6px rgba(42, 26, 8, 0.4);
    position: relative;
    overflow: hidden;
}

.welcome-bg-circle {
    position: absolute;
    border-radius: 50%;
    pointer-events: none;
}
.welcome-bg-circle--1 {
    width: 280px;
    height: 280px;
    background: radial-gradient(circle, rgba(201, 169, 110, 0.18) 0%, transparent 70%);
    top: -80px;
    right: 200px;
}
.welcome-bg-circle--2 {
    width: 200px;
    height: 200px;
    background: radial-gradient(circle, rgba(255, 255, 255, 0.06) 0%, transparent 70%);
    bottom: -60px;
    right: 60px;
}

.welcome-left {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
    position: relative;
    flex: 1;
    min-width: 0;
}

.welcome-eyebrow {
    display: flex;
    align-items: center;
    gap: 0.35rem;
    font-size: 0.72rem;
    font-weight: 600;
    color: #c9a96e;
    text-transform: uppercase;
    letter-spacing: 0.6px;
}

.welcome-title {
    font-size: 1.6rem;
    font-weight: 800;
    color: white;
    margin: 0;
    letter-spacing: -0.3px;
    line-height: 1.15;
}

.welcome-subtitle {
    font-size: 0.83rem;
    color: rgba(255, 255, 255, 0.5);
    margin: 0;
    line-height: 1.55;
    max-width: 420px;
}

.welcome-actions {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
    position: relative;
    flex-shrink: 0;
    min-width: 220px;
}

.cta-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 0.5rem;
}

.cta {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.45rem;
    padding: 0.62rem 1rem;
    border-radius: 9px;
    font-family: inherit;
    font-size: 0.82rem;
    font-weight: 600;
    text-decoration: none;
    cursor: pointer;
    border: 1px solid transparent;
    transition: all 0.2s ease;
    white-space: nowrap;
}

.cta--primary {
    background: #c9a96e;
    color: #2a1a08;
    box-shadow: 0 4px 12px rgba(201, 169, 110, 0.35);
}
.cta--primary:hover {
    background: #d4b87e;
    transform: translateY(-1px);
    box-shadow: 0 6px 16px rgba(201, 169, 110, 0.45);
}

.cta--glass {
    background: rgba(255, 255, 255, 0.1);
    color: rgba(255, 255, 255, 0.85);
    border-color: rgba(255, 255, 255, 0.18);
    backdrop-filter: blur(6px);
    font-size: 0.78rem;
}
.cta--glass:hover {
    background: rgba(255, 255, 255, 0.18);
    border-color: rgba(255, 255, 255, 0.32);
    color: white;
}
</style>