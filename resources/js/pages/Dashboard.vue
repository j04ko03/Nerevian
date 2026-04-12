<template>
    <DashboardAdmin v-if="authStore.isAdmin" />
    <DashboardCliente v-else-if="authStore.isUser" />
    <DashboardOperador v-else-if="authStore.isOperador" />
</template>

<script setup>
import { useAuthStore } from '@/stores/authStore';

import DashboardAdmin from '@/components/dashboard/DashboardAdmin.vue';
import DashboardCliente from '@/components/dashboard/DashboardCliente.vue';
import DashboardOperador from '@/components/dashboard/DashboardOperador.vue';

const authStore = useAuthStore();
</script>
