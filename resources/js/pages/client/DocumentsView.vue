<template>
    <AppLayout>
        <Header title="Documents i Arxius"
            subtitle="Accedeix, descarrega i gestiona tota la documentació de les teves operacions." />

        <div v-if="carregant" class="loading-state mt-4">
            <i class="pi pi-spin pi-spinner" style="font-size:2rem"></i>
            <p>Carregant documents...</p>
        </div>

        <div v-else class="docs-layout mt-4">
            <aside class="docs-sidebar">
                <p class="sidebar-title">Categories</p>
                <ul class="category-list">
                    <li :class="{ active: currentCategory === null }" @click="currentCategory = null">
                        <Folder :size="15" /> Tots els documents
                    </li>
                    <li v-for="cat in categoriesDisponibles" :key="cat.id"
                        :class="{ active: currentCategory === cat.id }" @click="currentCategory = cat.id">
                        <component :is="iconPerCategoria(cat.name)" :size="15" />
                        {{ cat.name }}
                    </li>
                </ul>
            </aside>

            <main class="docs-main">
                <div class="docs-toolbar">
                    <div class="search-bar">
                        <Search :size="15" class="search-icon" />
                        <input type="text" placeholder="Cercar per nom o referència..." v-model="searchQuery" />
                    </div>
                    <button class="btn-upload" @click="mostrarModalPujar = true">
                        <Upload :size="15" /> Pujar Document
                    </button>
                </div>

                <div v-if="filteredDocs.length === 0" class="empty-state mt-4">
                    <FolderOpen :size="40" class="empty-icon" />
                    <h3>Cap document trobat</h3>
                    <p>No hi ha documents en aquesta categoria o cerca.</p>
                </div>

                <div v-else class="files-grid mt-4">
                    <div v-for="doc in filteredDocs" :key="doc.id" class="file-card">
                        <div class="file-icon" :class="doc.colorClass">
                            <component :is="doc.icon" :size="20" />
                        </div>
                        <div class="file-info">
                            <h4 :title="doc.nom">{{ doc.nom }}</h4>
                            <span class="file-meta">{{ doc.ref }} · {{ doc.data }}</span>
                            <span class="file-size">{{ doc.size }}</span>
                        </div>
                        <button class="btn-download" @click="descarregar(doc.id, doc.nom)" title="Descarregar">
                            <Download :size="15" />
                        </button>
                    </div>
                </div>
            </main>
        </div>

        <!-- Modal Pujar Document -->
        <Dialog :visible="mostrarModalPujar" @update:visible="mostrarModalPujar = $event" modal
            header="Pujar Document" :style="{ width: '480px' }">
            <form @submit.prevent="pujarDocument" class="upload-form">
                <div class="form-group">
                    <label>Tipus de document *</label>
                    <select v-model="uploadForm.tipus_document" required class="form-input">
                        <option value="" disabled>Selecciona...</option>
                        <option v-for="cat in categoriesDisponibles" :key="cat.id" :value="cat.id">
                            {{ cat.name }}
                        </option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Fitxer *</label>
                    <input type="file" ref="fileInput" required class="form-input"
                        accept=".pdf,.png,.jpg,.jpeg,.doc,.docx,.xls,.xlsx" />
                </div>
                <div class="form-actions">
                    <button type="button" class="btn-secondary" @click="mostrarModalPujar = false">Cancel·lar</button>
                    <button type="submit" class="btn-primary" :disabled="pujant">
                        <i class="pi pi-upload" v-if="!pujant"></i>
                        <i class="pi pi-spin pi-spinner" v-else></i>
                        {{ pujant ? 'Pujant...' : 'Pujar' }}
                    </button>
                </div>
            </form>
        </Dialog>
    </AppLayout>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import {
    Folder, FolderOpen, FileText, FileSpreadsheet,
    Globe, Search, Upload, Download, File,
} from 'lucide-vue-next';
import Dialog from 'primevue/dialog';
import AppLayout from '@/layout/AppLayout.vue';
import Header from '@/layout/Header.vue';
import api from '@/plugins/axios';

const carregant = ref(true);
const currentCategory = ref(null);
const searchQuery = ref('');
const fileInput = ref(null);

const mostrarModalPujar = ref(false);
const pujant = ref(false);
const uploadForm = ref({ tipus_document: '' });

// Datos cargados de la BD
const documents = ref([]);
const categoriesDisponibles = ref([]);

// Mapeo de icono y color por nombre de categoría
const CATEGORY_ICONS = {
    'Factures':  { icon: FileSpreadsheet, color: 'icon-red' },
    'CMR':       { icon: FileText,        color: 'icon-blue' },
    'Aduanes':   { icon: Globe,           color: 'icon-green' },
};

const iconPerCategoria = (name) => {
    return CATEGORY_ICONS[name]?.icon ?? File;
};

const colorPerCategoria = (name) => {
    return CATEGORY_ICONS[name]?.color ?? 'icon-teal';
};

