<template>
    <form @submit.prevent="handleSubmit" class="register-form">
        <div class="form-grid">
            <div class="input-group">
                <label for="empresa">Nom de l'Empresa *</label>
                <input
                    type="text"
                    id="empresa"
                    v-model="form.nom_empresa"
                    placeholder="Logística Global S.L."
                    required
                />
            </div>
            <div class="input-group">
                <label for="contacto">Persona de Contacte *</label>
                <input
                    type="text"
                    id="contacto"
                    v-model="form.contacte"
                    placeholder="Juana Doe"
                    required
                />
            </div>
            <div class="input-group">
                <label for="email">Correu Electrònic *</label>
                <input
                    type="email"
                    id="email"
                    v-model="form.correu"
                    placeholder="contacto@empresa.com"
                    required
                />
            </div>
            <div class="input-group">
                <label for="telefono">Telèfon</label>
                <input
                    type="tel"
                    id="telefono"
                    v-model="form.telefon"
                    placeholder="+34 600 000 000"
                />
            </div>
            <div class="input-group">
                <label for="password">Contrasenya *</label>
                <input
                    type="password"
                    id="password"
                    v-model="form.contrasenya"
                    placeholder="••••••••"
                    required
                />
            </div>
            <div class="input-group">
                <label for="password_confirmation"
                    >Confirmar Contrasenya *</label
                >
                <input
                    type="password"
                    id="password_confirmation"
                    v-model="form.password_confirmation"
                    placeholder="••••••••"
                    required
                />
            </div>

            <div class="input-group full-width">
                <label for="rol">Tipus de Perfil *</label>
                <select id="rol" v-model="form.rol_id" required>
                    <option value="" disabled selected>
                        Seleccioni el rol de la compte...
                    </option>
                    <option value="2">Client</option>
                    <option value="3">Operador</option>
                    <option value="4">Agent</option>
                    <option value="1">Administrador</option>
                </select>
            </div>
        </div>

        <div class="input-group full-width mt-3">
            <label for="descripcion">Missatge o Descripció de Activitat</label>
            <textarea
                id="descripcion"
                v-model="form.missatge"
                rows="4"
                placeholder="Descriu breument la teva activitat i necessitats logístiques..."
            ></textarea>
        </div>

        <button type="submit" class="btn-submit">Enviar Sol·licitud</button>
    </form>
</template>

<script setup>
import { reactive } from 'vue';

const form = reactive({
    nom_empresa: '',
    contacte: '',
    correu: '',
    contrasenya: '',
    password_confirmation: '',
    telefon: '',
    rol_id: '',
    missatge: '',
});

const emit = defineEmits(['submitRegistro']);

const handleSubmit = () => {
    if (form.contrasenya !== form.password_confirmation) {
        alert('Las contraseñas no coinciden');
        return;
    }
    emit('submitRegistro', form);
};
</script>

<style scoped>
.register-form {
    display: flex;
    flex-direction: column;
}
.form-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 1.5rem;
}
@media (max-width: 600px) {
    .form-grid {
        grid-template-columns: 1fr;
    }
}
.input-group {
    display: flex;
    flex-direction: column;
}
.full-width {
    grid-column: 1 / -1;
}
.mt-3 {
    margin-top: 1.5rem;
}
label {
    font-family: 'Instrument Sans', system-ui, sans-serif;
    font-size: 0.9rem;
    font-weight: 500;
    color: rgba(42, 26, 8, 0.75);
    margin-bottom: 0.5rem;
}

input,
textarea,
select {
    font-family: 'Instrument Sans', system-ui, sans-serif;
    padding: 0.75rem;
    border: 1.5px solid rgba(201, 169, 110, 0.35);
    border-radius: 6px;
    font-size: 0.95rem;
    outline: none;
    transition: all 0.3s ease;
    color: #2a1a08;
    background: rgba(255, 252, 242, 0.85);
}

input::placeholder,
textarea::placeholder {
    color: rgba(42, 26, 8, 0.3);
}

select:invalid,
select option[value=''] {
    color: rgba(42, 26, 8, 0.3);
}

select option {
    background: #faf5e8;
    color: #2a1a08;
}

input:focus,
textarea:focus,
select:focus {
    border-color: #c9a96e;
    background: #fffcf2;
    box-shadow: 0 0 0 3px rgba(201, 169, 110, 0.2);
}

.btn-submit {
    font-family: 'Instrument Sans', system-ui, sans-serif;
    font-weight: 600;
    background-color: #8a6e3e;
    color: #f0e6d0;
    padding: 1rem;
    border: none;
    border-radius: 6px;
    cursor: pointer;
    font-size: 1rem;
    margin-top: 1.5rem;
    transition: all 0.3s ease;
    box-shadow: 0 4px 12px rgba(138, 110, 62, 0.4);
}
.btn-submit:hover {
    background-color: #7a5e30;
    transform: translateY(-1px);
    box-shadow: 0 6px 16px rgba(138, 110, 62, 0.5);
}
.btn-submit:active {
    transform: translateY(0);
}
</style>
