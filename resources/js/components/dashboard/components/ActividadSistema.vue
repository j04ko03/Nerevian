<template>
    <div class="card">
        <div class="card-header">
            <div class="card-title-row">
                <h2 class="card-title">
                    <Radio :size="15" class="title-icon" />
                    Monitor d'activitat
                </h2>
                <span class="live-badge">
                    <span class="live-dot" />
                    En viu
                </span>
            </div>
        </div>

        <div v-if="actividad && actividad.length > 0" class="feed">
            <div
                v-for="(item, i) in actividad"
                :key="i"
                class="feed-item"
            >
                <!-- Icon + connector -->
                <div class="feed-left">
                    <div class="feed-icon" :style="iconStyle(item.entidad)">
                        <component :is="entityIcon(item.entidad)" :size="14" />
                    </div>
                    <div v-if="i < actividad.length - 1" class="feed-line" />
                </div>

                <!-- Content -->
                <div class="feed-content">
                    <div class="feed-top">
                        <div class="feed-meta">
                            <span class="feed-entity">{{ item.entidad }}</span>
                            <span class="feed-registro">{{ item.registro }}</span>
                        </div>
                        <span
                            class="action-badge"
                            :class="item.accion === 'Nou' ? 'action-badge--new' : 'action-badge--update'"
                        >
                            {{ item.accion }}
                        </span>
                    </div>
                    <div class="feed-bottom">
                        <span class="feed-encargada">
                            <UserCircle :size="11" />
                            {{ item.encargada }}
                        </span>
                        <span class="feed-date">{{ item.fecha }}</span>
                    </div>
                </div>
            </div>
        </div>

        <div v-else class="empty-state">
            <Activity :size="28" />
            <p>No hi ha activitat recent del sistema</p>
        </div>
    </div>
</template>

<script setup>
import { Anchor, ScrollText, Globe, MapPin, UserCircle, Activity, Radio } from 'lucide-vue-next';

defineProps({
    actividad: {
        type: Array,
        default: () => [],
    },
});

const entityMap = {
    Puerto:   { icon: Anchor,     bg: '#eff6ff', color: '#3b82f6' },
    Incoterm: { icon: ScrollText, bg: '#f0fdf4', color: '#1a8a7d' },
    País:     { icon: Globe,      bg: '#ecfdf5', color: '#10b981' },
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
    display: flex;
    flex-direction: column;
    transition: box-shadow 0.25s ease;
}

.card:hover {
    box-shadow: 0 8px 24px -8px rgba(0, 0, 0, 0.1);
}

/* ── Header ──────────────────────────────── */
.card-header {
    padding: 1.25rem 1.5rem 1rem;
    border-bottom: 1px solid #f3f4f6;
}

.card-title-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.card-title {
    display: flex;
    align-items: center;
    gap: 0.45rem;
    font-size: 0.9rem;
    font-weight: 700;
    color: #111827;
    margin: 0;
    letter-spacing: -0.1px;
}

.title-icon {
    color: #111827;
    flex-shrink: 0;
}

.live-badge {
    display: flex;
    align-items: center;
    gap: 0.35rem;
    font-size: 0.7rem;
    font-weight: 600;
    color: #059669;
    background: #f0fdf4;
    border: 1px solid #bbf7d0;
    padding: 0.2rem 0.55rem;
    border-radius: 20px;
}

.live-dot {
    width: 6px;
    height: 6px;
    background: #22c55e;
    border-radius: 50%;
    animation: pulse-dot 2s ease-in-out infinite;
    box-shadow: 0 0 0 0 rgba(34, 197, 94, 0.4);
}

@keyframes pulse-dot {
    0%, 100% { box-shadow: 0 0 0 0 rgba(34, 197, 94, 0.4); }
    50%       { box-shadow: 0 0 0 4px rgba(34, 197, 94, 0); }
}

/* ── Feed ────────────────────────────────── */
.feed {
    padding: 0.75rem 1.5rem;
    flex: 1;
}

.feed-item {
    display: flex;
    gap: 0.85rem;
}

.feed-left {
    display: flex;
    flex-direction: column;
    align-items: center;
    flex-shrink: 0;
}

.feed-icon {
    width: 32px;
    height: 32px;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}

.feed-line {
    width: 1.5px;
    flex: 1;
    min-height: 0.75rem;
    background: linear-gradient(to bottom, #e5e7eb, transparent);
    margin: 4px 0;
}

.feed-content {
    flex: 1;
    padding: 0.3rem 0 0.9rem;
    min-width: 0;
}

.feed-top {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    gap: 0.5rem;
    margin-bottom: 0.3rem;
}

.feed-meta {
    display: flex;
    flex-direction: column;
    gap: 0.1rem;
    min-width: 0;
}

.feed-entity {
    font-size: 0.82rem;
    font-weight: 700;
    color: #111827;
}

.feed-registro {
    font-size: 0.775rem;
    color: #374151;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

/* ── Action badge ────────────────────────── */
.action-badge {
    font-size: 0.65rem;
    font-weight: 700;
    padding: 0.2rem 0.5rem;
    border-radius: 20px;
    white-space: nowrap;
    flex-shrink: 0;
    letter-spacing: 0.2px;
    text-transform: uppercase;
}

.action-badge--new {
    background: #dcfce7;
    color: #15803d;
    border: 1px solid #bbf7d0;
}

.action-badge--update {
    background: #eff6ff;
    color: #1d4ed8;
    border: 1px solid #bfdbfe;
}

/* ── Bottom row ──────────────────────────── */
.feed-bottom {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 0.5rem;
}

.feed-encargada {
    display: flex;
    align-items: center;
    gap: 0.25rem;
    font-size: 0.7rem;
    color: #9ca3af;
}

.feed-date {
    font-size: 0.7rem;
    color: #d1d5db;
}

/* ── Empty ───────────────────────────────── */
.empty-state {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 0.6rem;
    padding: 3rem 0;
    color: #d1d5db;
    font-size: 0.85rem;
}
</style>