<script setup lang="ts">
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { useLocale } from '@/composables/useLocale';

type Pricing = {
    collectif_room: number;
    room_of_four: number;
    room_of_three: number;
    room_of_two: number;
    feeding: number;
};

defineProps<{
    pricing: Pricing;
}>();

const emit = defineEmits<{
    update: [data: Partial<Pricing>];
}>();

const { t } = useLocale();

const updateField = (field: keyof Pricing, value: string) => {
    emit('update', { [field]: Number(value) || 0 });
};
</script>

<template>
    <div class="grid grid-cols-2 gap-3 sm:grid-cols-3">
        <div class="space-y-1.5">
            <Label class="text-sm text-muted-foreground">{{ t('offer_components.collectif_room') }}</Label>
            <div class="relative">
                <Input type="number" min="0" placeholder="0" class="pe-12 tabular-nums"
                    :model-value="pricing.collectif_room"
                    @update:model-value="(v) => updateField('collectif_room', v as string)" />
                <span
                    class="pointer-events-none absolute end-3 top-1/2 -translate-y-1/2 text-sm text-muted-foreground">DZD</span>
            </div>
        </div>

        <div class="space-y-1.5">
            <Label class="text-sm text-muted-foreground">{{ t('offer_components.room_of_four') }}</Label>
            <div class="relative">
                <Input type="number" min="0" placeholder="0" class="pe-12 tabular-nums"
                    :model-value="pricing.room_of_four"
                    @update:model-value="(v) => updateField('room_of_four', v as string)" />
                <span
                    class="pointer-events-none absolute end-3 top-1/2 -translate-y-1/2 text-sm text-muted-foreground">DZD</span>
            </div>
        </div>

        <div class="space-y-1.5">
            <Label class="text-sm text-muted-foreground">{{ t('offer_components.room_of_three') }}</Label>
            <div class="relative">
                <Input type="number" min="0" placeholder="0" class="pe-12 tabular-nums"
                    :model-value="pricing.room_of_three"
                    @update:model-value="(v) => updateField('room_of_three', v as string)" />
                <span
                    class="pointer-events-none absolute end-3 top-1/2 -translate-y-1/2 text-sm text-muted-foreground">DZD</span>
            </div>
        </div>

        <div class="space-y-1.5">
            <Label class="text-sm text-muted-foreground">{{ t('offer_components.room_of_two') }}</Label>
            <div class="relative">
                <Input type="number" min="0" placeholder="0" class="pe-12 tabular-nums"
                    :model-value="pricing.room_of_two"
                    @update:model-value="(v) => updateField('room_of_two', v as string)" />
                <span
                    class="pointer-events-none absolute end-3 top-1/2 -translate-y-1/2 text-sm text-muted-foreground">DZD</span>
            </div>
        </div>

        <div class="space-y-1.5">
            <Label class="text-sm text-muted-foreground">{{ t('offer_components.feeding') }}</Label>
            <div class="relative">
                <Input type="number" min="0" placeholder="0" class="pe-12 tabular-nums" :model-value="pricing.feeding"
                    @update:model-value="(v) => updateField('feeding', v as string)" />
                <span
                    class="pointer-events-none absolute end-3 top-1/2 -translate-y-1/2 text-sm text-muted-foreground">DZD</span>
            </div>
        </div>
    </div>
</template>
