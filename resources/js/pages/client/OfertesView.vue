<template>
    <AppLayout>
        <Header
            title="Sol·licituds"
            subtitle="Crea sol·licituds de cotització i gestiona les ofertes rebudes."
        />

        <div class="action-bar mt-4">
            <RouterLink to="/client/operacions" class="action-tile">
                <div class="action-tile__icon">
                    <PackageSearch :size="22" />
                </div>
                <div class="action-tile__text">
                    <span class="action-tile__label">Seguiment</span>
                    <span class="action-tile__desc">Consulta l'estat dels teus enviaments</span>
                </div>
            </RouterLink>
            <button @click="mostrarModal = true" class="action-tile action-tile--primary">
                <div class="action-tile__icon">
                    <PlusCircle :size="22" />
                </div>
                <div class="action-tile__text">
                    <span class="action-tile__label">Nova Sol·licitud</span>
                    <span class="action-tile__desc">Demana una nova cotització de transport</span>
                </div>
            </button>
            <RouterLink to="/client/documents" class="action-tile">
                <div class="action-tile__icon">
                    <FileText :size="22" />
                </div>
                <div class="action-tile__text">
                    <span class="action-tile__label">Documents</span>
                    <span class="action-tile__desc">Accedeix a tota la documentació</span>
                </div>
            </RouterLink>
        </div>

        <div class="card mt-4">
            <div class="card-header">
                <div class="card-title">
                    <ClipboardList :size="15" />
                    Llistat de Sol·licituds
                </div>
            </div>

            <TabView class="custom-tabs">

                <!-- ── En Espera (nova / en_revision) ──────────── -->
                <TabPanel :header="`En Espera (${enEspera.length})`">
                    <DataTable
                        :value="enEspera"
                        :paginator="enEspera.length > 8"
                        :rows="8"
                        responsiveLayout="scroll"
                        emptyMessage="Cap sol·licitud pendent."
                    >
                        <Column field="id" header="Ref." sortable style="width:90px">
                            <template #body="{ data }">
                                <strong class="ref">SOL-{{ String(data.id).padStart(3, '0') }}</strong>
                            </template>
                        </Column>
                        <Column header="Ruta (Origen → Destí)">
                            <template #body="{ data }">
                                {{ data.port_origen?.nom ?? '—' }} → {{ data.port_desti?.nom ?? '—' }}
                            </template>
                        </Column>
                        <Column header="Transport" style="width:130px">
                            <template #body="{ data }">{{ data.tipus_transport?.tipus ?? '—' }}</template>
                        </Column>
                        <Column header="Pes Brut" style="width:100px">
                            <template #body="{ data }">{{ data.pes_brut ? data.pes_brut + ' kg' : '—' }}</template>
                        </Column>
                        <Column header="Data" style="width:110px">
                            <template #body="{ data }">{{ formatDate(data.data_creacio) }}</template>
                        </Column>
                        <Column header="Estat" style="width:130px">
                            <template #body="{ data }">
                                <span :class="estatSolicitudClass(data.estat_solicitud_id)">
                                    {{ data.estat_solicitud?.estat ?? 'Nova' }}
                                </span>
                            </template>
                        </Column>
                    </DataTable>
                </TabPanel>

                <!-- ── Ofertes Rebudes (cotizada) ──────────────── -->
                <TabPanel :header="`Ofertes Rebudes (${ofertesRebudes.length})`">
                    <div v-if="ofertesRebudes.length === 0" class="empty-state">
                        <ClipboardList :size="36" class="empty-icon" />
                        <p>No tens cap oferta pendent de resposta.</p>
                    </div>

                    <div v-else class="oferta-list">
                        <div v-for="sol in ofertesRebudes" :key="sol.id" class="oferta-card">
                            <div class="oferta-card__header">
                                <div class="oferta-card__header-left">
                                    <strong class="ref">SOL-{{ String(sol.id).padStart(3, '0') }}</strong>
                                    <span class="oferta-card__ruta">
                                        {{ sol.port_origen?.nom ?? '—' }} → {{ sol.port_desti?.nom ?? '—' }}
                                    </span>
                                    <span v-if="sol.tipus_transport" class="meta-pill">{{ sol.tipus_transport.tipus }}</span>
                                    <span v-if="sol.pes_brut" class="meta-pill">{{ sol.pes_brut }} kg</span>
                                </div>
                                <span class="status-pill status--orange">Oferta Rebuda</span>
                            </div>

                            <template v-for="oferta in ofertesEnviades(sol)" :key="oferta.id">
                                <div class="oferta-card__body">
                                    <div class="oferta-details">
                                        <div class="oferta-detail-item">
                                            <span class="detail-label">Pressupost</span>
                                            <span class="detail-value price">
                                                {{ Number(oferta.pressupost).toFixed(2) }} €
                                            </span>
                                        </div>
                                        <div class="oferta-detail-item">
                                            <span class="detail-label">Moneda</span>
                                            <span class="detail-value">{{ oferta.moneda }}</span>
                                        </div>
                                        <div class="oferta-detail-item">
                                            <span class="detail-label">Vàlid fins</span>
                                            <span class="detail-value">{{ formatDate(oferta.data_validessa_final) }}</span>
                                        </div>
                                        <div v-if="oferta.comentaris" class="oferta-detail-item oferta-detail-item--wide">
                                            <span class="detail-label">Comentaris</span>
                                            <span class="detail-value">{{ oferta.comentaris }}</span>
                                        </div>
                                    </div>

                                    <div class="oferta-card__actions">
                                        <button
                                            class="btn-action btn-action--green"
                                            :disabled="actionLoading"
                                            @click="acceptarOferta(sol.id, oferta.id)"
                                        >
                                            <Check :size="15" /> Acceptar
                                        </button>
                                        <button
                                            class="btn-action btn-action--red"
                                            :disabled="actionLoading"
                                            @click="rebutjarOferta(sol.id, oferta.id)"
                                        >
                                            <X :size="15" /> Rebutjar
                                        </button>
                                        <button
                                            class="btn-action btn-action--yellow"
                                            :disabled="actionLoading"
                                            @click="obrirContraoferta(sol)"
                                        >
                                            <MessageSquare :size="15" /> Contraoferta
                                        </button>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>
                </TabPanel>

                <!-- ── En Negociació (en_negociacion) ─────────── -->
                <TabPanel :header="`En Negociació (${enNegociacio.length})`">
                    <DataTable
                        :value="enNegociacio"
                        :paginator="enNegociacio.length > 8"
                        :rows="8"
                        responsiveLayout="scroll"
                        emptyMessage="Cap sol·licitud en negociació."
                    >
                        <Column field="id" header="Ref." sortable style="width:90px">
                            <template #body="{ data }">
                                <strong class="ref">SOL-{{ String(data.id).padStart(3, '0') }}</strong>
                            </template>
                        </Column>
                        <Column header="Ruta">
                            <template #body="{ data }">
                                {{ data.port_origen?.nom ?? '—' }} → {{ data.port_desti?.nom ?? '—' }}
                            </template>
                        </Column>
                        <Column header="Contraoferta Enviada" style="width:175px">
                            <template #body="{ data }">
                                <span v-if="darreraContraoferta(data)" class="price">
                                    {{ Number(darreraContraoferta(data).pressupost).toFixed(2) }} €
                                </span>
                                <span v-else>—</span>
                            </template>
                        </Column>
                        <Column header="Estat" style="width:140px">
                            <template #body>
                                <span class="status-pill status--yellow">En Negociació</span>
                            </template>
                        </Column>
                    </DataTable>
                </TabPanel>

                <!-- ── Gestionades (oferta acceptada) ─────────── -->
                <TabPanel :header="`Gestionades (${gestionades.length})`">
                    <DataTable
                        :value="gestionades"
                        :paginator="gestionades.length > 8"
                        :rows="8"
                        responsiveLayout="scroll"
                        emptyMessage="Cap sol·licitud gestionada."
                    >
                        <Column field="id" header="Ref." sortable style="width:90px">
                            <template #body="{ data }">
                                <strong class="ref">SOL-{{ String(data.id).padStart(3, '0') }}</strong>
                            </template>
                        </Column>
                        <Column header="Ruta">
                            <template #body="{ data }">
                                {{ data.port_origen?.nom ?? '—' }} → {{ data.port_desti?.nom ?? '—' }}
                            </template>
                        </Column>
                        <Column header="Pressupost Acceptat" style="width:175px">
                            <template #body="{ data }">
                                <span v-if="ofertaAcceptada(data)" class="price">
                                    {{ Number(ofertaAcceptada(data).pressupost).toFixed(2) }} €
                                </span>
                                <span v-else>—</span>
                            </template>
                        </Column>
                        <Column header="Estat" style="width:130px">
                            <template #body>
                                <span class="status-pill status--green">Acceptada</span>
                            </template>
                        </Column>
                    </DataTable>
                </TabPanel>

            </TabView>
        </div>

        <NovaSolicitudModal
            v-model:visible="mostrarModal"
            @solicitudCreada="cargarDatos"
            @error="(msg) => toast.add({ severity: 'error', summary: 'Error', detail: msg, life: 4000 })"
        />

        <ContraofertaModal
            v-if="solicitudContraoferta"
            v-model:visible="mostrarContraoferta"
            :solicitud="solicitudContraoferta"
            @enviar="enviarContraoferta"
        />

        <Toast />
    </AppLayout>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { useToast } from 'primevue/usetoast';
