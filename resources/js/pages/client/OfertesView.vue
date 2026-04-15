<template>
    <AppLayout>
        <Header title="Ofertes i Sol·licituds" :subtitle="`Benvinguda, ${user?.nom}`" />

        <div class="welcome-card">
            <div class="welcome-bg-circle welcome-bg-circle--1" />
            <div class="welcome-bg-circle welcome-bg-circle--2" />

            <div class="welcome-left">
                <span class="welcome-eyebrow">
                    <Sparkles :size="13" />
                    Portal logístic
                </span>
                <h2 class="welcome-title">Hola, {{ user?.nom }}!</h2>
                <p class="welcome-subtitle">
                    Gestiona les teves sol·licituds, consulta l'estat dels teus pressupostos i aprova ofertes des
                    d'aquest tauler unificat.
                </p>
            </div>

            <div class="welcome-actions">
                <button @click="mostrarModal = true" class="cta cta--primary">
                    <PlusCircle :size="15" />
                    Nova sol·licitud d'oferta
                </button>
                <div class="cta-row">
                    <RouterLink to="/client/operacions" class="cta cta--glass">
                        <PackageSearch :size="14" />
                        Seguiment
                    </RouterLink>
                    <RouterLink to="/client/documents" class="cta cta--glass">
                        <FileText :size="14" />
                        Documents
                    </RouterLink>
                </div>
            </div>
        </div>

        <StatsGrid :columns="3" class="mt-4">
            <StatCard label="Sol·licituds Noves" :value="stats.solicitudes_nuevas" compare="pendents de cotitzar"
                color="yellow" :loading="loading">
                <template #icon>
                    <Send :size="18" />
                </template>
            </StatCard>
            <StatCard label="Ofertes Rebudes" :value="stats.ofertas_recibidas" compare="pendents d'aprovació"
                color="blue" :loading="loading">
                <template #icon>
                    <ClipboardList :size="18" />
                </template>
            </StatCard>
            <StatCard label="Ofertes Aprovades" :value="stats.ofertas_aprobadas" compare="llestes per operar"
                color="green" :loading="loading">
                <template #icon>
                    <Check :size="18" />
                </template>
            </StatCard>
        </StatsGrid>

        <div class="card mt-4">
            <div class="card-header">
                <div class="card-title">
                    <ClipboardList :size="15" />
                    Llistat de Sol·licituds i Ofertes
                </div>
            </div>

            <TabView class="custom-tabs">
                <TabPanel header="Pendent de Cotitzar">
                    <DataTable :value="ofertasPendientes" :paginator="true" :rows="5" responsiveLayout="scroll">
                        <Column field="id" header="Ref." sortable>
                            <template #body="slotProps">
                                <strong>SOL-{{ String(slotProps.data.id).padStart(3, '0') }}</strong>
                            </template>
                        </Column>
                        <Column field="ruta" header="Ruta (Origen → Destí)"></Column>
                        <Column field="tipus_transport.nom" header="Transport"></Column>
                        <Column header="Estat">
                            <template #body="slotProps">
                                <span class="status-pill status--purple">
                                    {{ slotProps.data.estado || 'En Revisió' }}
                                </span>
                            </template>
                        </Column>
                    </DataTable>
                </TabPanel>

                <TabPanel header="Ofertes Rebudes">
                    <DataTable :value="ofertasCotizadas" :paginator="true" :rows="5" responsiveLayout="scroll">
                        <Column field="id" header="Ref." sortable>
                            <template #body="slotProps">
                                <strong>OFE-{{ String(slotProps.data.id).padStart(3, '0') }}</strong>
                            </template>
                        </Column>
                        <Column field="ruta" header="Ruta"></Column>
                        <Column field="preu" header="Preu">
                            <template #body="slotProps">
                                <strong>{{ slotProps.data.preu }} €</strong>
                            </template>
                        </Column>
                        <Column header="Accions">
                            <template #body>
                                <div style="display: flex; gap: 0.5rem;">
                                    <button class="status-pill status--green"
                                        style="border:none; cursor:pointer;">Aprovar</button>
                                    <button class="status-pill status--red"
                                        style="border:none; cursor:pointer;">Rebutjar</button>
                                </div>
                            </template>
                        </Column>
                    </DataTable>
                </TabPanel>
            </TabView>
        </div>

        <NovaSolicitudModal v-model:visible="mostrarModal" @solicitudCreada="cargarDatos" />

    </AppLayout>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue';
