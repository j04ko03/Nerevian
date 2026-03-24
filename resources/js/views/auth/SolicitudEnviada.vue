<template>
  <AuthLayout>
    <div class="confirm-box">
      <div class="check-icon">
        <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
          <polyline points="20 6 9 17 4 12"></polyline>
        </svg>
      </div>
      <h2 class="title">Solicitud Enviada</h2>
      <p class="message">
        Hemos recibido tu solicitud de registro. Un
        administrador revisará tus datos y te contactará
        en breve.
      </p>
      <RouterLink to="/login" class="btn-primary">Volver al Inicio</RouterLink>
    </div>
  </AuthLayout>
</template>

<script setup>
import AuthLayout from '@/layouts/AuthNerevianLayout.vue'
</script>

<style scoped>
.confirm-box {
  background: white;
  border: 1px solid #E5E7EB;
  border-radius: 12px;
  padding: 3rem 2.5rem;
  width: 100%;
  max-width: 420px;
  text-align: center;
  box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03);
  font-family: 'Inter', sans-serif;
}

.check-icon {
  width: 64px;
  height: 64px;
  background: #D1FAE5;
  color: #059669;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  margin: 0 auto 1.5rem;
}

.title {
  font-size: 1.5rem;
  font-weight: 700;
  color: #111827;
  margin: 0 0 1rem;
}

.message {
  color: #6B7280;
  font-size: 0.95rem;
  line-height: 1.6;
  margin: 0 0 2rem;
}

.btn-primary {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  background: #1A8A7D;
  color: white;
  border-radius: 6px;
  padding: 0.75rem 2rem;
  font-size: 0.95rem;
  font-weight: 500;
  text-decoration: none;
  transition: background-color 0.2s;
  width: 100%;
  box-sizing: border-box;
}

.btn-primary:hover {
  background: #14706A;
}
</style>
