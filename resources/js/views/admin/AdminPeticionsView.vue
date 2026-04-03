<template>
    <AdminLayout>
        <template #header-title>Peticiones</template>

        <div class="peticiones-container">
            <div class="header-text">
                <h1 class="page-title">Peticiones de Registro</h1>
                <p class="page-subtitle">
                    Solicitudes de nuevos usuarios para unirse a la plataforma
                </p>
            </div>

            <div v-if="loading" class="loading-state">
                <div class="spinner"></div>
                <p>Cargando peticiones...</p>
            </div>

            <div v-else-if="peticiones.length === 0" class="empty-state">
                <svg
                    class="empty-icon"
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="1.5"
                >
                    <path
                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5.586a1 1 0 0 1 .707.293l5.414 5.414a1 1 0 0 1 .293.707V19a2 2 0 0 1-2 2z"
                    ></path>
                </svg>
                <h3>No hay peticiones pendientes</h3>
                <p>Todas las solicitudes han sido procesadas.</p>
            </div>

            <div v-else class="peticiones-list">
                <div
                    v-for="peticion in peticiones"
                    :key="peticion.id"
                    class="peticion-card"
                >
                    <div class="card-main">
                        <div class="card-header">
                            <div class="company-info">
                                <div class="company-icon">
                                    <svg
                                        viewBox="0 0 24 24"
                                        fill="none"
                                        stroke="currentColor"
                                        stroke-width="2"
                                    >
                                        <rect
                                            x="4"
                                            y="2"
                                            width="16"
                                            height="20"
                                            rx="2"
                                            ry="2"
                                        ></rect>
                                        <path d="M9 22v-4h6v4"></path>
                                        <path d="M8 6h.01"></path>
                                        <path d="M16 6h.01"></path>
                                        <path d="M8 10h.01"></path>
                                        <path d="M16 10h.01"></path>
                                        <path d="M8 14h.01"></path>
                                        <path d="M16 14h.01"></path>
                                    </svg>
                                </div>
                                <div class="company-details">
                                    <h3 class="company-name">
                                        {{ peticion.nom_empresa }}
                                    </h3>
                                    <span class="time-ago">{{
                                        peticion.data_creacio
                                    }}</span>
                                </div>
                            </div>
                            <div class="status-badge">Pendiente</div>
                        </div>

                        <div class="card-body">
                            <div class="info-grid">
                                <div class="info-item">
                                    <svg
                                        class="info-icon"
                                        viewBox="0 0 24 24"
                                        fill="none"
                                        stroke="currentColor"
                                        stroke-width="2"
                                    >
                                        <path
                                            d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"
                                        ></path>
                                        <circle cx="12" cy="7" r="4"></circle>
                                    </svg>
                                    <span>{{ peticion.contacte }}</span>
                                </div>
                                <div class="info-item">
                                    <svg
                                        class="info-icon"
                                        viewBox="0 0 24 24"
                                        fill="none"
                                        stroke="currentColor"
                                        stroke-width="2"
                                    >
                                        <path
                                            d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"
                                        ></path>
                                        <polyline
                                            points="22,6 12,13 2,6"
                                        ></polyline>
                                    </svg>
                                    <span>{{ peticion.correu }}</span>
                                </div>
                                <div class="info-item">
                                    <svg
                                        class="info-icon"
                                        viewBox="0 0 24 24"
                                        fill="none"
                                        stroke="currentColor"
                                        stroke-width="2"
                                    >
                                        <path
                                            d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"
                                        ></path>
                                    </svg>
                                    <span>{{ peticion.telefon || 'N/A' }}</span>
                                </div>
                            </div>

                            <div class="message-box">
                                <svg
                                    class="info-icon msg-icon"
                                    viewBox="0 0 24 24"
                                    fill="none"
                                    stroke="currentColor"
                                    stroke-width="2"
                                >
                                    <path
                                        d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"
                                    ></path>
                                </svg>
                                <p>
                                    {{
                                        peticion.missatge ||
                                        'Sin mensaje adicional.'
                                    }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="card-actions">
                        <button
                            @click="confirmApprove(peticion)"
                            class="btn-approve"
                            :disabled="processing === peticion.id"
                        >
                            <svg
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="3"
                                class="btn-icon"
                            >
                                <polyline points="20 6 9 17 4 12"></polyline>
                            </svg>
                            <span>Aprobar</span>
                        </button>
                        <button
                            @click="confirmReject(peticion)"
                            class="btn-reject"
                            :disabled="processing === peticion.id"
                        >
                            <svg
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="3"
                                class="btn-icon"
                            >
                                <line x1="18" y1="6" x2="6" y2="18"></line>
                                <line x1="6" y1="6" x2="18" y2="18"></line>
                            </svg>
                            <span>Rechazar</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal de Confirmación para Rechazo -->
        <div v-if="showModal" class="modal-overlay">
            <div class="modal-content">
                <div class="modal-header">
                    <div class="warning-icon">
                        <svg
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="2"
                        >
                            <path
                                d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"
                            ></path>
                            <line x1="12" y1="9" x2="12" y2="13"></line>
                            <line x1="12" y1="17" x2="12.01" y2="17"></line>
                        </svg>
                    </div>
                    <h3>Confirmar Rechazo</h3>
                </div>
                <div class="modal-body">
                    <p>
                        ¿Estás seguro de que deseas rechazar la solicitud de
                        <strong>{{ selectedPeticion?.nom_empresa }}</strong
                        >?
                    </p>
                    <p class="modal-subtext">
                        Esta acción marcará la solicitud como rechazada
                        definitivamente.
                    </p>
                </div>
                <div class="modal-footer">
                    <button @click="showModal = false" class="btn-secondary">
                        Cancelar
                    </button>
                    <button
                        @click="rejectPeticion"
                        class="btn-danger"
                        :disabled="processing === selectedPeticion?.id"
                    >
                        Confirmar Rechazo
                    </button>
                </div>
            </div>
        </div>

        <!-- Modal de Confirmación para Aprobación -->
        <div v-if="showApproveModal" class="modal-overlay">
            <div class="modal-content">
                <div class="modal-header">
                    <div class="success-icon-modal">
                        <svg
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="2.5"
                        >
                            <polyline points="20 6 9 17 4 12"></polyline>
                        </svg>
                    </div>
                    <h3>Confirmar Aprobación</h3>
                </div>
                <div class="modal-body">
                    <p>
                        ¿Estás seguro de que deseas aprobar la solicitud de
                        <strong>{{ selectedPeticion?.nom_empresa }}</strong
                        >?
                    </p>
                    <p class="modal-subtext">
                        Se creará un acceso de usuario automáticamente.
                    </p>
                </div>
                <div class="modal-footer">
                    <button
                        @click="showApproveModal = false"
                        class="btn-secondary"
                    >
                        Cancelar
                    </button>
                    <button
                        @click="approvePeticion"
                        class="btn-approve-modal"
                        :disabled="processing === selectedPeticion?.id"
                    >
                        Confirmar Aprobación
                    </button>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import AdminLayout from '@/layouts/AdminLayout.vue';
import api from '@/plugins/axios';

const loading = ref(true);
const peticiones = ref([]);
const processing = ref(null);
const showModal = ref(false);
const showApproveModal = ref(false);
const selectedPeticion = ref(null);

async function fetchPeticiones() {
    loading.value = true;
    try {
        const { data } = await api.get('/admin/registration-requests');
        peticiones.value = data.data;
    } catch (err) {
        console.error('Error fetching peticiones:', err);
        alert(
            err.response?.status === 403
                ? 'No tienes permisos para ver esto.'
                : 'Error al cargar las peticiones.',
        );
    } finally {
        loading.value = false;
    }
}

function confirmApprove(peticion) {
    selectedPeticion.value = peticion;
    showApproveModal.value = true;
}

async function approvePeticion() {
    if (!selectedPeticion.value) return;

    processing.value = selectedPeticion.value.id;
    try {
        await api.post(
            `/admin/registration-requests/${selectedPeticion.value.id}/approve`,
        );
        peticiones.value = peticiones.value.filter(
            (p) => p.id !== selectedPeticion.value.id,
        );
        showApproveModal.value = false;
    } catch (err) {
        alert(err.response?.data?.message || 'Error al aprobar la petición.');
    } finally {
        processing.value = null;
        selectedPeticion.value = null;
    }
}

function confirmReject(peticion) {
    selectedPeticion.value = peticion;
    showModal.value = true;
}

async function rejectPeticion() {
    if (!selectedPeticion.value) return;

    processing.value = selectedPeticion.value.id;
    try {
        await api.post(
            `/admin/registration-requests/${selectedPeticion.value.id}/reject`,
        );
        peticiones.value = peticiones.value.filter(
            (p) => p.id !== selectedPeticion.value.id,
        );
        showModal.value = false;
    } catch (err) {
        alert(err.response?.data?.message || 'Error al rechazar la petición.');
    } finally {
        processing.value = null;
        selectedPeticion.value = null;
    }
}

onMounted(fetchPeticiones);
</script>

<style scoped>
.peticiones-container {
    max-width: 1000px;
}

.header-text {
    margin-bottom: 2.5rem;
}

.page-title {
    font-size: 1.75rem;
    font-weight: 800;
    color: #111827;
    margin: 0 0 0.5rem;
}

.page-subtitle {
    color: #6b7280;
    font-size: 0.95rem;
    margin: 0;
}

/* List & Cards */
.peticiones-list {
    display: flex;
    flex-direction: column;
    gap: 1.5rem;
}

.peticion-card {
    background: white;
    border: 1px solid #e5e7eb;
    border-radius: 16px;
    display: flex;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
    overflow: hidden;
}

.card-main {
    flex: 1;
    padding: 1.75rem;
}

.card-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 1.5rem;
}

