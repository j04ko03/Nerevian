<template>
    <AppLayout>
        <Header
            title="Dashboard Clienta"
            :subtitle="`Benvinguda, ${user?.nom}`"
        />

        <!-- Stats -->
        <StatsGrid :columns="3">
            <StatCard
                label="Sol·licituds enviades"
                :value="displayStats.enviades"
                compare="en revisió"
                color="yellow"
                :loading="loading"
            >
                <template #icon><Send :size="18" /></template>
            </StatCard>
            <StatCard
                label="Pedides actives"
                :value="displayStats.actives"
                compare="en curs"
                color="green"
                :loading="loading"
            >
                <template #icon><Package :size="18" /></template>
            </StatCard>
            <StatCard
                label="Docs per revisar"
                :value="displayStats.docs"
                compare="pendents de revisió"
                :loading="loading"
            >
                <template #icon><FileText :size="18" /></template>
            </StatCard>
        </StatsGrid>

        <!-- Skeleton -->
        <div v-if="loading" class="dashboard">
            <div class="card-skeleton">
                <div class="skeleton-header">
                    <Skeleton width="50%" height="0.9rem" borderRadius="6px" />
                    <Skeleton width="2.5rem" height="1.5rem" borderRadius="20px" />
                </div>
                <div class="skeleton-list">
                    <div v-for="i in 3" :key="i" class="skeleton-journey">
                        <div class="skeleton-journey-top">
                            <Skeleton width="30%" height="0.8rem" borderRadius="6px" />
                            <Skeleton width="3.5rem" height="1.4rem" borderRadius="20px" />
                        </div>
                        <div class="skeleton-route">
                            <Skeleton shape="circle" width="0.65rem" height="0.65rem" />
                            <Skeleton width="100%" height="3px" borderRadius="4px" />
                            <Skeleton shape="circle" width="0.65rem" height="0.65rem" />
                        </div>
                        <div class="skeleton-ports">
                            <Skeleton width="35%" height="0.7rem" borderRadius="6px" />
                            <Skeleton width="35%" height="0.7rem" borderRadius="6px" />
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-skeleton">
                <div class="skeleton-header">
                    <Skeleton width="45%" height="0.9rem" borderRadius="6px" />
                </div>
                <div class="skeleton-list">
                    <div v-for="i in 4" :key="i" class="skeleton-pipeline-item">
                        <div class="skeleton-pipeline-top">
                            <Skeleton width="40%" height="0.8rem" borderRadius="6px" />
                            <Skeleton width="2.5rem" height="0.7rem" borderRadius="6px" />
                        </div>
                        <div class="skeleton-steps">
                            <div v-for="s in 5" :key="s" class="skeleton-step">
                                <Skeleton shape="circle" width="1.5rem" height="1.5rem" />
                                <Skeleton v-if="s < 5" width="100%" height="2px" borderRadius="2px" />
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Content -->
        <div v-else class="dashboard">

            <!-- ── Journey cards ──────────────────── -->
            <div class="card">
                <div class="card-header">
                    <div class="card-title">
                        <Truck :size="15" />
                        Pedides en trànsit
                    </div>
                    <span class="count-pill">{{ pedidosEnTransito.length }}</span>
                </div>

                <div v-if="pedidosEnTransito.length === 0" class="empty-state">
                    <Package :size="30" class="empty-icon" />
                    <p>Sense pedides en trànsit</p>
                </div>

                <div v-else class="journey-list">
                    <div
                        v-for="item in pedidosEnTransito"
                        :key="item.id"
                        class="journey-card"
                    >
                        <!-- Top row -->
                        <div class="journey-top">
                            <span class="journey-code">{{ item.codigo }}</span>
                            <span class="status-pill" :class="statusClass(item.estado_humano)">
                                {{ item.estado_humano }}
                            </span>
                        </div>

                        <!-- Route visualization -->
                        <div class="route-visual">
                            <div class="route-port route-port--origin">
                                <div class="port-dot" />
                                <span class="port-name">{{ routeParts(item.ruta).origin }}</span>
                            </div>

                            <div class="route-track">
                                <div class="route-line" />
                                <div
                                    class="route-traveller"
                                    :style="{ left: travellerPos(item.estado_id) }"
                                >
                                    <Plane :size="12" />
                                </div>
                            </div>

                            <div class="route-port route-port--dest">
                                <div class="port-dot" />
                                <span class="port-name">{{ routeParts(item.ruta).dest }}</span>
                            </div>
                        </div>

                        <!-- Footer -->
                        <div class="journey-footer">
                            <span class="journey-date" v-if="item.fecha_inicio !== 'Pendiente'">
                                <CalendarDays :size="11" />
                                {{ item.fecha_inicio }}
                            </span>
                            <span class="journey-date journey-date--pending" v-else>
                                Inici pendent
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ── Status pipeline ────────────────── -->
            <div class="card">
                <div class="card-header">
                    <div class="card-title">
                        <ClipboardList :size="15" />
                        Les meves sol·licituds
                    </div>
                    <span class="count-pill">{{ misSolicitudes.length }}</span>
                </div>

                <div v-if="misSolicitudes.length === 0" class="empty-state">
                    <ClipboardList :size="30" class="empty-icon" />
                    <p>Sense sol·licituds registrades</p>
                </div>

                <div v-else class="pipeline-list">
                    <div
                        v-for="item in misSolicitudes"
                        :key="item.id"
                        class="pipeline-item"
                    >
                        <div class="pipeline-top">
                            <div class="pipeline-info">
                                <span class="pipeline-code">SOL-{{ String(item.id).padStart(3, '0') }}</span>
                                <span class="pipeline-route">{{ item.ruta }}</span>
                            </div>
                            <span class="pipeline-step-label" :class="statusClass(item.sub_estado)">
                                {{ item.sub_estado }}
                            </span>
                        </div>

                        <!-- Step dots -->
                        <div class="pipeline-track">
                            <template v-for="(step, i) in pipelineSteps" :key="step.key">
                                <div
                                    class="pipeline-dot"
                                    :class="{
                                        'dot--done':    stepIndex(item.estado) > i,
                                        'dot--active':  stepIndex(item.estado) === i,
                                        'dot--pending': stepIndex(item.estado) < i,
                                    }"
                                    v-tooltip.top="step.label"
                                >
                                    <Check v-if="stepIndex(item.estado) > i" :size="8" />
                                </div>
                                <div
                                    v-if="i < pipelineSteps.length - 1"
                                    class="pipeline-connector"
                                    :class="stepIndex(item.estado) > i ? 'connector--done' : 'connector--pending'"
                                />
                            </template>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import {
    Send, Package, FileText, Truck, ClipboardList,
    CalendarDays, Plane, Check,
} from 'lucide-vue-next';
import Skeleton from 'primevue/skeleton';
import vTooltip from 'primevue/tooltip';
import AppLayout from '@/layout/AppLayout.vue';
import Header from '@/layout/Header.vue';
import { useAuthStore } from '@/stores/authStore';
import StatsGrid from '@/components/dashboard/components/StatsGrid.vue';
import StatCard from '@/components/dashboard/components/StatCard.vue';
import api from '@/plugins/axios';

