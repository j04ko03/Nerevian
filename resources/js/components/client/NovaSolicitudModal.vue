<template>
    <Dialog :visible="visible" @update:visible="$emit('update:visible', $event)" modal
        header="Nova Sol·licitud de Transport" :style="{ width: '60vw', maxWidth: '800px' }"
        :breakpoints="{ '960px': '75vw', '641px': '100vw' }">
        <div v-if="cargandoCatalogos" class="loading-state">
            <i class="pi pi-spin pi-spinner" style="font-size: 2rem"></i>
            <p>Carregant dades...</p>
        </div>

        <form v-else @submit.prevent="enviarSolicitud" class="solicitud-form">
            <div class="form-row">
                <div class="form-group">
                    <label>Tipus de Transport *</label>
                    <select v-model="form.tipus_transport_id" required class="form-input">
                        <option value="" disabled>Selecciona...</option>
                        <option v-for="t in catalogos.tipus_transports" :key="t.id" :value="t.id">
                            {{ t.tipus }}
                        </option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Incoterm *</label>
                    <select v-model="form.incoterm_id" required class="form-input">
                        <option value="" disabled>Selecciona...</option>
                        <option v-for="i in catalogos.incoterms" :key="i.id" :value="i.id">
                            {{ i.name }}
                        </option>
                    </select>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Port d'Origen *</label>
                    <select v-model="form.port_origen_id" required class="form-input">
                        <option value="" disabled>Selecciona port d'origen...</option>
                        <option v-for="p in catalogos.ports" :key="p.id" :value="p.id">
                            {{ p.nom }} ({{ p.ciutat?.nom || 'N/A' }})
                        </option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Port de Destí *</label>
                    <select v-model="form.port_desti_id" required class="form-input">
                        <option value="" disabled>Selecciona port de destí...</option>
                        <option v-for="p in catalogos.ports" :key="p.id" :value="p.id">
                            {{ p.nom }} ({{ p.ciutat?.nom || 'N/A' }})
                        </option>
                    </select>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Tipus de Càrrega *</label>
                    <select v-model="form.tipus_carrega_id" required class="form-input">
                        <option value="" disabled>Selecciona...</option>
                        <option v-for="c in catalogos.tipus_carregues" :key="c.id" :value="c.id">
                            {{ c.tipus }}
                        </option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Tipus de Flux *</label>
                    <select v-model="form.tipus_fluxe_id" required class="form-input">
                        <option value="" disabled>Selecciona...</option>
                        <option v-for="f in catalogos.tipus_fluxes" :key="f.id" :value="f.id">
                            {{ f.tipus }}
                        </option>
                    </select>
                </div>
            </div>

            <div class="form-row three-cols">
                <div class="form-group">
                    <label>Pes Brut (kg) *</label>
                    <input type="number" step="0.01" v-model="form.pes_brut" required class="form-input"
                        placeholder="Ex: 1500.50">
                </div>
                <div class="form-group">
                    <label>Volum (m³)</label>
                    <input type="number" step="0.01" v-model="form.volum" class="form-input" placeholder="Opcional">
                </div>
                <div class="form-group">
                    <label>Tipus Contenidor</label>
                    <select v-model="form.tipus_contenidor_id" class="form-input">
                        <option :value="null">No aplica</option>
                        <option v-for="c in catalogos.tipus_contenidors" :key="c.id" :value="c.id">
                            {{ c.tipus }}
                        </option>
                    </select>
                </div>
            </div>

            <div class="form-group full-width">
                <label>Operador Assignat</label>
                <select v-model="form.operador_id" class="form-input">
                    <option :value="null">Sense preferència</option>
                    <option v-for="o in catalogos.operadors" :key="o.id" :value="o.id">
                        {{ o.nom }} {{ o.cognoms }}
                    </option>
                </select>
            </div>

            <div class="form-group full-width">
                <label>Comentaris o Instruccions Especials</label>
                <textarea v-model="form.comentaris" class="form-input" rows="3"
                    placeholder="Detalls addicionals per a l'operador..."></textarea>
            </div>

            <div class="form-actions">
                <button type="button" class="btn-secondary" @click="$emit('update:visible', false)">Cancel·lar</button>
                <button type="submit" class="btn-primary" :disabled="enviando">
                    <i class="pi pi-send" v-if="!enviando"></i>
                    <i class="pi pi-spin pi-spinner" v-else></i>
                    {{ enviando ? 'Enviant...' : 'Sol·licitar Cotització' }}
                </button>
            </div>
        </form>
    </Dialog>
