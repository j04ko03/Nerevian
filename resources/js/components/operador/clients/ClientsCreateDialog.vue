<template>
    <Dialog
        :visible="visible"
        @update:visible="emit('update:visible', $event)"
        header="Nou client"
        :style="{ width: '460px' }"
        modal
        @hide="resetForm"
    >
        <div class="create-form">
            <div class="form-field">
                <label class="field-label">Usuari <span class="required">*</span></label>
                <Select
                    v-model="form.usuari_id"
                    :options="usuaris"
                    optionLabel="label"
                    optionValue="value"
                    placeholder="Selecciona un usuari..."
                    filter
                    :loading="loadingUsuaris"
                    fluid
                />
            </div>
        </div>

        <template #footer>
            <div class="dialog-footer">
                <button class="btn btn-secondary" @click="emit('update:visible', false)">
                    Cancel·lar
                </button>
                <button
                    class="btn btn-primary"
                    :disabled="!form.usuari_id || saving"
                    @click="emit('save', { ...form })"
                >
                    <LoaderCircle v-if="saving" :size="15" class="spinner" />
                    Crear client
                </button>
            </div>
        </template>
    </Dialog>
</template>

<script setup>
import { ref, watch } from 'vue';
import { LoaderCircle } from 'lucide-vue-next';
import Dialog from 'primevue/dialog';
import Select from 'primevue/select';
import api from '@/plugins/axios';

const props = defineProps({
    visible: {
        type: Boolean,
        required: true,
    },
    saving: {
        type: Boolean,
        default: false,
    },
});

const emit = defineEmits(['update:visible', 'save']);

const form = ref({ usuari_id: null });
const usuaris = ref([]);
const loadingUsuaris = ref(false);

async function fetchUsuaris() {
    loadingUsuaris.value = true;
    try {
        const { data } = await api.get('/operador/clients/usuaris-disponibles');
        usuaris.value = data.map((u) => ({
            label: `${u.nom} ${u.cognoms} — ${u.correu}`,
            value: u.id,
        }));
    } finally {
        loadingUsuaris.value = false;
    }
}

function resetForm() {
    form.value = { usuari_id: null };
}

watch(
    () => props.visible,
    (val) => { if (val) fetchUsuaris(); },
);
</script>

<style scoped>
.create-form {
    display: flex;
    flex-direction: column;
    gap: 1.1rem;
    padding-top: 0.25rem;
}

.form-field {
    display: flex;
    flex-direction: column;
    gap: 0.4rem;
}

.field-label {
    font-family: 'Instrument Sans', system-ui, sans-serif;
    font-weight: 600;
    color: #160800;
    font-size: 0.85rem;
}

.required {
    color: #ef4444;
}

/* ── Dialog Footer ───────────────────────── */
.dialog-footer {
    display: flex;
    justify-content: flex-end;
    gap: 0.75rem;
    padding-top: 0.5rem;
}

.btn {
    font-family: 'Instrument Sans', system-ui, sans-serif;
    font-weight: 600;
    padding: 0.65rem 1.25rem;
    border-radius: 8px;
    cursor: pointer;
    font-size: 0.9rem;
    transition: all 0.2s ease;
    border: none;
    display: flex;
    align-items: center;
    gap: 0.35rem;
}

.btn-primary {
    background-color: #118c8c;
    color: #fffdee;
    box-shadow: 0 4px 6px rgba(17, 140, 140, 0.2);
}

.btn-primary:hover:not(:disabled) {
    background-color: #0a3a40;
    transform: translateY(-1px);
    box-shadow: 0 6px 8px rgba(10, 58, 64, 0.25);
}

.btn-primary:disabled {
    opacity: 0.65;
    cursor: not-allowed;
}

.btn-secondary {
    background: white;
    color: #374151;
    border: 1.5px solid #e2e8f0;
}

.btn-secondary:hover {
    background: #f9fafb;
    border-color: #d1d5db;
}

.spinner {
    animation: spin 0.8s linear infinite;
}

@keyframes spin {
    from { transform: rotate(0deg); }
    to   { transform: rotate(360deg); }
}
</style>