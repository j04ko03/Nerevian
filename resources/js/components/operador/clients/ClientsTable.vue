<template>
    <div class="table-card">
        <div class="table-toolbar">
            <h2 class="table-title">Llistat de clients</h2>
            <IconField>
                <InputIcon><Search :size="15" /></InputIcon>
                <InputText
                    v-model="searchQuery"
                    placeholder="Cercar per nom, correu o DNI..."
                    class="search-input"
                />
            </IconField>
        </div>

        <DataTable
            :value="filteredUsuaris"
            :loading="loading"
            paginator
            :rows="10"
            :rowsPerPageOptions="[5, 10, 20]"
            tableStyle="min-width: 50rem"
            stripedRows
        >
            <template #empty>
                <div v-if="!loading" class="empty-state">
                    <Users :size="40" style="color: #d1d5db" />
                    <p>No s'han trobat usuaris</p>
                </div>
            </template>

            <Column field="id" header="ID" sortable style="width: 4rem; color: #9ca3af; font-size: 0.8rem" />

            <Column header="Usuari" sortable sortField="nom" style="min-width: 16rem">
                <template #body="{ data }">
                    <div class="user-cell">
                        <div class="user-avatar" :class="data.actiu ? 'avatar-actiu' : 'avatar-pendent'">
                            {{ getInitials(data.nom, data.cognoms) }}
                        </div>
                        <div class="user-info">
                            <span class="user-name">{{ data.nom }} {{ data.cognoms }}</span>
                            <span class="user-email">{{ data.correu }}</span>
                        </div>
                    </div>
                </template>
            </Column>

            <Column field="telefon" header="Telèfon" style="min-width: 10rem">
                <template #body="{ data }">
                    <span :class="data.telefon ? 'value-text' : 'no-data'">
                        {{ data.telefon || '—' }}
                    </span>
                </template>
            </Column>

            <Column header="DNI" style="min-width: 9rem">
                <template #body="{ data }">
                    <span :class="data.client?.dni ? 'value-text' : 'no-data'">
                        {{ data.client?.dni || '—' }}
                    </span>
                </template>
            </Column>

            <Column header="Sol·licituds" style="min-width: 9rem; text-align: center">
                <template #body="{ data }">
                    <span
                        v-if="data.actiu"
                        class="solicituds-badge"
                        :class="(data.client?.solicituds_count ?? 0) > 0 ? 'badge-active' : 'badge-empty'"
                    >
                        {{ data.client?.solicituds_count ?? 0 }}
                    </span>
                    <span v-else class="no-data">—</span>
                </template>
            </Column>

            <Column header="Estat" style="min-width: 9rem">
                <template #body="{ data }">
                    <span class="estat-badge" :class="data.actiu ? 'estat-actiu' : 'estat-pendent'">
                        {{ data.actiu ? 'Actiu' : 'Pendent' }}
                    </span>
                </template>
            </Column>

            <Column header="Accions" style="width: 9rem; text-align: center">
                <template #body="{ data }">
                    <div class="action-buttons">
                        <template v-if="data.actiu">
                            <button class="action-btn edit-btn" v-tooltip="'Editar'" @click="emit('edit', data)">
                                <Pencil :size="14" />
                            </button>
                            <button class="action-btn delete-btn" v-tooltip="'Eliminar'" @click="emit('delete', data)">
                                <Trash2 :size="14" />
                            </button>
                        </template>
                        <template v-else>
                            <button class="action-btn alta-btn" v-tooltip="'Donar d\'alta'" @click="emit('activate', data)">
                                <UserCheck :size="14" />
                                <span>Donar d'alta</span>
                            </button>
                        </template>
                    </div>
                </template>
            </Column>
        </DataTable>
    </div>
</template>

<script setup>
import { ref, computed } from 'vue';
import { Search, Pencil, Trash2, Users, UserCheck } from 'lucide-vue-next';
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import IconField from 'primevue/iconfield';
import InputIcon from 'primevue/inputicon';
import InputText from 'primevue/inputtext';
import vTooltip from 'primevue/tooltip';

