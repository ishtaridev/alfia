<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import { Plus } from '@lucide/vue';
import { ref } from 'vue';
import Heading from '@/components/Heading.vue';
import OfferCard from '@/components/offers/OfferCard.vue';
import { Button } from '@/components/ui/button';
import { Dialog, DialogContent, DialogDescription, DialogFooter, DialogHeader, DialogTitle } from '@/components/ui/dialog';
import { useLocale } from '@/composables/useLocale';
import { index as offersIndex, create as offersCreate, destroy as offersDestroy } from '@/routes/offers';

const { t } = useLocale();

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Offers',
                href: offersIndex().url,
            },
        ],
    },
});

type PaginatedOffers = {
    data: Array<{
        id: number;
        title: string;
        code: string;
        description: string | null;
        images: Array<{ id: number; url: string; order: number }>;
        variants: Array<{ id: number; travel_date: string; airport: string }>;
    }>;
    current_page: number;
    last_page: number;
    per_page: number;
    total: number;
};

defineProps<{
    offers: PaginatedOffers;
}>();

const showDeleteDialog = ref(false);
const offerToDelete = ref<{ code: string; title: string } | null>(null);

const confirmDelete = (offer: { code: string; title: string }) => {
    offerToDelete.value = offer;
    showDeleteDialog.value = true;
};

const deleteOffer = () => {
    if (! offerToDelete.value) {
        return;
    }

    router.delete(offersDestroy(offerToDelete.value.code).url, {
        onSuccess: () => {
            showDeleteDialog.value = false;
            offerToDelete.value = null;
        },
    });
};
</script>

<template>
    <Head :title="t('offers.index.title')" />

    <div class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
        <div class="flex items-center justify-between">
            <Heading
                :title="t('offers.index.title')"
                :description="t('offers.index.description')"
            />
            <Button as-child>
                <Link :href="offersCreate().url">
                    <Plus class="mr-2 h-4 w-4" />
                    {{ t('offers.index.create_button') }}
                </Link>
            </Button>
        </div>

        <div v-if="offers.data.length === 0" class="flex flex-col items-center justify-center py-16 text-center">
            <p class="text-lg font-medium text-muted-foreground">{{ t('offers.index.empty_title') }}</p>
            <p class="mt-1 text-sm text-muted-foreground/70">{{ t('offers.index.empty_description') }}</p>
            <Button class="mt-4" as-child>
                <Link :href="offersCreate().url">
                    <Plus class="mr-2 h-4 w-4" />
                    {{ t('offers.index.create_button') }}
                </Link>
            </Button>
        </div>

        <div v-else class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
            <OfferCard
                v-for="offer in offers.data"
                :key="offer.id"
                :offer="offer"
                @delete="confirmDelete"
            />
        </div>

        <div v-if="offers.last_page > 1" class="mt-4 flex justify-center gap-2">
            <Button
                v-for="page in offers.last_page"
                :key="page"
                variant="outline"
                size="sm"
                :class="{ 'bg-primary text-primary-foreground': page === offers.current_page }"
                as-child
            >
                <Link :href="offersIndex({ query: { page: String(page) } }).url">
                    {{ page }}
                </Link>
            </Button>
        </div>
    </div>

    <Dialog v-model:open="showDeleteDialog">
        <DialogContent>
            <DialogHeader>
                <DialogTitle>{{ t('offers.index.delete_title') }}</DialogTitle>
                <DialogDescription>
                    {{ t('offers.index.delete_confirmation', { title: offerToDelete?.title }) }}
                </DialogDescription>
            </DialogHeader>
            <DialogFooter>
                <Button variant="outline" @click="showDeleteDialog = false">{{ t('common.cancel') }}</Button>
                <Button variant="destructive" @click="deleteOffer">{{ t('common.delete') }}</Button>
            </DialogFooter>
        </DialogContent>
    </Dialog>
</template>
