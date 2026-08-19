<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import { Pencil, Trash2, ArrowLeft, MapPin, Calendar, List, ImageIcon, Plane } from '@lucide/vue';
import { ref } from 'vue';
import Heading from '@/components/Heading.vue';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Dialog, DialogContent, DialogDescription, DialogFooter, DialogHeader, DialogTitle } from '@/components/ui/dialog';
import { useLocale } from '@/composables/useLocale';
import { index as offersIndex, edit as offersEdit, destroy as offersDestroy } from '@/routes/offers';
import { index as reservationsIndex } from '@/routes/offer-variants/reservations';
import Lightbox from '@/components/ui/lightbox/Lightbox.vue';
import { formatDate } from '@/lib/formatDate';

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
const lightboxOpen = ref(false);
const lightboxIndex = ref(0);

const openLightbox = (index: number) => {
    lightboxIndex.value = index;
    lightboxOpen.value = true;
};



const deleteOffer = (code: string) => {
    router.delete(offersDestroy(code).url);
};
</script>

<template>
    <Head :title="offer.title" />

    <div class="flex flex-1 flex-col gap-6 p-4 md:p-6 lg:p-8">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
            <Heading
                :title="offer.title"
                :description="t('offers.show.code', { code: offer.code })"
            />
            <div class="flex flex-wrap gap-2">
                <Button variant="outline" size="sm" as-child>
                    <Link :href="offersIndex().url">
                        <ArrowLeft class="me-2 h-4 w-4" />
                        {{ t('common.back') }}
                    </Link>
                </Button>
                <Button variant="outline" size="sm" as-child>
                    <Link :href="offersEdit(offer.code).url">
                        <Pencil class="me-2 h-4 w-4" />
                        {{ t('common.edit') }}
                    </Link>
                </Button>
                <Button
                    variant="outline"
                    size="sm"
                    class="text-destructive hover:bg-destructive hover:text-destructive-foreground"
                    :aria-label="t('common.delete')"
                    @click="showDeleteDialog = true"
                >
                    <Trash2 class="h-4 w-4" />
                </Button>
            </div>
        </div>

        <div
            v-if="offer.images.length > 0"
            class="relative cursor-pointer overflow-hidden rounded-xl shadow-sm"
            @click="openLightbox(0)"
        >
            <img
                :src="offer.images[0].url"
                :alt="t('offer_components.offer_image', { order: 1 })"
                class="h-64 w-full object-cover transition-transform duration-300 hover:scale-[1.02] sm:h-80"
            />
            <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-transparent" />
            <div class="absolute bottom-0 start-0 p-6">
                <h2 class="text-2xl font-bold text-white">{{ offer.title }}</h2>
                <Badge variant="secondary" class="mt-2">
                    {{ t('offers.show.total_variants', { count: offer.variants.length }) }}
                </Badge>
            </div>
        </div>

        <Card v-else class="overflow-hidden">
            <div class="flex h-64 flex-col items-center justify-center gap-3 bg-muted sm:h-80">
                <ImageIcon class="h-12 w-12 text-muted-foreground opacity-40" />
                <p class="text-sm text-muted-foreground">{{ t('offer_components.no_image') }}</p>
            </div>
        </Card>

        <Card v-if="offer.description">
            <CardHeader>
                <CardTitle class="text-base">{{ t('offers.show.offer_details') }}</CardTitle>
            </CardHeader>
            <CardContent>
                <div class="tiptap prose prose-sm text-sm text-muted-foreground" v-html="offer.description" />
            </CardContent>
        </Card>

        <Card v-if="offer.images.length > 1">
            <CardHeader>
                <CardTitle class="text-base">{{ t('offers.show.gallery') }}</CardTitle>
            </CardHeader>
            <CardContent>
                <div class="grid grid-cols-2 gap-3 sm:grid-cols-3 lg:grid-cols-4">
                    <div
                        v-for="(image, index) in offer.images.slice(1)"
                        :key="image.id"
                        class="group aspect-square cursor-pointer overflow-hidden rounded-lg border border-border transition-shadow duration-200 hover:shadow-md"
                        @click="openLightbox(index + 1)"
                    >
                        <img
                            :src="image.url"
                            :alt="t('offer_components.offer_image', { order: image.order })"
                            class="h-full w-full object-cover transition-transform duration-300 group-hover:scale-105"
                        />
                    </div>
                </div>
            </CardContent>
        </Card>

        <Card>
            <CardHeader class="flex flex-row items-center justify-between gap-4">
                <CardTitle class="text-base">{{ t('offers.show.variants') }}</CardTitle>
                <Badge variant="secondary">{{ t('offers.show.total_variants', { count: offer.variants.length }) }}</Badge>
            </CardHeader>
            <CardContent>
                <div v-if="offer.variants.length === 0" class="flex flex-col items-center gap-2 py-12 text-center text-sm text-muted-foreground">
                    <Plane class="h-8 w-8 opacity-50" />
                    {{ t('offers.show.no_variants') }}
                </div>
                <div v-else class="grid gap-4 sm:grid-cols-2">
                    <div
                        v-for="variant in offer.variants"
                        :key="variant.id"
                        class="rounded-xl border border-border p-4 transition-shadow duration-200 hover:shadow-md"
                    >
                        <div class="mb-4 flex items-center gap-3">
                            <div class="flex h-10 w-10 items-center justify-center rounded-full bg-primary/10">
                                <Calendar class="h-5 w-5 text-primary" />
                            </div>
                            <div>
                                <span class="font-medium text-card-foreground">{{ formatDate(variant.travel_date) }}</span>
                                <div class="flex items-center gap-1 text-sm text-muted-foreground">
                                    <MapPin class="h-3.5 w-3.5" />
                                    {{ variant.airport }}
                                </div>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-2 sm:grid-cols-3 lg:grid-cols-5">
                            <div class="rounded-lg bg-muted/50 px-2 py-2 text-center">
                                <p class="text-xs text-muted-foreground">{{ t('offers.show.collectif_room') }}</p>
                                <p class="text-sm font-semibold tabular-nums text-card-foreground">{{ variant.pricing.collectif_room }} <span class="text-xs font-normal text-muted-foreground">DZD</span></p>
                            </div>
                            <div class="rounded-lg bg-muted/50 px-2 py-2 text-center">
                                <p class="text-xs text-muted-foreground">{{ t('offers.show.room_of_four') }}</p>
                                <p class="text-sm font-semibold tabular-nums text-card-foreground">{{ variant.pricing.room_of_four }} <span class="text-xs font-normal text-muted-foreground">DZD</span></p>
                            </div>
                            <div class="rounded-lg bg-muted/50 px-2 py-2 text-center">
                                <p class="text-xs text-muted-foreground">{{ t('offers.show.room_of_three') }}</p>
                                <p class="text-sm font-semibold tabular-nums text-card-foreground">{{ variant.pricing.room_of_three }} <span class="text-xs font-normal text-muted-foreground">DZD</span></p>
                            </div>
                            <div class="rounded-lg bg-muted/50 px-2 py-2 text-center">
                                <p class="text-xs text-muted-foreground">{{ t('offers.show.room_of_two') }}</p>
                                <p class="text-sm font-semibold tabular-nums text-card-foreground">{{ variant.pricing.room_of_two }} <span class="text-xs font-normal text-muted-foreground">DZD</span></p>
                            </div>
                            <div class="rounded-lg bg-muted/50 px-2 py-2 text-center">
                                <p class="text-xs text-muted-foreground">{{ t('offers.show.feeding') }}</p>
                                <p class="text-sm font-semibold tabular-nums text-card-foreground">{{ variant.pricing.feeding }} <span class="text-xs font-normal text-muted-foreground">DZD</span></p>
                            </div>
                        </div>

                        <div class="mt-4">
                            <Button variant="outline" size="sm" class="w-full" as-child>
                                <Link :href="reservationsIndex(variant.id).url">
                                    <List class="me-1 h-3.5 w-3.5" />
                                    {{ t('reservations.index.title') }}
                                </Link>
                            </Button>
                        </div>
                    </div>
                </div>
            </CardContent>
        </Card>
    </div>

    <Lightbox
        :images="offer.images"
        :initial-index="lightboxIndex"
        :open="lightboxOpen"
        @close="lightboxOpen = false"
    />

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
