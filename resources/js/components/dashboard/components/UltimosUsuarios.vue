<template>
    <div class="card">
        <div class="card-header">
            <h2 class="card-title">Usuaris més recents</h2>
        </div>
        <div class="card-body">
            <div
                v-for="usuario in usuarios"
                :key="usuario.nombre"
                class="request-item"
            >
                <div class="request-info">
                    <div class="request-avatar">
                        {{ initials(usuario.nombre) }}
                    </div>
                    <div>
                        <p class="request-name">{{ usuario.nombre }}</p>
                        <p class="request-meta">
                            {{ usuario.rol }} · {{ usuario.fecha }}
                        </p>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-footer">
            <RouterLink to="/admin/usuaris" class="link-more">
                Veure tots els usuaris
            </RouterLink>
        </div>
    </div>
</template>

<script setup>
defineProps({
    usuarios: {
        type: Array,
        default: () => [],
    },
});

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
.card-footer {
    padding: 0.75rem 1.5rem 1.25rem;
    border-top: 1px solid #f3f4f6;
}
.link-more {
    font-size: 0.82rem;
    color: #1a8a7d;
    text-decoration: none;
    font-weight: 500;
}
.link-more:hover {
    text-decoration: underline;
}
.request-item {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 0.75rem 0;
    border-bottom: 1px solid #f9fafb;
}
.request-item:last-child {
    border-bottom: none;
}
.request-info {
    display: flex;
    align-items: center;
    gap: 0.75rem;
}
.request-avatar {
    width: 34px;
    height: 34px;
    background: #e6f4f2;
    color: #1a8a7d;
    border-radius: 50%;
    font-size: 0.7rem;
    font-weight: 700;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}
.request-name {
    font-size: 0.875rem;
    font-weight: 600;
    color: #111827;
    margin: 0;
}
.request-meta {
    font-size: 0.75rem;
    color: #9ca3af;
    margin: 0.1rem 0 0;
}
</style>