.company-info {
    display: flex;
    gap: 1rem;
}

.company-icon {
    width: 48px;
    height: 48px;
    background-color: #f1f5f9;
    color: #64748b;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 10px;
}

.company-icon svg {
    width: 100%;
    height: 100%;
}

.company-name {
    font-size: 1.125rem;
    font-weight: 700;
    color: #111827;
    margin: 0 0 0.25rem;
}

.time-ago {
    font-size: 0.85rem;
    color: #94a3b8;
}

.status-badge {
    background-color: #fff7ed;
    color: #f97316;
    font-size: 0.75rem;
    font-weight: 600;
    padding: 0.35rem 0.75rem;
    border-radius: 9999px;
    border: 1px solid #ffedd5;
}

.card-body {
    display: flex;
    flex-direction: column;
    gap: 1.25rem;
}

.info-grid {
    display: flex;
    gap: 2rem;
    flex-wrap: wrap;
}

.info-item {
    display: flex;
    align-items: center;
    gap: 0.625rem;
    color: #4b5563;
    font-size: 0.9rem;
}

.info-icon {
    width: 18px;
    height: 18px;
    color: #94a3b8;
}

.message-box {
    background-color: #f8fafc;
    padding: 1rem 1.25rem;
    border-radius: 12px;
    display: flex;
    gap: 0.75rem;
    border: 1px solid #f1f5f9;
}

