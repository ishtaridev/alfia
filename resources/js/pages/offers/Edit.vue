<script setup lang="ts">
import { Head, router } from '@inertiajs/vue3';
import { ArrowLeft, ArrowRight, Send } from '@lucide/vue';
import { ref, computed } from 'vue';
import Heading from '@/components/Heading.vue';
import ImageUploader from '@/components/offers/ImageUploader.vue';
import OfferStepper from '@/components/offers/OfferStepper.vue';
import VariantFormCard from '@/components/offers/VariantFormCard.vue';
import { Button } from '@/components/ui/button';
import { TipTapEditor } from '@/components/ui/editor';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { useLocale } from '@/composables/useLocale';
import { index as offersIndex, update as offersUpdate } from '@/routes/offers';

const { t } = useLocale();

type Pricing = {
    collectif_room: number;
    room_of_four: number;
    room_of_three: number;
    room_of_two: number;
    feeding: number;
};

type Variant = {
    id?: number;
    travel_date: string;
    airport: string;
    pricing: Pricing;
};

type ExistingImage = {
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
    images: ExistingImage[];
};

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Offers',
                href: offersIndex().url,
            },
            {
                title: 'Edit Offer',
                href: '#',
            },
        ],
    },
});

const props = defineProps<{
    offer: Offer;
}>();

const currentStep = ref(0);
const steps = computed(() => [
    { label: t('offers.create.step_details') },
    { label: t('offers.create.step_variants') },
    { label: t('offers.create.step_images') },
    { label: t('offers.create.step_review') },
]);

const title = ref(props.offer.title);
const description = ref(props.offer.description ?? '');
const variants = ref<Variant[]>(
    props.offer.variants.map((v) => ({
        id: v.id,
        travel_date: v.travel_date,
        airport: v.airport,
        pricing: { ...v.pricing },
    })),
);
const images = ref<File[]>([]);
const deletedImageIds = ref<number[]>([]);

const canProceed = computed(() => {
    if (currentStep.value === 0) {
        return title.value.trim().length > 0;
    }

    if (currentStep.value === 1) {
        return variants.value.every(
            (v) => v.travel_date && v.airport.trim().length > 0,
        );
    }

    return true;
});

const nextStep = () => {
    if (canProceed.value && currentStep.value < steps.value.length - 1) {
        currentStep.value++;
    }
};

const prevStep = () => {
    if (currentStep.value > 0) {
        currentStep.value--;
    }
};

const addVariant = () => {
    variants.value.push({
        travel_date: '',
        airport: '',
        pricing: {
            collectif_room: 0,
            room_of_four: 0,
            room_of_three: 0,
            room_of_two: 0,
            feeding: 0,
        },
    });
};

const removeVariant = (index: number) => {
    variants.value.splice(index, 1);
};

const updateVariant = (index: number, data: Record<string, unknown>) => {
    Object.assign(variants.value[index], data);
};

const updateVariantPricing = (index: number, data: Record<string, unknown>) => {
    Object.assign(variants.value[index].pricing, data);
};

const deleteExistingImage = (id: number) => {
    deletedImageIds.value.push(id);
};

const filteredExistingImages = computed(() => {
    return props.offer.images.filter((img) => !deletedImageIds.value.includes(img.id));
});

const submit = () => {
    const formData = new FormData();
    formData.append('title', title.value);
    formData.append('description', description.value);
    formData.append('_method', 'put');

    variants.value.forEach((variant, i) => {
        if (variant.id) {
            formData.append(`variants[${i}][id]`, String(variant.id));
        }

        formData.append(`variants[${i}][travel_date]`, variant.travel_date);
        formData.append(`variants[${i}][airport]`, variant.airport);
        formData.append(`variants[${i}][pricing][collectif_room]`, String(variant.pricing.collectif_room));
        formData.append(`variants[${i}][pricing][room_of_four]`, String(variant.pricing.room_of_four));
        formData.append(`variants[${i}][pricing][room_of_three]`, String(variant.pricing.room_of_three));
        formData.append(`variants[${i}][pricing][room_of_two]`, String(variant.pricing.room_of_two));
        formData.append(`variants[${i}][pricing][feeding]`, String(variant.pricing.feeding));
    });

    deletedImageIds.value.forEach((id) => {
        formData.append('deleted_image_ids[]', String(id));
    });

    images.value.forEach((file) => {
        formData.append('images[]', file);
    });

    router.post(offersUpdate(props.offer.code).url, formData, {
        forceFormData: true,
    });
};
</script>

