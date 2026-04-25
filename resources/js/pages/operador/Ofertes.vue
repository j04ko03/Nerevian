<template>
    <AppLayout>
        <Header
            title="Sol·licituds i Ofertes"
            subtitle="Gestiona les sol·licituds assignades i envia les teves ofertes."
        />

        <div class="stats-row">
            <div class="stat-card">
                <span class="stat-value">{{ pendents.length }}</span>
                <span class="stat-label">Pendents d'oferta</span>
            </div>
            <div class="stat-card stat-card--yellow">
                <span class="stat-value">{{ enNegociacio.length }}</span>
                <span class="stat-label">En negociació</span>
            </div>
            <div class="stat-card stat-card--green">
                <span class="stat-value">{{ cotitzades.length }}</span>
                <span class="stat-label">Ja cotitzades</span>
            </div>
        </div>

        <div class="card mt-4">
            <div class="card-header">
                <div class="card-title">
                    <ClipboardList :size="15" />
                    Sol·licituds assignades
                </div>
                <TabView v-model:activeIndex="tabActiu" class="header-tabs">
                    <TabPanel :header="`Pendents (${pendents.length})`" />
                    <TabPanel
                        :header="`En Negociació (${enNegociacio.length})`"
                    />
                    <TabPanel :header="`Cotitzades (${cotitzades.length})`" />
                </TabView>
            </div>

            <!-- Tab 0: Pendents -->
            <DataTable
                v-if="tabActiu === 0"
                :value="pendents"
                :loading="loading"
                paginator
                :rows="10"
                stripedRows
                tableStyle="min-width: 50rem"
            >
                <template #empty>
                    <div v-if="empty" class="empty-state">
                        <ClipboardList :size="40" style="color: #d1d5db" />
                        <p>Cap sol·licitud pendent</p>
                    </div>
                </template>
                <Column field="id" header="Ref." sortable style="width: 5rem">
                    <template #body="{ data }">
                        <strong class="ref-text"
                            >SOL-{{ String(data.id).padStart(3, '0') }}</strong
                        >
                    </template>
                </Column>
                <Column header="Client" style="min-width: 12rem">
                    <template #body="{ data }">
                        <div class="user-cell">
                            <div class="user-avatar">
                                {{
                                    initials(
                                        data.client?.usuaris?.nom,
                                        data.client?.usuaris?.cognoms,
                                    )
                                }}
                            </div>
                            <div>
                                <div class="user-name">
                                    {{ data.client?.usuaris?.nom }}
                                    {{ data.client?.usuaris?.cognoms }}
                                </div>
                                <div class="user-email">
                                    {{ data.client?.usuaris?.correu }}
                                </div>
                            </div>
                        </div>
                    </template>
                </Column>
                <Column header="Ruta" style="min-width: 14rem">
                    <template #body="{ data }">
                        <span class="route-text"
                            >{{ data.port_origen?.nom ?? '—' }} →
                            {{ data.port_desti?.nom ?? '—' }}</span
                        >
                    </template>
                </Column>
                <Column header="Transport" style="min-width: 9rem">
                    <template #body="{ data }">{{
                        data.tipus_transport?.tipus ?? '—'
                    }}</template>
                </Column>
                <Column header="Pes (kg)" style="min-width: 7rem">
                    <template #body="{ data }">{{
                        data.pes_brut
                            ? Number(data.pes_brut).toLocaleString()
                            : '—'
                    }}</template>
                </Column>
                <Column
                    header="Data"
                    sortable
                    sortField="data_creacio"
                    style="min-width: 9rem"
                >
                    <template #body="{ data }">
                        <span class="date-text">{{
                            formatDate(data.data_creacio)
                        }}</span>
                    </template>
                </Column>
                <Column
                    header="Accions"
                    style="width: 8rem; text-align: center"
                >
                    <template #body="{ data }">
                        <button class="btn-oferta" @click="obrirModal(data)">
                            <Send :size="13" /> Ofertar
                        </button>
                    </template>
                </Column>
            </DataTable>

            <!-- Tab 1: En Negociació -->
            <div v-if="tabActiu === 1">
                <div v-if="enNegociacio.length === 0" class="empty-state">
                    <ClipboardList :size="40" style="color: #d1d5db" />
                    <p>Cap sol·licitud en negociació</p>
                </div>

                <div v-else class="negociacio-list">
                    <div
                        v-for="sol in enNegociacio"
                        :key="sol.id"
                        class="negociacio-card"
                    >
                        <!-- Capçalera sol·licitud -->
                        <div class="negociacio-card__header">
                            <div class="negociacio-card__header-left">
                                <strong class="ref-text"
                                    >SOL-{{
                                        String(sol.id).padStart(3, '0')
                                    }}</strong
                                >
                                <div class="user-cell">
                                    <div class="user-avatar">
                                        {{
                                            initials(
                                                sol.client?.usuaris?.nom,
                                                sol.client?.usuaris?.cognoms,
                                            )
                                        }}
                                    </div>
                                    <div>
                                        <div class="user-name">
                                            {{ sol.client?.usuaris?.nom }}
                                            {{ sol.client?.usuaris?.cognoms }}
                                        </div>
                                        <div class="user-email">
                                            {{ sol.client?.usuaris?.correu }}
                                        </div>
                                    </div>
                                </div>
                                <span class="route-text"
                                    >{{ sol.port_origen?.nom ?? '—' }} →
                                    {{ sol.port_desti?.nom ?? '—' }}</span
                                >
                            </div>
                            <span class="estat-badge estat-negociacio"
                                >En Negociació</span
                            >
                        </div>

                        <!-- Historial d'ofertes -->
                        <div class="negociacio-card__body">
                            <p class="section-label">Historial d'ofertes</p>
                            <div class="ofertes-history">
                                <div
                                    v-for="oferta in sol.ofertes"
                                    :key="oferta.id"
                                    class="history-item"
                                    :class="
                                        oferta.es_contraoferta
                                            ? 'history-item--client'
                                            : 'history-item--operador'
                                    "
                                >
                                    <div class="history-item__who">
                                        <span
                                            class="who-pill"
                                            :class="
                                                oferta.es_contraoferta
                                                    ? 'who-pill--client'
                                                    : 'who-pill--op'
                                            "
                                        >
                                            {{
                                                oferta.es_contraoferta
                                                    ? 'Client'
                                                    : 'Operador'
                                            }}
                                        </span>
                                        <span class="history-estat">{{
                                            oferta.estat?.estat ?? '—'
                                        }}</span>
                                    </div>
                                    <div class="history-item__details">
                                        <span class="history-price"
                                            >{{
                                                Number(
                                                    oferta.pressupost,
                                                ).toFixed(2)
                                            }}
                                            € ({{ oferta.moneda }})</span
                                        >
                                        <span
                                            v-if="oferta.comentaris"
                                            class="history-comment"
                                            >{{ oferta.comentaris }}</span
                                        >
                                    </div>

                                    <!-- Botó acceptar contraoferta -->
                                    <button
                                        v-if="
                                            oferta.es_contraoferta &&
                                            oferta.estat_oferta_id === 2
                                        "
                                        class="btn-acceptar"
                                        :disabled="actionLoading"
                                        @click="
                                            acceptarContraoferta(
                                                sol.id,
                                                oferta.id,
                                            )
                                        "
                                    >
                                        <Check :size="13" /> Acceptar
                                        contraoferta
                                    </button>
                                </div>
                            </div>

                            <!-- Alternativa: enviar nova oferta en comptes d'acceptar -->
                            <div class="negociacio-card__footer">
                                <button
                                    class="btn-oferta-secondary"
                                    @click="obrirModal(sol)"
                                >
                                    <Send :size="13" /> Enviar nova cotització
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tab 2: Cotitzades -->
            <DataTable
                v-if="tabActiu === 2"
                :value="cotitzades"
                :loading="loading"
                paginator
                :rows="10"
                stripedRows
                tableStyle="min-width: 50rem"
            >
                <template #empty>
                    <div class="empty-state">
                        <ClipboardList :size="40" style="color: #d1d5db" />
                        <p>Cap sol·licitud cotitzada</p>
                    </div>
                </template>
                <Column field="id" header="Ref." sortable style="width: 5rem">
                    <template #body="{ data }">
                        <strong class="ref-text"
                            >SOL-{{ String(data.id).padStart(3, '0') }}</strong
                        >
                    </template>
                </Column>
                <Column header="Client" style="min-width: 12rem">
                    <template #body="{ data }">
                        <div class="user-cell">
                            <div class="user-avatar">
                                {{
                                    initials(
                                        data.client?.usuaris?.nom,
                                        data.client?.usuaris?.cognoms,
                                    )
                                }}
                            </div>
                            <div>
                                <div class="user-name">
                                    {{ data.client?.usuaris?.nom }}
                                    {{ data.client?.usuaris?.cognoms }}
                                </div>
                                <div class="user-email">
                                    {{ data.client?.usuaris?.correu }}
                                </div>
                            </div>
                        </div>
                    </template>
                </Column>
                <Column header="Ruta" style="min-width: 14rem">
                    <template #body="{ data }">
                        <span class="route-text"
                            >{{ data.port_origen?.nom ?? '—' }} →
                            {{ data.port_desti?.nom ?? '—' }}</span
                        >
                    </template>
                </Column>
                <Column header="Oferta enviada" style="min-width: 10rem">
                    <template #body="{ data }">
                        <span
                            v-if="darrereOfertaOperador(data)"
                            class="price-text"
                        >
                            {{
                                Number(
                                    darrereOfertaOperador(data).pressupost,
                                ).toFixed(2)
                            }}
                            €
                        </span>
                        <span v-else>—</span>
                    </template>
                </Column>
                <Column header="Estat" style="min-width: 9rem">
                    <template #body="{ data }">
                        <span class="estat-badge estat-cotitzada">
                            {{ data.estat_solicitud?.estat ?? '—' }}
                        </span>
                    </template>
                </Column>
                <Column
                    header="Accions"
                    style="width: 8rem; text-align: center"
                >
                    <template #body="{ data }">
                        <span class="oferta-enviada"
                            ><Check :size="13" /> Enviada</span
                        >
                    </template>
                </Column>
            </DataTable>
        </div>

        <EnviarOfertaModal
            v-model:visible="modalVisible"
            :solicitud="solicitudSeleccionada"
            @ofertaEnviada="onOfertaEnviada"
            @error="
                (msg) =>
                    toast.add({
                        severity: 'error',
                        summary: 'Error',
                        detail: msg,
                        life: 4000,
                    })
            "
        />
        <Toast />
    </AppLayout>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { useToast } from 'primevue/usetoast';