import {
    Send,
    FileText,
    ClipboardList,
    Check,
    Sparkles,
    PlusCircle,
    PackageSearch,
} from 'lucide-vue-next';
import TabView from 'primevue/tabview';
import TabPanel from 'primevue/tabpanel';
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';

import AppLayout from '@/layout/AppLayout.vue';
import Header from '@/layout/Header.vue';
import { useAuthStore } from '@/stores/authStore';
import StatsGrid from '@/components/dashboard/components/StatsGrid.vue';
import StatCard from '@/components/dashboard/components/StatCard.vue';
import NovaSolicitudModal from '@/components/client/NovaSolicitudModal.vue';
import api from '@/plugins/axios';

const { user } = useAuthStore();

const loading = ref(true);
const mostrarModal = ref(false);

const stats = ref({
    solicitudes_nuevas: 0,
    ofertas_recibidas: 0,
    ofertas_aprobadas: 0,
});

const todasLasSolicitudes = ref([]);

// Filtramos las listas para las pestañas
const ofertasPendientes = computed(() =>
    todasLasSolicitudes.value.filter(s => s.estado === 'Nova' || s.estado === 'En Revisió')
);
const ofertasCotizadas = computed(() =>
    todasLasSolicitudes.value.filter(s => s.estado === 'Cotitzada')
);

const cargarDatos = async () => {
    loading.value = true;
    try {
        // Usamos la ruta REST oficial que ya tienes en api.php
        const { data } = await api.get('/solicitudes');

        if (data.status === 'success') {
            todasLasSolicitudes.value = data.data ?? [];

            // Calculamos las estadísticas leyendo directamente de la lista que nos da Laravel
            // Suponemos que 1 es Pendiente, 2 es Cotizada, 3 es Aceptada. 
            // (Ajusta estos IDs según los que tengas en tu BD real)
            stats.value = {
                solicitudes_nuevas: todasLasSolicitudes.value.filter(s => s.estats_solicitud_id === 1).length,
                ofertas_recibidas: todasLasSolicitudes.value.filter(s => s.estats_solicitud_id === 2).length,
                ofertas_aprobadas: todasLasSolicitudes.value.filter(s => s.estats_solicitud_id === 3).length,
            };
        }
    } catch (e) {
        console.error('Error cargando dades de ofertes', e);
    } finally {
        loading.value = false;
    }
};

onMounted(() => {
    cargarDatos();
});
</script>

<style scoped>
.mt-4 {
    margin-top: 1.5rem;
}

/* ── Card shell (De tu compañera) ──────────────────────────── */
.card {
    background: white;
    border: 1px solid #e5e7eb;
    border-radius: 14px;
    padding: 1.25rem 1.5rem;
    box-shadow: 0 1px 4px rgba(0, 0, 0, 0.03);
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.card-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding-bottom: 0.85rem;
    border-bottom: 1px solid #f3f4f6;
}

.card-title {
    display: flex;
    align-items: center;
    gap: 0.45rem;
    font-size: 0.875rem;
    font-weight: 700;
    color: #111827;
}

/* ── Status pills (De tu compañera) ────────────────────────── */
.status-pill {
    font-size: 0.68rem;
    font-weight: 600;
    padding: 0.3rem 0.6rem;
    border-radius: 20px;
    white-space: nowrap;
    display: inline-block;
}

.status--green {
    background: #f0fdf4;
    color: #166534;
}

.status--purple {
    background: #f5f3ff;
    color: #5b21b6;
}

.status--blue {
    background: #eff6ff;
    color: #1e40af;
}

.status--red {
    background: #fff1f2;
    color: #9f1239;
}

/* ── Welcome card (De tu compañera) ────────────────────────── */
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

/* Ajustes PrimeVue TabView */
:deep(.custom-tabs .p-tabview-nav) {
    background: transparent;
    border-bottom: 1px solid #e5e7eb;
    margin-bottom: 1rem;
}

:deep(.custom-tabs .p-tabview-nav-link) {
    color: #6b7280;
    font-weight: 600;
    padding: 1rem 1.5rem;
    border: none;
    border-bottom: 2px solid transparent;
    background: transparent;
}

:deep(.custom-tabs .p-tabview-nav-link:focus) {
    box-shadow: none;
}

:deep(.custom-tabs .p-highlight .p-tabview-nav-link) {
    color: #1a8a7d;
    border-bottom-color: #1a8a7d;
}
</style>