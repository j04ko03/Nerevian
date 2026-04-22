<template>
    <Dialog
        :visible="visible"
        @update:visible="$emit('update:visible', $event)"
        modal
        header="Enviar Oferta"
        :style="{ width: '480px' }"
    >
        <div v-if="solicitud" class="solicitud-info">
            <div class="info-row">
                <span class="info-label">Sol·licitud</span>
                <strong>SOL-{{ String(solicitud.id).padStart(3, '0') }}</strong>
            </div>
            <div class="info-row">
                <span class="info-label">Client</span>
                <span
                    >{{ solicitud.client?.usuaris?.nom }}
                    {{ solicitud.client?.usuaris?.cognoms }}</span
                >
            </div>
            <div class="info-row">
                <span class="info-label">Ruta</span>
                <span
                    >{{ solicitud.port_origen?.nom }} →
                    {{ solicitud.port_desti?.nom }}</span
                >
            </div>
        </div>

        <form @submit.prevent="enviar" class="oferta-form">
            <div class="form-group">
                <label>Preu (€) *</label>
                <input
                    type="number"
                    step="0.01"
                    min="0"
                    v-model="form.pressupost"
                    required
                    class="form-input"
                    placeholder="Ex: 1250.00"
                />
            </div>

            <div class="form-group">
                <label>Temps Estimat (dies)</label>
                <input
                    type="number"
                    min="1"
                    v-model="form.temps_estimat"
                    class="form-input"
                    placeholder="Ex: 15"
                />
            </div>

            <div class="form-group">
                <label>Comentaris</label>
                <textarea
                    v-model="form.comentaris"
                    class="form-input"
                    rows="3"
                    placeholder="Condicions, notes addicionals..."
                />
            </div>

            <div class="form-actions">
                <button
                    type="button"
                    class="btn-secondary"
                    @click="$emit('update:visible', false)"
                >
                    Cancel·lar
                </button>
                <button type="submit" class="btn-primary" :disabled="enviando">
                    <i class="pi pi-send" v-if="!enviando" />
                    <i class="pi pi-spin pi-spinner" v-else />
                    {{ enviando ? 'Enviant...' : 'Enviar Oferta' }}
                </button>
            </div>
        </form>
    </Dialog>
</template>

<script setup>
import { ref, watch } from 'vue';
import Dialog from 'primevue/dialog';
import api from '@/plugins/axios';

const props = defineProps({
    visible: Boolean,
    solicitud: { type: Object, default: null },
});

const emit = defineEmits(['update:visible', 'ofertaEnviada', 'error']);

const enviando = ref(false);
const formBuit = { preu: null, temps_estimat: null, comentaris: '' };
const form = ref({ ...formBuit });

watch(
    () => props.visible,
    (val) => {
        if (val) form.value = { ...formBuit };
    },
);

async function enviar() {
    if (!props.solicitud) return;
    enviando.value = true;
    try {
        await api.post(
            `/operador/solicituds/${props.solicitud.id}/oferta`,
            form.value,
        );
        emit('update:visible', false);
        emit('ofertaEnviada');
    } catch {
        emit('error', "No s'ha pogut enviar l'oferta.");
    } finally {
        enviando.value = false;
    }
}
</script>

<style scoped>
.solicitud-info {
    background: #f9fafb;
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    padding: 0.9rem 1rem;
    margin-bottom: 1.25rem;
    display: flex;
    flex-direction: column;
    gap: 0.4rem;
}

.info-row {
    display: flex;
    gap: 0.75rem;
    font-size: 0.875rem;
    color: #374151;
}

.info-label {
    font-weight: 600;
    min-width: 80px;
    color: #6b7280;
}

.oferta-form {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.form-group {
    display: flex;
    flex-direction: column;
    gap: 0.4rem;
}

.form-group label {
    font-size: 0.875rem;
    font-weight: 600;
    color: #374151;
}

.form-input {
    padding: 0.65rem 1rem;
    border: 1px solid #d1d5db;
    border-radius: 8px;
    background: #f9fafb;
    font-size: 0.95rem;
    color: #111827;
    font-family: inherit;
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
    gap: 0.75rem;
    margin-top: 0.5rem;
    padding-top: 1rem;
    border-top: 1px solid #e5e7eb;
}

.btn-primary {
    background: #1a8a7d;
    color: white;
    padding: 0.55rem 1.1rem;
    border-radius: 8px;
    border: none;
    font-weight: 600;
    cursor: pointer;
    display: flex;
    align-items: center;
    gap: 0.4rem;
    font-size: 0.9rem;
}

.btn-primary:disabled {
    opacity: 0.7;
    cursor: not-allowed;
}

.btn-secondary {
    background: white;
    color: #4b5563;
    padding: 0.55rem 1.1rem;
    border-radius: 8px;
    border: 1px solid #d1d5db;
    font-weight: 600;
    cursor: pointer;
    font-size: 0.9rem;
}
</style>
