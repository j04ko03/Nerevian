<template>
    <PageHeader :title="title" :subtitle="`Benvinguda, ${user?.nom}`" />
</template>

<script setup>
import { computed } from 'vue';
import { useAuthStore } from '@/stores/authStore';
import PageHeader from '@/layout/Header.vue';

const authStore = useAuthStore();
const user = authStore.user;

const title = computed(() => {
    const titles = { '1': 'Dashboard Admin', '2': 'Dashboard Cliente', '3': 'Dashboard Operador' };
    return titles[user?.rol_id] ?? 'Dashboard';
});
</script>