const formatSize = (bytes) => {
    if (!bytes) return '—';
    if (bytes < 1024) return bytes + ' B';
    if (bytes < 1048576) return (bytes / 1024).toFixed(0) + ' KB';
    return (bytes / 1048576).toFixed(1) + ' MB';
};

// Cargar los tipos de documento desde el catálogo
const carregarCategories = async () => {
    try {
        const { data } = await api.get('/catalogos');
        categoriesDisponibles.value = data.data.tipus_documents ?? [];
    } catch (e) {
        console.error('Error carregant categories:', e);
    }
};

// Cargar los documentos desde la BD
const carregarDocuments = async () => {
    try {
        const { data } = await api.get('/documentos');
        if (data.status === 'success') {
            documents.value = (data.data ?? []).map(doc => {
                const tipusNom = doc.tipo_documento?.name ?? 'Altres';
                return {
                    id: doc.id,
                    nom: doc.nom_original,
                    ref: doc.operacio_id ? `OP-${String(doc.operacio_id).padStart(4, '0')}`
                       : doc.solicitud_id ? `SOL-${String(doc.solicitud_id).padStart(4, '0')}`
                       : '—',
                    tipusDocumentId: doc.tipus_document,
                    tipusNom,
                    data: doc.data_pujada
                        ? new Date(doc.data_pujada).toLocaleDateString('ca-ES', { day: '2-digit', month: 'short', year: 'numeric' })
                        : '—',
                    size: formatSize(doc.mida),
                    icon: CATEGORY_ICONS[tipusNom]?.icon ?? File,
                    colorClass: CATEGORY_ICONS[tipusNom]?.color ?? 'icon-teal',
                };
            });
        }
    } catch (e) {
        console.error('Error carregant documents:', e);
    }
};

// Filtrado por categoría y búsqueda
const filteredDocs = computed(() =>
    documents.value.filter((doc) => {
        const matchesCategory = currentCategory.value === null || doc.tipusDocumentId === currentCategory.value;
        const q = searchQuery.value.toLowerCase();
        const matchesSearch = doc.nom.toLowerCase().includes(q) || doc.ref.toLowerCase().includes(q);
        return matchesCategory && matchesSearch;
    })
);

// Subir documento real al backend
const pujarDocument = async () => {
    const fitxer = fileInput.value?.files?.[0];
    if (!fitxer) return;

    pujant.value = true;
    try {
        const formData = new FormData();
        formData.append('fitxer', fitxer);
        formData.append('tipus_document', uploadForm.value.tipus_document);

        await api.post('/documentos', formData, {
            headers: { 'Content-Type': 'multipart/form-data' }
        });

        // Cerrar modal y resetear
        mostrarModalPujar.value = false;
        uploadForm.value = { tipus_document: '' };

        // Recargar la lista de documentos
        await carregarDocuments();
    } catch (error) {
        console.error('Error al pujar document:', error);
        alert('Hi ha hagut un error en pujar el document.');
    } finally {
        pujant.value = false;
    }
};

