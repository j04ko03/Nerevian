<template>
    <aside class="sidebar">
        <!-- Logo -->
        <div class="sidebar-logo">
            <a href="/dashboard">
                <img
                    :src="logoUrl"
                    alt="Nerevian"
                    fetchpriority="high"
                    width="160"
                    height="40"
                />
            </a>
        </div>

        <!-- Nav items -->
        <nav class="sidebar-nav">
            <RouterLink
                v-for="item in items"
                :key="item.to"
                :to="item.to"
                class="nav-item"
                active-class="nav-item--active"
            >
                <component :is="item.icon" :size="18" />
                <span>{{ item.label }}</span>
            </RouterLink>
        </nav>

        <!-- Footer: usuari + logout -->
        <div class="sidebar-footer">
            <div class="sidebar-user">
                <div class="user-avatar">{{ initials }}</div>
                <div class="user-info">
                    <span class="user-name">{{ user.name }}</span>
                    <span class="user-role">{{ user.role }}</span>
                </div>
            </div>
            <button class="logout-btn" @click="handleLogout">
                <LogOut :size="16" />
                <span>Tancar la sessió</span>
            </button>
        </div>
    </aside>
</template>

<script setup>
import { computed } from 'vue';
import { useRouter } from 'vue-router';
import { LogOut } from 'lucide-vue-next';
import logoUrl from '@/assets/logo.svg';
import { useNavItems } from './useNavItems';
import { useAuthStore } from '@/stores/authStore';

const props = defineProps({
    user: {
        type: Object,
        required: true,
        // { name: 'Usuario admin', role: 'admin' }
    },
});

const { items } = useNavItems(props.user.role);

const initials = computed(() =>
    props.user.name
        .split(' ')
        .map((w) => w[0])
        .slice(0, 2)
        .join('')
        .toUpperCase(),
);

const router = useRouter();
const auth = useAuthStore();

async function handleLogout() {
    await auth.logout();
    router.push('/login');
}
</script>

<style scoped>
.sidebar {
    width: 16%;
    min-height: 100vh;
    background: #1a3a3a;
    display: flex;
    flex-direction: column;
    position: fixed;
    left: 0;
    top: 0;
}

.sidebar-logo {
    padding: 1.5rem 1rem;
    border-bottom: 1px solid rgba(255, 255, 255, 0.08);
    display: flex;
    align-items: center;
    justify-content: center;
}

.sidebar-logo img {
    height: 40px;
    filter: brightness(0) invert(1) drop-shadow(0 4px 6px rgba(0, 0, 0, 0.3));
    transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
    transform-origin: center;
}

.sidebar-logo img:hover {
    transform: scale(1.08);
    filter: brightness(0) invert(1)
        drop-shadow(0 0 12px rgba(26, 138, 125, 0.8));
    cursor: pointer;
}

.sidebar-nav {
    flex: 1;
    display: flex;
    flex-direction: column;
    padding: 1rem 0;
    gap: 2px;
    overflow-y: auto;
}

.nav-item {
    display: flex;
    align-items: center;
    gap: 0.65rem;
    padding: 0.65rem 1rem;
    color: rgba(255, 255, 255, 0.65);
    text-decoration: none;
    font-size: 0.875rem;
    font-weight: 500;
    border-radius: 0;
    transition:
        background 0.15s,
        color 0.15s;
    margin: 0 0.5rem;
    border-radius: 8px;
}

.nav-item:hover {
    background: rgba(255, 255, 255, 0.07);
    color: white;
}

.nav-item--active {
    background: #1a8a7d;
    color: white;
}

.sidebar-footer {
    padding: 1rem;
    border-top: 1px solid rgba(255, 255, 255, 0.08);
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
}

.sidebar-user {
    display: flex;
    align-items: center;
    gap: 0.65rem;
}

.user-avatar {
    width: 32px;
    height: 32px;
    background: #1a8a7d;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.75rem;
    font-weight: 700;
    color: white;
    flex-shrink: 0;
}

.user-info {
    display: flex;
    flex-direction: column;
    overflow: hidden;
}

.user-name {
    font-size: 0.8rem;
    font-weight: 600;
    color: white;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.user-role {
    font-size: 0.7rem;
    color: rgba(255, 255, 255, 0.5);
}

.logout-btn {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    background: none;
    border: none;
    color: rgba(255, 255, 255, 0.5);
    font-size: 0.8rem;
    cursor: pointer;
    padding: 0.4rem 0;
    transition: color 0.15s;
    font-family: inherit;
}

.logout-btn:hover {
    color: white;
}
</style>
