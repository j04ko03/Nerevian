<template>
    <AppLayout>
        <div class="dashboard">
            <DashboardHeader />

            <div v-if="loading" class="loading-state">
                <div class="spinner" />
            </div>
        </div>
    </AppLayout>
</template>
<script setup>
import DashboardHeader from '@/components/dashboard/components/DashboardHeader.vue';
import AppLayout from '@/layout/AppLayout.vue';
</script>

<style scoped></style>
