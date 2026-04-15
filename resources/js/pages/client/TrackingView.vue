<template>
    <AppLayout>
        <div class="page-header mt-4">
            <button class="btn-back" @click="tornarAEnrere">
                <i class="pi pi-arrow-left"></i> Volver a Mis Envíos
            </button>
        </div>

        <div class="tracking-container mt-4">
            <div class="title-section mb-4">
                <h2>Seguiment: <span class="ref-code">{{ operacioInfo.ref }}</span></h2>
            </div>

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
                    <div class="stepper-container">
                        <div v-for="(step, index) in progressSteps" :key="index" class="step">
                            <div class="step-box">
                                <div class="step-circle" :class="step.status">
                                    <i :class="step.icon"></i>
                                </div>
                            </div>
                            <div class="step-text">
                                <h4>{{ step.label }}</h4>
                                <span class="step-date" :class="{ 'highlight': step.date === 'Actualidad' }">{{
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
import { ref } from 'vue';
import { useRouter } from 'vue-router';
import AppLayout from '@/layout/AppLayout.vue';

const router = useRouter();

// Datos extraídos de tus mockups
const operacioInfo = ref({
    ref: 'EXP-8821',
    origen: 'Shanghai',
    desti: 'Valencia',
});

// status: 'completed' (verde), 'active' (rojo), 'pending' (gris)
const progressSteps = ref([
    { label: 'Recogida', date: '20 Sep', icon: 'pi pi-check', status: 'completed' },
    { label: 'Puerto Salida', date: '25 Sep', icon: 'pi pi-check', status: 'completed' },
    { label: 'En Tránsito', date: 'Actualidad', icon: 'pi pi-truck', status: 'active' },
    { label: 'Puerto Destino', date: 'Est. 12 Oct 2023', icon: 'pi pi-circle', status: 'pending' },
    { label: 'Entrega Final', date: '', icon: 'pi pi-circle', status: 'pending' },
]);

const detallesCarga = ref({
    contenedor: 'MSKU901283',
    tipo: "40' HC",
    peso: '24.500 kg'
});

const documentos = ref([
    { nom: 'Bill of Lading' },
    { nom: 'Packing List' }
]);

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

/* Cabecera y Títulos */
.btn-back {
    background: transparent;
    border: none;
    color: #6b7280;
    font-size: 0.95rem;
    cursor: pointer;
    display: flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0;
    transition: color 0.2s;
}

.btn-back:hover {
    color: #111827;
}

.title-section h2 {
    margin: 0;
    font-size: 1.5rem;
    color: #111827;
    display: flex;
    align-items: center;
    gap: 1rem;
}

.ref-code {
    font-weight: 700;
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
}
</style>