const { user } = useAuthStore();

const loading           = ref(true);
const stats             = ref({ solicitudes_enviadas: 0, pedidos_activos: 0, docs_por_revisar: 0 });
const pedidosEnTransito = ref([]);
const misSolicitudes    = ref([]);
const displayStats      = ref({ enviades: 0, actives: 0, docs: 0 });

// Count-up
function countUp(key, target, duration = 900) {
    const start = Date.now();
    const tick = () => {
        const p = Math.min((Date.now() - start) / duration, 1);
        displayStats.value[key] = Math.round((1 - Math.pow(1 - p, 3)) * target);
        if (p < 1) requestAnimationFrame(tick);
    };
    requestAnimationFrame(tick);
}

// Route helpers
function routeParts(ruta) {
    const parts = (ruta ?? '').split(' → ');
    return { origin: parts[0] ?? 'N/A', dest: parts[1] ?? 'N/A' };
}

// Position of traveller icon based on estado_id (1–5 scale → 0–100%)
function travellerPos(estadoId) {
    const pct = Math.min(Math.max(((Number(estadoId) - 1) / 4) * 100, 2), 92);
    return `calc(${pct}% - 6px)`;
}

// Pipeline
const pipelineSteps = [
    { key: 1, label: 'Nova' },
    { key: 2, label: 'En Revisió' },
    { key: 3, label: 'Cotitzada' },
    { key: 4, label: 'En Negociació' },
    { key: 5, label: 'Tancada' },
];

