<template>
  <div class="table-card">
    <div class="table-toolbar">
      <h2 class="table-title">Llistat d'usuaris</h2>
      <IconField>
        <InputIcon><Search :size="15" /></InputIcon>
        <InputText
          v-model="searchQuery"
          placeholder="Cercar per nom, correu o rol..."
          class="search-input"
        />
      </IconField>
    </div>

    <DataTable
      :value="filteredUsers"
      :loading="loading"
      paginator
      :rows="10"
      :rowsPerPageOptions="[5, 10, 20]"
      tableStyle="min-width: 50rem"
      stripedRows
    >
      <template #empty>
        <div v-if="!loading" class="empty-state">
          <Users :size="40" style="color: #d1d5db;" />
          <p>No s'han trobat usuaris</p>
        </div>
      </template>

      <Column field="id" header="ID" sortable style="width: 4rem; color: #9ca3af; font-size: 0.8rem;" />

      <Column header="Usuari" sortable sortField="nom" style="min-width: 16rem">
        <template #body="{ data }">
          <div class="user-cell">
            <div
              class="user-avatar"
              :style="{
                backgroundColor: getRoleColor(data.rols?.rol).bg,
                color: getRoleColor(data.rols?.rol).text,
              }"
            >
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
          <span :class="data.telefon ? 'phone-value' : 'no-data'">
            {{ data.telefon || '—' }}
          </span>
        </template>
      </Column>

      <Column header="Rol" sortable sortField="rols.rol" style="min-width: 8rem">
        <template #body="{ data }">
          <span
            class="role-badge"
            :style="{
              backgroundColor: getRoleColor(data.rols?.rol).bg,
              color: getRoleColor(data.rols?.rol).text,
            }"
          >
            {{ data.rols?.rol || 'N/A' }}
          </span>
        </template>
      </Column>

      <Column header="Creat" sortable sortField="created_at" style="min-width: 10rem">
        <template #body="{ data }">
          <span class="no-data">{{ data.created_at ? formatDate(data.created_at) : '—' }}</span>
        </template>
      </Column>

      <Column header="Accions" style="width: 7rem; text-align: center">
        <template #body="{ data }">
          <div class="action-buttons">
            <button class="action-btn edit-btn" v-tooltip="'Editar'" @click="emit('edit', data)">
              <Pencil :size="14" />
            </button>
            <button class="action-btn delete-btn" v-tooltip="'Eliminar'" @click="emit('delete', data)">
              <Trash2 :size="14" />
            </button>
          </div>
        </template>
      </Column>
    </DataTable>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import { Search, Pencil, Trash2, Users } from 'lucide-vue-next'
import DataTable from 'primevue/datatable'
import Column from 'primevue/column'
import IconField from 'primevue/iconfield'
import InputIcon from 'primevue/inputicon'
import InputText from 'primevue/inputtext'
import vTooltip from 'primevue/tooltip'

const props = defineProps({
  users: {
    type: Array,
    required: true,
  },
  loading: {
    type: Boolean,
    default: false,
  },
})

const emit = defineEmits(['edit', 'delete'])

const searchQuery = ref('')

const roleColors = {
  admin:    { bg: '#e6f4f4', text: '#0a3a40' },
  user:     { bg: '#f0fdf4', text: '#166534' },
  operator: { bg: '#eff6ff', text: '#1e40af' },
  client:   { bg: '#fefce8', text: '#854d0e' },
}

function getRoleColor(rol) {
  return roleColors[rol?.toLowerCase()] || { bg: '#f3f4f6', text: '#374151' }
}

function getInitials(nom, cognoms) {
  const n = (nom || '').charAt(0).toUpperCase()
  const c = (cognoms || '').charAt(0).toUpperCase()
  return (n + c) || '?'
}

function formatDate(dateStr) {
  if (!dateStr) return '—'
  return new Date(dateStr).toLocaleDateString('ca-ES', {
    day: '2-digit', month: 'short', year: 'numeric',
  })
}

const filteredUsers = computed(() => {
  if (!searchQuery.value.trim()) return props.users
  const q = searchQuery.value.toLowerCase()
  return props.users.filter(u =>
    u.nom?.toLowerCase().includes(q) ||
    u.cognoms?.toLowerCase().includes(q) ||
    u.correu?.toLowerCase().includes(q) ||
    u.rols?.rol?.toLowerCase().includes(q),
  )
})
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

.search-input {
  width: 300px;
}

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

.user-info {
  display: flex;
  flex-direction: column;
  gap: 2px;
}

.user-name {
  font-weight: 600;
  font-size: 0.875rem;
  color: #111827;
}

.user-email {
  font-size: 0.78rem;
  color: #9ca3af;
}

/* ── Role Badge ──────────────────────────── */
.role-badge {
  display: inline-block;
  font-size: 0.75rem;
  font-weight: 600;
  padding: 0.2rem 0.6rem;
  border-radius: 4px;
  text-transform: capitalize;
}

/* ── Misc ────────────────────────────────── */
.phone-value {
  font-size: 0.875rem;
  color: #374151;
}

.no-data {
  font-size: 0.85rem;
  color: #9ca3af;
}

/* ── Action Buttons ──────────────────────── */
.action-buttons {
  display: flex;
  gap: 0.35rem;
  justify-content: center;
}

.action-btn {
  width: 32px;
  height: 32px;
  border-radius: 8px;
  border: 1px solid #e5e7eb;
  background: white;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 0.75rem;
  transition: all 0.2s ease;
  color: #6b7280;
}

.action-btn:hover {
  transform: translateY(-1px);
}

.edit-btn:hover {
  background: #e6f4f4;
  border-color: #118c8c;
  color: #118c8c;
}

.delete-btn:hover {
  background: #fef2f2;
  border-color: #ef4444;
  color: #ef4444;
}

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