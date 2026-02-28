<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { BookOpen, Github, LayoutGrid, Tags } from 'lucide-vue-next';
import { index as categoriesIndex } from '@/actions/App/Http/Controllers/CategoryController';
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
import { dashboard } from '@/routes';
import type { NavItem } from '@/types';

const mainNavItems: NavItem[] = [
    {
        title: 'Табло',
        href: dashboard(),
        icon: LayoutGrid,
    },
    {
        title: 'Категории',
        href: categoriesIndex(),
        icon: Tags,
    },
];

const footerNavItems: NavItem[] = [
    {
        title: 'Репозитория',
        href: 'https://github.com/stekatag/expense-tracker',
        icon: Github,
    },
    {
        title: 'Документация',
        href: 'https://laravel.com/docs',
        icon: BookOpen,
    },
];
</script>

<template>
    <Sidebar collapsible="icon" variant="inset">
        <SidebarHeader>
            <SidebarMenu>
                <SidebarMenuItem>
                    <SidebarMenuButton size="lg" as-child>
                        <Link :href="dashboard()">
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
            <NavFooter :items="footerNavItems" />
            <NavUser />
        </SidebarFooter>
    </Sidebar>
    <slot />
</template>
