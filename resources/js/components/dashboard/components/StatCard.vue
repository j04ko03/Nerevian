<template>
    <div class="stat-card" :class="`stat-card--${color}`">
        <template v-if="loading">
            <div class="skeleton-header">
                <Skeleton width="55%" height="0.85rem" borderRadius="6px" />
                <Skeleton width="1.1rem" height="1.1rem" borderRadius="6px" />
            </div>
            <Skeleton width="40%" height="2rem" borderRadius="8px" class="skeleton-value" />
            <Skeleton width="70%" height="0.75rem" borderRadius="6px" />
        </template>

        <template v-else>
            <div class="stat-header">
                <span class="stat-label">{{ label }}</span>
                <div class="stat-icon">
                    <slot name="icon" />
                </div>
            </div>
            <div class="stat-value">{{ value }}</div>
            <div v-if="badge || compare" class="stat-footer">
                <span v-if="badge" class="stat-badge positive">{{ badge }}</span>
                <span v-if="compare" class="stat-compare">{{ compare }}</span>
            </div>
        </template>
    </div>
</template>

<script setup>
import Skeleton from 'primevue/skeleton';

defineProps({
    label:   String,
    value:   [String, Number],
    badge:   String,
    compare: String,
    loading: {
        type: Boolean,
        default: false,
    },
    color: {
        type: String,
        default: 'default', // 'default' | 'yellow' | 'green' | 'red'
    },
});
</script>

<style scoped>
.stat-card {
    background: white;
    border: 1px solid #e5e7eb;
    border-radius: 12px;
    padding: 1.25rem 1.5rem;
    transition:
        transform 0.25s cubic-bezier(0.165, 0.84, 0.44, 1),
        box-shadow 0.25s cubic-bezier(0.165, 0.84, 0.44, 1);
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02);
}
.stat-card:hover {
    transform: translateY(-4px);
    box-shadow:
        0 12px 24px -6px rgba(0, 0, 0, 0.08),
        0 4px 10px -4px rgba(0, 0, 0, 0.04);
}
.stat-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 0.75rem;
}
.stat-label {
    font-size: 0.8rem;
    color: #6b7280;
    font-weight: 500;
}
.stat-icon {
    color: #1a8a7d;
    opacity: 0.8;
}
.stat-value {
    font-size: 2rem;
    font-weight: 700;
    color: #111827;
    line-height: 1;
    margin-bottom: 0.6rem;
}
.stat-footer {
    display: flex;
    align-items: center;
    gap: 0.4rem;
}
.stat-badge {
    font-size: 0.75rem;
    font-weight: 600;
    padding: 0.15rem 0.4rem;
    border-radius: 4px;
}
.stat-badge.positive {
    background: #d1fae5;
    color: #059669;
}
.stat-compare {
    font-size: 0.75rem;
    color: #9ca3af;
}

/* ── Color variants ──────────────────────── */
.stat-card--yellow {
    background: #fffbeb;
    border-color: #fde68a;
}
.stat-card--yellow .stat-icon { color: #d97706; }
.stat-card--yellow .stat-value { color: #92400e; }

.stat-card--green {
    background: #f0fdf4;
    border-color: #bbf7d0;
}
.stat-card--green .stat-icon { color: #059669; }
.stat-card--green .stat-value { color: #065f46; }

.stat-card--red {
    background: #fff1f2;
    border-color: #fecdd3;
}
.stat-card--red .stat-icon { color: #e11d48; }
.stat-card--red .stat-value { color: #9f1239; }

/* ── Skeleton ────────────────────────────── */
.skeleton-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 0.75rem;
}
.skeleton-value {
    margin-bottom: 0.6rem;
}
</style>