.msg-icon {
    flex-shrink: 0;
    margin-top: 3px;
}

.message-box p {
    margin: 0;
    font-size: 0.875rem;
    line-height: 1.5;
    color: #475569;
}

/* Actions */
.card-actions {
    width: 180px;
    background-color: #ffffff;
    border-left: 1px solid #f1f5f9;
    padding: 1.75rem;
    display: flex;
    flex-direction: column;
    justify-content: center;
    gap: 1rem;
}

.btn-approve,
.btn-reject {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.625rem;
    padding: 0.75rem;
    border-radius: 8px;
    font-size: 0.9rem;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s;
    width: 100%;
}

.btn-approve {
    background-color: #10b981;
    color: white;
    border: none;
}

.btn-approve:hover:not(:disabled) {
    background-color: #059669;
}

.btn-reject {
    background-color: white;
    color: #ef4444;
    border: 1px solid #fee2e2;
}

.btn-reject:hover:not(:disabled) {
    background-color: #fef2f2;
    border-color: #fecaca;
}

.btn-icon {
    width: 16px;
    height: 16px;
}

/* States */
.loading-state,
.empty-state {
    text-align: center;
    padding: 4rem 0;
}

.spinner {
    width: 40px;
    height: 40px;
    border: 3px solid #e2e8f0;
    border-top-color: #1a8a7d;
    border-radius: 50%;
    animation: spin 0.8s linear infinite;
    margin: 0 auto 1rem;
}

@keyframes spin {
    to {
        transform: rotate(360deg);
    }
}

.empty-icon {
    width: 64px;
    height: 64px;
    color: #cbd5e1;
    margin-bottom: 1.5rem;
}

.empty-state h3 {
    color: #334155;
    margin-bottom: 0.5rem;
}

.empty-state p {
    color: #64748b;
}

/* Modal */
.modal-overlay {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-color: rgba(0, 0, 0, 0.5);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 100;
    padding: 1rem;
}

.modal-content {
    background: white;
    border-radius: 16px;
    width: 100%;
    max-width: 450px;
    padding: 2rem;
    box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
}

.modal-header {
    text-align: center;
    margin-bottom: 1.5rem;
}

.warning-icon,
.success-icon-modal {
    width: 56px;
    height: 56px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 1.25rem;
}

.warning-icon {
    background-color: #fef2f2;
    color: #ef4444;
}

.success-icon-modal {
    background-color: #ecfdf5;
    color: #10b981;
}

.warning-icon svg,
.success-icon-modal svg {
    width: 28px;
    height: 28px;
}

.modal-header h3 {
    font-size: 1.25rem;
    margin: 0;
    color: #111827;
}

.modal-body {
    text-align: center;
    margin-bottom: 2rem;
}

.modal-body p {
    color: #4b5563;
    margin: 0 0 0.5rem;
}

.modal-subtext {
    font-size: 0.875rem;
    color: #9ca3af !important;
}

.modal-footer {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 1rem;
}

.btn-secondary {
    padding: 0.75rem;
    border-radius: 8px;
    border: 1px solid #d1d5db;
    background: white;
    color: #4b5563;
    font-weight: 600;
    cursor: pointer;
}

.btn-danger,
.btn-approve-modal {
    padding: 0.75rem;
    border-radius: 8px;
    border: none;
    font-weight: 600;
    cursor: pointer;
    color: white;
}

.btn-danger {
    background-color: #ef4444;
}

.btn-danger:hover {
    background-color: #dc2626;
}

.btn-approve-modal {
    background-color: #10b981;
}

.btn-approve-modal:hover {
    background-color: #059669;
}
</style>