import Toast from 'primevue/toast';
import {
    FileText,
    ClipboardList,
    PlusCircle,
    PackageSearch,
    Check,
    X,
    MessageSquare,
} from 'lucide-vue-next';
import TabView from 'primevue/tabview';
import TabPanel from 'primevue/tabpanel';
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';

import AppLayout from '@/layout/AppLayout.vue';
import Header from '@/layout/Header.vue';
import NovaSolicitudModal from '@/components/client/NovaSolicitudModal.vue';
import ContraofertaModal from '@/components/client/ContraofertaModal.vue';
import api from '@/plugins/axios';

const toast = useToast();

const loading = ref(true);
const actionLoading = ref(false);
const mostrarModal = ref(false);
const mostrarContraoferta = ref(false);
const solicitudContraoferta = ref(null);

const totes = ref([]);

// ── Computed filters ─────────────────────────────────────────
// estat_solicitud_id: 1=nueva, 2=en_revision, 3=cotizada, 4=en_negociacion, 5=rechazada
// estat_oferta_id:    1=aceptada, 2=enviada, 3=borrador, 4=rechazada, 5=expirada

const enEspera = computed(() =>
    totes.value.filter((s) => [1, 2].includes(s.estat_solicitud_id)),
);

const ofertesRebudes = computed(() =>
    totes.value.filter(
        (s) => s.estat_solicitud_id === 3 && ofertesEnviades(s).length > 0,
    ),
);

