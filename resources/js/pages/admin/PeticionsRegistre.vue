<template>
    <AppLayout>
        <!-- Header -->
        <PageHeader
            title="Peticions de Registre"
            subtitle="Gestió de sol·licituds d'accés al sistema"
        />

        <!-- Stats -->
        <StatsGrid :columns="3">
            <StatCard
                label="Pendents"
                :value="countByEstat(0)"
                compare="per revisar"
                color="yellow"
                :loading="loading"
            >
                <template #icon><Clock :size="18" /></template>
            </StatCard>
            <StatCard
                label="Acceptades"
                :value="countByEstat(1)"
                compare="usuaris creats"
                color="green"
                :loading="loading"
            >
                <template #icon><CheckCircle :size="18" /></template>
            </StatCard>
            <StatCard
                label="Rebutjades"
                :value="countByEstat(2)"
                compare="sol·licituds denegades"
                color="red"
                :loading="loading"
            >
                <template #icon><XCircle :size="18" /></template>
            </StatCard>
        </StatsGrid>

        <!-- Filter tabs -->
        <div class="filter-tabs">
            <button
                v-for="tab in tabs"
                :key="tab.value"
                class="tab"
                :class="{ 'tab--active': activeTab === tab.value }"
                @click="activeTab = tab.value"
            >
                {{ tab.label }}
                <span class="tab-count">{{
                    tab.value === null
                        ? peticions.length
                        : countByEstat(tab.value)
                }}</span>
            </button>
        </div>

        <!-- Cards grid -->
        <div v-if="loading" class="cards-grid">
            <div v-for="i in 6" :key="i" class="petition-skeleton">
                <div class="skeleton-top">
                    <Skeleton width="50%" height="1rem" borderRadius="6px" />
                    <Skeleton
                        width="4rem"
                        height="1.4rem"
                        borderRadius="20px"
                    />
                </div>
                <Skeleton
                    width="70%"
                    height="0.8rem"
                    borderRadius="6px"
                    class="mt-2"
                />
                <Skeleton
                    width="55%"
                    height="0.8rem"
                    borderRadius="6px"
                    class="mt-1"
                />
                <Skeleton
                    width="40%"
                    height="0.8rem"
                    borderRadius="6px"
                    class="mt-1"
                />
                <div class="skeleton-footer">
                    <Skeleton
                        width="5rem"
                        height="0.75rem"
                        borderRadius="6px"
                    />
                </div>
            </div>
        </div>

        <div v-else-if="filteredPeticions.length === 0" class="empty-state">
            <ClipboardList :size="40" style="color: #d1d5db" />
            <p>
                No hi ha peticions{{
                    activeTab !== null ? ' en aquest estat' : ''
                }}
            </p>
        </div>

        <div v-else class="cards-grid">
            <div
                v-for="p in filteredPeticions"
                :key="p.id"
                class="petition-card"
                :class="`card--${estatClass(p.estat)}`"
            >
                <!-- Card header -->
                <div class="card-head">
                    <div>
                        <h3 class="company-name">{{ p.nom_empresa }}</h3>
                        <span class="role-badge" :style="rolStyle(p.rol_id)">
                            {{ rolLabel(p.rol_id) }}
                        </span>
                    </div>
                    <span
                        class="estat-badge"
                        :class="`estat--${estatClass(p.estat)}`"
                    >
                        {{ estatLabel(p.estat) }}
                    </span>
                </div>

                <!-- Card body -->
                <div class="card-body">
                    <div class="info-row">
                        <User :size="13" class="info-icon" />
                        <span>{{ p.contacte }}</span>
                    </div>
                    <div class="info-row">
                        <Mail :size="13" class="info-icon" />
                        <span>{{ p.correu }}</span>
                    </div>
                    <div v-if="p.telefon" class="info-row">
                        <Phone :size="13" class="info-icon" />
                        <span>{{ p.telefon }}</span>
                    </div>
                    <div v-if="p.missatge" class="info-row info-row--message">
                        <MessageSquare :size="13" class="info-icon" />
                        <span class="message-text">{{ p.missatge }}</span>
                    </div>
                </div>

                <!-- Card footer -->
                <div class="card-footer">
                    <div class="footer-left">
                        <div class="date-labels">
                            <span
                                class="date-label"
                                v-tooltip.top="'Data de creació'"
                            >
                                <CalendarDays :size="12" />
                                {{ formatDate(p.data_creacio) }}
                            </span>
                            <template v-if="Number(p.estat) !== 0">
                                <span class="date-sep">·</span>
                                <span
                                    class="date-label"
                                    v-tooltip.top="'Data de resolució'"
                                >
                                    <CalendarCheck :size="12" />
                                    {{ formatDate(p.data_resolucio) ?? '—' }}
                                </span>
                            </template>
                        </div>
                        <div
                            v-if="Number(p.estat) !== 0"
                            class="encarregat-label"
                        >
                            <UserCheck :size="11" />
                            <span>{{
                                p.resolutor
                                    ? `${p.resolutor.nom} ${p.resolutor.cognoms}`.trim()
                                    : '—'
                            }}</span>
                        </div>
                    </div>

                    <div v-if="Number(p.estat) === 0" class="action-buttons">
                        <button
                            class="btn btn-accept"
                            :disabled="processingId === p.id"
                            @click="handleAccept(p)"
                        >
                            <LoaderCircle
                                v-if="
                                    processingId === p.id &&
                                    processingAction === 'accept'
                                "
                                :size="13"
                                class="spinner"
                            />
                            <Check v-else :size="13" />
                            Acceptar
                        </button>
                        <button
                            class="btn btn-reject"
                            :disabled="processingId === p.id"
                            @click="handleReject(p)"
                        >
                            <LoaderCircle
                                v-if="
                                    processingId === p.id &&
                                    processingAction === 'reject'
                                "
                                :size="13"
                                class="spinner"
                            />
                            <X v-else :size="13" />
                            Rebutjar
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <ConfirmDialog />
        <Toast />
    </AppLayout>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import {
    Clock,
    CheckCircle,
    XCircle,
    ClipboardList,
    User,
    Mail,
    Phone,
    MessageSquare,
    CalendarDays,
    CalendarCheck,
    UserCheck,
    Check,
    X,
    LoaderCircle,
} from 'lucide-vue-next';
import { useConfirm } from 'primevue/useconfirm';
import { useToast } from 'primevue/usetoast';
import vTooltip from 'primevue/tooltip';
import ConfirmDialog from 'primevue/confirmdialog';
import Toast from 'primevue/toast';
import Skeleton from 'primevue/skeleton';

