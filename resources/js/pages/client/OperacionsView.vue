<template>
    <AppLayout>
        <Header
            title="Les meves Operacions"
            subtitle="Consulta i fes el seguiment de tots els enviaments en curs."
        />

        <div v-if="operacions.length === 0" class="empty-state mt-4">
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
                        Entrega est. <strong>{{ op.data }}</strong>
                    </div>
                    <button class="btn-tracking" @click="anarAlTracking(op.id)">
                        Veure Tracking
                        <ArrowRight :size="13" />
                    </button>
                </div>

            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { ref } from 'vue';
import { useRouter } from 'vue-router';
import { Package, Truck, MapPin, Flag, Calendar, ArrowRight } from 'lucide-vue-next';
import AppLayout from '@/layout/AppLayout.vue';
import Header from '@/layout/Header.vue';

const router = useRouter();

const operacions = ref([
    { id: 1, ref: 'OP-2024-0892', transport: 'Marítim', origen: 'Madrid (ES)', desti: 'París (FR)', data: '12 Nov 2024', statusText: 'En Trànsit', statusClass: 'badge-blue' },
    { id: 2, ref: 'OP-2024-0895', transport: 'Terrestre', origen: 'Barcelona (ES)', desti: 'Milà (IT)', data: '15 Nov 2024', statusText: 'Pendent Recollida', statusClass: 'badge-yellow' },
]);

const anarAlTracking = (id) => {
    router.push(`/client/operacions/${id}/tracking`);
};
</script>

<style scoped>
.mt-4 {
    margin-top: 1.5rem;
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
    0%   { background-position: 100% 0; }
    100% { background-position: -100% 0; }
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