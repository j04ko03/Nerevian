<template>
    <div class="app-layout">
        <NavbarLateral :user="authUser" />
        <main class="main-content">
            <slot />
        </main>
    </div>
</template>

<script setup>
import { computed } from 'vue';
import NavbarLateral from '@/components/navbar/NavbarLateral.vue';
import { useAuthStore } from '@/stores/authStore';

const auth = useAuthStore();

const roleMap = {
    1: 'admin',
    2: 'cliente',
    3: 'operador'
};

const authUser = computed(() => {
    if (!auth.user) return { name: '', role: '' };
    return {
        name: `${auth.user.nom || ''} ${auth.user.cognoms || ''}`.trim(),
        role: roleMap[auth.user.rol_id] || ''
    };
});
</script>

<style>
.app-layout {
    display: flex;
}
.main-content {
    margin-left: 16%;
    flex: 1;
    min-height: 100vh;
}
</style>
