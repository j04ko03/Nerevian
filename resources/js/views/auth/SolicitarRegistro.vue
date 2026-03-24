<template>
  <AuthLayout>
    <div class="form-wrapper">
      <div class="nav-header">
        <RouterLink to="/login" class="back-link">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <line x1="19" y1="12" x2="5" y2="12"></line>
            <polyline points="12 19 5 12 12 5"></polyline>
          </svg>
          Volver al login
        </RouterLink>
      </div>

      <div class="form-box">
        <div class="header-text">
          <h2 class="title">Solicitud de Registro</h2>
          <p class="subtitle">Completa el formulario para unirte a Nerevian</p>
        </div>

        <form @submit.prevent="handleSubmit" class="form">
          <div class="grid-2">
            <div class="field">
              <label for="reg-nom-empresa">Nombre de Empresa *</label>
              <input
                id="reg-nom-empresa"
                v-model="form.nom_empresa"
                type="text"
                placeholder="Logística Global S.L."
                :class="{ error: errors.nom_empresa }"
              />
              <span v-if="errors.nom_empresa" class="error-msg">{{ errors.nom_empresa }}</span>
            </div>

            <div class="field">
              <label for="reg-contacte">Persona de Contacto *</label>
              <input
                id="reg-contacte"
                v-model="form.contacte"
                type="text"
                placeholder="Juan Pérez"
                :class="{ error: errors.contacte }"
              />
              <span v-if="errors.contacte" class="error-msg">{{ errors.contacte }}</span>
            </div>

            <div class="field">
              <label for="reg-correu">Correo Electrónico *</label>
              <input
                id="reg-correu"
                v-model="form.correu"
                type="email"
                placeholder="contacto@empresa.com"
                :class="{ error: errors.correu }"
              />
              <span v-if="errors.correu" class="error-msg">{{ errors.correu }}</span>
            </div>

            <div class="field">
              <label for="reg-telefon">Teléfono</label>
              <input
                id="reg-telefon"
                v-model="form.telefon"
                type="tel"
                placeholder="+34 600 000 000"
              />
            </div>
          </div>

          <div class="field">
            <label for="reg-missatge">Mensaje o Descripción de Actividad</label>
            <textarea
              id="reg-missatge"
              v-model="form.missatge"
              rows="4"
              placeholder="Describa brevemente su actividad y necesidades logísticas..."
            ></textarea>
          </div>

          <div v-if="serverError" class="server-error">
            <svg width="16" height="16" viewBox="0 0 16 16" fill="currentColor">
              <path d="M8 1a7 7 0 110 14A7 7 0 018 1zm0 10a1 1 0 100 2 1 1 0 000-2zm0-7a1 1 0 00-1 1v4a1 1 0 102 0V5a1 1 0 00-1-1z"/>
            </svg>
            {{ serverError }}
          </div>

          <button type="submit" class="btn-primary" :disabled="loading">
            <span v-if="loading" class="spinner"></span>
            <span v-if="loading">Enviando solicitud...</span>
            <span v-else>Enviar Solicitud</span>
          </button>
        </form>
      </div>
    </div>
  </AuthLayout>
</template>

<script setup>
import { reactive, ref } from 'vue'
import { useRouter } from 'vue-router'
import api from '@/plugins/axios'
import AuthLayout from '@/layouts/AuthNerevianLayout.vue'

const router      = useRouter()
const loading     = ref(false)
const serverError = ref('')

const form = reactive({
  nom_empresa: '', contacte: '', correu: '', telefon: '', missatge: '',
})
const errors = reactive({ nom_empresa: '', contacte: '', correu: '' })

function validate() {
  errors.nom_empresa = form.nom_empresa ? '' : 'El nombre de empresa es obligatorio.'
  errors.contacte    = form.contacte    ? '' : 'La persona de contacto es obligatoria.'
  errors.correu      = form.correu      ? '' : 'El correo es obligatorio.'
  return !errors.nom_empresa && !errors.contacte && !errors.correu
}

async function handleSubmit() {
  serverError.value = ''
  if (!validate()) return

  loading.value = true
  try {
    await api.post('/registration-requests', form)
    router.push({ name: 'solicitud-enviada' })
  } catch (err) {
    serverError.value = err.response?.data?.message || 'Error al enviar la solicitud.'
  } finally {
    loading.value = false
  }
}
</script>

<style scoped>
.form-wrapper {
  width: 100%;
  max-width: 550px; /* Slightly wider than login */
}

.nav-header {
  margin-bottom: 1rem;
}

.back-link {
  display: inline-flex;
  align-items: center;
  gap: 0.5rem;
  color: #6B7280;
  font-size: 0.875rem;
  text-decoration: none;
  font-weight: 500;
  transition: color 0.2s;
}

.back-link:hover {
  color: #1A8A7D;
}

.form-box {
  background: white;
  border: 1px solid #E5E7EB;
  border-radius: 12px;
  padding: 2.5rem;
  box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03);
  font-family: 'Inter', sans-serif;
}

.header-text {
  text-align: center;
  margin-bottom: 2rem;
}

.title {
  font-size: 1.5rem;
  font-weight: 700;
  color: #111827;
  margin: 0 0 0.5rem;
}

.subtitle {
  color: #6B7280;
  font-size: 0.95rem;
  margin: 0;
}

.form {
  display: flex;
  flex-direction: column;
  gap: 1.25rem;
}

.grid-2 {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 1.25rem;
}

@media (max-width: 500px) {
  .grid-2 { grid-template-columns: 1fr; gap: 1rem; }
}

.field {
  display: flex;
  flex-direction: column;
  gap: 0.4rem;
}

label {
  font-size: 0.875rem;
  font-weight: 500;
  color: #374151;
}

input,
textarea {
  width: 100%;
  padding: 0.625rem 0.875rem;
  border: 1px solid #D1D5DB;
  border-radius: 6px;
  font-size: 0.95rem;
  background: white;
  color: #111827;
  transition: all 0.2s;
  font-family: inherit;
  box-sizing: border-box;
}

textarea {
  resize: vertical;
}

input::placeholder,
textarea::placeholder {
  color: #9CA3AF;
}

input:focus,
textarea:focus {
  outline: none;
  border-color: #1A8A7D;
  box-shadow: 0 0 0 3px rgba(26, 138, 125, 0.15);
}

input.error {
  border-color: #EF4444;
  box-shadow: 0 0 0 1px #EF4444;
}

.error-msg {
  color: #EF4444;
  font-size: 0.8rem;
  margin-top: 0.25rem;
}

.server-error {
  background: #FEF2F2;
  color: #991B1B;
  border: 1px solid #FECACA;
  border-radius: 6px;
  padding: 0.75rem;
  font-size: 0.875rem;
  display: flex;
  align-items: center;
  gap: 0.5rem;
}

.btn-primary {
  background: #1A8A7D;
  color: white;
  border: none;
  border-radius: 6px;
  padding: 0.75rem;
  font-size: 0.95rem;
  font-weight: 500;
  cursor: pointer;
  transition: background-color 0.2s;
  margin-top: 0.5rem;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 0.5rem;
  width: 100%;
}

.btn-primary:hover:not(:disabled) {
  background: #14706A;
}

.btn-primary:disabled {
  opacity: 0.7;
  cursor: not-allowed;
}

.spinner {
  width: 18px;
  height: 18px;
  border: 2px solid rgba(255, 255, 255, 0.3);
  border-top-color: white;
  border-radius: 50%;
  animation: spin 0.6s linear infinite;
}

@keyframes spin {
  to { transform: rotate(360deg); }
}
</style>
