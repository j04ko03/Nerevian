<template>
    <StatsGrid :columns="3">
        <StatCard
            label="Total usuaris client"
            :value="usuaris.length"
            compare="registrats al sistema"
            :loading="loading"
        >
            <template #icon><Users :size="18" /></template>
        </StatCard>
        <StatCard
            label="Clients actius"
            :value="countActius"
            compare="accés activat"
            :loading="loading"
            color="green"
        >
            <template #icon><BadgeCheck :size="18" /></template>
        </StatCard>
        <StatCard
            label="Pendents d'activació"
            :value="countPendents"
            compare="sense ficha de client"
            :loading="loading"
            color="yellow"
        >
            <template #icon><Clock :size="18" /></template>
        </StatCard>
    </StatsGrid>
</template>

<script setup>
import { computed } from 'vue';
import { Users, BadgeCheck, Clock } from 'lucide-vue-next';
import StatsGrid from '@/components/dashboard/components/StatsGrid.vue';
import StatCard from '@/components/dashboard/components/StatCard.vue';

const props = defineProps({
    usuaris: {
        type: Array,
        required: true,
    },
    loading: {
        type: Boolean,
        default: false,
    },
});

const countActius   = computed(() => props.usuaris.filter((u) => u.actiu).length);
const countPendents = computed(() => props.usuaris.filter((u) => !u.actiu).length);
</script>