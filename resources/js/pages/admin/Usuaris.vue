<template>
    <AppLayout>
        <Header title="Usuaris" subtitle="Gestió dels usuaris del sistema" />
        <UsuarisStats :users="users" :loading="loading" />
        <UsuarisTable
            :users="users"
            :loading="loading"
            @edit="openEditDialog"
            @delete="confirmDelete"
        />
        <UsuarisEditDialog
            v-model:visible="editDialogVisible"
            :user="editingUser"
            :saving="saving"
            @save="saveUser"
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
import api from '@/plugins/axios';

import UsuarisStats from '@/components/admin/usuaris/UsuarisStats.vue';
import UsuarisTable from '@/components/admin/usuaris/UsuarisTable.vue';
import UsuarisEditDialog from '@/components/admin/usuaris/UsuarisEditDialog.vue';
import Header from '@/layout/Header.vue';

const confirm = useConfirm();
const toast = useToast();

const users = ref([]);
const loading = ref(false);
const saving = ref(false);
const editDialogVisible = ref(false);
const editingUser = ref(null);

async function fetchUsers() {
    loading.value = true;
    try {
        const { data } = await api.get('/admin/users');
        users.value = data;
    } catch {
        toast.add({
            severity: 'error',
            summary: 'Error',
            detail: "No s'han pogut carregar els usuaris",
            life: 3000,
        });
    } finally {
        loading.value = false;
    }
}

function openEditDialog(user) {
    editingUser.value = { ...user };
    editDialogVisible.value = true;
}

async function saveUser(userData) {
    saving.value = true;
    try {
        const { data: response } = await api.put(`/admin/users/${userData.id}`, userData);
        const idx = users.value.findIndex((u) => u.id === userData.id);
        if (idx !== -1) {
            users.value[idx] = { ...users.value[idx], ...response.data };
            users.value = [...users.value];
        }
        editDialogVisible.value = false;
        toast.add({
            severity: 'success',
            summary: 'Desat',
            detail: 'Usuari actualitzat correctament',
            life: 3000,
        });
    } catch {
        toast.add({
            severity: 'error',
            summary: 'Error',
            detail: "No s'ha pogut actualitzar l'usuari",
            life: 3000,
        });
    } finally {
        saving.value = false;
    }
}

function confirmDelete(user) {
    confirm.require({
        message: `Estàs segur que vols eliminar l'usuari ${user.nom} ${user.cognoms}?`,
        header: 'Confirmar eliminació',
        icon: 'pi pi-exclamation-triangle',
        rejectProps: {
            label: 'Cancel·lar',
            severity: 'secondary',
            outlined: true,
        },
        acceptProps: { label: 'Eliminar', severity: 'danger' },
        accept: () => deleteUser(user.id),
    });
}

async function deleteUser(id) {
    try {
        await api.delete(`/admin/users/${id}`);
        users.value = users.value.filter((u) => u.id !== id);
        toast.add({
            severity: 'success',
            summary: 'Eliminat',
            detail: 'Usuari eliminat correctament',
            life: 3000,
        });
    } catch {
        toast.add({
            severity: 'error',
            summary: 'Error',
            detail: "No s'ha pogut eliminar l'usuari",
            life: 3000,
        });
    }
}

onMounted(fetchUsers);
</script>

<style scoped></style>
