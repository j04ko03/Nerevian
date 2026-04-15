<template>
    <AuthLayout>
        <!-- BACK LINK (only for register) -->
        <div v-if="view === 'register'" class="back-link-container">
            <router-link to="/login" class="back-link"
                >&larr; Tornar al login</router-link
            >
        </div>

        <main
            v-if="view === 'login' || view === 'register'"
            class="auth-card"
            :class="view + '-card'"
        >
            <template v-if="view === 'login'">
                <div class="logo-container">
                    <img src="/logo.png" alt="Nerevian" class="logo" />
                </div>
                <div class="page-header">
                    <h2>{{ title }}</h2>
                    <p>{{ subtitle }}</p>
                </div>
            </template>

            <LoginForm v-if="view === 'login'" @login="procesarLogin" />
            <RegisterForm
                v-if="view === 'register'"
                @submitRegistro="enviarDatosLaravel"
            />
        </main>

        <div v-if="view === 'register-confirmed'" class="confirm-box">
            <div class="check-icon">
                <svg
                    width="32"
                    height="32"
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="2.5"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                >
                    <polyline points="20 6 9 17 4 12"></polyline>
                </svg>
            </div>
            <h2 class="title">Sol·licitud Enviada</h2>
            <p class="message">
                Hem rebut la teva sol·licitud de registre. Un administrador
                revisarà les teves dades i et contactarà en breu. <br />Gràcies
                per voler formar part de Nerevian!
            </p>
            <RouterLink to="/login" class="btn-primary"
                >Tornar al Inici</RouterLink
            >
        </div>

        <footer v-if="view === 'login'" class="auth-footer">
            <p>
                No tens compte?
                <router-link to="/register">Registra't</router-link>
            </p>
        </footer>
    </AuthLayout>
</template>

<script setup>
import { computed } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import AuthLayout from '@/layout/AuthLayout.vue';
import LoginForm from '@/components/auth/LoginForm.vue';
import RegisterForm from '@/components/auth/RegisterForm.vue';
import { useAuthStore } from '@/stores/authStore';
import api from '@/plugins/axios';

const route = useRoute();
const router = useRouter();
const auth = useAuthStore();

const view = computed(() => route.name);

const title = computed(() =>
    view.value === 'login'
        ? 'Benvinguda a Nerevian'
        : 'Sol·licitud de Registre',
);
const subtitle = computed(() =>
    view.value === 'login'
        ? 'Plataforma integral de gestió logística'
        : 'Completa el formulari per unir-te a Nerevian',
);

const procesarLogin = async (credenciales) => {
    try {
        await auth.login(credenciales);
        router.push('/dashboard');
    } catch {
        alert('Credenciales incorrectas');
    }
};

const enviarDatosLaravel = async (datos) => {
    try {
        await api.post('/registration-requests', datos);
        router.push('/register-confirmed');
    } catch {
        alert('Error al enviar la solicitud.');
    }
};
</script>

<style scoped>
/* ── Logo ───────────────────────────────── */
.logo-container {
    display: flex;
    justify-content: center;
    margin-bottom: 1.75rem;
}
.logo {
    height: 44px;
    object-fit: contain;
}

/* ── Header ─────────────────────────────── */
.page-header {
    text-align: center;
    margin-bottom: 1.75rem;
}
.page-header h2 {
    font-family: 'Instrument Sans', system-ui, sans-serif;
    font-weight: 700;
    font-size: 1.4rem;
    color: #2a1a08;
    margin: 0 0 0.35rem;
    letter-spacing: 0.2px;
}
.page-header p {
    font-family: 'Instrument Sans', system-ui, sans-serif;
    color: rgba(42, 26, 8, 0.55);
    font-size: 0.9rem;
}

/* ── Divider under header ───────────────── */
.page-header::after {
    content: '';
    display: block;
    width: 36px;
    height: 2px;
    background: #c9a96e;
    border-radius: 2px;
    margin: 0.75rem auto 0;
}

/* ── Glass card ─────────────────────────── */
.auth-card {
    background: rgba(250, 245, 232, 0.94);
    backdrop-filter: blur(20px);
    -webkit-backdrop-filter: blur(20px);
    border: 1px solid rgba(201, 169, 110, 0.3);
    border-radius: 16px;
    padding: 2.25rem 2.5rem;
    box-sizing: border-box;
    margin-bottom: 1.25rem;
    box-shadow:
        0 8px 32px rgba(0, 0, 0, 0.35),
        inset 0 1px 0 rgba(255, 255, 255, 0.6);
}
.login-card {
    max-width: 440px;
    margin-left: auto;
    margin-right: auto;
    width: 100%;
}
.register-card {
    max-width: 1050px;
    margin-left: auto;
    margin-right: auto;
    width: 100%;
}

/* ── Footer ─────────────────────────────── */
.auth-footer {
    text-align: center;
    font-family: 'Instrument Sans', system-ui, sans-serif;
    font-size: 0.875rem;
    color: rgba(232, 218, 190, 0.45);
}
.auth-footer a {
    color: #c9a96e;
    font-weight: 600;
    text-decoration: none;
    transition: color 0.15s;
}
.auth-footer a:hover {
    color: #f0e6d0;
}

/* ── Back link ──────────────────────────── */
.back-link-container {
    margin-bottom: 0.75rem;
    width: 100%;
}
.back-link {
    font-family: 'Instrument Sans', system-ui, sans-serif;
    color: rgba(232, 218, 190, 0.5);
    text-decoration: none;
    font-size: 0.875rem;
    transition: color 0.15s;
}
.back-link:hover {
    color: #f0e6d0;
}

/* ── Confirm box ────────────────────────── */
.confirm-box {
    display: flex;
    flex-direction: column;
    align-items: center;
    text-align: center;
    background: rgba(250, 245, 232, 0.94);
    backdrop-filter: blur(20px);
    -webkit-backdrop-filter: blur(20px);
    border: 1px solid rgba(201, 169, 110, 0.3);
    border-radius: 16px;
    padding: 3rem 2.5rem;
    width: 100%;
    max-width: 420px;
    margin: 0 auto;
    box-shadow:
        0 8px 32px rgba(0, 0, 0, 0.35),
        inset 0 1px 0 rgba(255, 255, 255, 0.6);
    font-family: 'Instrument Sans', system-ui, sans-serif;
}
.check-icon {
    width: 64px;
    height: 64px;
    background: rgba(201, 169, 110, 0.18);
    color: #8a6e3e;
    border: 1px solid rgba(201, 169, 110, 0.35);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 1.5rem;
}
.confirm-box .title {
    font-size: 1.5rem;
    font-weight: 700;
    color: #2a1a08;
    margin: 0 0 1rem;
}
.confirm-box .message {
    color: rgba(42, 26, 8, 0.6);
    font-size: 0.95rem;
    line-height: 1.6;
    margin: 0 0 2rem;
}
.btn-primary {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    background: #8a6e3e;
    color: #f0e6d0;
    border-radius: 8px;
    padding: 0.75rem 2rem;
    font-size: 0.95rem;
    font-weight: 600;
    text-decoration: none;
    transition:
        background 0.2s,
        box-shadow 0.2s;
    width: 100%;
    box-sizing: border-box;
    box-shadow: 0 4px 12px rgba(138, 110, 62, 0.4);
}
.btn-primary:hover {
    background: #7a5e30;
    box-shadow: 0 6px 16px rgba(138, 110, 62, 0.5);
}
</style>
