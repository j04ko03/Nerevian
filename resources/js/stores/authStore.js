import { defineStore } from 'pinia';
import api from '@/plugins/axios';

export const useAuthStore = defineStore('auth', {
    state: () => ({
        user: JSON.parse(sessionStorage.getItem('user') || 'null'),
        token: sessionStorage.getItem('token') || null,
    }),

    getters: {
        isLoggedIn: (state) => !!state.token,
        userRol: (state) => state.user?.rol ?? null,
        isAdmin: (state) => Number(state.user?.rol_id) === 1,
        isUser: (state) => Number(state.user?.rol_id) === 2,
        isOperador: (state) => Number(state.user?.rol_id) === 3,
    },

    actions: {
        async login(credentials) {
            // credentials = { correu, contrasenya }
            const { data } = await api.post('/login', credentials);

            this.token = data.token;
            this.user = data.user;
            sessionStorage.setItem('token', data.token);
            sessionStorage.setItem('user', JSON.stringify(data.user));
        },

        async logout() {
            try {
                await api.post('/logout');
            } finally {
                this.token = null;
                this.user = null;
                sessionStorage.removeItem('token');
                sessionStorage.removeItem('user');
            }
        },

        async fetchMe() {
            try {
                const { data } = await api.get('/me');
                this.user = data.data;
                sessionStorage.setItem('user', JSON.stringify(data.data));
            } catch {
                this.logout();
            }
        },
    },
});
