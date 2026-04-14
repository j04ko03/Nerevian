<template>
    <AppLayout>
        <div class="dashboard">
            <Header
                title="Dashboard Clienta"
                :subtitle="`Benvinguda, ${user?.nom}`"
            />

            <div v-if="loading" class="loading-state">
                <div class="spinner" />
            </div>
        </div>
    </AppLayout>
</template>
<script setup>
import AppLayout from '@/layout/AppLayout.vue';
import Header from '@/layout/Header.vue';
import { useAuthStore } from '@/stores/authStore';

const authStore = useAuthStore();
const user = authStore.user;
</script>

<style scoped></style>
