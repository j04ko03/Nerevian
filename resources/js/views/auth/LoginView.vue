<template>
  <AuthLayout>
    <div class="login-box">
      <div class="header-text">
        <h2 class="title">Bienvenido a Nerevian</h2>
        <p class="subtitle">Plataforma integral de gestión logística</p>
      </div>

      <form @submit.prevent="handleLogin" class="form">
        <div class="field">
          <label for="login-correu">Correo Electrónico</label>
          <input
            id="login-correu"
            v-model="form.correu"
            type="email"
            placeholder="nombre@empresa.com"
            :class="{ error: errors.correu }"
            autocomplete="email"
          />
          <span v-if="errors.correu" class="error-msg">{{ errors.correu }}</span>
        </div>

        <div class="field">
          <div class="label-row">
            <label for="login-contrasenya">Contraseña</label>
            <a href="#" class="forgot-link">¿Olvidaste tu contraseña?</a>
          </div>
          <input
            id="login-contrasenya"
            v-model="form.contrasenya"
            type="password"
            placeholder="••••••••"
            :class="{ error: errors.contrasenya }"
            autocomplete="current-password"
          />
          <span v-if="errors.contrasenya" class="error-msg">{{ errors.contrasenya }}</span>
        </div>

        <div v-if="serverError" class="server-error">
          <svg width="16" height="16" viewBox="0 0 16 16" fill="currentColor">
            <path d="M8 1a7 7 0 110 14A7 7 0 018 1zm0 10a1 1 0 100 2 1 1 0 000-2zm0-7a1 1 0 00-1 1v4a1 1 0 102 0V5a1 1 0 00-1-1z"/>
          </svg>
          {{ serverError }}
        </div>

        <button type="submit" class="btn-primary" :disabled="loading">
          <span v-if="loading" class="spinner"></span>
          <span v-if="loading">Iniciando sesión...</span>
          <span v-else>Iniciar Sesión</span>
        </button>
      </form>

      <div class="footer-text">
        <p class="register-link">
          ¿No tienes cuenta?
          <RouterLink to="/solicitar-registro">Solicitar registro</RouterLink>
        </p>
      </div>
    </div>
  </AuthLayout>
</template>

<script setup>
import { reactive, ref } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/authStore'
import AuthLayout from '@/layouts/AuthNerevianLayout.vue'

const router      = useRouter()
const authStore   = useAuthStore()
const loading     = ref(false)
const serverError = ref('')

const form = reactive({ correu: '', contrasenya: '' })
const errors = reactive({ correu: '', contrasenya: '' })

function validate() {
  errors.correu      = form.correu      ? '' : 'El correo es obligatorio.'
  errors.contrasenya = form.contrasenya ? '' : 'La contraseña es obligatoria.'
  return !errors.correu && !errors.contrasenya
}

async function handleLogin() {
  serverError.value = ''
  if (!validate()) return

  loading.value = true
  try {
    await authStore.login({ correu: form.correu, contrasenya: form.contrasenya })
    router.push({ name: 'dashboard' })
  } catch (err) {
    serverError.value = err.response?.data?.message || 'Credenciales incorrectas.'
  } finally {
    loading.value = false
  }
}
</script>

<style scoped>
.login-box {
  background: white;
  border: 1px solid #E5E7EB;
  border-radius: 12px;
  padding: 2.5rem;
  width: 100%;
  max-width: 500px;
  margin: 0 auto;
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

.field {
  display: flex;
  flex-direction: column;
  gap: 0.4rem;
}

.label-row {
  display: flex;
  justify-content: space-between;
  align-items: center;
}

label {
  font-size: 0.875rem;
  font-weight: 500;
  color: #374151;
}

.forgot-link {
  font-size: 0.8125rem;
  color: #1A8A7D;
  text-decoration: none;
  font-weight: 500;
}

.forgot-link:hover {
  text-decoration: underline;
}

input {
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

input::placeholder {
  color: #9CA3AF;
}

input:focus {
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

.footer-text {
  text-align: center;
  margin-top: 2rem;
}

.register-link {
  font-size: 0.875rem;
  color: #6B7280;
  margin: 0;
}

.register-link a {
  color: #1A8A7D;
  font-weight: 500;
  text-decoration: none;
}

.register-link a:hover {
  text-decoration: underline;
}
</style>
