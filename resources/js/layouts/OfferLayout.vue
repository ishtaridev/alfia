<script setup lang="ts">
import { Head, usePage } from '@inertiajs/vue3';
import { ref } from 'vue';
import { useLocale } from '@/composables/useLocale';
import GoogleTagManager from '@/components/GoogleTagManager.vue';
import Lightbox from '@/components/ui/lightbox/Lightbox.vue';

const { t } = useLocale();

const page = usePage();

type Image = {
    id: number;
    url: string;
    order: number;
};

type Offer = {
    id: number;
    title: string;
    code: string;
    description: string | null;
    images: Image[];
};

const offer = page.props.offer as Offer | undefined;

const lightboxOpen = ref(false);
const lightboxIndex = ref(0);

const openLightbox = (index: number) => {
    lightboxIndex.value = index;
    lightboxOpen.value = true;
};
</script>

<template>

    <Head :title="offer?.title ?? t('public_reserve.page_title')" />

    <GoogleTagManager />

    <div class="flex min-h-screen flex-col bg-background">
        <!-- Header / Logo -->
        <header class="border-b bg-card">
            <div class="mx-auto flex max-w-4xl items-center justify-center py-2">
                <img src="/images/alfia.jpeg" alt="Alfia" class="h-20 w-20 rounded-xl sm:h-24 sm:w-24" />
            </div>
        </header>

        <main class="mx-auto w-full max-w-4xl flex-1 px-4 py-8">
            <!-- Offer Info Section -->
            <div v-if="offer" class="mb-8 space-y-6">
                <!-- Title -->
                <div class="text-center">
                    <h1 class="text-3xl font-bold tracking-tight">{{ offer.title }}</h1>
                    <p class="mt-1 text-sm text-muted-foreground">{{ offer.code }}</p>
                </div>

                <!-- Description -->
                <div v-if="offer.description" class="rounded-lg border bg-card p-6">
                    <div class="prose prose-sm mx-auto max-w-none text-muted-foreground" v-html="offer.description" />
                </div>

                <!-- Image Gallery -->
                <div v-if="offer.images.length > 0">
                    <div class="grid grid-cols-2 gap-3 sm:grid-cols-3 lg:grid-cols-4">
                        <div v-for="(image, index) in offer.images" :key="image.id"
                            class="group aspect-square cursor-pointer overflow-hidden rounded-lg border border-border transition-shadow hover:shadow-md"
                            @click="openLightbox(index)">
                            <img :src="image.url" :alt="t('offer_components.offer_image', { order: image.order })"
                                class="h-full w-full object-cover transition-transform duration-300 group-hover:scale-105" />
                        </div>
                    </div>
                </div>
            </div>

            <!-- Reservation Form Slot -->
            <slot />
        </main>

        <!-- Footer -->
        <footer class="border-t bg-card py-6">
            <div class="mx-auto max-w-4xl px-4 text-center text-sm text-muted-foreground">
                Alfia &copy; {{ new Date().getFullYear() }}
            </div>
        </footer>
    </div>

    <Lightbox v-if="offer" :images="offer.images" :initial-index="lightboxIndex" :open="lightboxOpen"
        @close="lightboxOpen = false" />
</template>
