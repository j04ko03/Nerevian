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
    admin: [
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
    operador: [
        {
            label: 'Dashboard',
            to: '/operador/dashboard',
            icon: LayoutDashboard,
        },
        {
            label: 'Peticions de Oferta',
            to: '/operador/peticions',
            icon: ClipboardList,
        },
        { label: 'Cotizacions', to: '/operador/cotizacions', icon: FileText },
        {
            label: 'Precontractes',
            to: '/operador/precontrates',
            icon: FileText,
        },
        { label: 'Expedicions', to: '/operador/expedicions', icon: Truck },
        { label: 'Clients', to: '/operador/clients', icon: Users },
        { label: 'Documents', to: '/operador/documents', icon: Package },
        { label: 'Notificacions', to: '/operador/notificacions', icon: Bell },
    ],
    cliente: [
        { label: 'Dashboard', to: '/cliente/dashboard', icon: LayoutDashboard },
        {
            label: "Solicituds d'Oferta",
            to: '/cliente/solicituds',
            icon: Send,
        },
        {
            label: 'Les meves comandes',
            to: '/cliente/pedidos',
            icon: ShoppingBag,
        },
        { label: 'Tracking', to: '/cliente/tracking', icon: MapPin },
        { label: 'Documents', to: '/cliente/documents', icon: Package },
        { label: 'Notificacions', to: '/cliente/notificacions', icon: Bell },
    ],
};

export function useNavItems(role) {
    const items = computed(() => navConfig[role] ?? []);
    return { items };
}