</template>

<script setup>
import { ref, watch } from 'vue';
import api from '@/plugins/axios';
import Dialog from 'primevue/dialog';

const props = defineProps({
    visible: Boolean
});

const emit = defineEmits(['update:visible', 'solicitudCreada', 'error']);

const cargandoCatalogos = ref(true);
const enviando = ref(false);

const catalogos = ref({
    tipus_transports: [],
    tipus_carregues: [],
    tipus_fluxes: [],
    tipus_contenidors: [],
    incoterms: [],
    ports: [],
    operadors: [],
});

const formInicial = {
    tipus_transport_id: '',
    tipus_fluxe_id: '',
    tipus_carrega_id: '',
    incoterm_id: '',
    port_origen_id: '',
    port_desti_id: '',
    pes_brut: null,
    volum: null,
    comentaris: '',
    tipus_contenidor_id: null,
    tipus_validacio_id: null,
    operador_id: null,
};

const form = ref({ ...formInicial });

async function carregarCatalogos() {
    cargandoCatalogos.value = true;
    try {
        const response = await api.get('/catalogos');
        catalogos.value = response.data.data;
    } catch (error) {
        console.error('Error al cargar catálogos', error);
    } finally {
        cargandoCatalogos.value = false;
    }
}

// Carrega catàlegs quan s'obre el modal, no al muntar la pàgina
watch(
    () => props.visible,
    (val) => { if (val) carregarCatalogos(); },
);

// 2. Enviamos el formulario al endpoint que creé en el backend
const enviarSolicitud = async () => {
    enviando.value = true;
    try {
        await api.post('/solicitudes', form.value);
        // Reseteamos el formulario
        form.value = { ...formInicial };
        // Cerramos el modal
        emit('update:visible', false);
        // Le avisamos a la página principal para que recargue la lista
        emit('solicitudCreada');
    } catch (error) {
        console.error('Error al crear la solicitud', error);
        emit('error', 'Hi ha hagut un error en enviar la sol·licitud.');
    } finally {
        enviando.value = false;
    }
};
</script>

<style scoped>
.loading-state {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: 3rem;
    color: #6b7280;
}

.solicitud-form {
    display: flex;
    flex-direction: column;
    gap: 1.25rem;
    padding-top: 1rem;
}

.form-row {
    display: flex;
    gap: 1.25rem;
    width: 100%;
}

.form-row.three-cols {
    grid-template-columns: repeat(3, 1fr);
    display: grid;
}

.form-group {
    display: flex;
    flex-direction: column;
    flex: 1;
}

.form-group.full-width {
    width: 100%;
}

.form-group label {
    font-size: 0.875rem;
    font-weight: 600;
    color: #374151;
    margin-bottom: 0.5rem;
    font-family: 'Instrument Sans', sans-serif;
}

/* Estilos de input nativos basados en tu diseño */
.form-input {
    padding: 0.65rem 1rem;
    border: 1px solid #d1d5db;
    border-radius: 8px;
    background: #f9fafb;
    font-family: 'Instrument Sans', sans-serif;
    font-size: 0.95rem;
    color: #111827;
    transition: all 0.2s;
}

.form-input:focus {
    outline: none;
    border-color: #1a8a7d;
    box-shadow: 0 0 0 3px rgba(26, 138, 125, 0.1);
    background: white;
}

.form-actions {
    display: flex;
    justify-content: flex-end;
    gap: 1rem;
    margin-top: 1.5rem;
    border-top: 1px solid #e5e7eb;
    padding-top: 1.5rem;
}

.btn-primary {
    background: #1a8a7d;
    color: white;
    padding: 0.6rem 1.2rem;
    border-radius: 8px;
    border: none;
    font-weight: 600;
    cursor: pointer;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.btn-primary:disabled {
    opacity: 0.7;
    cursor: not-allowed;
}

.btn-secondary {
    background: white;
    color: #4b5563;
    padding: 0.6rem 1.2rem;
    border-radius: 8px;
    border: 1px solid #d1d5db;
    font-weight: 600;
    cursor: pointer;
}
</style>