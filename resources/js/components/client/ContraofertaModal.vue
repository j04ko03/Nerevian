<template>
    <Dialog
        :visible="visible"
        modal
        header="Enviar Contraoferta"
        :style="{ width: '460px' }"
        @hide="$emit('update:visible', false)"
    >
        <div class="form-body">
            <p class="form-hint">
                Proposa les teves condicions. L'operador rebrà la teva contraoferta i podrà enviar
                una nova cotització.
            </p>

            <div class="form-row">
                <label class="form-label">Preu proposat (€) <span class="required">*</span></label>
                <InputNumber
                    v-model="form.pressupost"
                    :min="0"
                    :minFractionDigits="2"
                    :maxFractionDigits="2"
                    placeholder="0.00"
                    class="w-full"
                />
            </div>

            <div class="form-row">
                <label class="form-label">Temps estimat (dies)</label>
                <InputNumber v-model="form.temps_estimat" :min="1" placeholder="ex. 15" class="w-full" />
            </div>

            <div class="form-row">
                <label class="form-label">Comentaris / Justificació</label>
                <Textarea
                    v-model="form.comentaris"
                    rows="3"
                    placeholder="Explica el motiu de la contraoferta..."
                    class="w-full"
                />
            </div>
        </div>

        <template #footer>
            <Button
                label="Cancel·lar"
                severity="secondary"
                outlined
                @click="$emit('update:visible', false)"
            />
            <Button
                label="Enviar Contraoferta"
                icon="pi pi-send"
                :disabled="!form.pressupost"
                @click="enviar"
            />
        </template>
    </Dialog>
</template>

<script setup>
import { ref, watch } from 'vue';
import Dialog from 'primevue/dialog';
import InputNumber from 'primevue/inputnumber';
import Textarea from 'primevue/textarea';
import Button from 'primevue/button';

const props = defineProps({
    visible: Boolean,
    solicitud: Object,
});

const emit = defineEmits(['update:visible', 'enviar']);

const form = ref({ pressupost: null, temps_estimat: null, comentaris: '' });

watch(
    () => props.visible,
    (v) => {
        if (v) form.value = { pressupost: null, temps_estimat: null, comentaris: '' };
    },
);

function enviar() {
    emit('enviar', { ...form.value });
}
</script>

<style scoped>
.form-body {
    display: flex;
    flex-direction: column;
    gap: 1.1rem;
    padding: 0.25rem 0;
}

.form-hint {
    font-size: 0.825rem;
    color: #6b7280;
    margin: 0;
    line-height: 1.5;
}

.form-row {
    display: flex;
    flex-direction: column;
    gap: 0.35rem;
}

.form-label {
    font-size: 0.825rem;
    font-weight: 600;
    color: #374151;
}

.required {
    color: #ef4444;
}

.w-full {
    width: 100%;
}
</style>