<template>
    <AppLayout>
        <DashboardHeader />

            <StatsGrid :columns="3">
                <StatCard
                    label="Total Usuaris"
                    :value="stats.totalUsuarios"
                    badge="+12"
                    compare="vs mes anterior"
                    :loading="loading"
                >
                    <template #icon><Users :size="18" /></template>
                </StatCard>
                <StatCard
                    label="Peticions pendents"
                    :value="stats.peticionesPendientes"
                    :loading="loading"
                >
                    <template #icon><UserPlus :size="18" /></template>
                </StatCard>
                <StatCard
                    label="Total Operadors"
                    :value="stats.totalOperadores"
                    :loading="loading"
                >
                    <template #icon><Monitor :size="18" /></template>
                </StatCard>
            </StatsGrid>

            <div v-if="loading" class="bottom-grid">
                <div class="card-skeleton">
                    <div class="skeleton-card-header">
                        <Skeleton width="45%" height="0.95rem" borderRadius="6px" />
                    </div>
                    <div class="skeleton-rows">
                        <div v-for="i in 5" :key="i" class="skeleton-row">
                            <Skeleton shape="circle" width="2.1rem" height="2.1rem" />
                            <div class="skeleton-row-text">
                                <Skeleton width="60%" height="0.85rem" borderRadius="6px" />
                                <Skeleton width="40%" height="0.7rem" borderRadius="6px" class="mt-1" />
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-skeleton">
                    <div class="skeleton-card-header">
                        <Skeleton width="35%" height="0.95rem" borderRadius="6px" />
                    </div>
                    <div class="skeleton-rows">
                        <div v-for="i in 5" :key="i" class="skeleton-row">
                            <div class="skeleton-row-text">
                                <Skeleton width="75%" height="0.85rem" borderRadius="6px" />
                                <Skeleton width="50%" height="0.7rem" borderRadius="6px" class="mt-1" />
                            </div>
                            <Skeleton width="4rem" height="0.75rem" borderRadius="6px" />
                        </div>
                    </div>
                </div>
            </div>

            <div v-else class="bottom-grid">
                <UltimosUsuarios :usuarios="ultimosUsuarios" />
                <ActividadSistema :actividad="monitorCarga" />
        </div>
    </AppLayout>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { Users, UserPlus, Monitor } from 'lucide-vue-next';
import AppLayout from '@/layout/AppLayout.vue';
import api from '@/plugins/axios';
import Skeleton from 'primevue/skeleton';

import DashboardHeader from '@/components/dashboard/components/DashboardHeader.vue';
import StatsGrid from '@/components/dashboard/components/StatsGrid.vue';
import StatCard from '@/components/dashboard/components/StatCard.vue';
import UltimosUsuarios from '@/components/dashboard/components/UltimosUsuarios.vue';
import ActividadSistema from '@/components/dashboard/components/ActividadSistema.vue';

const loading = ref(true);
const stats = ref({ totalUsuarios: 0, peticionesPendientes: 0, totalOperadores: 0 });
const monitorCarga = ref([]);
const ultimosUsuarios = ref([]);

onMounted(async () => {
    loading.value = true;
    try {
        const { data } = await api.get('/dashboard');
        if (data.success) {
            stats.value = data.stats;
            monitorCarga.value = data.monitorCarga;
            ultimosUsuarios.value = data.ultimosUsuarios;
        }
    } catch (error) {
        console.error('Error cargando dashboard', error);
    } finally {
        loading.value = false;
    }
});
</script>

<style scoped>
.bottom-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 1.5rem;
}
.card-skeleton {
    background: white;
    border: 1px solid #e5e7eb;
    border-radius: 12px;
    padding: 1.25rem 1.5rem;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02);
}
.skeleton-card-header {
    margin-bottom: 1rem;
    padding-bottom: 0.75rem;
    border-bottom: 1px solid #f3f4f6;
}
.skeleton-rows {
    display: flex;
    flex-direction: column;
    gap: 0.85rem;
}
.skeleton-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 0.75rem;
}
.skeleton-row-text {
    flex: 1;
    display: flex;
    flex-direction: column;
    gap: 0.3rem;
}
.mt-1 {
    margin-top: 0.25rem;
}
</style>