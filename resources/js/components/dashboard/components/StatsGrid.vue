<template>
    <div class="stats-grid" :style="{ gridTemplateColumns: `repeat(${columns}, 1fr)` }">
        <slot />
    </div>
</template>

<script setup>
defineProps({
    columns: {
        type: Number,
        default: 3,
    },
});
</script>

<style scoped>
.stats-grid {
    display: grid;
    gap: 1rem;
    margin-bottom: 1.5rem;
}
</style>