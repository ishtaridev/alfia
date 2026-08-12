<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { Eye, Pencil, Trash2 } from '@lucide/vue';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardFooter, CardHeader, CardTitle } from '@/components/ui/card';
import { useLocale } from '@/composables/useLocale';
import { show as offersShow, edit as offersEdit } from '@/routes/offers';

type Image = {
    id: number;
    url: string;
    order: number;
};

type Variant = {
    id: number;
    travel_date: string;
    airport: string;
};

type Offer = {
    id: number;
    title: string;
    code: string;
    description: string | null;
    images: Image[];
    variants: Variant[];
};

defineProps<{
    offer: Offer;
}>();

const emit = defineEmits<{
    delete: [offer: Offer];
}>();

const { t } = useLocale();

const formatDate = (date: string): string => {
    return new Date(date).toLocaleDateString('en-US', {
        month: 'short',
        day: 'numeric',
        year: 'numeric',
    });
};
</script>

<template>
    <Card class="overflow-hidden transition-shadow hover:shadow-md">
        <div class="relative aspect-video overflow-hidden bg-muted">
            <img
                v-if="offer.images.length > 0"
                :src="offer.images[0].url"
                :alt="offer.title"
                class="h-full w-full object-cover"
            />
            <div
                v-else
                class="flex h-full w-full items-center justify-center text-muted-foreground"
            >
                {{ t('offer_components.no_image') }}
            </div>
            <Badge
                variant="secondary"
                class="absolute top-2 right-2 text-xs"
            >
                {{ t('offer_components.variants_count', { count: offer.variants.length }) }}
            </Badge>
        </div>

        <CardHeader class="pb-3">
            <div class="flex items-start justify-between">
                <CardTitle class="line-clamp-1 text-lg">{{ offer.title }}</CardTitle>
                <Badge variant="outline" class="ml-2 shrink-0 text-xs">
                    {{ offer.code }}
                </Badge>
            </div>
        </CardHeader>

        <CardContent class="pb-3">
            <p
                v-if="offer.description"
                class="line-clamp-2 text-sm text-muted-foreground"
                v-html="offer.description"
            />
            <p
                v-if="offer.variants.length > 0"
                class="mt-2 text-xs text-muted-foreground"
            >
                {{ t('offer_components.next') }} {{ formatDate(offer.variants[0].travel_date) }} - {{ offer.variants[0].airport }}
            </p>
        </CardContent>

        <CardFooter class="flex gap-2 border-t pt-3">
            <Button variant="outline" size="sm" as-child>
                <Link :href="offersShow(offer.code).url">
                    <Eye class="mr-1 h-3.5 w-3.5" />
                    {{ t('offer_components.view') }}
                </Link>
            </Button>
            <Button variant="outline" size="sm" as-child>
                <Link :href="offersEdit(offer.code).url">
                    <Pencil class="mr-1 h-3.5 w-3.5" />
                    {{ t('common.edit') }}
                </Link>
            </Button>
            <Button
                variant="outline"
                size="sm"
                class="ml-auto text-destructive hover:bg-destructive hover:text-destructive-foreground"
                @click="emit('delete', offer)"
            >
                <Trash2 class="h-3.5 w-3.5" />
            </Button>
        </CardFooter>
    </Card>
</template>
