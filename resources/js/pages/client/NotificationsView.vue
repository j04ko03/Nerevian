<template>
    <AppLayout>
        <div class="notifications-header">
            <h1>Notificacions</h1>
            <p>Historial d'actualitzacions dels teus pedidos</p>
        </div>

        <div v-if="loading" class="loading-state">
            <i class="pi pi-spin pi-spinner" style="font-size: 2rem"></i>
            <p>Carregant notificacions...</p>
        </div>

        <div v-else-if="notifications.length === 0" class="empty-state">
            <i class="pi pi-bell-slash" style="font-size: 3rem; color: #d1d5db;"></i>
            <h3>No tens notificacions</h3>
            <p>T'avisarem quan hi hagi canvis en els teus pedidos.</p>
        </div>

        <div v-else class="notifications-list">
            <div v-for="notif in notifications" :key="notif.id" class="notification-card" :class="{ 'unread': !notif.llegida }">
                <div class="notif-icon">
                    <i class="pi pi-bell"></i>
                </div>
                <div class="notif-content">
                    <div class="notif-top">
                        <h3>{{ notif.titol }}</h3>
                        <span class="notif-date">{{ formatData(notif.created_at) }}</span>
                    </div>
                    <p>{{ notif.missatge }}</p>
                    <div class="notif-actions">
                        <button v-if="!notif.llegida" @click="marcarLlegida(notif.id)" class="btn-text">Marcar com a llegida</button>
                        <button @click="eliminarNotificacio(notif.id)" class="btn-text delete">Eliminar</button>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import AppLayout from '@/layout/AppLayout.vue';
import api from '@/plugins/axios';

const notifications = ref([]);
const loading = ref(true);

const fetchNotifications = async () => {
    try {
        const { data } = await api.get('/notificaciones');
        notifications.value = data.data;
    } catch (e) {
        console.error("Error carregant notificacions:", e);
    } finally {
        loading.value = false;
    }
};

const marcarLlegida = async (id) => {
    try {
        await api.post(`/notificaciones/${id}/read`);
        const notif = notifications.value.find(n => n.id === id);
        if (notif) notif.llegida = true;
    } catch (e) {
        console.error("Error marcant notificació com a llegida:", e);
    }
};

const eliminarNotificacio = async (id) => {
    try {
        await api.delete(`/notificaciones/${id}`);
        notifications.value = notifications.value.filter(n => n.id !== id);
    } catch (e) {
        console.error("Error eliminant notificació:", e);
    }
};

const formatData = (dataStr) => {
    const d = new Date(dataStr);
    return d.toLocaleDateString('ca-ES', { day: '2-digit', month: '2-digit', year: 'numeric', hour: '2-digit', minute: '2-digit' });
};

onMounted(fetchNotifications);
</script>

<style scoped>
.notifications-header {
    margin-bottom: 2rem;
}

.notifications-header h1 {
    font-size: 2rem;
    font-weight: 700;
    color: #111827;
    margin: 0;
}

.notifications-header p {
    color: #6b7280;
    margin-top: 0.5rem;
}

.loading-state, .empty-state {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: 4rem;
    text-align: center;
    background: white;
    border-radius: 12px;
    border: 1px solid #e5e7eb;
}

.empty-state h3 {
    margin-top: 1.5rem;
    color: #374151;
}

.empty-state p {
    color: #6b7280;
}

.notifications-list {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.notification-card {
    display: flex;
    gap: 1.5rem;
    padding: 1.5rem;
    background: white;
    border-radius: 12px;
    border: 1px solid #e5e7eb;
    transition: all 0.2s;
}

.notification-card.unread {
    border-left: 4px solid #1a8a7d;
    background: #f0fdfa;
}

.notif-icon {
    width: 48px;
    height: 48px;
    border-radius: 50%;
    background: #f3f4f6;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #6b7280;
    flex-shrink: 0;
}

.unread .notif-icon {
    background: #1a8a7d;
    color: white;
}

.notif-content {
    flex: 1;
}

.notif-top {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 0.5rem;
}

.notif-top h3 {
    margin: 0;
    font-size: 1.1rem;
    font-weight: 600;
    color: #111827;
}

.notif-date {
    font-size: 0.85rem;
    color: #9ca3af;
}

.notif-content p {
    color: #4b5563;
    margin: 0 0 1rem 0;
    line-height: 1.5;
}

.notif-actions {
    display: flex;
    gap: 1rem;
}

.btn-text {
    background: none;
    border: none;
    color: #1a8a7d;
    font-size: 0.9rem;
    font-weight: 600;
    cursor: pointer;
    padding: 0;
    transition: color 0.2s;
}

.btn-text:hover {
    color: #146e63;
}

.btn-text.delete {
    color: #ef4444;
}

.btn-text.delete:hover {
    color: #dc2626;
}
</style>