const enNegociacio = computed(() =>
    totes.value.filter((s) => s.estat_solicitud_id === 4),
);

const gestionades = computed(() =>
    totes.value.filter((s) => ofertaAcceptada(s) !== null),
);

// Offers sent by operator (not counter-offers), still awaiting client response
const ofertesEnviades = (sol) =>
    (sol.ofertes ?? []).filter((o) => o.estat_oferta_id === 2 && !o.es_contraoferta);

const ofertaAcceptada = (sol) =>
    (sol.ofertes ?? []).find((o) => o.estat_oferta_id === 1) ?? null;

const darreraContraoferta = (sol) => {
    const contraoferts = (sol.ofertes ?? []).filter((o) => o.es_contraoferta);
    return contraoferts.at(-1) ?? null;
};

// ── Helpers ──────────────────────────────────────────────────
function formatDate(dateStr) {
    if (!dateStr) return '—';
    return new Date(dateStr).toLocaleDateString('ca-ES', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric',
    });
}

function estatSolicitudClass(id) {
    const map = {
        1: 'status-pill status--purple',
        2: 'status-pill status--blue',
        3: 'status-pill status--orange',
        4: 'status-pill status--yellow',
        5: 'status-pill status--red',
    };
    return map[id] ?? 'status-pill status--blue';
}