import AppLayout from '@/layout/AppLayout.vue';
import PageHeader from '@/layout/Header.vue';
import StatsGrid from '@/components/dashboard/components/StatsGrid.vue';
import StatCard from '@/components/dashboard/components/StatCard.vue';
import api from '@/plugins/axios';

const confirm = useConfirm();
const toast = useToast();

const peticions = ref([]);
const loading = ref(false);
const processingId = ref(null);
const processingAction = ref(null);
const activeTab = ref(0);

const tabs = [
    { label: 'Pendents', value: 0 },
    { label: 'Acceptades', value: 1 },
    { label: 'Rebutjades', value: 2 },
    { label: 'Totes', value: null },
];

const rolMap = {
    1: { label: 'Admin', bg: '#e6f4f4', color: '#0a3a40' },
    2: { label: 'Client', bg: '#fefce8', color: '#854d0e' },
    3: { label: 'Operador', bg: '#eff6ff', color: '#1e40af' },
    4: { label: 'Agent', bg: '#f0fdf4', color: '#166534' },
};

function rolLabel(id) {
    return rolMap[id]?.label ?? 'Desconegut';
}
function rolStyle(id) {
    const r = rolMap[id] ?? { bg: '#f3f4f6', color: '#374151' };
    return { backgroundColor: r.bg, color: r.color };
}

function estatLabel(estat) {
    return (
        { 0: 'Pendent', 1: 'Acceptada', 2: 'Rebutjada' }[Number(estat)] ?? '—'
    );
}
function estatClass(estat) {
    return (
        { 0: 'pending', 1: 'accepted', 2: 'rejected' }[Number(estat)] ??
        'pending'
    );
}

function countByEstat(estat) {
    return peticions.value.filter((p) => Number(p.estat) === estat).length;
}

function formatDate(date) {
    if (!date) return '—';
    const d = new Date(date);
    const dayMonth = d.toLocaleDateString('ca-ES', {
        day: 'numeric',
        month: 'short',
    });
    return `${dayMonth} del ${d.getFullYear()}`;
}

