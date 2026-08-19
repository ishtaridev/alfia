<script setup lang="ts">
import { computed } from 'vue';
import { Line } from 'vue-chartjs';
import {
    Chart as ChartJS,
    Title,
    Tooltip,
    Legend,
    Filler,
    LineElement,
    PointElement,
    CategoryScale,
    LinearScale,
    type ChartOptions,
    type ChartData,
} from 'chart.js';
import { useChartTheme, getChartColors, getChartColorsWithAlpha } from '@/composables/useChartTheme';

ChartJS.register(Title, Tooltip, Legend, Filler, LineElement, PointElement, CategoryScale, LinearScale);

const props = defineProps<{
    labels: string[];
    data: number[];
    label?: string;
}>();

const { colors, isDark } = useChartTheme();

const chartColor = computed(() => getChartColors(1)[0]);
const chartColorAlpha = computed(() => getChartColorsWithAlpha(1, 0.15)[0]);

const chartData = computed<ChartData<'line'>>(() => ({
    labels: props.labels,
    datasets: [
        {
            label: props.label ?? 'Value',
            data: props.data,
            borderColor: chartColor.value,
            backgroundColor: chartColorAlpha.value,
            borderWidth: 2,
            tension: 0.4,
            fill: true,
            pointRadius: 3,
            pointHoverRadius: 5,
            pointBackgroundColor: chartColor.value,
            pointBorderColor: 'transparent',
        },
    ],
}));

const chartOptions = computed<ChartOptions<'line'>>(() => ({
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
        <Line :data="chartData" :options="chartOptions" />
    </div>
</template>
