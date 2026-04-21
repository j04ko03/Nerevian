<template>
    <AppLayout>
        <Header title="Les meves Operacions" subtitle="Consulta i fes el seguiment de tots els enviaments en curs." />

        <div v-if="carregant" class="loading-state mt-4">
            <i class="pi pi-spin pi-spinner" style="font-size:2rem"></i>
            <p>Carregant operacions...</p>
        </div>

        <div v-else-if="operacions.length === 0" class="empty-state mt-4">
            <Package :size="40" class="empty-icon" />
            <h3>Cap operació activa</h3>
            <p>Quan tinguis enviaments en curs apareixeran aquí.</p>
        </div>

        <div v-else class="orders-grid mt-4">
            <div v-for="op in operacions" :key="op.id" class="order-card">

                <div class="order-header">
                    <div class="order-header__left">
                        <span class="ref">{{ op.ref }}</span>
                        <span class="transport-type">
                            <Truck :size="12" />
                            {{ op.transport }}
                        </span>
                    </div>
                    <span class="badge" :class="op.statusClass">{{ op.statusText }}</span>
                </div>

                <div class="order-route">
                    <div class="location">
                        <MapPin :size="16" class="origin-icon" />
                        <span>{{ op.origen }}</span>
                    </div>
                    <div class="route-line">
                        <div class="line animated"></div>
                        <div class="truck-wrapper">
                            <Truck :size="13" class="truck-icon" />
                        </div>
                    </div>
                    <div class="location">
                        <Flag :size="16" class="dest-icon" />
                        <span>{{ op.desti }}</span>
                    </div>
                </div>

                <div class="order-footer">
                    <div class="date">
                        <Calendar :size="13" />
                        Inici: <strong>{{ op.data }}</strong>
                    </div>
                    <button class="btn-tracking" @click="anarAlTracking(op.solicitudId)">
                        Veure Tracking
                        <ArrowRight :size="13" />
                    </button>
                </div>

            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import { Package, Truck, MapPin, Flag, Calendar, ArrowRight } from 'lucide-vue-next';
import AppLayout from '@/layout/AppLayout.vue';
import Header from '@/layout/Header.vue';
import api from '@/plugins/axios';

const router = useRouter();

const carregant = ref(true);
const operacions = ref([]);

// Mapeo de estado a clase CSS del badge
const STATUS_MAP = {
    1: { text: 'Pendent',           cls: 'badge-yellow' },
    2: { text: 'En Recollida',      cls: 'badge-yellow' },
    3: { text: 'En Trànsit',        cls: 'badge-blue'   },
    4: { text: 'En Duana',          cls: 'badge-blue'   },
    5: { text: 'Entregat',          cls: 'badge-green'  },
};

const formatPort = (port) => {
    if (!port) return '—';
    const ciutat = port.ciutat?.nom ? ` (${port.ciutat.nom})` : '';
    return `${port.nom}${ciutat}`;
};

onMounted(async () => {
    try {
        const { data } = await api.get('/operaciones');
        if (data.status === 'success') {
            operacions.value = (data.data ?? []).map(op => {
                const sol = op.solicitud;
                const estat = op.estat;
                const statusInfo = STATUS_MAP[op.estat_id] ?? { text: estat?.estat ?? '—', cls: 'badge-yellow' };

                return {
                    id: op.id,
                    solicitudId: sol?.id ?? null,
                    ref: op.codi_referencia ?? `OP-${String(op.id).padStart(4, '0')}`,
                    transport: sol?.tipus_transport?.tipus ?? '—',
                    origen: formatPort(sol?.port_origen),
                    desti: formatPort(sol?.port_desti),
                    data: op.data_inici
                        ? new Date(op.data_inici).toLocaleDateString('ca-ES', { day: '2-digit', month: 'short', year: 'numeric' })
                        : '—',
                    statusText: statusInfo.text,
                    statusClass: statusInfo.cls,
                };
            });
        }
    } catch (e) {
        console.error('Error carregant operacions', e);
    } finally {
        carregant.value = false;
    }
});

const anarAlTracking = (solicitudId) => {
    if (!solicitudId) {
        console.warn('Aquesta operació no té una sol·licitud vinculada.');
        return;
    }
    router.push(`/client/operacions/${solicitudId}/tracking`);
};
</script>

<style scoped>
.mt-4 {
    margin-top: 1.5rem;
}