const filteredPeticions = computed(() => {
    if (activeTab.value === null) return peticions.value;
    return peticions.value.filter((p) => Number(p.estat) === activeTab.value);
});

async function fetchPeticions() {
    loading.value = true;
    try {
        const { data } = await api.get('/admin/registration-requests');
        peticions.value = data;
    } catch {
        toast.add({
            severity: 'error',
            summary: 'Error',
            detail: "No s'han pogut carregar les peticions",
            life: 3000,
        });
    } finally {
        loading.value = false;
    }
}

function handleAccept(p) {
    confirm.require({
        message: `Vols acceptar la sol·licitud de ${p.nom_empresa} i crear el seu usuari?`,
        header: 'Confirmar acceptació',
        icon: 'pi pi-check-circle',
        rejectProps: {
            label: 'Cancel·lar',
            severity: 'secondary',
            outlined: true,
        },
        acceptProps: { label: 'Acceptar', severity: 'success' },
        accept: () => doAccept(p.id),
    });
}

function handleReject(p) {
    confirm.require({
        message: `Vols rebutjar la sol·licitud de ${p.nom_empresa}?`,
        header: 'Confirmar rebuig',
        icon: 'pi pi-times-circle',
        rejectProps: {
            label: 'Cancel·lar',
            severity: 'secondary',
            outlined: true,
        },
        acceptProps: { label: 'Rebutjar', severity: 'danger' },
        accept: () => doReject(p.id),
    });
}

async function doAccept(id) {
    processingId.value = id;
    processingAction.value = 'accept';
    try {
        await api.post(`/admin/registration-requests/${id}/approve`);
        await fetchPeticions();
        toast.add({
            severity: 'success',
            summary: 'Acceptada',
            detail: 'Usuari creat correctament',
            life: 3000,
        });
    } catch {
        toast.add({
            severity: 'error',
            summary: 'Error',
            detail: "No s'ha pogut acceptar la petició",
            life: 3000,
        });
    } finally {
        processingId.value = null;
        processingAction.value = null;
    }
}

async function doReject(id) {
    processingId.value = id;
    processingAction.value = 'reject';
    try {
        await api.post(`/admin/registration-requests/${id}/reject`);
        await fetchPeticions();
        toast.add({
            severity: 'warn',
            summary: 'Rebutjada',
            detail: 'Sol·licitud rebutjada',
            life: 3000,
        });
    } catch {
        toast.add({
            severity: 'error',
            summary: 'Error',
            detail: "No s'ha pogut rebutjar la petició",
            life: 3000,
        });
    } finally {
        processingId.value = null;
        processingAction.value = null;
    }
}

onMounted(fetchPeticions);
</script>

<style scoped>
/* ── Filter tabs ────────────────────────── */
.filter-tabs {
    display: flex;
    gap: 0.3rem;
    margin-bottom: 1.75rem;
    background: white;
    border: 1px solid #e5e7eb;
    border-radius: 10px;
    padding: 0.3rem;
    width: fit-content;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04);
}
.tab {
    display: flex;
    align-items: center;
    gap: 0.45rem;
    padding: 0.5rem 1.1rem;
    border-radius: 7px;
    border: none;
    background: transparent;
    font-family: inherit;
    font-size: 0.85rem;
    font-weight: 500;
    color: #6b7280;
    cursor: pointer;
    transition: all 0.15s;
}
.tab:hover {
    background: #f9fafb;
    color: #111827;
}
.tab--active {
    background: #1a3a3a;
    color: white;
    box-shadow: 0 1px 4px rgba(0, 0, 0, 0.12);
}
.tab-count {
    font-size: 0.7rem;
    font-weight: 700;
    background: rgba(0, 0, 0, 0.07);
    padding: 0.1rem 0.45rem;
    border-radius: 10px;
    min-width: 1.4rem;
    text-align: center;
}
.tab--active .tab-count {
    background: rgba(255, 255, 255, 0.18);
}

/* ── Cards grid ─────────────────────────── */
.cards-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(340px, 1fr));
    gap: 1.1rem;
}

