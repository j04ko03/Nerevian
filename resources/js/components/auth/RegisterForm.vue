<template>
    <div class="register-layout">
        <!-- LEFT: Branding panel -->
        <div class="brand-panel">
            <img src="/logo.png" alt="Nerevian" class="brand-logo" />
            <div class="brand-content">
                <h2 class="brand-title">Sol·licitud de Registre</h2>
                <p class="brand-subtitle">
                    Completa el formulari per unir-te a la plataforma integral
                    de gestió logística de Nerevian.
                </p>
                <div class="brand-divider"></div>
                <ul class="brand-features">
                    <li>
                        <span class="feature-dot"></span>Gestió d'operacions i
                        enviaments
                    </li>
                    <li>
                        <span class="feature-dot"></span>Seguiment en temps real
                    </li>
                    <li>
                        <span class="feature-dot"></span>Accés multi-rol
                        personalitzat
                    </li>
                </ul>
            </div>
        </div>

        <!-- RIGHT: Form panel -->
        <form @submit.prevent="handleSubmit" class="form-panel">
            <div class="form-grid">
                <div class="input-group">
                    <label for="empresa"
                        >Nom de l'Empresa <span class="required">*</span></label
                    >
                    <input
                        type="text"
                        id="empresa"
                        v-model="form.nom_empresa"
                        placeholder="Logística Global S.L."
                        required
                    />
                </div>

                <div class="input-group">
                    <label for="contacto"
                        >Persona de Contacte
                        <span class="required">*</span></label
                    >
                    <input
                        type="text"
                        id="contacto"
                        v-model="form.contacte"
                        placeholder="Juana Doe"
                        required
                    />
                </div>

                <div class="input-group">
                    <label for="email"
                        >Correu Electrònic
                        <span class="required">*</span></label
                    >
                    <input
                        type="email"
                        id="email"
                        v-model="form.correu"
                        placeholder="contacte@empresa.com"
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
                    <label for="password"
                        >Contrasenya <span class="required">*</span></label
                    >
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
                        >Confirmar Contrasenya
                        <span class="required">*</span></label
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
                    <label for="rol"
                        >Tipus de Perfil <span class="required">*</span></label
                    >
                    <select
                        id="rol"
                        v-model="form.rol_id"
                        placeholder="Seleccioni el rol del compte..."
                        required
                    >
                        <option value="" disabled>
                            Seleccioni el rol del compte...
                        </option>
                        <option value="2">Client</option>
                        <option value="3">Operador</option>
                        <option value="4">Agent</option>
                        <option value="1">Administrador</option>
                    </select>
                </div>

                <div class="input-group full-width">
                    <label for="descripcion"
                        >Missatge o Descripció d'Activitat</label
                    >
                    <textarea
                        id="descripcion"
                        v-model="form.missatge"
                        rows="3"
                        placeholder="Descriu breument la teva activitat i necessitats logístiques..."
                    ></textarea>
                </div>
            </div>

            <button type="submit" class="btn-submit">Enviar Sol·licitud</button>
        </form>
    </div>
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
        alert('Les contrasenyes no coincideixen');
        return;
    } if (form.contrasenya.length < 8) {
        alert('La contrasenya ha de tenir almenys 8 caràcters');
        return;
    }
    emit('submitRegistro', form);
};
</script>

<style scoped>
/* ── Two-panel wrapper ───────────────────── */
.register-layout {
    display: flex;
    gap: 0;
    min-height: 0;
}

/* ── Left: Branding panel ────────────────── */
.brand-panel {
    display: flex;
    flex-direction: column;
    width: 38%;
    flex-shrink: 0;
    padding: 2rem 2rem 2rem 0;
    border-right: 1px solid rgba(201, 169, 110, 0.25);
    margin-right: 2rem;
}

.brand-logo {
    height: 38px;
    object-fit: contain;
    align-self: flex-start;
    margin-bottom: 2rem;
}