/* ── Loading state ─────────────────────────────────────────── */
.loading-state {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: 4rem;
    color: #6b7280;
    gap: 1rem;
}

/* ── Empty state ────────────────────────────────────────────── */
.empty-state {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: 5rem 2rem;
    color: #9ca3af;
    gap: 0.75rem;
    text-align: center;
}

.empty-icon {
    color: #d1d5db;
}

.empty-state h3 {
    margin: 0;
    font-size: 1.1rem;
    font-weight: 600;
    color: #6b7280;
}

.empty-state p {
    margin: 0;
    font-size: 0.875rem;
}

/* ── Grid ───────────────────────────────────────────────────── */
.orders-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(380px, 1fr));
    gap: 1.25rem;
}

/* ── Card ───────────────────────────────────────────────────── */
.order-card {
    background: white;
    border-radius: 14px;
    padding: 1.4rem;
    border: 1px solid #e5e7eb;
    box-shadow: 0 1px 4px rgba(0, 0, 0, 0.04);
    display: flex;
    flex-direction: column;
    gap: 1.1rem;
    transition: box-shadow 0.2s, transform 0.2s;
}

.order-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(0, 0, 0, 0.07);
    border-color: #d1d5db;
}

/* ── Card header ────────────────────────────────────────────── */
.order-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
}

.order-header__left {
    display: flex;
    flex-direction: column;
    gap: 0.3rem;
}

.ref {
    font-size: 1rem;
    font-weight: 700;
    color: #111827;
    letter-spacing: -0.01em;
}

.transport-type {
    display: inline-flex;
    align-items: center;
    gap: 0.3rem;
    font-size: 0.72rem;
    font-weight: 500;
    color: #9ca3af;
}

.badge {
    padding: 0.3rem 0.75rem;
    border-radius: 999px;
    font-size: 0.72rem;
    font-weight: 600;
    white-space: nowrap;
}

.badge-blue {
    background: #dbeafe;
    color: #1d4ed8;
}

.badge-yellow {
    background: #fef3c7;
    color: #b45309;
}

.badge-green {
    background: #dcfce7;
    color: #15803d;
}

/* ── Route section ──────────────────────────────────────────── */
.order-route {
    display: flex;
    align-items: center;
    justify-content: space-between;
    background: #f9fafb;
    padding: 1.1rem 1rem;
    border-radius: 10px;
    gap: 0.5rem;
}

.location {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 0.4rem;
    font-weight: 600;
    color: #374151;
    font-size: 0.85rem;
    z-index: 2;
    background: #f9fafb;
    padding: 0 0.4rem;
    min-width: 0;
    text-align: center;
}

.origin-icon {
    color: #1a8a7d;
}

.dest-icon {
    color: #ef4444;
}

.route-line {
    flex: 1;
    display: flex;
    align-items: center;
    justify-content: center;
    position: relative;
    margin: 0 0.25rem;
}

.line {
    width: 100%;
    height: 2px;
    background: #e5e7eb;
    position: absolute;
    z-index: 1;
}

.line.animated {
    background: linear-gradient(90deg, #1a8a7d 50%, #e5e7eb 50%);
    background-size: 200% 100%;
    animation: move 2.5s linear infinite;
}

.truck-wrapper {
    background: white;
    border: 2px solid #1a8a7d;
    color: #1a8a7d;
    border-radius: 50%;
    width: 30px;
    height: 30px;
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 2;
    position: relative;
}

@keyframes move {
    0% {
        background-position: 100% 0;
    }

    100% {
        background-position: -100% 0;
    }
}

/* ── Card footer ────────────────────────────────────────────── */
.order-footer {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding-top: 1rem;
    border-top: 1px solid #f3f4f6;
}

.date {
    display: flex;
    align-items: center;
    gap: 0.4rem;
    font-size: 0.82rem;
    color: #6b7280;
}

.date strong {
    color: #374151;
}

.btn-tracking {
    display: inline-flex;
    align-items: center;
    gap: 0.4rem;
    background: #1a8a7d;
    color: white;
    border: none;
    padding: 0.5rem 1rem;
    border-radius: 8px;
    font-size: 0.82rem;
    font-weight: 600;
    cursor: pointer;
    transition: background 0.2s;
    font-family: inherit;
}

.btn-tracking:hover {
    background: #136a60;
}
</style>