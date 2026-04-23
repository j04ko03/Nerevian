<template>
    <AppLayout>
        <div class="tracking-top-bar">
            <button class="btn-back" @click="tornarAEnrere">
                <i class="pi pi-arrow-left"></i>
            </button>
            <div class="tracking-top-info">
                <h1 class="tracking-top-title">{{ carregant ? 'Carregant...' : operacioInfo.ref }}</h1>
                <p v-if="!carregant" class="tracking-top-route">{{ operacioInfo.origen }} → {{ operacioInfo.desti }}</p>
            </div>
        </div>

        <div v-if="carregant" class="loading-state">
            <i class="pi pi-spin pi-spinner" style="font-size:2rem"></i>
            <p>Carregant seguiment...</p>
        </div>
        <div v-else-if="error" class="error-state">{{ error }}</div>

        <div v-else class="tracking-container">

            <a :href="`https://www.google.com/maps/dir/${operacioInfo.origen}/${operacioInfo.desti}`" target="_blank"
                class="map-placeholder mb-4">
                <div class="map-content">
                    <i class="pi pi-map-marker map-icon"></i>
                    <span>Mapa Interactivo de Carga</span>
                </div>
            </a>

            <div class="details-card">
                <div class="route-header">
                    <h3>Detalls de Ruta: {{ operacioInfo.origen }} <i class="pi pi-arrow-right arrow-icon"></i> {{
                        operacioInfo.desti }}</h3>
                </div>

                <div class="stepper-wrapper mt-5 mb-5">
                    <div class="stepper-line"></div>
                    <div class="stepper-line-progress" :style="{ width: progressPercentage + '%' }"></div>
                    <div class="stepper-container">
                        <div v-for="(step, index) in progressSteps" :key="index" class="step">
                            <div class="step-box">
                                <div class="step-circle" :class="step.status">
                                    <i :class="step.icon"></i>
                                </div>
                            </div>
                            <div class="step-text">
                                <h4>{{ step.label }}</h4>
                                <span class="step-date" :class="{ 'highlight': step.date === 'Actualitat' }">{{
                                    step.date }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bottom-grid mt-5">
                    <div class="carga-section">
                        <h3>Detalles de Carga</h3>
                        <div class="carga-list">
                            <div class="carga-item">
                                <span class="label">Contenedor:</span>
                                <span class="value"><strong>{{ detallesCarga.contenedor }}</strong></span>
                            </div>
                            <div class="carga-item">
                                <span class="label">Tipo:</span>
                                <span class="value"><strong>{{ detallesCarga.tipo }}</strong></span>
                            </div>
                            <div class="carga-item">
                                <span class="label">Peso:</span>
                                <span class="value"><strong>{{ detallesCarga.peso }}</strong></span>
                            </div>
                        </div>
                    </div>

                    <div class="docs-section">
                        <h3>Documentación</h3>
                        <div class="docs-list">
                            <button v-for="(doc, index) in documentos" :key="index" class="btn-outline">
                                <i class="pi pi-download"></i> {{ doc.nom }}
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue';
import { useRouter, useRoute } from 'vue-router';
import api from '@/plugins/axios';
import AppLayout from '@/layout/AppLayout.vue';

const router = useRouter();
const route = useRoute();

const carregant = ref(true);
const error = ref(null);

const operacioInfo = ref({ ref: '', origen: '', desti: '' });
const historial = ref([]);
const detallesCarga = ref({ contenedor: null, tipo: null, peso: null });
const documentos = ref([]);

const STEP_LABELS = [
    { label: 'Recogida', icon_pending: 'pi pi-circle', icon_done: 'pi pi-check' },
    { label: 'Puerto Salida', icon_pending: 'pi pi-circle', icon_done: 'pi pi-check' },
    { label: 'En Tránsito', icon_pending: 'pi pi-truck', icon_done: 'pi pi-check' },
    { label: 'Puerto Destino', icon_pending: 'pi pi-circle', icon_done: 'pi pi-check' },
    { label: 'Entrega Final', icon_pending: 'pi pi-circle', icon_done: 'pi pi-check' },
];

const pasoActualMobile = ref('');

const progressSteps = computed(() => {
    // Orden lógico de los pasos (lo que recibimos de la API)
    const ORDER = ['Recogida', 'Puerto Salida', 'En Tránsito', 'Puerto Destino', 'Entrega Final'];

    const currentIndex = ORDER.indexOf(pasoActualMobile.value);

    return STEP_LABELS.map((s, i) => {
        if (i < currentIndex) return { ...s, label: s.label, status: 'completed', icon: s.icon_done };
        if (i === currentIndex) return { ...s, label: s.label, status: 'active', icon: s.icon_pending };
        return { ...s, label: s.label, status: 'pending', icon: s.icon_pending };
    });
});

const progressPercentage = computed(() => {
    const ORDER = ['Recogida', 'Puerto Salida', 'En Tránsito', 'Puerto Destino', 'Entrega Final'];
    const idx = ORDER.indexOf(pasoActualMobile.value);
    if (idx === -1) return 0;
    return (idx / (ORDER.length - 1)) * 90;
});

onMounted(async () => {
    try {
        const { data } = await api.get(`/tracking/${route.params.id}`);
        const res = data.data; // Aquí está tu nuevo JSON unificado

        operacioInfo.value = {
            ref: res.ref,
            origen: res.origen,
            desti: res.desti
        };

        detallesCarga.value = {
            tipo: res.carrega,
            peso: res.pes_brut ?? '—',
        };
        historial.value = res.historial;
        pasoActualMobile.value = res.tracking.paso_nombre;
    } catch (e) {
        error.value = 'No s\'ha pogut carregar el seguiment.';
        console.error(e);
    } finally {
        carregant.value = false;
    }
});

const tornarAEnrere = () => {
    router.push('/client/operacions');
};
</script>

<style scoped>
.mt-4 {
    margin-top: 1.5rem;
}

.mt-5 {
    margin-top: 2rem;
}

.mb-4 {
    margin-bottom: 1.5rem;
}

.mb-5 {
    margin-bottom: 2rem;
}

/* ── Tracking Top Bar ──────────────────────────────────────── */
.tracking-top-bar {
    display: flex;
    align-items: center;
    gap: 1rem;
    margin-bottom: 1.75rem;
    padding-bottom: 1.5rem;
    border-bottom: 1px solid #e5e7eb;
}

.btn-back {
    background: transparent;
    border: 1px solid #d1d5db;
    color: #6b7280;
    font-size: 1rem;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    width: 38px;
    height: 38px;
    border-radius: 8px;
    transition: all 0.2s;
    flex-shrink: 0;
}

.btn-back:hover {
    border-color: #1a8a7d;
    color: #1a8a7d;
    background: #f0fdfa;
}

.tracking-top-info {
    display: flex;
    flex-direction: column;
    gap: 0.2rem;
    min-width: 0;
}

.tracking-top-title {
    font-size: 1.75rem;
    font-weight: 700;
    color: #111827;
    margin: 0;
    letter-spacing: -0.02em;
}

.tracking-top-route {
    font-size: 0.875rem;
    color: #9ca3af;
    margin: 0;
}

/* Loading / Error */
.loading-state {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: 4rem;
    color: #6b7280;
    gap: 1rem;
}

.error-state {
    padding: 2rem;
    color: #ef4444;
    font-weight: 500;
}

/* Mapa Interactivo */
.map-placeholder {
    display: block;
    background-color: #e2e8f0;
    height: 250px;
    border-radius: 12px;
    text-decoration: none;
    position: relative;
    overflow: hidden;
    transition: opacity 0.2s;
}

.map-placeholder:hover {
    opacity: 0.9;
}

.map-content {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 0.5rem;
    color: #1a8a7d;
}

.map-icon {
    font-size: 2rem;
}

.map-content span {
    font-weight: 500;
    font-size: 0.95rem;
    color: #4b5563;
}

/* Tarjeta Principal */
.details-card {
    background: white;
    border-radius: 12px;
    padding: 2.5rem;
    border: 1px solid #e5e7eb;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
}

.route-header h3 {
    margin: 0;
    font-size: 1.1rem;
    color: #111827;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.arrow-icon {
    font-size: 0.8rem;
    color: #6b7280;
}

/* Stepper Horizontal */
.stepper-wrapper {
    position: relative;
    display: flex;
    justify-content: center;
    width: 100%;
    padding: 1rem 0;
}

.stepper-line {
    position: absolute;
    top: 35px;
    left: 5%;
    right: 5%;
    height: 2px;
    background-color: #e5e7eb;
    z-index: 1;
}

.stepper-container {
    display: flex;
    justify-content: space-between;
    width: 100%;
    position: relative;
    z-index: 2;
}

.step {
    display: flex;
    flex-direction: column;
    align-items: center;
    width: 120px;
}

/* Fondo crema de los iconos */
.step-box {
    background-color: #fcfcf5;
    padding: 0.75rem;
}

.step-circle {
    width: 45px;
    height: 45px;
    border-radius: 50%;
    display: flex;
    justify-content: center;
    align-items: center;
    font-size: 1.2rem;
    background-color: white;
    border: 2px solid #e5e7eb;
    color: #d1d5db;
}

/* Estados del Stepper */
.step-circle.completed {
    background-color: #1a8a7d;
    border-color: #1a8a7d;
    color: white;
}

.step-circle.active {
    background-color: white;
    border: 2px solid #ef4444;
    color: #ef4444;
}

.step-text {
    text-align: center;
    margin-top: 0.75rem;
}

.step-text h4 {
    margin: 0;
    font-size: 0.9rem;
    color: #111827;
    font-weight: 600;
}

.step-date {
    font-size: 0.8rem;
    color: #9ca3af;
    display: block;
    margin-top: 0.25rem;
}

.step-date.highlight {
    color: #d1d5db;
    /* Ajusta según mockup */
}

/* Grid Inferior (Carga y Docs) */
.bottom-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 4rem;
}

