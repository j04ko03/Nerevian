<template>
    <AppLayout>
        <Header title="Les meves Operacions" subtitle="Seguiment i estat dels teus enviaments en curs." />

        <div class="orders-grid mt-4">
            <div v-for="op in operacions" :key="op.id" class="order-card">
                <div class="order-header">
                    <h3 class="ref">{{ op.ref }}</h3>
                    <span class="badge" :class="op.statusClass">{{ op.statusText }}</span>
                </div>

                <div class="order-route">
                    <div class="location">
                        <i class="pi pi-map-marker origin-icon"></i>
                        <span>{{ op.origen }}</span>
                    </div>
                    <div class="route-line">
                        <div class="line animated"></div>
                        <i class="pi pi-truck icon-transit"></i>
                    </div>
                    <div class="location">
                        <i class="pi pi-flag dest-icon"></i>
                        <span>{{ op.desti }}</span>
                    </div>
                </div>

                <div class="order-footer">
                    <span class="date">Data estimada: <strong>{{ op.data }}</strong></span>
                    <button class="btn-tracking" @click="anarAlTracking(op.id)">
                        Veure Tracking Complet
                    </button>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { ref } from 'vue';
import { useRouter } from 'vue-router';
import AppLayout from '@/layout/AppLayout.vue';
import Header from '@/layout/Header.vue';

const router = useRouter();

const operacions = ref([
    { id: 1, ref: 'OP-2024-0892', origen: 'Madrid (ES)', desti: 'París (FR)', data: '12 Nov 2024', statusText: 'En Trànsit', statusClass: 'badge-blue' },
    { id: 2, ref: 'OP-2024-0895', origen: 'Barcelona (ES)', desti: 'Milà (IT)', data: '15 Nov 2024', statusText: 'Pendent Recollida', statusClass: 'badge-yellow' },
]);

const anarAlTracking = (id) => {
    // Navegamos a la nueva ruta pasándole el ID de la operación
    router.push(`/client/operacions/${id}/tracking`);
};
</script>

<style scoped>
.mt-4 {
    margin-top: 1.5rem;
}

.orders-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(380px, 1fr));
    gap: 1.5rem;
}

.order-card {
    background: white;
    border-radius: 12px;
    padding: 1.5rem;
    border: 1px solid #e5e7eb;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.02);
    display: flex;
    flex-direction: column;
    gap: 1.25rem;
    transition: transform 0.2s;
}

.order-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 6px 12px rgba(0, 0, 0, 0.05);
}

.order-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.ref {
    font-size: 1.2rem;
    font-weight: 700;
    color: #111827;
    margin: 0;
}

.badge {
    padding: 0.35rem 0.8rem;
    border-radius: 999px;
    font-size: 0.75rem;
    font-weight: 600;
}

.badge-blue {
    background: #dbeafe;
    color: #2563eb;
}

.badge-yellow {
    background: #fef3c7;
    color: #d97706;
}

.order-route {
    display: flex;
    align-items: center;
    justify-content: space-between;
    background: #f9fafb;
    padding: 1.5rem 1rem;
    border-radius: 8px;
    position: relative;
}

.location {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 0.5rem;
    font-weight: 600;
    color: #374151;
    font-size: 0.9rem;
    z-index: 2;
    background: #f9fafb;
    padding: 0 0.5rem;
}

.origin-icon {
    color: #10b981;
    font-size: 1.2rem;
}

.dest-icon {
    color: #ef4444;
    font-size: 1.2rem;
}

.route-line {
    flex: 1;
    display: flex;
    align-items: center;
    justify-content: center;
    position: relative;
    margin: 0 1rem;
}

.line {
    width: 100%;
    height: 2px;
    background: #e5e7eb;
    position: absolute;
    z-index: 1;
}

.line.animated {
    background: linear-gradient(90deg, #3b82f6 50%, #e5e7eb 50%);
    background-size: 200% 100%;
    animation: move 2s linear infinite;
}

.icon-transit {
    background: white;
    border: 2px solid #3b82f6;
    color: #3b82f6;
    border-radius: 50%;
    padding: 0.3rem;
    font-size: 0.9rem;
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

.order-footer {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding-top: 1rem;
    border-top: 1px solid #e5e7eb;
}

.date {
    font-size: 0.85rem;
    color: #6b7280;
}

.date strong {
    color: #111827;
}

.btn-tracking {
    background: #1a8a7d;
    color: white;
    border: none;
    padding: 0.6rem 1.2rem;
    border-radius: 8px;
    font-weight: 600;
    cursor: pointer;
    transition: background 0.2s;
}

.btn-tracking:hover {
    background: #136a60;
}
</style>