<script setup lang="ts">
import { Globe } from '@lucide/vue';
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuItem,
    DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu';
import {
    SidebarMenu,
    SidebarMenuButton,
    SidebarMenuItem,
} from '@/components/ui/sidebar';
import { useLocale } from '@/composables/useLocale';

const { t, locale, setLocale } = useLocale();

const languages = [
    { code: 'en', label: 'language_switcher.en' },
    { code: 'ar', label: 'language_switcher.ar' },
    { code: 'fr', label: 'language_switcher.fr' },
] as const;
</script>

<template>
    <SidebarMenu>
        <SidebarMenuItem>
            <DropdownMenu>
                <DropdownMenuTrigger as-child>
                    <SidebarMenuButton
                        size="lg"
                        class="data-[state=open]:bg-sidebar-accent data-[state=open]:text-sidebar-accent-foreground"
                    >
                        <Globe class="size-4" />
                        <span>{{ t(`language_switcher.${locale}`) }}</span>
                    </SidebarMenuButton>
                </DropdownMenuTrigger>
                <DropdownMenuContent
                    side="top"
                    class="w-(--reka-dropdown-menu-trigger-width) min-w-56"
                >
                    <DropdownMenuItem
                        v-for="lang in languages"
                        :key="lang.code"
                        @click="setLocale(lang.code)"
                        :class="{ 'bg-accent': locale === lang.code }"
                    >
                        {{ t(lang.label) }}
                    </DropdownMenuItem>
                </DropdownMenuContent>
            </DropdownMenu>
        </SidebarMenuItem>
    </SidebarMenu>
</template>