.carga-section h3,
.docs-section h3 {
    margin: 0 0 1.5rem 0;
    font-size: 1.05rem;
    color: #111827;
    font-weight: 700;
}

/* Detalles de Carga */
.carga-list {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.carga-item {
    display: grid;
    grid-template-columns: 100px 1fr;
    gap: 1rem;
}

.carga-item .label {
    color: #6b7280;
    font-size: 0.95rem;
}

.carga-item .value {
    color: #111827;
    font-size: 0.95rem;
    text-align: right;
}

/* Documentación */
.docs-list {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.btn-outline {
    background: white;
    border: 1px solid #1a8a7d;
    color: #1a8a7d;
    padding: 0.75rem 1rem;
    border-radius: 6px;
    font-weight: 500;
    font-size: 0.95rem;
    cursor: pointer;
    display: flex;
    justify-content: center;
    align-items: center;
    gap: 0.5rem;
    transition: background 0.2s;
}

.btn-outline:hover {
    background: #f0fdfa;
}

.step-circle.active {
    background-color: #fff;
    border: 2px solid #1a8a7d;
    color: #1a8a7d;
    box-shadow: 0 0 0 0 rgba(26, 138, 125, 0.4);
    animation: pulse-green 2s infinite;
    /* ¡Efecto de pulso! */
}

@keyframes pulse-green {
    0% {
        box-shadow: 0 0 0 0 rgba(26, 138, 125, 0.7);
    }

    70% {
        box-shadow: 0 0 0 10px rgba(26, 138, 125, 0);
    }

    100% {
        box-shadow: 0 0 0 0 rgba(26, 138, 125, 0);
    }
}

.step-circle.completed {
    background-color: #1a8a7d;
    border-color: #1a8a7d;
    color: white;
}

.step-circle {
    width: 45px;
    height: 45px;
    border-radius: 50%;
    display: flex;
    justify-content: center;
    align-items: center;
    font-size: 1.2rem;
    background-color: white;
    border: 2px solid #e5e7eb;
    color: #d1d5db;
    z-index: 2;
}

/* Para que la línea también se pinte de verde */
.stepper-line-progress {
    position: absolute;
    top: 24px;
    left: 5%;
    height: 2px;
    background-color: #1a8a7d;
    /* Verde Nerevian */
    z-index: 1;
    transition: width 0.5s ease;
}

.stepper-line,
.stepper-line-progress {
    position: absolute;
    top: 37px;
    z-index: 1;
}

/* Quitamos el fondo cuadrado y el padding molesto */
.step-box {
    background-color: transparent;
    padding: 0;
    display: flex;
    justify-content: center;
    align-items: center;
    height: 48px;
}

/* Responsive */
@media (max-width: 768px) {
    .bottom-grid {
        grid-template-columns: 1fr;
        gap: 2rem;
    }

    .stepper-wrapper {
        overflow-x: auto;
        padding-bottom: 1rem;
        justify-content: flex-start;
    }

    .stepper-container {
        min-width: 600px;
    }

    .tracking-top-title {
        font-size: 1.3rem;
    }
}
</style>