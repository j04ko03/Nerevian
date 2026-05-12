<template>
    <div class="superset-container" :style="{ height: height }">
        <iframe v-if="dashboardUrl" :src="dashboardUrl" width="100%" height="100%" frameborder="0"
            allowTransparency="true" style="border: none;"></iframe>
        <div v-else class="alert alert-warning">
            No se ha configurado la URL del dashboard de Superset.
        </div>
    </div>
</template>

<script>
export default {
    name: 'SupersetDashboard',
    props: {
        // El ID lo sacas de la URL de Superset (ej: /superset/dashboard/8/)
        dashboardId: {
            type: [String, Number],
            required: true
        },
        height: {
            type: String,
            default: '600px'
        }
    },
    computed: {
        dashboardUrl() {
            // Tomamos la base URL de las variables de entorno de Laravel (process.env)
            const baseUrl = process.env.MIX_SUPERSET_URL || 'http://localhost:8088';
            // Añadimos '?standalone=true' para que no salga el menú de Superset dentro del iframe
            return `${baseUrl}/superset/dashboard/${this.dashboardId}/?standalone=true&show_filters=0`;
        }
    }
}
</script>

<style scoped>
.superset-container {
    width: 100%;
    background: #f9f9f9;
    border-radius: 8px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
}
</style>