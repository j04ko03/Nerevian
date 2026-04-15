<template>
    <AppLayout>
        <Header
            title="Documents i Arxius"
            subtitle="Accedeix, descarrega i gestiona tota la documentació de les teves operacions."
        />

        <div class="docs-layout mt-4">
            <aside class="docs-sidebar">
                <p class="sidebar-title">Categories</p>
                <ul class="category-list">
                    <li :class="{ active: currentCategory === 'Tots' }" @click="currentCategory = 'Tots'">
                        <Folder :size="15" /> Tots els documents
                    </li>
                    <li :class="{ active: currentCategory === 'Factures' }" @click="currentCategory = 'Factures'">
                        <FileSpreadsheet :size="15" /> Factures
                    </li>
                    <li :class="{ active: currentCategory === 'CMR' }" @click="currentCategory = 'CMR'">
                        <FileText :size="15" /> CMRs / Albarans
                    </li>
                    <li :class="{ active: currentCategory === 'Aduanes' }" @click="currentCategory = 'Aduanes'">
                        <Globe :size="15" /> Duanes
                    </li>
                </ul>
            </aside>

            <main class="docs-main">
                <div class="docs-toolbar">
                    <div class="search-bar">
                        <Search :size="15" class="search-icon" />
                        <input type="text" placeholder="Cercar per nom o referència..." v-model="searchQuery" />
                    </div>
                    <button class="btn-upload" @click="triggerUpload">
                        <Upload :size="15" /> Pujar Document
                    </button>
                    <input type="file" ref="fileInput" hidden @change="handleFileUpload" accept=".pdf,.png,.jpg,.jpeg" />
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
                        <button class="btn-download" @click="descarregar(doc.id)" title="Descarregar">
                            <Download :size="15" />
                        </button>
                    </div>
                </div>
            </main>
        </div>
    </AppLayout>
</template>

<script setup>
import { ref, computed } from 'vue';
import {
    Folder, FolderOpen, FileText, FileSpreadsheet,
    Globe, Search, Upload, Download, File,
} from 'lucide-vue-next';
import AppLayout from '@/layout/AppLayout.vue';
import Header from '@/layout/Header.vue';

const currentCategory = ref('Tots');
const searchQuery = ref('');
const fileInput = ref(null);

const triggerUpload = () => fileInput.value.click();

const handleFileUpload = (event) => {
    const file = event.target.files[0];
    if (file) {
        console.log('Arxiu seleccionat llest per enviar al backend:', file.name);
        event.target.value = '';
    }
};

const documents = ref([
    { id: 1, nom: 'Factura_F24-001.pdf',  ref: 'OP-2024-0892', categoria: 'Factures', data: '08 Nov 2024', size: '245 KB',  icon: FileSpreadsheet, colorClass: 'icon-red'   },
    { id: 2, nom: 'CMR_Signat_892.pdf',   ref: 'OP-2024-0892', categoria: 'CMR',      data: '10 Nov 2024', size: '1.2 MB',  icon: FileText,        colorClass: 'icon-blue'  },
    { id: 3, nom: 'DUA_Exportacio.pdf',   ref: 'OP-2024-0895', categoria: 'Aduanes',  data: '14 Nov 2024', size: '890 KB',  icon: Globe,           colorClass: 'icon-green' },
]);

const filteredDocs = computed(() =>
    documents.value.filter((doc) => {
        const matchesCategory = currentCategory.value === 'Tots' || doc.categoria === currentCategory.value;
        const q = searchQuery.value.toLowerCase();
        const matchesSearch = doc.nom.toLowerCase().includes(q) || doc.ref.toLowerCase().includes(q);
        return matchesCategory && matchesSearch;
    })
);

const descarregar = (id) => {
    console.log('Descarregant document ID:', id);
};
</script>

<style scoped>
.mt-4 {
    margin-top: 1.5rem;
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