<template>
    <div class="card">
        <div class="card-header">
            <div class="card-title-row">
                <h2 class="card-title">
                    <Users :size="15" class="title-icon" />
                    Usuaris més recents
                </h2>
                <span class="card-count">{{ usuarios.length }}</span>
            </div>
        </div>

        <div class="timeline">
            <div
                v-for="(usuario, i) in usuarios"
                :key="usuario.nombre"
                class="timeline-item"
            >
                <!-- Connector -->
                <div class="timeline-left">
                    <div
                        class="timeline-avatar"
                        :style="avatarStyle(usuario.rol)"
                    >
                        {{ initials(usuario.nombre) }}
                    </div>
                    <div v-if="i < usuarios.length - 1" class="timeline-line" />
                </div>

                <!-- Content -->
                <div class="timeline-content">
                    <div class="timeline-row">
                        <span class="timeline-name">{{ usuario.nombre }}</span>
                        <span class="role-pill" :style="rolePillStyle(usuario.rol)">
                            {{ usuario.rol }}
                        </span>
                    </div>
                    <span class="timeline-date">{{ usuario.fecha }}</span>
                </div>
            </div>
        </div>

        <div class="card-footer">
            <RouterLink to="/admin/usuaris" class="link-more">
                Veure tots els usuaris
                <ArrowRight :size="13" />
            </RouterLink>
        </div>
    </div>
</template>

<script setup>
import { ArrowRight, Users } from 'lucide-vue-next';

defineProps({
    usuarios: {
        type: Array,
        default: () => [],
    },
});

const roleMap = {
    Admin:    { avatar: { bg: '#fef3c7', color: '#92400e' }, pill: { bg: '#fef9ee', color: '#b45309', border: '#fde68a' } },
    Operador: { avatar: { bg: '#eff6ff', color: '#1e40af' }, pill: { bg: '#eff6ff', color: '#1e40af', border: '#bfdbfe' } },
    Cliente:  { avatar: { bg: '#f0fdf4', color: '#166534' }, pill: { bg: '#f0fdf4', color: '#166534', border: '#bbf7d0' } },
};
const fallbackRole = {
    avatar: { bg: '#e6f4f2', color: '#1a8a7d' },
    pill:   { bg: '#e6f4f2', color: '#1a8a7d', border: '#99d6d0' },
};

function avatarStyle(rol) {
    const r = roleMap[rol] ?? fallbackRole;
    return { backgroundColor: r.avatar.bg, color: r.avatar.color };
}

function rolePillStyle(rol) {
    const r = roleMap[rol] ?? fallbackRole;
    return { backgroundColor: r.pill.bg, color: r.pill.color, borderColor: r.pill.border };
}

function initials(name) {
    if (!name) return '?';
    return name
        .split(' ')
        .filter((w) => w.length > 0)
        .map((w) => w[0])
        .slice(0, 2)
        .join('')
        .toUpperCase();
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

.card-count {
    font-size: 0.72rem;
    font-weight: 700;
    background: #f3f4f6;
    color: #6b7280;
    padding: 0.15rem 0.5rem;
    border-radius: 10px;
}

/* ── Timeline ────────────────────────────── */
.timeline {
    padding: 0.75rem 1.5rem;
    flex: 1;
}

.timeline-item {
    display: flex;
    gap: 0.85rem;
}

.timeline-left {
    display: flex;
    flex-direction: column;
    align-items: center;
    flex-shrink: 0;
}

.timeline-avatar {
    width: 36px;
    height: 36px;
    border-radius: 50%;
    font-size: 0.7rem;
    font-weight: 800;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    letter-spacing: 0.3px;
    border: 2px solid white;
    box-shadow: 0 0 0 1.5px rgba(0,0,0,0.06);
}

.timeline-line {
    width: 1.5px;
    flex: 1;
    min-height: 1rem;
    background: linear-gradient(to bottom, #e5e7eb, transparent);
    margin: 4px 0;
}

.timeline-content {
    flex: 1;
    padding: 0.45rem 0 0.9rem;
    min-width: 0;
}

.timeline-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 0.5rem;
    margin-bottom: 0.2rem;
}

.timeline-name {
    font-size: 0.855rem;
    font-weight: 600;
    color: #111827;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.role-pill {
    font-size: 0.68rem;
    font-weight: 600;
    padding: 0.15rem 0.5rem;
    border-radius: 20px;
    border: 1px solid;
    white-space: nowrap;
    flex-shrink: 0;
}

.timeline-date {
    font-size: 0.72rem;
    color: #9ca3af;
}

/* ── Footer ──────────────────────────────── */
.card-footer {
    padding: 0.85rem 1.5rem;
    border-top: 1px solid #f3f4f6;
    background: #fafafa;
}

.link-more {
    display: flex;
    align-items: center;
    gap: 0.3rem;
    font-size: 0.8rem;
    color: #1a8a7d;
    text-decoration: none;
    font-weight: 600;
    transition: gap 0.2s ease;
}

.link-more:hover {
    gap: 0.5rem;
}
</style>