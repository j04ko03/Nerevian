<template>
    <AppLayout>
        <Header
            title="Ofertes i Sol·licituds"
            subtitle="Gestiona les teves sol·licituds i consulta l'estat dels pressupostos."
        />

        <div class="action-bar mt-4">
            <RouterLink to="/client/operacions" class="action-tile">
                <div class="action-tile__icon">
                    <PackageSearch :size="22" />
                </div>
                <div class="action-tile__text">
                    <span class="action-tile__label">Seguiment</span>
                    <span class="action-tile__desc"
                        >Consulta l'estat dels teus enviaments</span
                    >
                </div>
            </RouterLink>
            <button
                @click="mostrarModal = true"
                class="action-tile action-tile--primary"
            >
                <div class="action-tile__icon">
                    <PlusCircle :size="22" />
                </div>
                <div class="action-tile__text">
                    <span class="action-tile__label">Nova Sol·licitud</span>
                    <span class="action-tile__desc"
                        >Demana una nova cotització de transport</span
                    >
                </div>
            </button>
            <RouterLink to="/client/documents" class="action-tile">
                <div class="action-tile__icon">
                    <FileText :size="22" />
                </div>
                <div class="action-tile__text">
                    <span class="action-tile__label">Documents</span>
                    <span class="action-tile__desc"
                        >Accedeix a tota la documentació</span
                    >
                </div>
            </RouterLink>
        </div>

        <div class="card mt-4">
            <div class="card-header">
                <div class="card-title">
                    <ClipboardList :size="15" />
                    Llistat de Sol·licituds i Ofertes
                </div>
            </div>

            <TabView class="custom-tabs">
                <TabPanel header="Pendent de Cotitzar">
                    <DataTable
                        :value="ofertasPendientes"
                        :paginator="true"
                        :rows="5"
                        responsiveLayout="scroll"
                    >
                        <Column field="id" header="Ref." sortable>
                            <template #body="slotProps">
                                <strong
                                    >SOL-{{
                                        String(slotProps.data.id).padStart(
                                            3,
                                            '0',
                                        )
                                    }}</strong
                                >
                            </template>
                        </Column>
                        <Column
                            field="ruta"
                            header="Ruta (Origen → Destí)"
                        ></Column>
                        <Column
                            field="tipus_transport.nom"
                            header="Transport"
                        ></Column>
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
                    <DataTable
                        :value="ofertasCotizadas"
                        :paginator="true"
                        :rows="5"
                        responsiveLayout="scroll"
                    >
                        <Column field="id" header="Ref." sortable>
                            <template #body="slotProps">
                                <strong
                                    >OFE-{{
                                        String(slotProps.data.id).padStart(
                                            3,
                                            '0',
                                        )
                                    }}</strong
                                >
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
                                <div style="display: flex; gap: 0.5rem">
                                    <button
                                        class="status-pill status--green"
                                        style="border: none; cursor: pointer"
                                    >
                                        Aprovar
                                    </button>
                                    <button
                                        class="status-pill status--red"
                                        style="border: none; cursor: pointer"
                                    >
                                        Rebutjar
                                    </button>
                                </div>
                            </template>
                        </Column>
                    </DataTable>
                </TabPanel>
            </TabView>
        </div>

        <NovaSolicitudModal
            v-model:visible="mostrarModal"
            @solicitudCreada="cargarDatos"
        />
    </AppLayout>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue';
import {
    Send,
    FileText,
    ClipboardList,
    Check,
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
    todasLasSolicitudes.value.filter(
        (s) => s.estado === 'Nova' || s.estado === 'En Revisió',
    ),
);
const ofertasCotizadas = computed(() =>
    todasLasSolicitudes.value.filter((s) => s.estado === 'Cotitzada'),
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
                solicitudes_nuevas: todasLasSolicitudes.value.filter(
                    (s) => s.estats_solicitud_id === 1,
                ).length,
                ofertas_recibidas: todasLasSolicitudes.value.filter(
                    (s) => s.estats_solicitud_id === 2,
                ).length,
                ofertas_aprobadas: todasLasSolicitudes.value.filter(
                    (s) => s.estats_solicitud_id === 3,
                ).length,
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

/* ── Action bar ─────────────────────────────────────────────── */
.action-bar {
    display: grid;
    grid-template-columns: 1fr 1fr 1fr;
    gap: 1rem;
}

.action-tile {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 1.1rem 1.4rem;
    background: white;
    border: 1px solid #e5e7eb;
    border-radius: 12px;
    text-decoration: none;
    cursor: pointer;
    font-family: inherit;
    text-align: left;
    transition: all 0.2s;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04);
}

.action-tile:hover {
    border-color: #1a8a7d;
    box-shadow: 0 4px 12px rgba(26, 138, 125, 0.1);
    transform: translateY(-1px);
}

.action-tile--primary {
    background: #1a8a7d;
    border-color: #1a8a7d;
}

.action-tile--primary:hover {
    background: #136a60;
    border-color: #136a60;
    box-shadow: 0 4px 12px rgba(26, 138, 125, 0.25);
}

.action-tile__icon {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 42px;
    height: 42px;
    border-radius: 10px;
    background: #f0fdfa;
    color: #1a8a7d;
    flex-shrink: 0;
}

.action-tile--primary .action-tile__icon {
    background: rgba(255, 255, 255, 0.2);
    color: white;
}

.action-tile__text {
    display: flex;
    flex-direction: column;
    gap: 0.15rem;
}

.action-tile__label {
    font-size: 0.9rem;
    font-weight: 700;
    color: #111827;
}

.action-tile--primary .action-tile__label {
    color: white;
}

.action-tile__desc {
    font-size: 0.775rem;
    color: #6b7280;
    line-height: 1.4;
}

.action-tile--primary .action-tile__desc {
    color: rgba(255, 255, 255, 0.75);
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
