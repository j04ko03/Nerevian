<template>
    <AppLayout>
        <Header
            title="Dashboard Administradora"
            :subtitle="`Benvinguda, ${user?.nom}`"
        />

        <!-- ── Stat cards ────────────────────────── -->
        <StatsGrid :columns="3">
            <StatCard
                label="Total Usuaris"
                :value="displayStats.totalUsuarios"
                compare="registrats al sistema"
                :loading="loading"
            >
                <template #icon><Users :size="18" /></template>
            </StatCard>
            <StatCard
                label="Peticions pendents"
                :value="displayStats.peticionesPendientes"
                compare="per revisar"
                color="yellow"
                :loading="loading"
            >
                <template #icon><UserPlus :size="18" /></template>
            </StatCard>
            <StatCard
                label="Total Operadors"
                :value="displayStats.totalOperadores"
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
import { Users, UserPlus, Monitor } from 'lucide-vue-next';
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

// Count-up animation
const displayStats = ref({ totalUsuarios: 0, peticionesPendientes: 0, totalOperadores: 0 });

function countUp(key, target, duration = 900) {
    const start = Date.now();
    const tick = () => {
        const elapsed = Date.now() - start;
        const progress = Math.min(elapsed / duration, 1);
        const ease = 1 - Math.pow(1 - progress, 3);
        displayStats.value[key] = Math.round(ease * target);
        if (progress < 1) requestAnimationFrame(tick);
    };
    requestAnimationFrame(tick);
}

onMounted(async () => {
    try {
        const { data } = await api.get('/dashboard');
        if (data.success) {
            stats.value         = data.stats;
            monitorCarga.value  = data.monitorCarga;
            ultimosUsuarios.value = data.ultimosUsuarios;
            countUp('totalUsuarios',        data.stats.totalUsuarios);
            countUp('peticionesPendientes', data.stats.peticionesPendientes, 700);
            countUp('totalOperadores',      data.stats.totalOperadores, 1100);
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
</style>