<template>
    <div class="superset-container" :style="{ height: height }">
        <iframe 
            v-if="dashboardUrl" 
            :src="dashboardUrl" 
            width="100%" 
            height="100%" 
            frameborder="0"
            allowTransparency="true" 
            style="border: none;"
        ></iframe>
        
        <div v-else class="loading-placeholder">
            <i class="pi pi-exclamation-triangle"></i>
            <p>Error de configuració.</p>
        </div>
    </div>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
    dashboardId: {
        type: [String, Number],
        required: true
    },
    height: {
        type: String,
        default: '700px'
    }
});

const dashboardUrl = computed(() => {
    const baseUrl = 'http://localhost:8088';
    
    if (!props.dashboardId) return null;

    return `${baseUrl}/superset/dashboard/${props.dashboardId}/?standalone=true&show_filters=0`;
});
</script>

<style scoped>
.superset-container {
    width: 100%;
    overflow: hidden;
    background: #ffffff;
    border-radius: 12px;
    border: 1px solid #e5e7eb;
}

.loading-placeholder {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    height: 100%;
    color: #6b7280;
    gap: 1rem;
}
</style>