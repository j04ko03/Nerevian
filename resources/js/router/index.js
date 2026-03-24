import { createRouter, createWebHistory } from 'vue-router'
import { useAuthStore } from '@/stores/authStore'

const routes = [
    // ── Auth (públicas) ──────────────────────────────────
    {
        path: '/login',
        name: 'login',
        component: () => import('@/views/auth/LoginView.vue'),
        meta: { guest: true },
    },
    {
        path: '/solicitar-registro',
        name: 'solicitar-registro',
        component: () => import('@/views/auth/SolicitarRegistro.vue'),
        meta: { guest: true },
    },
    {
        path: '/solicitud-enviada',
        name: 'solicitud-enviada',
        component: () => import('@/views/auth/SolicitudEnviada.vue'),
    },

    // ── Protegidas (requieren login) ──────────────────────
    {
        path: '/dashboard',
        name: 'dashboard',
        component: () => import('@/views/DashboardView.vue'),
        meta: { requiresAuth: true },
    },

    // Redireccionamiento raíz
    { path: '/', redirect: '/login' },
]

const router = createRouter({
    history: createWebHistory(),
    routes,
})

// Guard global: se ejecuta antes de cada cambio de ruta
router.beforeEach((to, from, next) => {
    const auth = useAuthStore()

    if (to.meta.requiresAuth && !auth.isLoggedIn) {
        return next({ name: 'login' })
    }

    if (to.meta.guest && auth.isLoggedIn) {
        return next({ name: 'dashboard' })
    }

    next()
})

export default router
