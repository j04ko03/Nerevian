<template>
    <StatsGrid :columns="4">
        <StatCard
            label="Total usuaris"
            :value="users.length"
            compare="registrats al sistema"
            :loading="loading"
        >
            <template #icon><Users :size="18" /></template>
        </StatCard>
        <StatCard
            label="Admins"
            :value="countByRole('admin')"
            compare="amb accés total"
            :loading="loading"
        >
            <template #icon><ShieldCheck :size="18" /></template>
        </StatCard>
        <StatCard
            label="Operadors"
            :value="countByRole('operator')"
            compare="gestió d'operacions"
            :loading="loading"
        >
            <template #icon><Briefcase :size="18" /></template>
        </StatCard>
        <StatCard
            label="Clients"
            :value="countByRole('client')"
            compare="empreses registrades"
            :loading="loading"
        >
            <template #icon><Building2 :size="18" /></template>
        </StatCard>
    </StatsGrid>
</template>

<script setup>
import { Users, ShieldCheck, Briefcase, Building2 } from 'lucide-vue-next';
import StatsGrid from '@/components/dashboard/components/StatsGrid.vue';
import StatCard from '@/components/dashboard/components/StatCard.vue';

const props = defineProps({
    users: {
        type: Array,
        required: true,
    },
    loading: {
        type: Boolean,
        default: false,
    },
});

function countByRole(rolName) {
    return props.users.filter(u => u.rols?.rol?.toLowerCase() === rolName).length;
}
</script>