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
    <div class="flex items-center justify-center py-2">
        <template v-for="(step, index) in steps" :key="index">
            <div class="flex items-center">
                <div class="flex flex-col items-center">
                    <div
                        class="flex h-10 w-10 items-center justify-center rounded-full border-2 text-sm font-semibold transition-all duration-200"
                        :class="[
                            index < currentStep
                                ? 'border-primary bg-primary text-primary-foreground shadow-sm'
                                : index === currentStep
                                    ? 'border-primary bg-primary/10 text-primary shadow-sm ring-2 ring-primary/20'
                                    : 'border-muted-foreground/20 bg-background text-muted-foreground',
                        ]"
                    >
                        <Check v-if="index < currentStep" class="h-5 w-5" />
                        <span v-else>{{ index + 1 }}</span>
                    </div>
                    <span
                        class="mt-1.5 hidden text-xs font-medium transition-colors duration-200 sm:block"
                        :class="[
                            index <= currentStep ? 'text-primary' : 'text-muted-foreground/60',
                        ]"
                    >
                        {{ step.label }}
                    </span>
                </div>
                <div
                    v-if="index < steps.length - 1"
                    class="mx-3 mb-6 h-0.5 w-12 transition-colors duration-200 sm:w-20"
                    :class="[
                        index < currentStep ? 'bg-primary' : 'bg-muted-foreground/15',
                    ]"
                />
            </div>
        </template>
    </div>
</template>
