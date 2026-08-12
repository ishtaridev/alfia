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
import { index as offersIndex, create as offersCreate, store as offersStore } from '@/routes/offers';

const { t } = useLocale();

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Offers',
                href: offersIndex().url,
            },
            {
                title: 'Create Offer',
                href: offersCreate().url,
            },
        ],
    },
});

const currentStep = ref(0);
const steps = computed(() => [
    { label: t('offers.create.step_details') },
    { label: t('offers.create.step_variants') },
    { label: t('offers.create.step_images') },
    { label: t('offers.create.step_review') },
]);

const title = ref('');
const description = ref('');
const variants = ref([
    {
        travel_date: '',
        airport: '',
        pricing: {
            collectif_room: 0,
            room_of_four: 0,
            room_of_three: 0,
            room_of_two: 0,
            feeding: 0,
        },
    },
]);
const images = ref<File[]>([]);

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

const submit = () => {
    const formData = new FormData();
    formData.append('title', title.value);

    if (description.value) {
        formData.append('description', description.value);
    }

    variants.value.forEach((variant, i) => {
        formData.append(`variants[${i}][travel_date]`, variant.travel_date);
        formData.append(`variants[${i}][airport]`, variant.airport);
        formData.append(`variants[${i}][pricing][collectif_room]`, String(variant.pricing.collectif_room));
        formData.append(`variants[${i}][pricing][room_of_four]`, String(variant.pricing.room_of_four));
        formData.append(`variants[${i}][pricing][room_of_three]`, String(variant.pricing.room_of_three));
        formData.append(`variants[${i}][pricing][room_of_two]`, String(variant.pricing.room_of_two));
        formData.append(`variants[${i}][pricing][feeding]`, String(variant.pricing.feeding));
    });

    images.value.forEach((file) => {
        formData.append('images[]', file);
    });

    router.post(offersStore().url, formData, {
        forceFormData: true,
    });
};
</script>

<template>
    <Head :title="t('offers.create.title')" />

    <div class="flex h-full flex-1 flex-col gap-6 overflow-x-auto rounded-xl p-4">
        <Heading
            :title="t('offers.create.title')"
            :description="t('offers.create.description')"
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
                    :key="index"
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
                <ImageUploader v-model="images" />
            </div>

            <div v-if="currentStep === 3" class="mx-auto max-w-3xl space-y-6">
                <h3 class="text-lg font-medium">{{ t('offers.create.review') }}</h3>

                <div class="rounded-lg border border-border p-4">
                    <h4 class="font-medium">{{ title || t('offers.create.untitled_offer') }}</h4>
                    <div
                        v-if="description"
                        class="tiptap mt-2 text-sm text-muted-foreground prose prose-sm"
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

                <div v-if="images.length > 0" class="space-y-2">
                    <h4 class="font-medium">{{ t('offers.create.images_count', { count: images.length }) }}</h4>
                    <p class="text-sm text-muted-foreground">{{ t('offers.create.images_will_be_uploaded', { count: images.length }) }}</p>
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
                {{ t('offers.create.submit') }}
            </Button>
        </div>
    </div>
</template>
