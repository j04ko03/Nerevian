<template>
    <AppLayout>
        <Header title="Gestor Documental"
            subtitle="Descarrega i puja tota la documentació associada a les teves operacions." />

        <div class="docs-layout mt-4">
            <aside class="docs-sidebar">
                <h3 class="sidebar-title">Categories</h3>
                <ul class="category-list">
                    <li :class="{ active: currentCategory === 'Tots' }" @click="currentCategory = 'Tots'">
                        <i class="pi pi-folder"></i> Tots els documents
                    </li>
                    <li :class="{ active: currentCategory === 'Factures' }" @click="currentCategory = 'Factures'">
                        <i class="pi pi-file-excel"></i> Factures
                    </li>
                    <li :class="{ active: currentCategory === 'CMR' }" @click="currentCategory = 'CMR'">
                        <i class="pi pi-file"></i> CMRs / Albarans
                    </li>
                    <li :class="{ active: currentCategory === 'Aduanes' }" @click="currentCategory = 'Aduanes'">
                        <i class="pi pi-globe"></i> Duanes
                    </li>
                </ul>
            </aside>

            <main class="docs-main">
                <div class="docs-toolbar">
                    <div class="search-bar">
                        <i class="pi pi-search"></i>
                        <input type="text" placeholder="Cercar per nom o referència..." v-model="searchQuery">
                    </div>

                    <button class="btn-upload" @click="triggerUpload">
                        <i class="pi pi-cloud-upload"></i> Pujar Document
                    </button>
                    <input type="file" ref="fileInput" hidden @change="handleFileUpload" accept=".pdf,.png,.jpg,.jpeg">
                </div>

                <div class="files-grid mt-4">
                    <div v-for="doc in filteredDocs" :key="doc.id" class="file-card">
                        <div class="file-icon" :class="doc.colorClass">
                            <i :class="doc.icon"></i>
                        </div>
                        <div class="file-info">
                            <h4 :title="doc.nom">{{ doc.nom }}</h4>
                            <span class="file-meta">{{ doc.ref }} • {{ doc.data }}</span>
                            <span class="file-size">{{ doc.size }}</span>
                        </div>
                        <button class="btn-download" @click="descarregar(doc.id)" title="Descarregar">
                            <i class="pi pi-download"></i>
                        </button>
                    </div>
                </div>

                <div v-if="filteredDocs.length === 0" class="empty-state mt-4">
                    <i class="pi pi-folder-open empty-icon"></i>
                    <p>No hi ha documents en aquesta categoria.</p>
                </div>
            </main>
        </div>
    </AppLayout>
</template>

<script setup>
import { ref, computed } from 'vue';
import AppLayout from '@/layout/AppLayout.vue';
import Header from '@/layout/Header.vue';

// Lógica de Categorías y Buscador
const currentCategory = ref('Tots');
const searchQuery = ref('');

// Lógica de Subida de Archivos
const fileInput = ref(null);

const triggerUpload = () => {
    fileInput.value.click(); // Simula el click en el input oculto
};

const handleFileUpload = (event) => {
    const file = event.target.files[0];
    if (file) {
        console.log("Archivo seleccionado listo para enviar al backend:", file.name);
        // Aquí iría tu axios.post('/documentos/subir', formData...)

        // Limpiamos el input para poder subir el mismo archivo otra vez si hace falta
        event.target.value = '';
    }
};

// Datos Simulados
const documents = ref([
    { id: 1, nom: 'Factura_F24-001.pdf', ref: 'OP-2024-0892', categoria: 'Factures', data: '08 Nov 2024', size: '245 KB', icon: 'pi pi-file-pdf', colorClass: 'icon-red' },
    { id: 2, nom: 'CMR_Signat_892.pdf', ref: 'OP-2024-0892', categoria: 'CMR', data: '10 Nov 2024', size: '1.2 MB', icon: 'pi pi-file', colorClass: 'icon-blue' },
    { id: 3, nom: 'DUA_Exportacio.pdf', ref: 'OP-2024-0895', categoria: 'Aduanes', data: '14 Nov 2024', size: '890 KB', icon: 'pi pi-globe', colorClass: 'icon-green' },
]);

