<script setup>
import { ref } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';
import { 
    HomeIcon, 
    BuildingOfficeIcon, 
    UsersIcon, 
    UserGroupIcon, 
    ChartBarIcon,
    Cog6ToothIcon
} from '@heroicons/vue/24/outline';

const user = usePage().props.auth.user;

const hasPermission = (permission) => {
    return user.permissions.includes(permission);
};

const menuItems = ref([
    {
        name: 'Dashboard',
        icon: HomeIcon,
        route: 'dashboard',
        show: true
    },
    {
        name: 'İlanlar',
        icon: BuildingOfficeIcon,
        route: 'properties.index',
        show: hasPermission('view-listings')
    },
    {
        name: 'Danışmanlar',
        icon: UsersIcon,
        route: 'agents.index',
        show: hasPermission('view-agents')
    },
    {
        name: 'Müşteriler',
        icon: UserGroupIcon,
        route: 'customers.index',
        show: hasPermission('view-customers')
    },
    {
        name: 'Raporlar',
        icon: ChartBarIcon,
        route: 'reports.index',
        show: hasPermission('view-reports')
    },
    {
        name: 'Ayarlar',
        icon: Cog6ToothIcon,
        route: 'settings.index',
        show: hasPermission('view-settings')
    }
]);
</script>

<template>
    <nav class="space-y-1">
        <Link
            v-for="item in menuItems.filter(item => item.show)"
            :key="item.name"
            :href="route(item.route)"
            :class="[
                route().current(item.route + '*')
                    ? 'bg-primary-100 text-primary-900'
                    : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900',
                'group flex items-center px-3 py-2 text-sm font-medium rounded-md'
            ]"
        >
            <component
                :is="item.icon"
                :class="[
                    route().current(item.route + '*')
                        ? 'text-primary-500'
                        : 'text-gray-400 group-hover:text-gray-500',
                    'mr-3 flex-shrink-0 h-6 w-6'
                ]"
                aria-hidden="true"
            />
            <span class="truncate">{{ item.name }}</span>
        </Link>
    </nav>
</template> 