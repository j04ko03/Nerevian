import { computed } from 'vue';
import {
    LayoutDashboard,
    Users,
    ClipboardList,
    Database,
    Settings,
    FileText,
    Package,
    Truck,
    Bell,
    Send,
    ShoppingBag,
    MapPin,
} from 'lucide-vue-next';

const navConfig = {
    Administradora: [
        { label: 'Dashboard', to: '/admin/dashboard', icon: LayoutDashboard },
        { label: 'Usuaris', to: '/admin/usuaris', icon: Users },
        {
            label: 'Peticions de Registre',
            to: '/admin/peticions',
            icon: ClipboardList,
        },
        { label: 'Dades mestres', to: '/admin/dades', icon: Database },
        { label: 'Configuració', to: '/admin/configuracio', icon: Settings },
    ],
    Operadora: [
        {
            label: 'Dashboard',
            to: '/operador/dashboard',
            icon: LayoutDashboard,
        },
        {
            label: 'Ofertes',
            to: '/operador/ofertes',
            icon: Send,
        },
        {
            label: 'Operacions',
            to: '/operador/operacions',
            icon: ShoppingBag,
        },
        { label: 'Clients', to: '/operador/clients', icon: Users },
        { label: 'Configuració', to: '/operador/configuracio', icon: Settings },
    ],
    Clienta: [
        { label: 'Dashboard', to: '/client/dashboard', icon: LayoutDashboard },
        {
            label: 'Ofertes',
            to: '/client/ofertes',
            icon: Send,
        },
        {
            label: 'Les meves operacions',
            to: '/client/operacions',
            icon: ShoppingBag,
        },
        {
            label: 'Documents',
            to: '/client/documents',
            icon: FileText,
        },
        { label: 'Configuració', to: '/client/configuracio', icon: Settings },
    ],
};

export function useNavItems(role) {
    const items = computed(() => navConfig[role] ?? []);
    return { items };
}
