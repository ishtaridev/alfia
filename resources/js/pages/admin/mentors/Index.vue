<script setup lang="ts">
import { Head, router } from '@inertiajs/vue3';
import { InfiniteScroll } from '@inertiajs/vue3';
import { MapPin, Phone, Search, User, Users, X } from '@lucide/vue';
import { computed, ref, watch } from 'vue';
import Heading from '@/components/Heading.vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import { useLocale } from '@/composables/useLocale';
import { index as mentorsIndex } from '@/routes/admin/mentors';

const { t } = useLocale();

type Mentor = {
    id: number;
    full_name: string;
    phone: string;
    commune: string;
    created_at: string;
};

type ScrollableMentors = {
    data: Mentor[];
};

type Filters = {
    search: string | null;
    commune: string | null;
};

const props = defineProps<{
    mentors: ScrollableMentors;
    communes: string[];
    filters: Filters;
}>();

const search = ref(props.filters.search ?? '');
const commune = ref(props.filters.commune ?? '');

const hasActiveFilters = computed(() => search.value !== '' || commune.value !== '');

let searchTimeout: ReturnType<typeof setTimeout> | null = null;

const applyFilters = (): void => {
    const query: Record<string, string> = {};

    if (search.value.trim() !== '') {
        query.search = search.value.trim();
    }

    if (commune.value !== '') {
        query.commune = commune.value;
    }

    router.get(mentorsIndex().url, query, {
        preserveState: true,
        preserveScroll: true,
        replace: true,
    });
};

watch(search, () => {
    if (searchTimeout) {
        clearTimeout(searchTimeout);
    }

    searchTimeout = setTimeout(() => {
        applyFilters();
    }, 300);
});

watch(commune, () => {
    applyFilters();
});

const clearFilters = (): void => {
    search.value = '';
    commune.value = '';

    router.get(mentorsIndex().url, {}, {
        preserveState: true,
        preserveScroll: true,
        replace: true,
    });
};

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Mentors',
                href: mentorsIndex().url,
            },
        ],
    },
});
</script>

<template>
    <Head :title="t('mentors.admin.index.title')" />

    <div class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
        <div class="flex items-center justify-between">
            <Heading
                :title="t('mentors.admin.index.title')"
                :description="t('mentors.admin.index.description')"
            />
        </div>

        <div class="grid gap-4 md:grid-cols-[1fr_auto_auto]">
            <div class="space-y-2">
                <Label for="search" class="sr-only">
                    {{ t('mentors.admin.index.search_placeholder') }}
                </Label>
                <div class="relative">
                    <Search class="absolute start-3 top-1/2 h-4 w-4 -translate-y-1/2 text-muted-foreground" />
                    <Input
                        id="search"
                        v-model="search"
                        type="text"
                        :placeholder="t('mentors.admin.index.search_placeholder')"
                        class="ps-9"
                    />
                </div>
            </div>

            <div class="space-y-2">
                <Label for="commune" class="sr-only">
                    {{ t('mentors.admin.index.select_commune') }}
                </Label>
                <Select v-model="commune">
                    <SelectTrigger id="commune" class="w-full md:w-56">
                        <div class="flex items-center gap-2">
                            <MapPin class="h-4 w-4 text-muted-foreground" />
                            <SelectValue :placeholder="t('mentors.admin.index.select_commune')" />
                        </div>
                    </SelectTrigger>
                    <SelectContent>
                        <SelectItem
                            v-for="communeName in communes"
                            :key="communeName"
                            :value="communeName"
                        >
                            {{ communeName }}
                        </SelectItem>
                    </SelectContent>
                </Select>
            </div>

            <Button
                variant="outline"
                :disabled="!hasActiveFilters"
                @click="clearFilters"
            >
                <X class="me-2 h-4 w-4" />
                {{ t('mentors.admin.index.clear_filters') }}
            </Button>
        </div>

        <div
            v-if="mentors.data.length === 0"
            class="flex flex-col items-center justify-center py-16 text-center"
        >
            <Users class="h-12 w-12 text-muted-foreground/50" />
            <p class="mt-4 text-lg font-medium text-muted-foreground">
                {{ t('mentors.admin.index.empty_title') }}
            </p>
            <p class="mt-1 text-sm text-muted-foreground/70">
                {{ hasActiveFilters ? t('mentors.admin.index.no_results') : t('mentors.admin.index.empty_description') }}
            </p>
        </div>

        <InfiniteScroll v-else data="mentors" class="space-y-4">
            <Card v-for="mentor in mentors.data" :key="mentor.id" class="overflow-hidden">
                <CardContent class="flex flex-col gap-4 p-4 sm:flex-row sm:items-center sm:justify-between">
                    <div class="flex items-center gap-4">
                        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-primary/10">
                            <User class="h-5 w-5 text-primary" />
                        </div>
                        <div>
                            <p class="font-medium">{{ mentor.full_name }}</p>
                            <p class="mt-1 flex items-center gap-2 text-sm text-muted-foreground" dir="ltr">
                                <Phone class="h-3.5 w-3.5" />
                                {{ mentor.phone }}
                            </p>
                        </div>
                    </div>
                    <div class="flex items-center gap-4 sm:justify-end">
                        <span class="inline-flex items-center gap-1.5 rounded-full bg-muted px-3 py-1 text-sm text-muted-foreground">
                            <MapPin class="h-3.5 w-3.5" />
                            {{ mentor.commune }}
                        </span>
                        <span class="text-sm text-muted-foreground">
                            {{ new Date(mentor.created_at).toLocaleDateString() }}
                        </span>
                    </div>
                </CardContent>
            </Card>
        </InfiniteScroll>
    </div>
</template>