// ── Data loading ─────────────────────────────────────────────
const cargarDatos = async () => {
    loading.value = true;
    try {
        const { data } = await api.get('/solicitudes');
        if (data.status === 'success') {
            totes.value = data.data ?? [];
        }
    } catch (e) {
        console.error('Error carregant sol·licituds', e);
        toast.add({
            severity: 'error',
            summary: 'Error',
            detail: "No s'han pogut carregar les sol·licituds.",
            life: 4000,
        });
    } finally {
        loading.value = false;
    }
};

// ── Offer actions ────────────────────────────────────────────
const acceptarOferta = async (solicitudId, ofertaId) => {
    actionLoading.value = true;
    try {
        await api.post(`/solicitudes/${solicitudId}/ofertes/${ofertaId}/acceptar`);
        toast.add({
            severity: 'success',
            summary: 'Acceptada',
            detail: "Oferta acceptada. S'ha creat l'operació.",
            life: 5000,
        });
        await cargarDatos();
    } catch (e) {
        toast.add({
            severity: 'error',
            summary: 'Error',
            detail: e.response?.data?.message ?? "Error acceptant l'oferta.",
            life: 4000,
        });
    } finally {
        actionLoading.value = false;
    }
};

const rebutjarOferta = async (solicitudId, ofertaId) => {
    actionLoading.value = true;
    try {
        await api.post(`/solicitudes/${solicitudId}/ofertes/${ofertaId}/rebutjar`);
        toast.add({
            severity: 'info',
            summary: 'Rebutjada',
            detail: 'Oferta rebutjada. Espereu una nova cotització.',
            life: 5000,
        });
        await cargarDatos();
    } catch (e) {
        toast.add({
            severity: 'error',
            summary: 'Error',
            detail: e.response?.data?.message ?? "Error rebutjant l'oferta.",
            life: 4000,
        });
    } finally {
        actionLoading.value = false;
    }
};

const obrirContraoferta = (sol) => {
    solicitudContraoferta.value = sol;
    mostrarContraoferta.value = true;
};

const enviarContraoferta = async (formData) => {
    actionLoading.value = true;
    try {
        await api.post(
            `/solicitudes/${solicitudContraoferta.value.id}/contraoferta`,
            formData,
        );
        mostrarContraoferta.value = false;
        toast.add({
            severity: 'success',
            summary: 'Enviada',
            detail: 'Contraoferta enviada correctament.',
            life: 5000,
        });
        await cargarDatos();
    } catch (e) {
        toast.add({
            severity: 'error',
            summary: 'Error',
            detail: e.response?.data?.message ?? 'Error enviant la contraoferta.',
            life: 4000,
        });
    } finally {
        actionLoading.value = false;
    }
};

onMounted(cargarDatos);
</script>

<style scoped>
.mt-4 {
    margin-top: 1.5rem;
}

/* ── Card shell ──────────────────────────────── */
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

/* ── Status pills ────────────────────────────── */
.status-pill {
    font-size: 0.68rem;
    font-weight: 600;
    padding: 0.3rem 0.65rem;
    border-radius: 20px;
    white-space: nowrap;
    display: inline-block;
}

