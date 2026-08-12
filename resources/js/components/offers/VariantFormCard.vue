<script setup lang="ts">
import { X } from '@lucide/vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { useLocale } from '@/composables/useLocale';
import PricingFields from './PricingFields.vue';

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

type Props = {
    variant: Variant;
    index: number;
    canDelete: boolean;
};

const props = defineProps<Props>();

const emit = defineEmits<{
    update: [index: number, data: Partial<Variant>];
    updatePricing: [index: number, data: Partial<Pricing>];
    remove: [index: number];
}>();

const { t } = useLocale();

const updateField = (field: keyof Variant, value: string) => {
    emit('update', props.index, { [field]: value });
};
</script>

<template>
    <div class="rounded-lg border border-border p-4">
        <div class="mb-4 flex items-center justify-between">
            <h4 class="text-sm font-medium text-muted-foreground">
                {{ t('offer_components.variant_number', { number: index + 1 }) }}
            </h4>
            <Button
                v-if="canDelete"
                type="button"
                variant="ghost"
                size="sm"
                class="h-8 w-8 p-0 text-muted-foreground hover:text-destructive"
                @click="emit('remove', index)"
            >
                <X class="h-4 w-4" />
            </Button>
        </div>

        <div class="grid gap-4 sm:grid-cols-2">
            <div class="space-y-2">
                <Label :for="`travel_date_${index}`">{{ t('offer_components.travel_date') }}</Label>
                <Input
                    :id="`travel_date_${index}`"
                    type="date"
                    :model-value="variant.travel_date"
                    @update:model-value="(v) => updateField('travel_date', v as string)"
                />
            </div>

            <div class="space-y-2">
                <Label :for="`airport_${index}`">{{ t('offer_components.airport') }}</Label>
                <Input
                    :id="`airport_${index}`"
                    type="text"
                    :placeholder="t('offer_components.airport_placeholder')"
                    :model-value="variant.airport"
                    @update:model-value="(v) => updateField('airport', v as string)"
                />
            </div>
        </div>

        <div class="mt-4">
            <PricingFields
                :pricing="variant.pricing"
                @update="(data) => emit('updatePricing', index, data)"
            />
        </div>
    </div>
</template>