.brand-content {
    display: flex;
    flex-direction: column;
    flex: 1;
}

.brand-title {
    font-family: 'Instrument Sans', system-ui, sans-serif;
    font-weight: 700;
    font-size: 1.3rem;
    color: #2a1a08;
    margin: 0 0 0.6rem;
    letter-spacing: 0.2px;
    line-height: 1.3;
}

.brand-subtitle {
    font-family: 'Instrument Sans', system-ui, sans-serif;
    font-size: 0.875rem;
    color: rgba(42, 26, 8, 0.55);
    line-height: 1.6;
    margin: 0;
}

.brand-divider {
    width: 32px;
    height: 2px;
    background: #c9a96e;
    border-radius: 2px;
    margin: 1.25rem 0;
}

.brand-features {
    list-style: none;
    padding: 0;
    margin: 0;
    display: flex;
    flex-direction: column;
    gap: 0.65rem;
}

.brand-features li {
    font-family: 'Instrument Sans', system-ui, sans-serif;
    font-size: 0.85rem;
    color: rgba(42, 26, 8, 0.6);
    display: flex;
    align-items: center;
    gap: 0.55rem;
}

.feature-dot {
    display: inline-block;
    width: 6px;
    height: 6px;
    border-radius: 50%;
    background: #c9a96e;
    flex-shrink: 0;
}

/* ── Right: Form panel ───────────────────── */
.form-panel {
    flex: 1;
    display: flex;
    flex-direction: column;
    min-width: 0;
}

.form-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 1.1rem 1.25rem;
    flex: 1;
}

.input-group {
    display: flex;
    flex-direction: column;
}

.full-width {
    grid-column: 1 / -1;
}

/* ── Labels ──────────────────────────────── */
label {
    font-family: 'Instrument Sans', system-ui, sans-serif;
    font-size: 0.85rem;
    font-weight: 600;
    color: rgba(42, 26, 8, 0.75);
    margin-bottom: 0.4rem;
}

.required {
    color: #c9a96e;
    font-weight: 700;
}

/* ── Inputs ──────────────────────────────── */
input,
textarea,
select {
    font-family: 'Instrument Sans', system-ui, sans-serif;
    font-weight: 500;
    padding: 0.75rem 1rem;
    border: 1.5px solid rgba(201, 169, 110, 0.35);
    border-radius: 8px;
    font-size: 0.9rem;
    outline: none;
    transition: all 0.2s ease-in-out;
    color: #2a1a08;
    background: rgba(255, 252, 242, 0.85);
}

input::placeholder,
textarea::placeholder {
    color: rgba(42, 26, 8, 0.3);
    font-weight: 400;
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

textarea {
    resize: vertical;
    min-height: 72px;
}

/* ── Submit button ───────────────────────── */
.btn-submit {
    font-family: 'Instrument Sans', system-ui, sans-serif;
    font-weight: 600;
    background-color: #8a6e3e;
    color: #f0e6d0;
    padding: 0.85rem;
    border: none;
    border-radius: 8px;
    cursor: pointer;
    font-size: 1rem;
    margin-top: 1.25rem;
    transition: all 0.3s ease;
    box-shadow: 0 4px 12px rgba(138, 110, 62, 0.4);
    width: 100%;
}

.btn-submit:hover {
    background-color: #7a5e30;
    transform: translateY(-1px);
    box-shadow: 0 6px 16px rgba(138, 110, 62, 0.5);
}

.btn-submit:active {
    transform: translateY(0);
}

/* ── Responsive ──────────────────────────── */
@media (max-width: 680px) {
    .register-layout {
        flex-direction: column;
    }

    .brand-panel {
        width: 100%;
        border-right: none;
        border-bottom: 1px solid rgba(201, 169, 110, 0.25);
        padding: 0 0 1.5rem 0;
        margin-right: 0;
        margin-bottom: 1.5rem;
    }

    .form-grid {
        grid-template-columns: 1fr;
    }
}
</style>
