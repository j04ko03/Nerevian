<template>
    <Dialog
        :visible="visible"
        @update:visible="emit('update:visible', $event)"
        :header="`Editar usuari ID-${localUser?.id}`"
        :style="{ width: '500px' }"
        modal
    >
        <div v-if="localUser" class="edit-form">
            <div class="form-row">
                <div class="form-field">
                    <label class="field-label">Nom</label>
                    <InputText id="edit-nom" v-model="localUser.nom" fluid />
                </div>
                <div class="form-field">
                    <label class="field-label">Cognoms</label>
                    <InputText
                        id="edit-cognoms"
                        v-model="localUser.cognoms"
                        fluid
                    />
                </div>
            </div>
            <div class="form-field">
                <label class="field-label">Correu electrònic</label>
                <InputText
                    id="edit-correu"
                    v-model="localUser.correu"
                    type="email"
                    fluid
                />
            </div>
            <div class="form-field">
                <label class="field-label">Telèfon</label>
                <InputText
                    id="edit-telefon"
                    v-model="localUser.telefon"
                    fluid
                />
            </div>
            <div class="form-field">
                <label class="field-label">Rol</label>
                <Select
                    id="edit-rol"
                    v-model="localUser.rol_id"
                    :options="roleOptions"
                    optionLabel="label"
                    optionValue="value"
                    fluid
                />
            </div>
        </div>

        <template #footer>
            <div class="dialog-footer">
                <button
                    class="btn btn-secondary"
                    @click="emit('update:visible', false)"
                >
                    Cancel·lar
                </button>
                <button
                    class="btn btn-primary"
                    @click="emit('save', localUser)"
                    :disabled="saving"
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
import Select from 'primevue/select';

const props = defineProps({
    visible: {
        type: Boolean,
        required: true,
    },
    user: {
        type: Object,
        default: null,
    },
    saving: {
        type: Boolean,
        default: false,
    },
});

const emit = defineEmits(['update:visible', 'save']);

const roleOptions = [
    { label: 'Admin', value: '1' },
    { label: 'Client', value: '2' },
    { label: 'Operator', value: '3' },
    { label: 'Agent', value: '4' },
];

const localUser = ref(null);

watch(
    () => props.user,
    (val) => {
        localUser.value = val ? { ...val } : null;
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
    from {
        transform: rotate(0deg);
    }
    to {
        transform: rotate(360deg);
    }
}
</style>
