<template>
    <div class="app-layout">
        <NavbarLateral :user="authUser" />
        <main class="main-content">
            <slot />
        </main>
    </div>
</template>

<script setup>
import { computed, watch, onMounted } from 'vue';
import NavbarLateral from '@/components/navbar/NavbarLateral.vue';
import { useAuthStore } from '@/stores/authStore';

const auth = useAuthStore();

const roleMap = {
    1: 'Administradora',
    2: 'Clienta',
    3: 'Operadora',
};

const authUser = computed(() => {
    if (!auth.user) return { name: '', role: '' };
    return {
        name: `${auth.user.nom || ''} ${auth.user.cognoms || ''}`.trim(),
        role: roleMap[auth.user.rol_id] || '',
    };
});

const loadChat = () => {
    // 1. Manejo de visibilidad por CSS
    var visibilityStyle = document.getElementById('n8n-chat-visibility');
    if (!visibilityStyle) {
        visibilityStyle = document.createElement('style');
        visibilityStyle.id = 'n8n-chat-visibility';
        document.head.appendChild(visibilityStyle);
    }
    visibilityStyle.innerHTML = auth.isOperador
        ? ''
        : '.n8n-chat-button, .chat-window { display: none !important; }';
    // 2. Inyectar Scripts y Estilos solo si es operador
    if (auth.isOperador && !document.getElementById('n8n-chat-script')) {
        console.log('Inyectando todo lo necesario para el Chat...');
        // --- IMPORTANTE: Recuperamos el CSS oficial de n8n ---
        if (!document.getElementById('n8n-chat-style')) {
            const link = document.createElement('link');
            link.id = 'n8n-chat-style';
            link.rel = 'stylesheet';
            link.href = 'https://cdn.jsdelivr.net/npm/@n8n/chat/dist/style.css';
            document.head.appendChild(link);
        }
        // Estilos personalizados (Z-index)
        if (!document.getElementById('n8n-chat-custom-style')) {
            const style = document.createElement('style');
            style.id = 'n8n-chat-custom-style';
            style.innerHTML = `
                :root { --n8n-chat-primary-color: #1a8a7d; --n8n-chat-button-background: #1a8a7d; }
                .chat-window { z-index: 999999 !important; }
                .chat-window input { all: revert !important; }
            `;
            document.head.appendChild(style);
        }
        // Script de n8n
        const script = document.createElement('script');
        script.id = 'n8n-chat-script';
        script.type = 'module';
        script.innerHTML = `
            import { createChat } from 'https://cdn.jsdelivr.net/npm/@n8n/chat/dist/chat.bundle.es.js';
            createChat({
                webhookUrl: 'http://localhost:5678/webhook/3e2f1022-a06a-40d0-a401-077128195595/chat'
            });
        `;
        document.body.appendChild(script);
    }
};


// Vigilamos cambios en el usuario para activar/desactivar chat
watch(() => auth.user, () => {
    loadChat();
}, { immediate: true, deep: true });

</script>


<style>
.app-layout {
    display: flex;
}

.main-content {
    margin-left: 16%;
    flex: 1;
    min-height: 100vh;
    background: #f5f7f0;
    padding: 2rem 2.5rem;
    font-family: 'Instrument Sans', system-ui, sans-serif;
}
</style>
