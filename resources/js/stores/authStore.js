import { defineStore } from 'pinia'
import api from '@/plugins/axios'

export const useAuthStore = defineStore('auth', {
    state: () => ({
        user:  JSON.parse(localStorage.getItem('user') || 'null'),
        token: localStorage.getItem('token') || null,
    }),

    getters: {
        isLoggedIn: state => !!state.token,
        userRol:    state => state.user?.rol ?? null,
        isAdmin:    state => Number(state.user?.rol_id) === 1,
        isUser:     state => Number(state.user?.rol_id) === 2,
        isOperador: state => state.user?.rol === 'operador',
    },

    actions: {
        async login(credentials) {
            // credentials = { correu, contrasenya }
            const { data } = await api.post('/login', credentials)

            this.token = data.token
            this.user  = data.user
            localStorage.setItem('token', data.token)
            localStorage.setItem('user',  JSON.stringify(data.user))
        },

        async logout() {
            try {
                await api.post('/logout')
            } finally {
                this.token = null
                this.user  = null
                localStorage.removeItem('token')
                localStorage.removeItem('user')
            }
        },

        async fetchMe() {
            try {
                const { data } = await api.get('/me')
                this.user = data.data
                localStorage.setItem('user', JSON.stringify(data.data))
            } catch {
                this.logout()
            }
        },
    },
})