// Descargar documento real desde el backend
const descarregar = async (id, nomOriginal) => {
    try {
        const response = await api.get(`/documentos/${id}/descargar`, {
            responseType: 'blob'
        });

        // Crear un enlace temporal para forzar la descarga
        const url = window.URL.createObjectURL(new Blob([response.data]));
        const link = document.createElement('a');
        link.href = url;

        // Intentar obtener nombre del header, si no usar nomOriginal
        const disposition = response.headers['content-disposition'];
        const filename = disposition
            ? decodeURIComponent(disposition.split('filename=')[1]?.replace(/"/g, '') ?? nomOriginal)
            : nomOriginal;

        link.setAttribute('download', filename);
        document.body.appendChild(link);
        link.click();
        link.remove();
        window.URL.revokeObjectURL(url);
    } catch (error) {
        console.error('Error al descarregar:', error);
        alert('No s\'ha pogut descarregar el document.');
    }
};

onMounted(async () => {
    await Promise.all([carregarCategories(), carregarDocuments()]);
    carregant.value = false;
});
</script>

<style scoped>
.mt-4 {
    margin-top: 1.5rem;
}

/* ── Loading ───────────────────────────────────────────────── */
.loading-state {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: 4rem;
    color: #6b7280;
    gap: 1rem;
}

/* ── Layout ─────────────────────────────────────────────────── */
.docs-layout {
    display: flex;
    gap: 1.5rem;
    align-items: flex-start;
}

/* ── Sidebar ────────────────────────────────────────────────── */
.docs-sidebar {
    width: 220px;
    background: white;
    padding: 1.25rem;
    border-radius: 14px;
    border: 1px solid #e5e7eb;
    box-shadow: 0 1px 4px rgba(0, 0, 0, 0.04);
    flex-shrink: 0;
}

.sidebar-title {
    font-size: 0.7rem;
    text-transform: uppercase;
    letter-spacing: 0.07em;
    color: #9ca3af;
    font-weight: 700;
    margin: 0 0 0.75rem 0.25rem;
}

.category-list {
    list-style: none;
    padding: 0;
    margin: 0;
    display: flex;
    flex-direction: column;
    gap: 0.25rem;
}

.category-list li {
    padding: 0.65rem 0.85rem;
    border-radius: 8px;
    cursor: pointer;
    display: flex;
    align-items: center;
    gap: 0.6rem;
    color: #4b5563;
    font-size: 0.875rem;
    font-weight: 500;
    transition: all 0.15s;
}

.category-list li:hover {
    background: #f3f4f6;
    color: #111827;
}

.category-list li.active {
    background: #f0fdfa;
    color: #1a8a7d;
    font-weight: 600;
}

/* ── Main area ──────────────────────────────────────────────── */
.docs-main {
    flex: 1;
    min-width: 0;
}

/* ── Toolbar ────────────────────────────────────────────────── */
.docs-toolbar {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    background: white;
    padding: 0.85rem 1rem;
    border-radius: 12px;
    border: 1px solid #e5e7eb;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.03);
}

.search-bar {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    background: #f9fafb;
    padding: 0.5rem 0.85rem;
    border-radius: 8px;
    flex: 1;
    border: 1px solid #e5e7eb;
    transition: border-color 0.2s;
}

.search-bar:focus-within {
    border-color: #1a8a7d;
}

.search-icon {
    color: #9ca3af;
    flex-shrink: 0;
}

.search-bar input {
    border: none;
    background: transparent;
    outline: none;
    width: 100%;
    font-size: 0.875rem;
    color: #374151;
    font-family: inherit;
}

.btn-upload {
    display: inline-flex;
    align-items: center;
    gap: 0.4rem;
    background: #1a8a7d;
    color: white;
    border: none;
    padding: 0.55rem 1rem;
    border-radius: 8px;
    font-size: 0.875rem;
    font-weight: 600;
    cursor: pointer;
    white-space: nowrap;
    transition: background 0.2s;
    font-family: inherit;
}

.btn-upload:hover {
    background: #136a60;
}

/* ── Files grid ─────────────────────────────────────────────── */
.files-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
    gap: 1rem;
}

.file-card {
    background: white;
    border: 1px solid #e5e7eb;
    border-radius: 12px;
    padding: 1rem 1.1rem;
    display: flex;
    align-items: center;
    gap: 1rem;
    transition: box-shadow 0.2s, border-color 0.2s, transform 0.2s;
}

.file-card:hover {
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.06);
    border-color: #d1d5db;
    transform: translateY(-1px);
}

.file-icon {
    width: 44px;
    height: 44px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}

.icon-red   { background: #fee2e2; color: #ef4444; }
.icon-blue  { background: #dbeafe; color: #3b82f6; }
.icon-green { background: #d1fae5; color: #10b981; }
.icon-teal  { background: #f0fdfa; color: #1a8a7d; }

.file-info {
    flex: 1;
    min-width: 0;
    display: flex;
    flex-direction: column;
    gap: 0.15rem;
}

.file-info h4 {
    margin: 0;
    font-size: 0.875rem;
    font-weight: 600;
    color: #111827;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.file-meta {
    font-size: 0.75rem;
    color: #6b7280;
}

.file-size {
    font-size: 0.72rem;
    color: #9ca3af;
    font-weight: 500;
}

.btn-download {
    background: transparent;
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    width: 34px;
    height: 34px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #6b7280;
    cursor: pointer;
    flex-shrink: 0;
    transition: all 0.2s;
    font-family: inherit;
}

.btn-download:hover {
    background: #f0fdfa;
    border-color: #1a8a7d;
    color: #1a8a7d;
}

/* ── Empty state ────────────────────────────────────────────── */
.empty-state {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: 4rem 2rem;
    background: white;
    border-radius: 14px;
    border: 1px dashed #d1d5db;
    color: #9ca3af;
    gap: 0.75rem;
    text-align: center;
}

.empty-icon {
    color: #d1d5db;
}

.empty-state h3 {
    margin: 0;
    font-size: 1rem;
    font-weight: 600;
    color: #6b7280;
}

.empty-state p {
    margin: 0;
    font-size: 0.875rem;
}

/* ── Upload Modal Form ─────────────────────────────────────── */
.upload-form {
    display: flex;
    flex-direction: column;
    gap: 1.25rem;
    padding-top: 1rem;
}

.form-group {
    display: flex;
    flex-direction: column;
}

.form-group label {
    font-size: 0.875rem;
    font-weight: 600;
    color: #374151;
    margin-bottom: 0.5rem;
    font-family: 'Instrument Sans', sans-serif;
}

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
    margin-top: 1rem;
    border-top: 1px solid #e5e7eb;
    padding-top: 1rem;
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
    font-family: inherit;
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
    font-family: inherit;
}

/* ── Responsive ─────────────────────────────────────────────── */
@media (max-width: 768px) {
    .docs-layout {
        flex-direction: column;
    }

    .docs-sidebar {
        width: 100%;
    }

    .category-list {
        flex-direction: row;
        flex-wrap: wrap;
    }
}
</style>