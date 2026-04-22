<template>
    <AppLayout>
        <Header title="Clients" subtitle="Gestió dels usuaris amb rol client" />
        <ClientsStats :usuaris="usuaris" :loading="loading" />
        <ClientsTable
            :usuaris="usuaris"
            :loading="loading"
            @edit="openEditDialog"
            @delete="confirmDelete"
            @activate="activateClient"
        />
        <ClientsEditDialog
            v-model:visible="editDialogVisible"
            :client="editingClient"
            :saving="saving"
            @save="saveClient"
        />
        <ConfirmDialog />
        <Toast />
    </AppLayout>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useConfirm } from 'primevue/useconfirm';
import { useToast } from 'primevue/usetoast';
import ConfirmDialog from 'primevue/confirmdialog';
import Toast from 'primevue/toast';

import AppLayout from '@/layout/AppLayout.vue';
import Header from '@/layout/Header.vue';
import api from '@/plugins/axios';

import ClientsStats from '@/components/operador/clients/ClientsStats.vue';
import ClientsTable from '@/components/operador/clients/ClientsTable.vue';
import ClientsEditDialog from '@/components/operador/clients/ClientsEditDialog.vue';

const confirm = useConfirm();
const toast = useToast();

const usuaris = ref([]);
const loading = ref(false);
const saving = ref(false);
const editDialogVisible = ref(false);
const editingClient = ref(null);

async function fetchUsuaris() {
    loading.value = true;
    try {
        const { data } = await api.get('/operador/clients');
        usuaris.value = data;
    } catch {
        toast.add({
            severity: 'error',
            summary: 'Error',
            detail: "No s'han pogut carregar els clients",
            life: 3000,
        });
    } finally {
        loading.value = false;
    }
}

// Donar d'alta un usuari pendent
async function activateClient(usuari) {
    try {
        await api.post('/operador/clients', { usuari_id: usuari.id });
        const idx = usuaris.value.findIndex((u) => u.id === usuari.id);
        if (idx !== -1) {
            usuaris.value[idx] = { ...usuaris.value[idx], actiu: true };
            usuaris.value = [...usuaris.value];
        }
        toast.add({
            severity: 'success',
            summary: 'Alta realitzada',
            detail: `${usuari.nom} ${usuari.cognoms} ara és un client actiu`,
            life: 3000,
        });
    } catch {
        toast.add({
            severity: 'error',
            summary: 'Error',
            detail: "No s'ha pogut donar d'alta el client",
            life: 3000,
        });
    }
}

function openEditDialog(usuari) {
    editingClient.value = { ...usuari };
    editDialogVisible.value = true;
}

async function saveClient(clientData) {
    saving.value = true;
    try {
        const clientId = clientData.client?.id;
        const { data: response } = await api.put(`/operador/clients/${clientId}`, clientData);
        await fetchUsuaris();
        editDialogVisible.value = false;
        toast.add({
            severity: 'success',
            summary: 'Desat',
            detail: 'Client actualitzat correctament',
            life: 3000,
        });
    } catch {
        toast.add({
            severity: 'error',
            summary: 'Error',
            detail: "No s'ha pogut actualitzar el client",
            life: 3000,
        });
    } finally {
        saving.value = false;
    }
}

function confirmDelete(usuari) {
    const nom = `${usuari.nom ?? ''} ${usuari.cognoms ?? ''}`.trim();
    confirm.require({
        message: `Estàs segur que vols eliminar la ficha de client de ${nom}?`,
        header: 'Confirmar eliminació',
        icon: 'pi pi-exclamation-triangle',
        rejectProps: { label: 'Cancel·lar', severity: 'secondary', outlined: true },
        acceptProps: { label: 'Eliminar', severity: 'danger' },
        accept: () => deleteClient(usuari),
    });
}

async function deleteClient(usuari) {
    try {
        await api.delete(`/operador/clients/${usuari.client.id}`);
        const idx = usuaris.value.findIndex((u) => u.id === usuari.id);
        if (idx !== -1) {
            usuaris.value[idx] = { ...usuaris.value[idx], actiu: false, client: null };
            usuaris.value = [...usuaris.value];
        }
        toast.add({
            severity: 'success',
            summary: 'Eliminat',
            detail: 'Ficha de client eliminada correctament',
            life: 3000,
        });
    } catch {
        toast.add({
            severity: 'error',
            summary: 'Error',
            detail: "No s'ha pogut eliminar la ficha de client",
            life: 3000,
        });
    }
}

onMounted(fetchUsuaris);
</script>

<style scoped>
</style>