import Toast from 'primevue/toast';
import TabView from 'primevue/tabview';
import TabPanel from 'primevue/tabpanel';
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import { ClipboardList, Send, Check } from 'lucide-vue-next';

import AppLayout from '@/layout/AppLayout.vue';
import Header from '@/layout/Header.vue';
import api from '@/plugins/axios';
import EnviarOfertaModal from '@/components/operador/ofertes/EnviarOfertaModal.vue';

const toast = useToast();

const solicituds = ref([]);
const loading = ref(false);
const actionLoading = ref(false);
const tabActiu = ref(0);
const modalVisible = ref(false);
const solicitudSeleccionada = ref(null);

// estat_solicitud_id: 1=nueva, 2=en_revision, 3=cotizada, 4=en_negociacion
const pendents = computed(() =>
    solicituds.value.filter((s) => [1, 2].includes(s.estat_solicitud_id)),
);
const enNegociacio = computed(() =>
    solicituds.value.filter((s) => s.estat_solicitud_id === 4),
);
const cotitzades = computed(() =>
    solicituds.value.filter((s) => s.estat_solicitud_id === 3),
);

const darrereOfertaOperador = (sol) =>
    [...(sol.ofertes ?? [])].reverse().find((o) => !o.es_contraoferta) ?? null;

