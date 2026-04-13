<template>
    <div class="page-header">
        <div>
            <h1 v-if="user?.rol_id === '1'" class="page-title">
                Dashboard Admin
            </h1>

            <h1 v-else-if="user?.rol_id === '2'" class="page-title">
                Dashboard Cliente
            </h1>

            <h1 v-else-if="user?.rol_id === '3'" class="page-title">
                Dashboard Operador
            </h1>

            <p class="page-subtitle">Benvinguda, {{ user?.nom }}</p>
        </div>
    </div>
</template>

<script setup>
import { useAuthStore } from '@/stores/authStore';
const authStore = useAuthStore();
const user = authStore.user;
console.log(user);
</script>

<style scoped>
.page-header {
    display: flex;
    align-items: flex-end;
    justify-content: space-between;
    margin-bottom: 1.75rem;
    padding-bottom: 1.5rem;
    border-bottom: 1px solid #e5e7eb;
}
.page-title {
    font-size: 1.75rem;
    font-weight: 700;
    color: #111827;
    margin: 0;
    letter-spacing: -0.02em;
}
.page-subtitle {
    font-size: 0.875rem;
    color: #9ca3af;
    margin: 0.3rem 0 0;
}
</style>
