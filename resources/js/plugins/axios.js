import axios from 'axios';
import router from '@/router';

const api = axios.create({
    baseURL: import.meta.env.VITE_API_URL,
    headers: {
        'Content-Type': 'application/json',
        Accept: 'application/json',
    },
});

// Interceptor de REQUEST: añade el token a cada petición
api.interceptors.request.use((config) => {
    const token = sessionStorage.getItem('token');
    if (token) {
        config.headers.Authorization = `Bearer ${token}`;
    }
    return config;
});

// Interceptor de RESPONSE: si el token caducó, limpia sesión
api.interceptors.response.use(
    (response) => response,
    (error) => {
        if (error.response?.status === 401) {
            // 2. Limpiamos AMBAS cosas del sessionStorage
            sessionStorage.removeItem('token');
            sessionStorage.removeItem('user');

            // 3. Usamos el router en lugar de window.location
            router.push({ name: 'login' });
        }
        return Promise.reject(error);
    },
);

export default api;
