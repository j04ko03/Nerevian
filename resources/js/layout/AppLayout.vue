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
        // Estilos personalizados – tema Nerevian
        if (!document.getElementById('n8n-chat-custom-style')) {
            const style = document.createElement('style');
            style.id = 'n8n-chat-custom-style';
            style.innerHTML = `
                /* ── Variables oficiales de n8n chat ── */
                :root {
                    --chat--color-primary:             #2a1a08;
                    --chat--color-primary-shade-50:    #3d2710;
                    --chat--color-primary-shade-100:   #6b4c24;
                    --chat--color-secondary:           #c9a96e;
                    --chat--color-white:               #ffffff;
                    --chat--color-light:               #f9fafb;
                    --chat--color-light-shade-50:      #f3f4f6;
                    --chat--color-light-shade-100:     #e5e7eb;
                    --chat--color-medium:              #6b7280;
                    --chat--color-dark:                #111827;
                    --chat--font-family:               'Instrument Sans', system-ui, sans-serif;
                    --chat--font-size:                 0.875rem;
                    --chat--window--width:             380px;
                    --chat--window--height:            560px;
                    --chat--header--background:        #2a1a08;
                    --chat--header--color:             #ffffff;
                    --chat--message--bot--background:  #f5f3ef;
                    --chat--message--bot--color:       #111827;
                    --chat--message--user--background: #c9a96e;
                    --chat--message--user--color:      #2a1a08;
                    --chat--toggle--background:        #2a1a08;
                    --chat--toggle--hover--background: #3d2710;
                    --chat--toggle--active--background:#6b4c24;
                    --chat--toggle--color:             #ffffff;
                    --chat--toggle--size:              52px;
                    --chat--border-radius:             14px;
                    --chat--message--border-radius:    12px;
                }

                /* ── Ventana ── */
                .chat-window {
                    z-index: 999999 !important;
                    border-radius: 18px !important;
                    overflow: hidden !important;
                    border: 1px solid #e5e7eb !important;
                    box-shadow: 0 20px 60px -8px rgba(42,26,8,0.18),
                                0 4px 16px -4px rgba(0,0,0,0.07) !important;
                    font-family: 'Instrument Sans', system-ui, sans-serif !important;
                }

                /* ── Header ── */
                .chat-window .chat-header {
                    background: linear-gradient(135deg, #2a1a08 0%, #6b4c24 100%) !important;
                    padding: 1.1rem 1.25rem !important;
                    border-bottom: none !important;
                }
                .chat-window .chat-header * { color: #ffffff !important; }
                .chat-window .chat-header .chat-heading {
                    font-size: 0.92rem !important;
                    font-weight: 700 !important;
                    color: #c9a96e !important;
                }
                .chat-window .chat-header .chat-subtitle {
                    font-size: 0.76rem !important;
                    color: rgba(201, 169, 110, 0.7) !important;
                    margin-top: 2px !important;
                }

                /* ── Área de mensajes ── */
                .chat-window .chat-messages-list {
                    background: #ffffff !important;
                    padding: 1rem 0.9rem !important;
                }

                /* Burbuja bot */
                .chat-window .chat-message.chat-message-from-bot .chat-message-bubble {
                    background: #f5f3ef !important;
                    color: #111827 !important;
                    border: 1px solid #e9e5df !important;
                    border-radius: 4px 12px 12px 12px !important;
                    font-size: 0.875rem !important;
                    line-height: 1.55 !important;
                    padding: 0.65rem 0.9rem !important;
                    box-shadow: none !important;
                }

                /* Burbuja usuario */
                .chat-window .chat-message.chat-message-from-user .chat-message-bubble {
                    background: #c9a96e !important;
                    color: #2a1a08 !important;
                    border-radius: 12px 4px 12px 12px !important;
                    font-size: 0.875rem !important;
                    font-weight: 500 !important;
                    line-height: 1.55 !important;
                    padding: 0.65rem 0.9rem !important;
                    box-shadow: none !important;
                }

                /* Typing dots */
                .chat-window .chat-message-typing .chat-message-bubble {
                    background: #f5f3ef !important;
                    border: 1px solid #e9e5df !important;
                    border-radius: 4px 12px 12px 12px !important;
                }

                /* ── Input ── */
                .chat-window .chat-input {
                    background: #ffffff !important;
                    border-top: 1px solid #f3f4f6 !important;
                    padding: 0.75rem 0.9rem !important;
                    gap: 0.5rem !important;
                }
                .chat-window input,
                .chat-window textarea {
                    all: revert !important;
                    width: 100% !important;
                    box-sizing: border-box !important;
                    font-family: 'Instrument Sans', system-ui, sans-serif !important;
                    font-size: 0.875rem !important;
                    background: #f9fafb !important;
                    border: 1.5px solid #e5e7eb !important;
                    border-radius: 10px !important;
                    color: #111827 !important;
                    padding: 0.6rem 0.85rem !important;
                    outline: none !important;
                    line-height: 1.4 !important;
                    transition: border-color 0.2s, box-shadow 0.2s !important;
                }
                .chat-window input:focus,
                .chat-window textarea:focus {
                    border-color: #c9a96e !important;
                    background: #ffffff !important;
                    box-shadow: 0 0 0 3px rgba(201,169,110,0.15) !important;
                }
                .chat-window input::placeholder,
                .chat-window textarea::placeholder { color: #9ca3af !important; }

                /* ── Botón enviar ── */
                .chat-window .chat-input-send-button {
                    background: #2a1a08 !important;
                    color: #ffffff !important;
                    border: none !important;
                    border-radius: 8px !important;
                    cursor: pointer !important;
                    transition: background 0.15s, transform 0.1s !important;
                }
                .chat-window .chat-input-send-button:hover {
                    background: #3d2710 !important;
                    transform: scale(1.05) !important;
                }

                /* ── Botón flotante (FAB) ── */
                .n8n-chat-button {
                    background: linear-gradient(135deg, #2a1a08 0%, #6b4c24 100%) !important;
                    box-shadow: 0 4px 20px rgba(42,26,8,0.38) !important;
                    transition: box-shadow 0.2s, transform 0.15s !important;
                }
                .n8n-chat-button:hover {
                    box-shadow: 0 6px 28px rgba(42,26,8,0.48) !important;
                    transform: scale(1.06) !important;
                }
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
	webhookUrl: '${import.meta.env.VITE_URL_CHATBOT}',
	webhookConfig: {
		method: 'POST',
		headers: {}
	},
	initialMessages: [
		'Hola! Soc la Norma, la teva assistent de logística. En què et puc ajudar avui?'
	],
	i18n: {
		en: {
			title: 'Norma 🤖',
			subtitle: 'Assistent de logística · Nerevian',
			footer: '',
			getStarted: 'Nova conversa',
			inputPlaceholder: 'Escriu el teu missatge...',
		},
	},
});
        `;
        document.body.appendChild(script);
    }
};

// Vigilamos cambios en el usuario para activar/desactivar chat
watch(
    () => auth.user,
    () => {
        loadChat();
    },
    { immediate: true, deep: true },
);
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
