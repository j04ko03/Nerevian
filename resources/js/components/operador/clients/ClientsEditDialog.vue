<template>
    <Dialog
        :visible="visible"
        @update:visible="emit('update:visible', $event)"
        :header="`Editar client ID-${localClient?.id}`"
        :style="{ width: '480px' }"
        modal
    >
        <div v-if="localClient" class="edit-form">
            <div class="form-row">
                <div class="form-field">
                    <label class="field-label">Nom</label>
                    <InputText v-model="localClient.nom" fluid />
                </div>
                <div class="form-field">
                    <label class="field-label">Cognoms</label>
                    <InputText v-model="localClient.cognoms" fluid />
                </div>
            </div>
            <div class="form-field">
                <label class="field-label">Telèfon</label>
                <InputText v-model="localClient.telefon" fluid />
            </div>
            <div class="form-field">
                <label class="field-label">DNI</label>
                <InputText v-model="localClient.dni" fluid />
            </div>
        </div>

        <template #footer>
            <div class="dialog-footer">
                <button class="btn btn-secondary" @click="emit('update:visible', false)">
                    Cancel·lar
                </button>
                <button
                    class="btn btn-primary"
                    :disabled="saving"
                    @click="emit('save', localClient)"
                >
                    <LoaderCircle v-if="saving" :size="15" class="spinner" />
                    Desar canvis
                </button>
            </div>
        </template>
    </Dialog>
</template>

<script setup>
import { ref, watch } from 'vue';
import { LoaderCircle } from 'lucide-vue-next';
import Dialog from 'primevue/dialog';
import InputText from 'primevue/inputtext';

const props = defineProps({
    visible: {
        type: Boolean,
        required: true,
    },
    client: {
        type: Object,
        default: null,
    },
    saving: {
        type: Boolean,
        default: false,
    },
});

const emit = defineEmits(['update:visible', 'save']);

const localClient = ref(null);

watch(
    () => props.client,
    (val) => {
        if (!val) { localClient.value = null; return; }
        localClient.value = {
            id:      val.id,
            nom:     val.nom ?? '',
            cognoms: val.cognoms ?? '',
            telefon: val.telefon ?? '',
            dni:     val.client?.dni ?? '',
            client:  val.client,
        };
    },
    { immediate: true },
);
</script>

<style scoped>
.edit-form {
    display: flex;
    flex-direction: column;
    gap: 1.1rem;
    padding-top: 0.25rem;
}

.form-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 1rem;
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