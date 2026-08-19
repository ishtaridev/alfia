<script setup lang="ts">
import { Link, usePage } from '@inertiajs/vue3';
import { LayoutGrid, Plane, Users } from '@lucide/vue';
import { computed } from 'vue';
import AppLogo from '@/components/AppLogo.vue';
import LanguageSwitcher from '@/components/LanguageSwitcher.vue';
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
import { useLocale } from '@/composables/useLocale';
import { dashboard } from '@/routes';
import { index as offersIndex } from '@/routes/offers';
import { index as usersIndex } from '@/routes/users';
import type { NavItem } from '@/types';

const { t, locale } = useLocale();
const sidebarSide = computed(() => (locale.value === 'ar' ? 'right' : 'left'));
const page = usePage();
const user = computed(() => page.props.auth.user as { role: string });
const canManageOffers = computed(() => user.value.role === 'admin' || user.value.role === 'superadmin');
const isSuperAdmin = computed(() => user.value.role === 'superadmin');

const mainNavItems = computed<NavItem[]>(() => {
    const items: NavItem[] = [
        {
            title: t('navigation.dashboard'),
            href: dashboard(),
            icon: LayoutGrid,
        },
    ];

    if (canManageOffers.value) {
        items.push({
            title: t('navigation.offers'),
            href: offersIndex().url,
            icon: Plane,
        });
    }

    if (isSuperAdmin.value) {
        items.push({
            title: t('navigation.users'),
            href: usersIndex().url,
            icon: Users,
        });
    }

    return items;
});

</script>

<template>
    <Sidebar :side="sidebarSide" collapsible="icon" variant="inset">
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
            <NavFooter :items="[]" />
            <LanguageSwitcher />
            <NavUser />
        </SidebarFooter>
    </Sidebar>
    <slot />
</template>
