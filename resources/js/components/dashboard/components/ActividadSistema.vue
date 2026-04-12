<template>
    <div class="card">
        <div class="card-header">
            <h2 class="card-title">Activitat del Sistema</h2>
        </div>
        <div class="card-body">
            <template v-if="actividad && actividad.length > 0">
                <div
                    v-for="(item, i) in actividad"
                    :key="i"
                    class="activity-item"
                >
                    <div class="activity-icon" :style="iconStyle(item.entidad)">
                        <component :is="entityIcon(item.entidad)" :size="14" />
                    </div>
                    <div class="activity-content">
                        <div class="activity-top">
                            <span class="activity-entity">{{ item.entidad }}</span>
                            <span class="activity-action">{{ item.accion }}</span>
                        </div>
                        <p class="activity-registro">{{ item.registro }}</p>
                        <p class="activity-date">{{ item.fecha }}</p>
                    </div>
                </div>
            </template>
            <div v-else class="empty-state">
                <p>No hi ha activitat del sistema per mostrar</p>
            </div>
        </div>
    </div>
</template>

<script setup>
import { Anchor, ScrollText, Globe, MapPin } from 'lucide-vue-next';

defineProps({
    actividad: {
        type: Array,
        default: () => [],
    },
});

const entityMap = {
    Puerto:   { icon: Anchor,     bg: '#eff6ff', color: '#3b82f6' },
    Incoterm: { icon: ScrollText, bg: '#f0fdf4', color: '#1a8a7d' },
    País:     { icon: Globe,      bg: '#f0fdf4', color: '#10b981' },
    Ciudad:   { icon: MapPin,     bg: '#fffbeb', color: '#f59e0b' },
};

const fallback = { icon: ScrollText, bg: '#f3f4f6', color: '#6b7280' };

function entityIcon(entidad) {
    return (entityMap[entidad] ?? fallback).icon;
}

function iconStyle(entidad) {
    const { bg, color } = entityMap[entidad] ?? fallback;
    return { backgroundColor: bg, color };
}
</script>

<style scoped>
.card {
    background: white;
    border: 1px solid #e5e7eb;
    border-radius: 12px;
    overflow: hidden;
    transition:
        transform 0.25s ease,
        box-shadow 0.25s ease;
}
.card:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 20px -8px rgba(0, 0, 0, 0.08);
}
.card-header {
    padding: 1.25rem 1.5rem 0;
}
.card-title {
    font-size: 0.95rem;
    font-weight: 600;
    color: #111827;
    margin: 0;
}
.card-body {
    padding: 1rem 1.5rem;
    display: flex;
    flex-direction: column;
    gap: 0.25rem;
}
.activity-item {
    display: flex;
    align-items: flex-start;
    gap: 0.75rem;
    padding: 0.65rem 0;
    border-bottom: 1px solid #f9fafb;
}
.activity-item:last-child {
    border-bottom: none;
}
.activity-icon {
    width: 28px;
    height: 28px;
    border-radius: 7px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    margin-top: 0.05rem;
}
.activity-content {
    flex: 1;
    min-width: 0;
}
.activity-top {
    display: flex;
    gap: 0.4rem;
    align-items: baseline;
}
.activity-entity {
    font-size: 0.825rem;
    font-weight: 600;
    color: #111827;
}
.activity-action {
    font-size: 0.775rem;
    color: #6b7280;
}
.activity-registro {
    font-size: 0.775rem;
    color: #374151;
    margin: 0.1rem 0 0;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
.activity-date {
    font-size: 0.72rem;
    color: #9ca3af;
    margin: 0.1rem 0 0;
}
.empty-state {
    padding: 2rem 0;
    text-align: center;
    color: #9ca3af;
    font-size: 0.85rem;
}
</style>