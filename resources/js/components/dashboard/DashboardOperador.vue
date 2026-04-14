<template>
    <AppLayout>
        <Header
            title="Dashboard Operadora"
            :subtitle="`Benvinguda, ${user?.nom}`"
        />

        <!-- Stats -->
        <StatsGrid :columns="3">
            <StatCard
                label="Cotitzacions fredes"
                :value="displayCounts.frides"
                compare="requereixen atenció"
                :color="critiques > 0 ? 'red' : 'yellow'"
                :loading="loading"
            >
                <template #icon><Thermometer :size="18" /></template>
            </StatCard>
            <StatCard
                label="Activitat recent"
                :value="displayCounts.recent"
                compare="darreres gestionades"
                color="green"
                :loading="loading"
            >
                <template #icon><Activity :size="18" /></template>
            </StatCard>
            <StatCard
                label="Crítiques"
                :value="displayCounts.critiques"
                compare="+7 dies sense canvis"
                :color="critiques > 0 ? 'red' : 'default'"
                :loading="loading"
            >
                <template #icon><AlertOctagon :size="18" /></template>
            </StatCard>
        </StatsGrid>

        <!-- Skeleton -->
        <div v-if="loading" class="dashboard">
            <div class="card-skeleton">
                <div class="skeleton-header">
                    <Skeleton width="55%" height="0.9rem" borderRadius="6px" />
                    <Skeleton width="3rem" height="1.5rem" borderRadius="20px" />
                </div>
                <div class="skeleton-list">
                    <div v-for="i in 4" :key="i" class="skeleton-urgency-item">
                        <Skeleton width="2.4rem" height="2.4rem" borderRadius="10px" />
                        <div class="skeleton-urgency-content">
                            <div class="skeleton-urgency-top">
                                <Skeleton width="50%" height="0.8rem" borderRadius="6px" />
                                <Skeleton width="3.5rem" height="1.4rem" borderRadius="20px" />
                            </div>
                            <Skeleton width="75%" height="0.7rem" borderRadius="6px" />
                            <Skeleton width="100%" height="4px" borderRadius="4px" class="mt-1" />
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-skeleton">
                <div class="skeleton-header">
                    <Skeleton width="45%" height="0.9rem" borderRadius="6px" />
                </div>
                <div class="skeleton-list">
                    <div v-for="i in 4" :key="i" class="skeleton-feed-item">
                        <div class="skeleton-feed-left">
                            <Skeleton shape="circle" width="2rem" height="2rem" />
                            <div v-if="i < 4" class="skeleton-feed-line" />
                        </div>
                        <div class="skeleton-feed-content">
                            <Skeleton width="55%" height="0.8rem" borderRadius="6px" />
                            <Skeleton width="35%" height="0.7rem" borderRadius="6px" class="mt-1" />
                        </div>
                        <Skeleton width="4rem" height="1.4rem" borderRadius="20px" />
                    </div>
                </div>
            </div>
        </div>

        <!-- Content -->
        <div v-else class="dashboard">

            <!-- ── Urgency queue ───────────────────── -->
            <div class="card">
                <div class="card-header">
                    <div class="card-title">
                        <Thermometer :size="15" />
                        Cua d'urgència
                    </div>
                    <span v-if="critiques > 0" class="alert-pill">
                        <AlertOctagon :size="11" />
                        {{ critiques }} crítiques
                    </span>
                    <span v-else class="count-pill">{{ cotizacionesFrias.length }}</span>
                </div>

                <div v-if="cotizacionesFrias.length === 0" class="empty-state">
                    <CheckCircle :size="30" class="empty-icon--ok" />
                    <p>Tot al dia · sense cotitzacions pendents</p>
                </div>

                <div v-else class="urgency-list">
                    <div
                        v-for="item in sortedFrides"
                        :key="item.id"
                        class="urgency-item"
                    >
                        <!-- Days meter -->
                        <div class="days-meter" :class="urgencyClass(item.dias_sin_cambios)">
                            <span class="days-number">{{ item.dias_sin_cambios }}</span>
                            <span class="days-unit">d</span>
                        </div>

                        <!-- Info + bar -->
                        <div class="urgency-body">
                            <div class="urgency-top">
                                <div class="urgency-info">
                                    <span class="urgency-code">{{ item.codigo }}</span>
                                    <span class="urgency-route">{{ item.ruta }}</span>
                                </div>
                                <span class="status-pill" :class="statusClass(item.estado)">
                                    {{ item.estado }}
                                </span>
                            </div>
                            <!-- Urgency bar -->
                            <div class="urgency-track">
                                <div
                                    class="urgency-bar"
                                    :class="urgencyClass(item.dias_sin_cambios)"
                                    :style="{ width: urgencyPct(item.dias_sin_cambios) }"
                                />
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ── Activity feed ──────────────────── -->
            <div class="card">
                <div class="card-header">
                    <div class="card-title">
                        <Activity :size="15" />
                        La meva activitat recent
                    </div>
                    <span class="count-pill">{{ actividadRecienteMia.length }}</span>
                </div>

                <div v-if="actividadRecienteMia.length === 0" class="empty-state">
                    <ClipboardList :size="30" class="empty-icon" />
                    <p>Sense activitat recent registrada</p>
                </div>

                <div v-else class="feed">
                    <div
                        v-for="(item, i) in actividadRecienteMia"
                        :key="item.id"
                        class="feed-item"
                    >
                        <div class="feed-left">
                            <div class="feed-dot" :class="statusClass(item.estado_actual)" />
                            <div v-if="i < actividadRecienteMia.length - 1" class="feed-line" />
                        </div>
                        <div class="feed-body">
                            <div class="feed-top">
                                <span class="feed-code">{{ item.codigo }}</span>
                                <span class="status-pill" :class="statusClass(item.estado_actual)">
                                    {{ item.estado_actual }}
                                </span>
                            </div>
                            <span class="feed-time">{{ item.tiempo_transcurrido }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import {
    Thermometer, Activity, AlertOctagon, ClipboardList, CheckCircle,
} from 'lucide-vue-next';
import Skeleton from 'primevue/skeleton';
import AppLayout from '@/layout/AppLayout.vue';
import Header from '@/layout/Header.vue';
import { useAuthStore } from '@/stores/authStore';
import StatsGrid from '@/components/dashboard/components/StatsGrid.vue';
import StatCard from '@/components/dashboard/components/StatCard.vue';
import api from '@/plugins/axios';

const { user } = useAuthStore();

const loading   = ref(true);
const cotizacionesFrias   = ref([]);
const actividadRecienteMia = ref([]);

// Count-up
const displayCounts = ref({ frides: 0, recent: 0, critiques: 0 });

function countUp(key, target, duration = 900) {
    const start = Date.now();
    const tick = () => {
        const p = Math.min((Date.now() - start) / duration, 1);
        displayCounts.value[key] = Math.round((1 - Math.pow(1 - p, 3)) * target);
        if (p < 1) requestAnimationFrame(tick);
    };
    requestAnimationFrame(tick);
}

const critiques = computed(() =>
    cotizacionesFrias.value.filter(i => i.dias_sin_cambios >= 7).length
);

const sortedFrides = computed(() =>
    [...cotizacionesFrias.value].sort((a, b) => b.dias_sin_cambios - a.dias_sin_cambios)
);

// Urgency helpers — max scale 10 days = 100%
function urgencyPct(days) {
    return Math.min((days / 10) * 100, 100) + '%';
}

function urgencyClass(days) {
    if (days >= 7) return 'urgency--critical';
    if (days >= 4) return 'urgency--high';
    return 'urgency--low';
}

const statusMap = {
    'Cotizada':       'status--blue',
    'En Negociación': 'status--amber',
    'Nueva':          'status--green',
    'En Revisión':    'status--purple',
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
            cotizacionesFrias.value    = data.cotizacionesFrias    ?? [];
            actividadRecienteMia.value = data.actividadRecienteMia ?? [];

            const crit = cotizacionesFrias.value.filter(i => i.dias_sin_cambios >= 7).length;
            countUp('frides',    cotizacionesFrias.value.length);
            countUp('recent',    actividadRecienteMia.value.length, 700);
            countUp('critiques', crit, 1100);
        }
    } catch (e) {
        console.error('Error cargando dashboard operador', e);
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

.alert-pill {
    display: flex;
    align-items: center;
    gap: 0.3rem;
    font-size: 0.72rem;
    font-weight: 700;
    background: #fff1f2;
    color: #be123c;
    border: 1px solid #fecdd3;
    padding: 0.2rem 0.55rem;
    border-radius: 20px;
    animation: alert-pulse 2.5s ease-in-out infinite;
}

@keyframes alert-pulse {
    0%, 100% { opacity: 1; }
    50%       { opacity: 0.65; }
}

/* ── Urgency queue ───────────────────────── */
.urgency-list {
    display: flex;
    flex-direction: column;
    gap: 0.7rem;
}

.urgency-item {
    display: flex;
    align-items: flex-start;
    gap: 0.75rem;
    padding: 0.65rem 0.8rem;
    border-radius: 10px;
    border: 1px solid #f3f4f6;
    background: #fafafa;
    transition: background 0.15s, box-shadow 0.15s;
}

.urgency-item:hover {
    background: #f9fafb;
    box-shadow: 0 2px 8px -2px rgba(0,0,0,0.06);
}

/* Days meter box */
.days-meter {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    width: 40px;
    height: 40px;
    border-radius: 10px;
    flex-shrink: 0;
    font-weight: 800;
    line-height: 1;
}

.days-number { font-size: 1.05rem; }
.days-unit   { font-size: 0.6rem; font-weight: 600; opacity: 0.75; }

.days-meter.urgency--low      { background: #f0fdf4; color: #16a34a; }
.days-meter.urgency--high     { background: #fffbeb; color: #d97706; }
.days-meter.urgency--critical { background: #fff1f2; color: #dc2626; }

/* Body */
.urgency-body {
    flex: 1;
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
    min-width: 0;
}

.urgency-top {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    gap: 0.5rem;
}

.urgency-info {
    display: flex;
    flex-direction: column;
    gap: 0.15rem;
    min-width: 0;
}

.urgency-code {
    font-size: 0.8rem;
    font-weight: 700;
    color: #111827;
}

.urgency-route {
    font-size: 0.73rem;
    color: #6b7280;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

/* Progress bar */
.urgency-track {
    height: 4px;
    background: #f3f4f6;
    border-radius: 4px;
    overflow: hidden;
}

.urgency-bar {
    height: 100%;
    border-radius: 4px;
    transition: width 0.8s cubic-bezier(0.16, 1, 0.3, 1);
}

.urgency-bar.urgency--low      { background: linear-gradient(to right, #86efac, #22c55e); }
.urgency-bar.urgency--high     { background: linear-gradient(to right, #fcd34d, #f59e0b); }
.urgency-bar.urgency--critical { background: linear-gradient(to right, #fca5a5, #ef4444); }

/* ── Activity feed ───────────────────────── */
.feed {
    display: flex;
    flex-direction: column;
}

.feed-item {
    display: flex;
    align-items: flex-start;
    gap: 0.85rem;
}

.feed-left {
    display: flex;
    flex-direction: column;
    align-items: center;
    flex-shrink: 0;
    padding-top: 0.3rem;
}

.feed-dot {
    width: 10px;
    height: 10px;
    border-radius: 50%;
    flex-shrink: 0;
    border: 2px solid white;
    box-shadow: 0 0 0 1.5px currentColor;
}

.feed-line {
    width: 1.5px;
    flex: 1;
    min-height: 1.5rem;
    background: linear-gradient(to bottom, #e5e7eb, transparent);
    margin: 4px 0;
}

.feed-body {
    flex: 1;
    display: flex;
    flex-direction: column;
    gap: 0.2rem;
    padding: 0.2rem 0 0.9rem;
    min-width: 0;
}

.feed-top {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 0.5rem;
}

.feed-code {
    font-size: 0.83rem;
    font-weight: 700;
    color: #111827;
}

.feed-time {
    font-size: 0.72rem;
    color: #9ca3af;
}

/* ── Status pills ────────────────────────── */
.status-pill {
    font-size: 0.68rem;
    font-weight: 600;
    padding: 0.18rem 0.5rem;
    border-radius: 20px;
    white-space: nowrap;
    flex-shrink: 0;
}

.status--blue   { background: #eff6ff; color: #1e40af; }
.status--amber  { background: #fffbeb; color: #92400e; }
.status--green  { background: #f0fdf4; color: #166534; }
.status--purple { background: #f5f3ff; color: #5b21b6; }
.status--red    { background: #fff1f2; color: #9f1239; }
.status--gray   { background: #f3f4f6; color: #374151; }

/* feed dot colors */
.feed-dot.status--blue   { color: #3b82f6; }
.feed-dot.status--amber  { color: #f59e0b; }
.feed-dot.status--green  { color: #22c55e; }
.feed-dot.status--purple { color: #8b5cf6; }
.feed-dot.status--red    { color: #ef4444; }
.feed-dot.status--gray   { color: #9ca3af; }

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

.empty-icon     { color: #d1d5db; }
.empty-icon--ok { color: #6ee7b7; }

/* ── Skeleton ────────────────────────────── */
.card-skeleton {
    background: white;
    border: 1px solid #e5e7eb;
    border-radius: 14px;
    padding: 1.25rem 1.5rem;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02);
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
    gap: 0.8rem;
}

.skeleton-urgency-item {
    display: flex;
    gap: 0.75rem;
    align-items: flex-start;
}

.skeleton-urgency-content {
    flex: 1;
    display: flex;
    flex-direction: column;
    gap: 0.35rem;
}

.skeleton-urgency-top {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 0.5rem;
}

.skeleton-feed-item {
    display: flex;
    align-items: center;
    gap: 0.75rem;
}

.skeleton-feed-left {
    display: flex;
    flex-direction: column;
    align-items: center;
    flex-shrink: 0;
}

.skeleton-feed-line {
    width: 2px;
    height: 1.2rem;
    background: #f3f4f6;
    margin: 3px 0;
    border-radius: 2px;
}

.skeleton-feed-content {
    flex: 1;
    display: flex;
    flex-direction: column;
    gap: 0.3rem;
}

.mt-1 { margin-top: 0.25rem; }
</style>