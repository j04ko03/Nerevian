<template>
    <div class="pendent-layout">
        <div class="pendent-card">
            <div class="icon-wrap">
                <Clock :size="40" />
            </div>
            <h1 class="title">Compte pendent d'activació</h1>
            <p class="description">
                El teu compte ha estat registrat correctament.<br />
                Un operador l'activarà en breu perquè puguis accedir a la plataforma.
            </p>
            <button class="btn-logout" @click="logout">Tancar sessió</button>
        </div>
    </div>
</template>

<script setup>
import { Clock } from 'lucide-vue-next';
import { useAuthStore } from '@/stores/authStore';
import { useRouter } from 'vue-router';

const auth = useAuthStore();
const router = useRouter();

async function logout() {
    await auth.logout();
    router.push({ name: 'login' });
}
</script>

<style scoped>
.pendent-layout {
    min-height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
    background: #f9fafb;
}

.pendent-card {
    background: white;
    border: 1px solid #e5e7eb;
    border-radius: 16px;
    padding: 3rem 2.5rem;
    max-width: 420px;
    width: 100%;
    text-align: center;
    box-shadow: 0 4px 24px rgba(0, 0, 0, 0.06);
}

.icon-wrap {
    width: 72px;
    height: 72px;
    border-radius: 50%;
    background: #fef9c3;
    color: #ca8a04;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 1.5rem;
}

.title {
    font-size: 1.35rem;
    font-weight: 700;
    color: #111827;
    margin: 0 0 0.75rem;
}

.description {
    font-size: 0.9rem;
    color: #6b7280;
    line-height: 1.6;
    margin: 0 0 2rem;
}

.btn-logout {
    font-family: 'Instrument Sans', system-ui, sans-serif;
    font-weight: 600;
    font-size: 0.875rem;
    padding: 0.6rem 1.4rem;
    border-radius: 8px;
    border: 1.5px solid #e2e8f0;
    background: white;
    color: #374151;
    cursor: pointer;
    transition: all 0.2s ease;
}

.btn-logout:hover {
    background: #f9fafb;
    border-color: #d1d5db;
}
</style>