import { createRouter, createWebHistory } from 'vue-router';
import { useAuthStore } from '@/stores/authStore';

const routes = [
    // ── Auth (públicas) ──────────────────────────────────
    {
        path: '/login',
        name: 'login',
        component: () => import('@/pages/Auth.vue'),
        meta: { guest: true },
    },
    {
        path: '/register',
        name: 'register',
        component: () => import('@/pages/Auth.vue'),
        meta: { guest: true },
    },
    {
        path: '/register-confirmed',
        name: 'register-confirmed',
        component: () => import('@/pages/Auth.vue'),
    },

    // ── Protegidas (requieren login) ──────────────────────
    {
        path: '/dashboard',
        name: 'dashboard',
        component: () => import('@/pages/Dashboard.vue'),
        meta: { requiresAuth: true },
    },

    // ── Admin ─────────────────────────────────────────────
    {
        path: '/admin/dashboard',
        name: 'admin-dashboard',
        component: () => import('@/pages/Dashboard.vue'),
        meta: { requiresAuth: true, requiresAdmin: true },
    },
    {
        path: '/admin/usuaris',
        name: 'admin-usuaris',
        component: () => import('@/pages/admin/Usuaris.vue'),
        meta: { requiresAuth: true, requiresAdmin: true },
    },
    {
        path: '/admin/peticions',
        name: 'admin-peticions',
        component: () => import('@/pages/admin/PeticionsRegistre.vue'),
        meta: { requiresAuth: true, requiresAdmin: true },
    },
    {
        path: '/admin/dades',
        name: 'admin-dades',
        component: () => import('@/pages/Proximamente.vue'),
        meta: { requiresAuth: true, requiresAdmin: true },
    },
    {
        path: '/admin/configuracio',
        name: 'admin-configuracio',
        component: () => import('@/pages/Proximamente.vue'),
        meta: { requiresAuth: true, requiresAdmin: true },
    },

    // ── Operador ─────────────────────────────────────────────
    {
        path: '/operador/dashboard',
        name: 'operador-dashboard',
        component: () => import('@/pages/Dashboard.vue'),
        meta: { requiresAuth: true, requiresOperator: true },
    },

    // Redireccionamiento raíz
    { path: '/', redirect: '/login' },
];

const router = createRouter({
    history: createWebHistory(),
    routes,
});

// Guard global: se ejecuta antes de cada cambio de ruta
router.beforeEach((to, from) => {
    const auth = useAuthStore();

    if (to.meta.requiresAuth && !auth.isLoggedIn) {
        return { name: 'login' };
    }

    if (to.meta.guest && auth.isLoggedIn) {
        return { name: 'dashboard' };
    }

    return true;
});

export default router;
