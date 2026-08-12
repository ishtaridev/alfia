<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import { Pencil, Trash2, ArrowLeft } from '@lucide/vue';
import { ref } from 'vue';
import Heading from '@/components/Heading.vue';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Dialog, DialogContent, DialogDescription, DialogFooter, DialogHeader, DialogTitle } from '@/components/ui/dialog';
import { useLocale } from '@/composables/useLocale';
import { index as offersIndex, edit as offersEdit, destroy as offersDestroy } from '@/routes/offers';

const { t } = useLocale();

type Pricing = {
    collectif_room: number;
    room_of_four: number;
    room_of_three: number;
    room_of_two: number;
    feeding: number;
};

type Variant = {
    id: number;
    travel_date: string;
    airport: string;
    pricing: Pricing;
};

type Image = {
    id: number;
    path: string;
    url: string;
    order: number;
};

type Offer = {
    id: number;
    title: string;
    code: string;
    description: string | null;
    variants: Variant[];
    images: Image[];
};

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Offers',
                href: offersIndex().url,
            },
            {
                title: 'Offer Details',
                href: '#',
            },
        ],
    },
});

defineProps<{
    offer: Offer;
}>();

const showDeleteDialog = ref(false);

const formatDate = (date: string): string => {
    return new Date(date).toLocaleDateString('en-US', {
        weekday: 'long',
        month: 'long',
        day: 'numeric',
        year: 'numeric',
    });
};

const deleteOffer = (code: string) => {
    router.delete(offersDestroy(code).url);
};
</script>

<template>
    <Head :title="offer.title" />

    <div class="flex h-full flex-1 flex-col gap-6 overflow-x-auto rounded-xl p-4">
        <div class="flex items-center justify-between">
            <Heading
                :title="offer.title"
                :description="t('offers.show.code', { code: offer.code })"
            />
            <div class="flex gap-2">
                <Button variant="outline" as-child>
                    <Link :href="offersIndex().url">
                        <ArrowLeft class="mr-2 h-4 w-4" />
                        {{ t('common.back') }}
                    </Link>
                </Button>
                <Button variant="outline" as-child>
                    <Link :href="offersEdit(offer.code).url">
                        <Pencil class="mr-2 h-4 w-4" />
                        {{ t('common.edit') }}
                    </Link>
                </Button>
                <Button
                    variant="outline"
                    class="text-destructive hover:bg-destructive hover:text-destructive-foreground"
                    @click="showDeleteDialog = true"
                >
                    <Trash2 class="h-4 w-4" />
                </Button>
            </div>
        </div>

        <div v-if="offer.images.length > 0" class="overflow-hidden rounded-xl">
            <img
                :src="offer.images[0].url"
                :alt="offer.title"
                class="h-64 w-full object-cover sm:h-80"
            />
        </div>

        <div v-if="offer.description" class="tiptap prose prose-sm max-w-none text-muted-foreground" v-html="offer.description" />

        <div v-if="offer.images.length > 1" class="space-y-3">
            <h3 class="text-lg font-medium">{{ t('offers.show.gallery') }}</h3>
            <div class="grid grid-cols-2 gap-3 sm:grid-cols-3 lg:grid-cols-4">
                <div
                    v-for="image in offer.images.slice(1)"
                    :key="image.id"
                    class="aspect-square overflow-hidden rounded-lg border border-border"
                >
                    <img
                        :src="image.url"
                        :alt="`Image ${image.order}`"
                        class="h-full w-full object-cover"
                    />
                </div>
            </div>
        </div>

        <div class="space-y-4">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-medium">{{ t('offers.show.variants') }}</h3>
                <Badge variant="secondary">{{ t('offers.show.total_variants', { count: offer.variants.length }) }}</Badge>
            </div>

            <div class="grid gap-4 sm:grid-cols-2">
                <div
                    v-for="variant in offer.variants"
                    :key="variant.id"
                    class="rounded-lg border border-border p-4"
                >
                    <div class="mb-3 flex items-center justify-between">
                        <div>
                            <span class="font-medium">{{ formatDate(variant.travel_date) }}</span>
                            <span class="ml-2 text-muted-foreground">{{ variant.airport }}</span>
                        </div>
                    </div>

                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b border-border text-left text-xs text-muted-foreground">
                                <th class="pb-1 font-medium">{{ t('offers.show.type') }}</th>
                                <th class="pb-1 text-right font-medium">{{ t('offers.show.price') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr class="text-sm">
                                <td class="py-1 text-muted-foreground">{{ t('offers.show.collectif_room') }}</td>
                                <td class="py-1 text-right font-medium">{{ variant.pricing.collectif_room }}</td>
                            </tr>
                            <tr class="text-sm">
                                <td class="py-1 text-muted-foreground">{{ t('offers.show.room_of_four') }}</td>
                                <td class="py-1 text-right font-medium">{{ variant.pricing.room_of_four }}</td>
                            </tr>
                            <tr class="text-sm">
                                <td class="py-1 text-muted-foreground">{{ t('offers.show.room_of_three') }}</td>
                                <td class="py-1 text-right font-medium">{{ variant.pricing.room_of_three }}</td>
                            </tr>
                            <tr class="text-sm">
                                <td class="py-1 text-muted-foreground">{{ t('offers.show.room_of_two') }}</td>
                                <td class="py-1 text-right font-medium">{{ variant.pricing.room_of_two }}</td>
                            </tr>
                            <tr class="text-sm">
                                <td class="py-1 text-muted-foreground">{{ t('offers.show.feeding') }}</td>
                                <td class="py-1 text-right font-medium">{{ variant.pricing.feeding }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <Dialog v-model:open="showDeleteDialog">
        <DialogContent>
            <DialogHeader>
                <DialogTitle>{{ t('offers.show.delete_title') }}</DialogTitle>
                <DialogDescription>
                    {{ t('offers.show.delete_confirmation') }}
                </DialogDescription>
            </DialogHeader>
            <DialogFooter>
                <Button variant="outline" @click="showDeleteDialog = false">{{ t('common.cancel') }}</Button>
                <Button variant="destructive" @click="deleteOffer(offer.code)">{{ t('common.delete') }}</Button>
            </DialogFooter>
        </DialogContent>
    </Dialog>
</template>
