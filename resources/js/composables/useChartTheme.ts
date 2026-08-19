import type { ComputedRef } from 'vue';
import { computed } from 'vue';
import { useAppearance } from '@/composables/useAppearance';

export interface ChartColors {
    text: string;
    grid: string;
    background: string;
    tooltipBackground: string;
    tooltipText: string;
}

export interface ChartTheme {
    colors: ComputedRef<ChartColors>;
    isDark: ComputedRef<boolean>;
}

const chartPalette = [
    'hsl(12 76% 61%)',   // chart-1: coral
    'hsl(173 58% 39%)',  // chart-2: teal
    'hsl(197 37% 24%)',  // chart-3: dark blue
    'hsl(43 74% 66%)',   // chart-4: gold
    'hsl(27 87% 67%)',   // chart-5: orange
    'hsl(220 70% 50%)',  // blue
    'hsl(160 60% 45%)',  // green
    'hsl(280 65% 60%)',  // purple
];

export function useChartTheme(): ChartTheme {
    const { resolvedAppearance } = useAppearance();

    const isDark = computed(() => resolvedAppearance.value === 'dark');

    const colors = computed<ChartColors>(() => {
        if (isDark.value) {
            return {
                text: 'rgba(255, 255, 255, 0.7)',
                grid: 'rgba(255, 255, 255, 0.1)',
                background: 'transparent',
                tooltipBackground: 'hsl(205 64% 18%)',
                tooltipText: '#ffffff',
            };
        }

        return {
            text: 'rgba(0, 0, 0, 0.6)',
            grid: 'rgba(0, 0, 0, 0.08)',
            background: 'transparent',
            tooltipBackground: '#ffffff',
            tooltipText: '#000000',
        };
    });

    return {
        colors,
        isDark,
    };
}

export function getChartColors(count: number = chartPalette.length): string[] {
    return chartPalette.slice(0, count);
}

export function getChartColorsWithAlpha(count: number = chartPalette.length, alpha: number = 0.15): string[] {
    return chartPalette.slice(0, count).map(color => {
        // Convert hsl(...) to hsla(...)
        return color.replace('hsl(', 'hsla(').replace(')', `, ${alpha})`);
    });
}