/* ── Petition card ──────────────────────── */
.petition-card {
    background: white;
    border: 1px solid #e5e7eb;
    border-radius: 14px;
    padding: 1.35rem 1.6rem;
    box-shadow: 0 1px 4px rgba(0, 0, 0, 0.03);
    display: flex;
    flex-direction: column;
    gap: 0.85rem;
    transition:
        transform 0.2s ease,
        box-shadow 0.2s ease;
}
.petition-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 24px -8px rgba(0, 0, 0, 0.1);
}
.card--pending {
    border-left: 4px solid #f59e0b;
    background: #fffcf5;
}
.card--accepted {
    border-left: 4px solid #10b981;
    background: #f9fefb;
}
.card--rejected {
    border-left: 4px solid #ef4444;
    background: #fff9f9;
}

.card-head {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    gap: 0.75rem;
}
.company-name {
    font-size: 0.95rem;
    font-weight: 700;
    color: #111827;
    margin: 0 0 0.35rem;
}
.role-badge {
    display: inline-block;
    font-size: 0.7rem;
    font-weight: 600;
    padding: 0.15rem 0.5rem;
    border-radius: 4px;
    text-transform: capitalize;
}
.estat-badge {
    font-size: 0.72rem;
    font-weight: 600;
    padding: 0.25rem 0.6rem;
    border-radius: 20px;
    white-space: nowrap;
    flex-shrink: 0;
}
.estat--pending {
    background: #fef3c7;
    color: #92400e;
}
.estat--accepted {
    background: #d1fae5;
    color: #065f46;
}
.estat--rejected {
    background: #fee2e2;
    color: #991b1b;
}

/* ── Card body ──────────────────────────── */
.card-body {
    display: flex;
    flex-direction: column;
    gap: 0.4rem;
}
.info-row {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-size: 0.825rem;
    color: #374151;
}
.info-row--message {
    align-items: flex-start;
}
.info-icon {
    color: #9ca3af;
    flex-shrink: 0;
}
.message-text {
    color: #6b7280;
    font-size: 0.8rem;
    line-height: 1.4;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

/* ── Card footer ────────────────────────── */
.card-footer {
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 0.5rem;
    margin-top: auto;
    padding-top: 0.75rem;
    border-top: 1px solid #f3f4f6;
}
.footer-left {
    display: flex;
    flex-direction: column;
    gap: 0.3rem;
}
.date-labels {
    display: flex;
    flex-direction: row;
    align-items: center;
    gap: 0.35rem;
    flex-wrap: wrap;
}
.encarregat-label {
    display: flex;
    align-items: center;
    gap: 0.25rem;
    font-size: 0.72rem;
    color: #b0b9c6;
    cursor: default;
}
.date-label {
    display: flex;
    align-items: center;
    gap: 0.25rem;
    font-size: 0.75rem;
    color: #9ca3af;
    cursor: default;
}
.date-sep {
    font-size: 0.75rem;
    color: #d1d5db;
    line-height: 1;
}
.action-buttons {
    display: flex;
    gap: 0.4rem;
}
.btn {
    display: flex;
    align-items: center;
    gap: 0.3rem;
    padding: 0.4rem 0.85rem;
    border-radius: 7px;
    font-family: inherit;
    font-size: 0.8rem;
    font-weight: 600;
    cursor: pointer;
    border: none;
    transition: all 0.15s;
}
.btn:disabled {
    opacity: 0.6;
    cursor: not-allowed;
}
.btn-accept {
    background: #d1fae5;
    color: #065f46;
}
.btn-accept:hover:not(:disabled) {
    background: #a7f3d0;
}
.btn-reject {
    background: #fee2e2;
    color: #991b1b;
}
.btn-reject:hover:not(:disabled) {
    background: #fecaca;
}

/* ── Skeleton ───────────────────────────── */
.petition-skeleton {
    background: white;
    border: 1px solid #e5e7eb;
    border-radius: 12px;
    padding: 1.25rem 1.5rem;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02);
}
.skeleton-top {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 0.75rem;
}
.skeleton-footer {
    margin-top: 1rem;
    padding-top: 0.75rem;
    border-top: 1px solid #f3f4f6;
}
.mt-1 {
    margin-top: 0.35rem;
}
.mt-2 {
    margin-top: 0.6rem;
}

/* ── Empty state ────────────────────────── */
.empty-state {
    text-align: center;
    padding: 4rem;
    color: #9ca3af;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 0.75rem;
    font-size: 0.9rem;
}
</style>
