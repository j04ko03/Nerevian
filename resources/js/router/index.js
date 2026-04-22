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
    {
        path: '/operador/clients',
        name: 'operador-clients',
        component: () => import('@/pages/operador/Clients.vue'),
        meta: { requiresAuth: true, requiresOperator: true },
    },
    {
        path: '/operador/ofertes',
        name: 'operador-ofertes',
        component: () => import('@/pages/operador/Ofertes.vue'),
        meta: { requiresAuth: true, requiresOperator: true },
    },
    {
        path: '/operador/configuracio',
        name: 'operador-configuracio',
        component: () => import('@/pages/Proximamente.vue'),
        meta: { requiresAuth: true, requiresOperator: true },
    },

    // ── Client ─────────────────────────────────────────────
    {
        path: '/client/pendent',
        name: 'client-pendent',
        component: () => import('@/pages/client/PendentActivacio.vue'),
        meta: { requiresAuth: true },
    },
    {
        path: '/client/dashboard',
        name: 'client-dashboard',
        component: () => import('@/pages/Dashboard.vue'),
        meta: { requiresAuth: true, requiresClientActiu: true },
    },
    {
        path: '/client/ofertes',
        name: 'client-ofertes',
        component: () => import('@/pages/client/OfertesView.vue'),
        meta: { requiresAuth: true, requiresClientActiu: true },
    },
    {
        path: '/client/operacions',
        name: 'client-operacions',
        component: () => import('@/pages/client/OperacionsView.vue'),
        meta: { requiresAuth: true, requiresClientActiu: true },
    },
    {
        path: '/client/operacions/:id/tracking',
        name: 'client-tracking',
        component: () => import('@/pages/client/TrackingView.vue'),
        meta: { requiresAuth: true, requiresClientActiu: true },
    },
    {
        path: '/client/documents',
        name: 'client-documents',
        component: () => import('@/pages/client/DocumentsView.vue'),
        meta: { requiresAuth: true, requiresClientActiu: true },
    },
    {
        path: '/client/configuracio',
        name: 'client-configuracio',
        component: () => import('@/pages/Proximamente.vue'),
        meta: { requiresAuth: true, requiresClientActiu: true },
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

    // Client amb rol_id=2 però sense activació → pendent
    if (auth.isLoggedIn && auth.isUser && !auth.isClientActiu) {
        if (to.name !== 'client-pendent') {
            return { name: 'client-pendent' };
        }
        return true;
    }

    // Ruta que requereix client actiu
    if (to.meta.requiresClientActiu && !auth.isClientActiu) {
        return { name: 'client-pendent' };
    }

    return true;
});

export default router;