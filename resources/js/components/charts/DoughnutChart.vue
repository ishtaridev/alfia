<script setup lang="ts">
import { computed } from 'vue';
import { Doughnut } from 'vue-chartjs';
import {
    Chart as ChartJS,
    Title,
    Tooltip,
    Legend,
    ArcElement,
    type ChartOptions,
    type ChartData,
} from 'chart.js';
import { useChartTheme, getChartColors, getChartColorsWithAlpha } from '@/composables/useChartTheme';

ChartJS.register(Title, Tooltip, Legend, ArcElement);

const props = defineProps<{
    labels: string[];
    data: number[];
}>();

const { colors, isDark } = useChartTheme();

const chartColors = computed(() => getChartColors(props.labels.length));
const chartColorsAlpha = computed(() => getChartColorsWithAlpha(props.labels.length, 0.7));

const chartData = computed<ChartData<'doughnut'>>(() => ({
    labels: props.labels,
    datasets: [
        {
            data: props.data,
            backgroundColor: chartColorsAlpha.value,
            borderColor: chartColors.value,
            borderWidth: 2,
            hoverOffset: 4,
        },
    ],
}));

const chartOptions = computed<ChartOptions<'doughnut'>>(() => ({
    responsive: true,
    maintainAspectRatio: false,
    cutout: '65%',
    plugins: {
        legend: {
            position: 'bottom',
            labels: {
                color: colors.value.text,
                padding: 16,
                usePointStyle: true,
                pointStyle: 'circle',
                font: {
                    size: 12,
                },
            },
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
}));
</script>

<template>
    <div class="relative h-full w-full">
        <Doughnut :data="chartData" :options="chartOptions" />
    </div>
</template>
