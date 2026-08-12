<script setup lang="ts">
import { Check } from '@lucide/vue';

type Step = {
    label: string;
};

type Props = {
    steps: Step[];
    currentStep: number;
};

defineProps<Props>();
</script>

<template>
    <div class="flex items-center justify-center">
        <template v-for="(step, index) in steps" :key="index">
            <div class="flex items-center">
                <div class="flex flex-col items-center">
                    <div class="flex h-10 w-10 items-center justify-center rounded-full border-2 text-sm font-semibold transition-colors"
                        :class="[
                            index < currentStep
                                ? 'border-primary bg-primary text-primary-foreground'
                                : index === currentStep
                                    ? 'border-primary bg-primary/10 text-primary'
                                    : 'border-muted-foreground/30 bg-background text-muted-foreground',
                        ]">
                        <Check v-if="index < currentStep" class="h-5 w-5" />
                        <span v-else>{{ index + 1 }}</span>
                    </div>
                    <span class="mt-2 text-xs font-medium" :class="[
                        index <= currentStep ? 'text-primary' : 'text-muted-foreground',
                    ]">
                        {{ step.label }}
                    </span>
                </div>
                <div v-if="index < steps.length - 1" class="mx-2 mb-6 h-0.5 w-16 sm:w-24" :class="[
                    index < currentStep ? 'bg-primary' : 'bg-muted-foreground/30',
                ]" />
            </div>
        </template>
    </div>
</template>