async function carregarSolicituds() {
    loading.value = true;
    try {
        const { data } = await api.get('/operador/solicituds');
        if (data.status === 'success') solicituds.value = data.data ?? [];
    } catch {
        toast.add({
            severity: 'error',
            summary: 'Error',
            detail: "No s'han pogut carregar les sol·licituds",
            life: 3000,
        });
    } finally {
        loading.value = false;
    }
}

async function acceptarContraoferta(solicitudId, ofertaId) {
    actionLoading.value = true;
    try {
        await api.post(
            `/operador/solicituds/${solicitudId}/ofertes/${ofertaId}/acceptar`,
        );
        toast.add({
            severity: 'success',
            summary: 'Acceptada',
            detail: "Contraoferta acceptada. S'ha creat l'operació.",
            life: 4000,
        });
        await carregarSolicituds();
    } catch (e) {
        toast.add({
            severity: 'error',
            summary: 'Error',
            detail:
                e.response?.data?.message ?? 'Error acceptant la contraoferta.',
            life: 4000,
        });
    } finally {
        actionLoading.value = false;
    }
}

function obrirModal(solicitud) {
    solicitudSeleccionada.value = solicitud;
    modalVisible.value = true;
}

function onOfertaEnviada() {
    toast.add({
        severity: 'success',
        summary: 'Oferta enviada',
        detail: "L'oferta ha estat enviada al client",
        life: 3000,
    });
    carregarSolicituds();
}