function stepIndex(estadoId) {
    const idx = pipelineSteps.findIndex(s => s.key === Number(estadoId));
    return idx === -1 ? 0 : idx;
}

// Status pill
const statusMap = {
    'Nueva':          'status--green',
    'En Revisión':    'status--purple',
    'Cotizada':       'status--blue',
    'En Negociación': 'status--amber',
    'Rechazada':      'status--red',
    'Desconocido':    'status--gray',
};
function statusClass(estado) {
    return statusMap[estado] ?? 'status--gray';
}

onMounted(async () => {
    try {
        const { data } = await api.get('/dashboard');
        if (data.success) {
            stats.value             = data.stats             ?? stats.value;
            pedidosEnTransito.value = data.pedidos_en_transito ?? [];
            misSolicitudes.value    = data.mis_solicitudes    ?? [];

            countUp('enviades', data.stats.solicitudes_enviadas);
            countUp('actives',  data.stats.pedidos_activos,  700);
            countUp('docs',     data.stats.docs_por_revisar, 1100);
        }
    } catch (e) {
        console.error('Error cargando dashboard cliente', e);
    } finally {
        loading.value = false;
    }
});
</script>

<style scoped>
.dashboard {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 1.5rem;
}

/* ── Card shell ──────────────────────────── */
.card {
    background: white;
    border: 1px solid #e5e7eb;
    border-radius: 14px;
    padding: 1.25rem 1.5rem;
    box-shadow: 0 1px 4px rgba(0,0,0,0.03);
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

.count-pill {
    font-size: 0.72rem;
    font-weight: 700;
    background: #f3f4f6;
    color: #6b7280;
    padding: 0.15rem 0.55rem;
    border-radius: 20px;
}

/* ── Journey cards ───────────────────────── */
.journey-list {
    display: flex;
    flex-direction: column;
    gap: 0.85rem;
}

.journey-card {
    border: 1px solid #e5e7eb;
    border-radius: 12px;
    padding: 0.9rem 1rem;
    display: flex;
    flex-direction: column;
    gap: 0.7rem;
    transition: box-shadow 0.2s, transform 0.2s;
    background: #fafafa;
}

.journey-card:hover {
    box-shadow: 0 4px 16px -4px rgba(0,0,0,0.1);
    transform: translateY(-1px);
}

.journey-top {
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.journey-code {
    font-size: 0.8rem;
    font-weight: 700;
    color: #111827;
}

/* Route visualization */
.route-visual {
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.route-port {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 0.25rem;
    flex-shrink: 0;
    min-width: 4.5rem;
}

.route-port--dest { align-items: flex-end; }

.port-dot {
    width: 10px;
    height: 10px;
    border-radius: 50%;
    border: 2px solid #1a8a7d;
    background: white;
}

.port-name {
    font-size: 0.68rem;
    font-weight: 600;
    color: #374151;
    text-align: center;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    max-width: 4.5rem;
}

.route-track {
    flex: 1;
    position: relative;
    height: 10px;
    display: flex;
    align-items: center;
}

.route-line {
    position: absolute;
    left: 0;
    right: 0;
    height: 2px;
    background: repeating-linear-gradient(
        to right,
        #cbd5e1 0px,
        #cbd5e1 6px,
        transparent 6px,
        transparent 12px
    );
}

.route-traveller {
    position: absolute;
    color: #1a8a7d;
    display: flex;
    align-items: center;
    animation: fly 3s ease-in-out infinite;
}

@keyframes fly {
    0%, 100% { transform: translateY(0); }
    50%       { transform: translateY(-2px); }
}

.journey-footer {
    display: flex;
    align-items: center;
}

.journey-date {
    display: flex;
    align-items: center;
    gap: 0.3rem;
    font-size: 0.72rem;
    color: #9ca3af;
}

.journey-date--pending {
    font-style: italic;
    color: #d1d5db;
}

/* ── Pipeline ────────────────────────────── */
.pipeline-list {
    display: flex;
    flex-direction: column;
    gap: 0.9rem;
}

.pipeline-item {
    border: 1px solid #f3f4f6;
    border-radius: 10px;
    padding: 0.75rem 0.9rem;
    background: #fafafa;
    display: flex;
    flex-direction: column;
    gap: 0.65rem;
    transition: background 0.15s;
}

.pipeline-item:hover { background: #f3f4f6; }

.pipeline-top {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    gap: 0.5rem;
}

.pipeline-info {
    display: flex;
    flex-direction: column;
    gap: 0.15rem;
    min-width: 0;
}

.pipeline-code {
    font-size: 0.8rem;
    font-weight: 700;
    color: #111827;
}

.pipeline-route {
    font-size: 0.7rem;
    color: #9ca3af;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.pipeline-step-label {
    font-size: 0.68rem;
    font-weight: 600;
    padding: 0.18rem 0.5rem;
    border-radius: 20px;
    white-space: nowrap;
    flex-shrink: 0;
}

/* Steps track */
.pipeline-track {
    display: flex;
    align-items: center;
}

.pipeline-dot {
    width: 22px;
    height: 22px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    transition: background 0.3s, border-color 0.3s;
}

.dot--done {
    background: #1a8a7d;
    border: 2px solid #1a8a7d;
    color: white;
}

.dot--active {
    background: white;
    border: 2.5px solid #1a8a7d;
    box-shadow: 0 0 0 3px rgba(26, 138, 125, 0.15);
    animation: dot-pulse 2s ease-in-out infinite;
}

.dot--pending {
    background: white;
    border: 2px solid #e5e7eb;
}

@keyframes dot-pulse {
    0%, 100% { box-shadow: 0 0 0 3px rgba(26,138,125,0.15); }
    50%       { box-shadow: 0 0 0 5px rgba(26,138,125,0.05); }
}

.pipeline-connector {
    flex: 1;
    height: 2px;
    transition: background 0.3s;
}

.connector--done    { background: #1a8a7d; }
.connector--pending { background: #e5e7eb; }

/* ── Status pills ────────────────────────── */
.status-pill {
    font-size: 0.68rem;
    font-weight: 600;
    padding: 0.18rem 0.5rem;
    border-radius: 20px;
    white-space: nowrap;
    flex-shrink: 0;
}

.status--green  { background: #f0fdf4; color: #166534; }
.status--purple { background: #f5f3ff; color: #5b21b6; }
.status--blue   { background: #eff6ff; color: #1e40af; }
.status--amber  { background: #fffbeb; color: #92400e; }
.status--red    { background: #fff1f2; color: #9f1239; }
.status--gray   { background: #f3f4f6; color: #374151; }

/* ── Empty ───────────────────────────────── */
.empty-state {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 0.5rem;
    padding: 2.5rem 0;
    color: #9ca3af;
    font-size: 0.85rem;
}

.empty-icon { color: #d1d5db; }

/* ── Skeleton ────────────────────────────── */
.card-skeleton {
    background: white;
    border: 1px solid #e5e7eb;
    border-radius: 14px;
    padding: 1.25rem 1.5rem;
    box-shadow: 0 1px 3px rgba(0,0,0,0.02);
}

.skeleton-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 1.1rem;
    padding-bottom: 0.85rem;
    border-bottom: 1px solid #f3f4f6;
}

.skeleton-list {
    display: flex;
    flex-direction: column;
    gap: 0.9rem;
}

.skeleton-journey {
    border: 1px solid #f3f4f6;
    border-radius: 10px;
    padding: 0.85rem;
    display: flex;
    flex-direction: column;
    gap: 0.6rem;
}

.skeleton-journey-top {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 0.5rem;
}

.skeleton-route {
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.skeleton-ports {
    display: flex;
    justify-content: space-between;
}

.skeleton-pipeline-item {
    border: 1px solid #f3f4f6;
    border-radius: 10px;
    padding: 0.75rem 0.9rem;
    display: flex;
    flex-direction: column;
    gap: 0.6rem;
}

.skeleton-pipeline-top {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 0.5rem;
}

.skeleton-steps {
    display: flex;
    align-items: center;
    gap: 0;
}

.skeleton-step {
    display: flex;
    align-items: center;
    flex: 1;
    gap: 0;
}

.skeleton-step:last-child { flex: 0; }

.mt-1 { margin-top: 0.25rem; }
</style>