<script setup lang="ts">
import { computed, type Component } from 'vue';
import { Card, CardContent } from '@/components/ui/card';

const props = defineProps<{
    icon: Component;
    label: string;
    value: string | number;
    subValue?: string;
    trend?: number;
    trendLabel?: string;
    color?: 'primary' | 'blue' | 'emerald' | 'amber' | 'rose' | 'violet';
}>();

const colorMap = computed(() => {
    const map: Record<NonNullable<typeof props.color>, { bg: string; text: string }> = {
        primary: { bg: 'bg-primary/10', text: 'text-primary' },
        blue: { bg: 'bg-blue-500/10', text: 'text-blue-600 dark:text-blue-400' },
        emerald: { bg: 'bg-emerald-500/10', text: 'text-emerald-600 dark:text-emerald-400' },
        amber: { bg: 'bg-amber-500/10', text: 'text-amber-600 dark:text-amber-400' },
        rose: { bg: 'bg-rose-500/10', text: 'text-rose-600 dark:text-rose-400' },
        violet: { bg: 'bg-violet-500/10', text: 'text-violet-600 dark:text-violet-400' },
    };

    return map[props.color ?? 'primary'] ?? map.primary;
});

const trendText = computed(() => {
    if (props.trend === undefined || props.trend === null) {
        return null;
    }
    const prefix = props.trend > 0 ? '+' : '';
    return `${prefix}${props.trend}%`;
});

const trendIsPositive = computed(() => {
    return props.trend !== undefined && props.trend !== null && props.trend >= 0;
});
</script>

<template>
    <Card class="relative overflow-hidden transition-shadow duration-200 hover:shadow-md">
        <CardContent class="flex items-start gap-4 p-5">
            <div
                class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl"
                :class="colorMap.bg"
            >
                <component
                    :is="icon"
                    class="h-5 w-5"
                    :class="colorMap.text"
                />
            </div>
            <div class="min-w-0 flex-1">
                <p class="text-sm font-medium text-muted-foreground">
                    {{ label }}
                </p>
                <div class="mt-1 flex items-baseline gap-2">
                    <p class="text-2xl font-bold tracking-tight text-card-foreground">
                        {{ value }}
                    </p>
                    <span
                        v-if="subValue"
                        class="text-sm font-normal text-muted-foreground"
                    >
                        {{ subValue }}
                    </span>
                </div>
                <div
                    v-if="trendText"
                    class="mt-1.5 flex items-center gap-1"
                >
                    <span
                        class="text-xs font-medium"
                        :class="
                            trendIsPositive
                                ? 'text-emerald-600 dark:text-emerald-400'
                                : 'text-destructive'
                        "
                    >
                        {{ trendText }}
                    </span>
                    <span
                        v-if="trendLabel"
                        class="text-xs text-muted-foreground"
                    >
                        {{ trendLabel }}
                    </span>
                </div>
            </div>
        </CardContent>
    </Card>
</template>