function initials(nom, cognoms) {
    return (
        ((nom || '').charAt(0) + (cognoms || '').charAt(0)).toUpperCase() || '?'
    );
}

function formatDate(dateStr) {
    if (!dateStr) return '—';
    return new Date(dateStr).toLocaleDateString('ca-ES', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric',
    });
}

onMounted(carregarSolicituds);
</script>

<style scoped>
.mt-4 {
    margin-top: 1.5rem;
}

/* ── Stats ───────────────────────────────── */
.stats-row {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 1rem;
    margin-top: 1.5rem;
}

.stat-card {
    background: white;
    border: 1px solid #e5e7eb;
    border-radius: 12px;
    padding: 1.1rem 1.4rem;
    display: flex;
    flex-direction: column;
    gap: 0.25rem;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.03);
}

.stat-card--green {
    border-left: 3px solid #10b981;
}
.stat-card--yellow {
    border-left: 3px solid #f59e0b;
}

.stat-value {
    font-size: 1.75rem;
    font-weight: 700;
    color: #111827;
}
.stat-label {
    font-size: 0.8rem;
    color: #6b7280;
    font-weight: 500;
}

/* ── Card ────────────────────────────────── */
.card {
    background: white;
    border: 1px solid #e5e7eb;
    border-radius: 14px;
    padding: 1.25rem 1.5rem;
    box-shadow: 0 1px 4px rgba(0, 0, 0, 0.03);
}

.card-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding-bottom: 0.85rem;
    border-bottom: 1px solid #f3f4f6;
    margin-bottom: 0.5rem;
}

.card-title {
    display: flex;
    align-items: center;
    gap: 0.45rem;
    font-size: 0.875rem;
    font-weight: 700;
    color: #111827;
}

/* ── Table cells ─────────────────────────── */
.ref-text {
    font-size: 0.85rem;
    color: #6b7280;
}
.route-text {
    font-size: 0.875rem;
    color: #374151;
}
.date-text {
    font-size: 0.85rem;
    color: #6b7280;
}
.price-text {
    font-weight: 700;
    color: #1a8a7d;
}

.user-cell {
    display: flex;
    align-items: center;
    gap: 0.65rem;
}

.user-avatar {
    width: 34px;
    height: 34px;
    border-radius: 50%;
    background: #e6f4f4;
    color: #0a3a40;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.72rem;
    font-weight: 700;
    flex-shrink: 0;
}

.user-name {
    font-weight: 600;
    font-size: 0.875rem;
    color: #111827;
}
.user-email {
    font-size: 0.77rem;
    color: #9ca3af;
}

/* ── Estat badges ────────────────────────── */
.estat-badge {
    display: inline-block;
    font-size: 0.72rem;
    font-weight: 600;
    padding: 0.25rem 0.6rem;
    border-radius: 4px;
}

.estat-pendent {
    background: #fef9c3;
    color: #92400e;
}
.estat-cotitzada {
    background: #d1fae5;
    color: #065f46;
}
.estat-negociacio {
    background: #fff7ed;
    color: #c2410c;
}

/* ── Action buttons ──────────────────────── */
.btn-oferta {
    display: inline-flex;
    align-items: center;
    gap: 0.3rem;
    padding: 0.35rem 0.75rem;
    background: #1a8a7d;
    color: white;
    border: none;
    border-radius: 6px;
    font-size: 0.8rem;
    font-weight: 600;
    cursor: pointer;
    transition: background 0.2s;
    font-family: inherit;
}
.btn-oferta:hover {
    background: #136a60;
}

