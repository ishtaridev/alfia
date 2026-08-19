<script setup lang="ts">
import { Head, router } from '@inertiajs/vue3';
import { ArrowLeft, ArrowRight, Send } from '@lucide/vue';
import { ref, computed } from 'vue';
import Heading from '@/components/Heading.vue';
import ImageUploader from '@/components/offers/ImageUploader.vue';
import OfferStepper from '@/components/offers/OfferStepper.vue';
import VariantFormCard from '@/components/offers/VariantFormCard.vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle, CardDescription } from '@/components/ui/card';
import { TipTapEditor } from '@/components/ui/editor';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { useLocale } from '@/composables/useLocale';
import { index as offersIndex, update as offersUpdate } from '@/routes/offers';

const { t, direction } = useLocale();

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

    <div class="flex flex-1 flex-col gap-6 p-4 md:p-6 lg:p-8">
        <Heading
            :title="t('offers.edit.title')"
            :description="t('offers.edit.editing', { title: offer.title })"
        />

        <OfferStepper :steps="steps" :current-step="currentStep" />

        <p class="text-center text-sm text-muted-foreground">
            {{ t('offers.create.step_counter', { current: currentStep + 1, total: steps.length }) }}
        </p>

        <div>
            <div v-if="currentStep === 0" class="mx-auto max-w-2xl">
                <Card>
                    <CardHeader>
                        <CardTitle>{{ t('offers.create.step_details') }}</CardTitle>
                        <CardDescription>{{ t('offers.create.step_subtitle_details') }}</CardDescription>
                    </CardHeader>
                    <CardContent class="space-y-6">
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
                    </CardContent>
                </Card>
            </div>

            <div v-if="currentStep === 1" class="mx-auto max-w-3xl space-y-4">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-lg font-semibold">{{ t('offers.create.variants_pricing') }}</h3>
                        <p class="text-sm text-muted-foreground">{{ t('offers.create.step_subtitle_variants') }}</p>
                    </div>
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

            <div v-if="currentStep === 2" class="mx-auto max-w-3xl">
                <Card>
                    <CardHeader>
                        <CardTitle>{{ t('offers.create.images') }}</CardTitle>
                        <CardDescription>{{ t('offers.create.step_subtitle_images') }}</CardDescription>
                    </CardHeader>
                    <CardContent>
                        <ImageUploader
                            v-model="images"
                            :existing-images="filteredExistingImages"
                            @delete-existing="deleteExistingImage"
                        />
                    </CardContent>
                </Card>
            </div>

            <div v-if="currentStep === 3" class="mx-auto max-w-3xl space-y-4">
                <div>
                    <h3 class="text-lg font-semibold text-card-foreground">{{ t('offers.create.review') }}</h3>
                    <p class="text-sm text-muted-foreground">{{ t('offers.create.step_subtitle_review') }}</p>
                </div>

                <Card>
                    <CardHeader>
                        <CardTitle class="text-base">{{ title || t('offers.create.untitled_offer') }}</CardTitle>
                    </CardHeader>
                    <CardContent>
                        <div
                            v-if="description"
                            class="tiptap prose prose-sm text-sm text-muted-foreground"
                            v-html="description"
                        />
                    </CardContent>
                </Card>

                <Card>
                    <CardHeader>
                        <CardTitle class="text-base">{{ t('offers.create.variants') }}</CardTitle>
                    </CardHeader>
                    <CardContent class="space-y-3">
                        <div
                            v-for="(variant, index) in variants"
                            :key="index"
                            class="flex flex-col gap-3 rounded-lg bg-muted/50 px-4 py-3 sm:flex-row sm:items-center sm:justify-between"
                        >
                            <div class="flex items-center gap-3">
                                <span class="font-medium text-card-foreground">{{ variant.travel_date || t('offers.create.no_date') }}</span>
                                <span class="text-muted-foreground">|</span>
                                <span class="text-muted-foreground">{{ variant.airport || t('offers.create.no_airport') }}</span>
                            </div>
                            <div class="flex flex-wrap gap-x-4 gap-y-1 text-xs text-muted-foreground">
                                <span>{{ t('offers.create.collectif') }} <span class="font-medium tabular-nums text-card-foreground">{{ variant.pricing.collectif_room }} DZD</span></span>
                                <span>{{ t('offers.create.room_of_4') }} <span class="font-medium tabular-nums text-card-foreground">{{ variant.pricing.room_of_four }} DZD</span></span>
                                <span>{{ t('offers.create.room_of_3') }} <span class="font-medium tabular-nums text-card-foreground">{{ variant.pricing.room_of_three }} DZD</span></span>
                                <span>{{ t('offers.create.room_of_2') }} <span class="font-medium tabular-nums text-card-foreground">{{ variant.pricing.room_of_two }} DZD</span></span>
                                <span>{{ t('offers.create.feeding') }} <span class="font-medium tabular-nums text-card-foreground">{{ variant.pricing.feeding }} DZD</span></span>
                            </div>
                        </div>
                    </CardContent>
                </Card>

                <Card v-if="images.length > 0 || deletedImageIds.length > 0">
                    <CardHeader>
                        <CardTitle class="text-base">
                            {{ t('offers.edit.images_detail', { existing: filteredExistingImages.length, new: images.length }) }}
                        </CardTitle>
                    </CardHeader>
                    <CardContent>
                        <p v-if="deletedImageIds.length > 0" class="text-sm text-destructive">
                            {{ t('offers.edit.images_will_be_deleted', { count: deletedImageIds.length }) }}
                        </p>
                    </CardContent>
                </Card>
            </div>
        </div>

        <div class="sticky bottom-0 -mx-4 mt-6 flex items-center justify-between border-t border-border bg-background px-4 py-4 shadow-[0_-4px_6px_-1px_rgba(0,0,0,0.05)] md:-mx-6 md:px-6 lg:-mx-8 lg:px-8">
            <Button
                variant="outline"
                :disabled="currentStep === 0"
                @click="prevStep"
            >
                <ArrowLeft v-if="direction === 'ltr'" class="me-2 h-4 w-4" />
                <ArrowRight v-else class="me-2 h-4 w-4" />
                {{ t('offers.create.previous') }}
            </Button>

            <Button
                v-if="currentStep < steps.length - 1"
                :disabled="!canProceed"
                @click="nextStep"
            >
                {{ t('offers.create.next') }}
                <ArrowRight v-if="direction === 'ltr'" class="ms-2 h-4 w-4" />
                <ArrowLeft v-else class="ms-2 h-4 w-4" />
            </Button>

            <Button
                v-else
                @click="submit"
            >
                <Send class="me-2 h-4 w-4" />
                {{ t('offers.edit.submit') }}
            </Button>
        </div>
    </div>
</template>