// Computed para filtrar por Categoría y Buscador
const filteredDocs = computed(() => {
    return documents.value.filter(doc => {
        const matchesCategory = currentCategory.value === 'Tots' || doc.categoria === currentCategory.value;
        const matchesSearch = doc.nom.toLowerCase().includes(searchQuery.value.toLowerCase()) || doc.ref.toLowerCase().includes(searchQuery.value.toLowerCase());
        return matchesCategory && matchesSearch;
    });
});

const descarregar = (id) => {
    console.log("Descarregant document ID:", id);
};
</script>

<style scoped>
.mt-4 {
    margin-top: 1.5rem;
}

.docs-layout {
    display: flex;
    gap: 2rem;
    align-items: flex-start;
}

/* Sidebar */
.docs-sidebar {
    width: 250px;
    background: white;
    padding: 1.5rem;
    border-radius: 12px;
    border: 1px solid #e5e7eb;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.02);
    flex-shrink: 0;
}

.sidebar-title {
    font-size: 0.9rem;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    color: #9ca3af;
    margin-bottom: 1rem;
    font-weight: 700;
}

.category-list {
    list-style: none;
    padding: 0;
    margin: 0;
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
}

.category-list li {
    padding: 0.75rem 1rem;
    border-radius: 8px;
    cursor: pointer;
    display: flex;
    align-items: center;
    gap: 0.75rem;
    color: #4b5563;
    font-weight: 500;
    transition: all 0.2s;
}

.category-list li:hover {
    background: #f3f4f6;
}

.category-list li.active {
    background: #e0f2fe;
    color: #0284c7;
}

/* Main Area */
.docs-main {
    flex: 1;
    min-width: 0;
}

.docs-toolbar {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 1rem;
    background: white;
    padding: 1rem;
    border-radius: 12px;
    border: 1px solid #e5e7eb;
}

.search-bar {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    background: #f9fafb;
    padding: 0.5rem 1rem;
    border-radius: 8px;
    flex: 1;
    border: 1px solid #e5e7eb;
}

.search-bar input {
    border: none;
    background: transparent;
    outline: none;
    width: 100%;
    color: #374151;
}

.btn-upload {
    background: #1a8a7d;
    color: white;
    border: none;
    padding: 0.6rem 1.2rem;
    border-radius: 8px;
    font-weight: 600;
    cursor: pointer;
    display: flex;
    align-items: center;
    gap: 0.5rem;
    transition: background 0.2s;
}

.btn-upload:hover {
    background: #136a60;
}

/* Grid de Archivos */
.files-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
    gap: 1rem;
}

.file-card {
    background: white;
    border: 1px solid #e5e7eb;
    border-radius: 10px;
    padding: 1rem;
    display: flex;
    align-items: center;
    gap: 1rem;
    transition: box-shadow 0.2s;
}

.file-card:hover {
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
    border-color: #d1d5db;
}

.file-icon {
    width: 48px;
    height: 48px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    flex-shrink: 0;
}

.icon-red {
    background: #fee2e2;
    color: #ef4444;
}

.icon-blue {
    background: #dbeafe;
    color: #3b82f6;
}

.icon-green {
    background: #d1fae5;
    color: #10b981;
}

.file-info {
    flex: 1;
    min-width: 0;
    display: flex;
    flex-direction: column;
    gap: 0.2rem;
}

.file-info h4 {
    margin: 0;
    font-size: 0.95rem;
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
    font-size: 0.75rem;
    color: #9ca3af;
    font-weight: 500;
}

.btn-download {
    background: transparent;
    border: 1px solid #e5e7eb;
    border-radius: 6px;
    width: 36px;
    height: 36px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #4b5563;
    cursor: pointer;
    transition: all 0.2s;
}

.btn-download:hover {
    background: #f3f4f6;
    color: #111827;
}

.empty-state {
    text-align: center;
    padding: 4rem 2rem;
    background: white;
    border-radius: 12px;
    border: 1px dashed #d1d5db;
    color: #6b7280;
}

.empty-icon {
    font-size: 3rem;
    color: #d1d5db;
    margin-bottom: 1rem;
}
</style>