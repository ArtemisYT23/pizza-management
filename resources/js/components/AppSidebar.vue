<script setup lang="ts">
import { Link, usePage } from '@inertiajs/vue3';
import {
    BookOpen,
    ChefHat,
    Flame,
    FolderGit2,
    LayoutGrid,
    Pizza,
    Receipt,
} from 'lucide-vue-next';
import { computed } from 'vue';
import AppLogo from '@/components/AppLogo.vue';
import NavFooter from '@/components/NavFooter.vue';
import NavMain from '@/components/NavMain.vue';
import NavUser from '@/components/NavUser.vue';
import {
    Sidebar,
    SidebarContent,
    SidebarFooter,
    SidebarHeader,
    SidebarMenu,
    SidebarMenuButton,
    SidebarMenuItem,
} from '@/components/ui/sidebar';
import adminPages from '@/routes/admin';
import { admin, home } from '@/routes';
import orderRoutes from '@/routes/orders';
import type { NavItem } from '@/types';

const page = usePage();

const isAdmin = computed(
    () => !!page.props.auth?.user && !!(page.props.auth.user as { is_admin?: boolean }).is_admin,
);

const logoHref = computed(() => (isAdmin.value ? admin() : home()));

const mainNavItems = computed<NavItem[]>(() => {
    if (isAdmin.value) {
        return [
            {
                title: 'Administración',
                href: admin(),
                icon: LayoutGrid,
            },
            {
                title: 'Ingredientes',
                href: adminPages.ingredients(),
                icon: ChefHat,
            },
            {
                title: 'Pizzas',
                href: adminPages.pizzas(),
                icon: Flame,
            },
            {
                title: 'Todos los pedidos',
                href: adminPages.orders(),
                icon: Receipt,
            },
        ];
    }

    const items: NavItem[] = [
        {
            title: 'Carta',
            href: home(),
            icon: Pizza,
        },
    ];

    if (page.props.auth?.user) {
        items.push({
            title: 'Mis pedidos',
            href: orderRoutes.mine(),
            icon: Receipt,
        });
    }

    return items;
});

const footerNavItems = computed<NavItem[]>(() => {
    if (page.props.auth?.user) {
        return [];
    }

    return [
        {
            title: 'Repository',
            href: 'https://github.com/laravel/vue-starter-kit',
            icon: FolderGit2,
        },
        {
            title: 'Documentation',
            href: 'https://laravel.com/docs/starter-kits#vue',
            icon: BookOpen,
        },
    ];
});
</script>

<template>
    <Sidebar collapsible="icon" variant="inset">
        <SidebarHeader>
            <SidebarMenu>
                <SidebarMenuItem>
                    <SidebarMenuButton size="lg" as-child>
                        <Link :href="logoHref">
                            <AppLogo />
                        </Link>
                    </SidebarMenuButton>
                </SidebarMenuItem>
            </SidebarMenu>
        </SidebarHeader>

        <SidebarContent>
            <NavMain :items="mainNavItems" />
        </SidebarContent>

        <SidebarFooter>
            <NavFooter v-if="footerNavItems.length > 0" :items="footerNavItems" />
            <NavUser />
        </SidebarFooter>
    </Sidebar>
    <slot />
</template>