<template>
    <Head :title="t('offers.edit.title')" />

    <div class="flex h-full flex-1 flex-col gap-6 overflow-x-auto rounded-xl p-4">
        <Heading
            :title="t('offers.edit.title')"
            :description="t('offers.edit.editing', { title: offer.title })"
        />

        <OfferStepper :steps="steps" :current-step="currentStep" />

        <div class="mt-4">
            <div v-if="currentStep === 0" class="mx-auto max-w-2xl space-y-6">
                <div class="space-y-2">
                    <Label for="title">{{ t('offers.create.offer_title') }}</Label>
                    <Input
                        id="title"
                        v-model="title"
                        :placeholder="t('offers.create.title_placeholder')"
                    />
                </div>

                <div class="space-y-2">
                    <Label>{{ t('offers.create.description_label') }}</Label>
                    <TipTapEditor v-model="description" />
                </div>
            </div>

            <div v-if="currentStep === 1" class="mx-auto max-w-3xl space-y-4">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-medium">{{ t('offers.create.variants_pricing') }}</h3>
                    <Button type="button" variant="outline" size="sm" @click="addVariant">
                        {{ t('offers.create.add_variant') }}
                    </Button>
                </div>

                <VariantFormCard
                    v-for="(variant, index) in variants"
                    :key="variant.id ?? index"
                    :variant="variant"
                    :index="index"
                    :can-delete="variants.length > 1"
                    @update="updateVariant"
                    @update-pricing="updateVariantPricing"
                    @remove="removeVariant"
                />
            </div>

            <div v-if="currentStep === 2" class="mx-auto max-w-3xl space-y-4">
                <h3 class="text-lg font-medium">{{ t('offers.create.images') }}</h3>
                <ImageUploader
                    v-model="images"
                    :existing-images="filteredExistingImages"
                    @delete-existing="deleteExistingImage"
                />
            </div>

            <div v-if="currentStep === 3" class="mx-auto max-w-3xl space-y-6">
                <h3 class="text-lg font-medium">{{ t('offers.create.review') }}</h3>

                <div class="rounded-lg border border-border p-4">
                    <h4 class="font-medium">{{ title || t('offers.create.untitled_offer') }}</h4>
                    <div
                        v-if="description"
                        class="tiptap prose prose-sm mt-2 text-sm text-muted-foreground"
                        v-html="description"
                    />
                </div>

                <div class="space-y-3">
                    <h4 class="font-medium">{{ t('offers.create.variants') }}</h4>
                    <div
                        v-for="(variant, index) in variants"
                        :key="index"
                        class="rounded-lg border border-border p-3"
                    >
                        <div class="flex items-center gap-4 text-sm">
                            <span class="font-medium">{{ variant.travel_date || t('offers.create.no_date') }}</span>
                            <span class="text-muted-foreground">|</span>
                            <span>{{ variant.airport || t('offers.create.no_airport') }}</span>
                        </div>
                        <div class="mt-2 grid grid-cols-5 gap-2 text-xs text-muted-foreground">
                            <div>{{ t('offers.create.collectif') }} {{ variant.pricing.collectif_room }}</div>
                            <div>{{ t('offers.create.room_of_4') }} {{ variant.pricing.room_of_four }}</div>
                            <div>{{ t('offers.create.room_of_3') }} {{ variant.pricing.room_of_three }}</div>
                            <div>{{ t('offers.create.room_of_2') }} {{ variant.pricing.room_of_two }}</div>
                            <div>{{ t('offers.create.feeding') }} {{ variant.pricing.feeding }}</div>
                        </div>
                    </div>
                </div>

                <div v-if="images.length > 0 || deletedImageIds.length > 0" class="space-y-2">
                    <h4 class="font-medium">
                        {{ t('offers.edit.images_detail', { existing: filteredExistingImages.length, new: images.length }) }}
                    </h4>
                    <p v-if="deletedImageIds.length > 0" class="text-sm text-destructive">
                        {{ t('offers.edit.images_will_be_deleted', { count: deletedImageIds.length }) }}
                    </p>
                </div>
            </div>
        </div>

        <div class="mt-6 flex items-center justify-between border-t border-border pt-4">
            <Button
                variant="outline"
                :disabled="currentStep === 0"
                @click="prevStep"
            >
                <ArrowLeft class="mr-2 h-4 w-4" />
                {{ t('offers.create.previous') }}
            </Button>

            <Button
                v-if="currentStep < steps.length - 1"
                :disabled="!canProceed"
                @click="nextStep"
            >
                {{ t('offers.create.next') }}
                <ArrowRight class="ml-2 h-4 w-4" />
            </Button>

            <Button
                v-else
                @click="submit"
            >
                <Send class="mr-2 h-4 w-4" />
                {{ t('offers.edit.submit') }}
            </Button>
        </div>
    </div>
</template>