const props = defineProps({
    usuaris: {
        type: Array,
        required: true,
    },
    loading: {
        type: Boolean,
        default: false,
    },
});

const emit = defineEmits(['edit', 'delete', 'activate']);

const searchQuery = ref('');

function getInitials(nom, cognoms) {
    const n = (nom || '').charAt(0).toUpperCase();
    const c = (cognoms || '').charAt(0).toUpperCase();
    return n + c || '?';
}

const filteredUsuaris = computed(() => {
    if (!searchQuery.value.trim()) return props.usuaris;
    const q = searchQuery.value.toLowerCase();
    return props.usuaris.filter(
        (u) =>
            u.nom?.toLowerCase().includes(q) ||
            u.cognoms?.toLowerCase().includes(q) ||
            u.correu?.toLowerCase().includes(q) ||
            u.client?.dni?.toLowerCase().includes(q),
    );
});
</script>

<style scoped>
.table-card {
    background: white;
    border: 1px solid #e5e7eb;
    border-radius: 12px;
    padding: 1.5rem;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02);
    overflow: hidden;
}

.table-toolbar {
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 1rem;
    margin-bottom: 1.25rem;
}

.table-title {
    font-size: 0.95rem;
    font-weight: 600;
    color: #111827;
    margin: 0;
}

.search-input { width: 300px; }

/* ── User Cell ───────────────────────────── */
.user-cell {
    display: flex;
    align-items: center;
    gap: 0.75rem;
}

.user-avatar {
    width: 38px;
    height: 38px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.75rem;
    font-weight: 700;
    flex-shrink: 0;
}

.avatar-actiu   { background: #e6f4f4; color: #0a3a40; }
.avatar-pendent { background: #fef9c3; color: #92400e; }

.user-info { display: flex; flex-direction: column; gap: 2px; }
.user-name  { font-weight: 600; font-size: 0.875rem; color: #111827; }
.user-email { font-size: 0.78rem; color: #9ca3af; }

/* ── Estat Badge ─────────────────────────── */
.estat-badge {
    display: inline-block;
    font-size: 0.75rem;
    font-weight: 600;
    padding: 0.2rem 0.55rem;
    border-radius: 4px;
}

.estat-actiu   { background: #d1fae5; color: #065f46; }
.estat-pendent { background: #fef9c3; color: #92400e; }

/* ── Sol·licituds Badge ───────────────────── */
.solicituds-badge {
    display: inline-block;
    font-size: 0.75rem;
    font-weight: 600;
    padding: 0.2rem 0.55rem;
    border-radius: 4px;
}

.badge-active { background: #d1fae5; color: #065f46; }
.badge-empty  { background: #f3f4f6; color: #9ca3af; }

/* ── Misc ────────────────────────────────── */
.value-text { font-size: 0.875rem; color: #374151; }
.no-data    { font-size: 0.85rem; color: #9ca3af; }

/* ── Action Buttons ──────────────────────── */
.action-buttons {
    display: flex;
    gap: 0.35rem;
    justify-content: center;
}

.action-btn {
    height: 32px;
    border-radius: 8px;
    border: 1px solid #e5e7eb;
    background: white;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.3rem;
    transition: all 0.2s ease;
    color: #6b7280;
    padding: 0 0.6rem;
}

.action-btn:hover { transform: translateY(-1px); }

.edit-btn:hover   { background: #e6f4f4; border-color: #118c8c; color: #118c8c; }
.delete-btn:hover { background: #fef2f2; border-color: #ef4444; color: #ef4444; }

.alta-btn {
    font-size: 0.78rem;
    font-weight: 600;
}
.alta-btn:hover { background: #d1fae5; border-color: #059669; color: #065f46; }

/* ── Empty State ─────────────────────────── */
.empty-state {
    text-align: center;
    padding: 3rem;
    color: #9ca3af;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 0.75rem;
}
</style>