<script setup lang="ts">
import { computed, watch } from 'vue';
import { Bar } from 'vue-chartjs';
import {
    Chart as ChartJS,
    Title,
    Tooltip,
    Legend,
    BarElement,
    CategoryScale,
    LinearScale,
    type ChartOptions,
    type ChartData,
} from 'chart.js';
import { useChartTheme, getChartColors, getChartColorsWithAlpha } from '@/composables/useChartTheme';

ChartJS.register(Title, Tooltip, Legend, BarElement, CategoryScale, LinearScale);

const props = withDefaults(
    defineProps<{
        labels: string[];
        data: number[];
        label?: string;
        color?: string;
    }>(),
    {
        color: getChartColors(1)[0],
    },
);

const { colors, isDark } = useChartTheme();

const backgroundColor = computed(() => props.color.replace('hsl(', 'hsla(').replace(')', ', 0.7)'));

const chartData = computed<ChartData<'bar'>>(() => ({
    labels: props.labels,
    datasets: [
        {
            label: props.label ?? 'Value',
            data: props.data,
            backgroundColor: backgroundColor.value,
            borderColor: props.color,
            borderWidth: 1,
            borderRadius: 4,
        },
    ],
}));

const chartOptions = computed<ChartOptions<'bar'>>(() => ({
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
        legend: {
            display: false,
        },
        tooltip: {
            backgroundColor: colors.value.tooltipBackground,
            titleColor: colors.value.tooltipText,
            bodyColor: colors.value.tooltipText,
            borderColor: colors.value.grid,
            borderWidth: 1,
            padding: 10,
            cornerRadius: 8,
        },
    },
    scales: {
        x: {
            grid: {
                display: false,
            },
            ticks: {
                color: colors.value.text,
                font: {
                    size: 11,
                },
            },
        },
        y: {
            grid: {
                color: colors.value.grid,
            },
            ticks: {
                color: colors.value.text,
                font: {
                    size: 11,
                },
            },
            beginAtZero: true,
        },
    },
}));
</script>

<template>
    <div class="relative h-full w-full">
        <Bar :data="chartData" :options="chartOptions" />
    </div>
</template>