.btn-oferta-secondary {
    display: inline-flex;
    align-items: center;
    gap: 0.3rem;
    padding: 0.35rem 0.75rem;
    background: white;
    color: #1a8a7d;
    border: 1px solid #1a8a7d;
    border-radius: 6px;
    font-size: 0.8rem;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s;
    font-family: inherit;
}
.btn-oferta-secondary:hover {
    background: #f0fdfa;
}

.btn-acceptar {
    display: inline-flex;
    align-items: center;
    gap: 0.3rem;
    padding: 0.35rem 0.75rem;
    background: #f0fdf4;
    color: #166534;
    border: 1px solid #bbf7d0;
    border-radius: 6px;
    font-size: 0.8rem;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.15s;
    font-family: inherit;
    margin-top: 0.5rem;
}
.btn-acceptar:hover:not(:disabled) {
    background: #dcfce7;
}
.btn-acceptar:disabled {
    opacity: 0.5;
    cursor: not-allowed;
}

.oferta-enviada {
    display: inline-flex;
    align-items: center;
    gap: 0.3rem;
    font-size: 0.78rem;
    font-weight: 600;
    color: #059669;
}

/* ── Negociació cards ────────────────────── */
.negociacio-list {
    display: flex;
    flex-direction: column;
    gap: 1rem;
    padding-top: 0.5rem;
}

.negociacio-card {
    border: 1px solid #e5e7eb;
    border-radius: 12px;
    overflow: hidden;
}

.negociacio-card__header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 0.85rem 1.1rem;
    background: #f9fafb;
    border-bottom: 1px solid #e5e7eb;
    gap: 1rem;
    flex-wrap: wrap;
}

.negociacio-card__header-left {
    display: flex;
    align-items: center;
    gap: 1rem;
    flex-wrap: wrap;
}

.negociacio-card__body {
    padding: 1rem 1.25rem;
    display: flex;
    flex-direction: column;
    gap: 0.85rem;
}

.negociacio-card__footer {
    padding-top: 0.75rem;
    border-top: 1px solid #f3f4f6;
}

.section-label {
    font-size: 0.75rem;
    font-weight: 700;
    color: #9ca3af;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    margin: 0;
}

/* ── Ofertes history ─────────────────────── */
.ofertes-history {
    display: flex;
    flex-direction: column;
    gap: 0.6rem;
}

.history-item {
    padding: 0.75rem 1rem;
    border-radius: 8px;
    display: flex;
    flex-direction: column;
    gap: 0.4rem;
}

.history-item--operador {
    background: #f0fdfa;
    border: 1px solid #99f6e4;
}
.history-item--client {
    background: #fffbeb;
    border: 1px solid #fde68a;
}

.history-item__who {
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.who-pill {
    font-size: 0.68rem;
    font-weight: 700;
    padding: 0.15rem 0.5rem;
    border-radius: 20px;
}
.who-pill--op {
    background: #ccfbf1;
    color: #0f766e;
}
.who-pill--client {
    background: #fef3c7;
    color: #92400e;
}

.history-estat {
    font-size: 0.75rem;
    color: #6b7280;
}

.history-item__details {
    display: flex;
    flex-direction: column;
    gap: 0.2rem;
}

.history-price {
    font-weight: 700;
    color: #111827;
    font-size: 0.9rem;
}
.history-comment {
    font-size: 0.82rem;
    color: #6b7280;
}

/* ── Tabs ────────────────────────────────── */
:deep(.header-tabs .p-tabview-nav) {
    background: transparent;
    border: none;
}
:deep(.header-tabs .p-tabview-nav-link) {
    padding: 0.5rem 1rem;
    font-size: 0.82rem;
    font-weight: 600;
    color: #6b7280;
    border: none;
    border-bottom: 2px solid transparent;
    background: transparent;
}
:deep(.header-tabs .p-tabview-nav-link:focus) {
    box-shadow: none;
}
:deep(.header-tabs .p-highlight .p-tabview-nav-link) {
    color: #1a8a7d;
    border-bottom-color: #1a8a7d;
}
:deep(.header-tabs .p-tabview-panels) {
    display: none;
}

/* ── Empty state ─────────────────────────── */
.empty-state {
    text-align: center;
    padding: 3rem;
    color: #9ca3af;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 0.75rem;
}
</style>
