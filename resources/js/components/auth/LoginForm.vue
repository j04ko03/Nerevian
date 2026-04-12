<template>
    <form @submit.prevent="handleSubmit" class="login-form">
        <div class="input-group">
            <label for="email" class="label">Correu Electrònic</label>
            <input
                type="email"
                id="email"
                v-model="form.correu"
                placeholder="nombre@empresa.com"
                class="input-field"
                required
            />
        </div>

        <div class="input-group">
            <label for="password" class="label">Contrasenya</label>
            <input
                type="password"
                id="password"
                v-model="form.contrasenya"
                placeholder="••••••••"
                class="input-field"
                required
            />
        </div>

        <!-- <div class="forgot-password">
            <a href="#">¿Olvidaste tu contraseña?</a>
        </div> -->

        <button type="submit" class="btn btn-primary">Iniciar Sessió</button>
    </form>
</template>

<script setup>
import { reactive } from 'vue';

const form = reactive({ correu: '', contrasenya: '' });
const emit = defineEmits(['login']);

const handleSubmit = () => {
    emit('login', { correu: form.correu, contrasenya: form.contrasenya });
};
</script>

<style scoped>
/* Eliminamos el @import de Montserrat porque Instrument Sans ya se carga en tu HTML principal */

.login-form {
    display: flex;
    flex-direction: column;
}

.input-group {
    display: flex;
    flex-direction: column;
    margin-bottom: 1.25rem;
}

.label {
    font-family: 'Instrument Sans', system-ui, sans-serif;
    font-weight: 600;
    color: rgba(42, 26, 8, 0.75);
    margin-bottom: 0.4rem;
    font-size: 0.85rem;
}

.input-field {
    font-family: 'Instrument Sans', system-ui, sans-serif;
    font-weight: 500;
    padding: 0.85rem 1rem;
    border: 1.5px solid rgba(201, 169, 110, 0.35);
    border-radius: 8px;
    font-size: 0.95rem;
    color: #2a1a08;
    background: rgba(255, 252, 242, 0.85);
    outline: none;
    transition: all 0.2s ease-in-out;
}

.input-field::placeholder {
    color: rgba(42, 26, 8, 0.3);
    font-weight: 400;
}

.input-field:focus {
    border-color: #c9a96e;
    background: #fffcf2;
    box-shadow: 0 0 0 3px rgba(201, 169, 110, 0.2);
}

.forgot-password {
    text-align: right;
    margin-top: -0.5rem;
    margin-bottom: 1.75rem;
}

.forgot-password a {
    font-family: 'Instrument Sans', system-ui, sans-serif;
    font-size: 0.85rem;
    color: #8a6e3e;
    text-decoration: none;
    font-weight: 500;
    transition: color 0.2s;
}

.forgot-password a:hover {
    color: #2a1a08;
    text-decoration: underline;
}

.btn {
    font-family: 'Instrument Sans', system-ui, sans-serif;
    font-weight: 600;
    width: 100%;
    padding: 0.85rem;
    border-radius: 8px;
    cursor: pointer;
    font-size: 1rem;
    transition: all 0.3s ease;
}

.btn-primary {
    background-color: #8a6e3e;
    color: #f0e6d0;
    border: none;
    box-shadow: 0 4px 12px rgba(138, 110, 62, 0.4);
}

.btn-primary:hover {
    background-color: #7a5e30;
    transform: translateY(-1px);
    box-shadow: 0 6px 16px rgba(138, 110, 62, 0.5);
}

.btn-primary:active {
    transform: translateY(0);
}
</style>