.status--green  { background: #f0fdf4; color: #166534; }
.status--purple { background: #f5f3ff; color: #5b21b6; }
.status--blue   { background: #eff6ff; color: #1e40af; }
.status--red    { background: #fff1f2; color: #9f1239; }
.status--orange { background: #fff7ed; color: #c2410c; }
.status--yellow { background: #fefce8; color: #854d0e; }

/* ── Action bar ──────────────────────────────── */
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

.action-tile--primary { background: #1a8a7d; border-color: #1a8a7d; }
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

.action-tile__text { display: flex; flex-direction: column; gap: 0.15rem; }

.action-tile__label { font-size: 0.9rem; font-weight: 700; color: #111827; }
.action-tile--primary .action-tile__label { color: white; }

.action-tile__desc { font-size: 0.775rem; color: #6b7280; line-height: 1.4; }
.action-tile--primary .action-tile__desc { color: rgba(255, 255, 255, 0.75); }

/* ── Helpers ─────────────────────────────────── */
.ref   { font-size: 0.8rem; font-family: monospace; color: #374151; }
.price { font-weight: 700; color: #1a8a7d; }

.meta-pill {
    font-size: 0.72rem;
    background: #f3f4f6;
    color: #6b7280;
    padding: 0.2rem 0.55rem;
    border-radius: 20px;
    white-space: nowrap;
}

/* ── Empty state ─────────────────────────────── */
.empty-state {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 0.75rem;
    padding: 3rem 0;
    color: #9ca3af;
    font-size: 0.875rem;
}
.empty-icon { opacity: 0.35; }

/* ── Oferta cards (Ofertes Rebudes tab) ──────── */
.oferta-list { display: flex; flex-direction: column; gap: 1rem; }

.oferta-card {
    border: 1px solid #e5e7eb;
    border-radius: 12px;
    overflow: hidden;
}

.oferta-card__header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 0.85rem 1.1rem;
    background: #f9fafb;
    border-bottom: 1px solid #e5e7eb;
    gap: 0.75rem;
    flex-wrap: wrap;
}

.oferta-card__header-left {
    display: flex;
    align-items: center;
    gap: 0.6rem;
    flex-wrap: wrap;
}

.oferta-card__ruta { font-size: 0.875rem; color: #374151; font-weight: 500; }

.oferta-card__body {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    gap: 1.5rem;
    padding: 1.1rem 1.25rem;
    flex-wrap: wrap;
}

.oferta-details { display: flex; gap: 1.5rem; flex-wrap: wrap; flex: 1; }

.oferta-detail-item {
    display: flex;
    flex-direction: column;
    gap: 0.2rem;
    min-width: 110px;
}

.oferta-detail-item--wide { flex-basis: 100%; }

.detail-label {
    font-size: 0.7rem;
    font-weight: 600;
    color: #9ca3af;
    text-transform: uppercase;
    letter-spacing: 0.05em;
}

.detail-value { font-size: 0.9rem; color: #111827; }

.oferta-card__actions {
    display: flex;
    gap: 0.5rem;
    flex-shrink: 0;
    align-items: center;
}

/* ── Action buttons ──────────────────────────── */
.btn-action {
    display: inline-flex;
    align-items: center;
    gap: 0.35rem;
    padding: 0.45rem 0.85rem;
    border-radius: 8px;
    font-size: 0.8rem;
    font-weight: 600;
    border: none;
    cursor: pointer;
    transition: all 0.15s;
    white-space: nowrap;
    font-family: inherit;
}

.btn-action:disabled { opacity: 0.5; cursor: not-allowed; }

.btn-action--green  { background: #f0fdf4; color: #166534; border: 1px solid #bbf7d0; }
.btn-action--green:hover:not(:disabled)  { background: #dcfce7; }

.btn-action--red    { background: #fff1f2; color: #9f1239; border: 1px solid #fecdd3; }
.btn-action--red:hover:not(:disabled)    { background: #ffe4e6; }

.btn-action--yellow { background: #fffbeb; color: #92400e; border: 1px solid #fde68a; }
.btn-action--yellow:hover:not(:disabled) { background: #fef3c7; }

/* ── PrimeVue TabView overrides ──────────────── */
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

:deep(.custom-tabs .p-tabview-nav-link:focus) { box-shadow: none; }

:deep(.custom-tabs .p-highlight .p-tabview-nav-link) {
    color: #1a8a7d;
    border-bottom-color: #1a8a7d;
}
</style>