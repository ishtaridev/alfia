<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import { computed } from 'vue';
import {
    Package,
    Users,
    CreditCard,
    CalendarDays,
    Clock,
    CheckCircle,
    XCircle,
    Inbox,
} from '@lucide/vue';
import { useLocale } from '@/composables/useLocale';
import { i18n } from '@/i18n';
import { dashboard } from '@/routes';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Badge } from '@/components/ui/badge';
import MetricCard from '@/components/MetricCard.vue';
import BarChart from '@/components/charts/BarChart.vue';
import DoughnutChart from '@/components/charts/DoughnutChart.vue';
import LineChart from '@/components/charts/LineChart.vue';

const { t, locale } = useLocale();

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: i18n.global.t('dashboard.title'),
                href: dashboard().url,
            },
        ],
    },
});

type Stats = {
    total_offers: number;
    total_reservations: number;
    total_revenue: number;
    total_users: number;
    total_travellers: number;
};

type RecentReservation = {
    id: number;
    customer: string;
    status: string;
    total_price: number;
    travellers_number: number;
    created_at: string;
    offer_title: string;
};

type TopOffer = {
    title: string;
    reservations_count: number;
};

const props = defineProps<{
    stats: Stats;
    statusBreakdown: Record<string, number>;
    monthlyReservations: Record<string, number>;
    recentReservations: RecentReservation[];
    topOffers: TopOffer[];
}>();

const formatCurrency = (value: string | number): string => {
    return Number(value).toLocaleString(locale.value);
};

const formatDate = (dateString: string): string => {
    return new Date(dateString).toLocaleDateString(locale.value, {
        month: 'short',
        day: 'numeric',
        year: 'numeric',
    });
};

const statusBadgeClass = (status: string): string => {
    switch (status) {
        case 'confirmed':
            return 'bg-emerald-500/10 text-emerald-700 border-emerald-500/20 dark:text-emerald-400';
        case 'pending':
            return 'bg-amber-500/10 text-amber-700 border-amber-500/20 dark:text-amber-400';
        case 'cancelled':
            return 'bg-destructive/10 text-destructive border-destructive/20';
        default:
            return 'bg-muted text-muted-foreground';
    }
};

const statusIcon = (status: string) => {
    switch (status) {
        case 'confirmed':
            return CheckCircle;
        case 'pending':
            return Clock;
        case 'cancelled':
            return XCircle;
        default:
            return Clock;
    }
};

const translateStatus = (status: string): string => {
    return t(`reservations.status.${status}`);
};

const monthLabels = computed(() => {
    return Object.keys(props.monthlyReservations).map(month => {
        const [year, monthNum] = month.split('-');
        return new Date(Number(year), Number(monthNum) - 1).toLocaleDateString(locale.value, {
            month: 'short',
            year: '2-digit',
        });
    });
});

const monthData = computed(() => Object.values(props.monthlyReservations));

const statusLabels = computed(() =>
    Object.keys(props.statusBreakdown).map(status => translateStatus(status)),
);

const statusData = computed(() => Object.values(props.statusBreakdown));

const revenueLabels = computed(() => monthLabels.value);
const revenueData = computed(() => {
    // Approximate revenue per month using reservation counts as proxy
    // In a real app, you'd query actual monthly revenue from DB
    const avgRevenue = props.stats.total_revenue / Math.max(props.stats.total_reservations, 1);
    return monthData.value.map((count: number) => Math.round(count * avgRevenue));
});

const topOffersMaxCount = computed(() =>
    Math.max(...props.topOffers.map(offer => offer.reservations_count), 1),
);

const reservationBarColor = 'hsl(205 80% 50%)';
</script>

<template>
    <Head :title="t('dashboard.title')" />

    <div class="flex flex-1 flex-col gap-6 p-4 md:p-6 lg:p-8">
        <!-- Metric Cards -->
        <div class="grid grid-cols-2 gap-4 lg:grid-cols-4">
            <MetricCard
                :icon="Package"
                :label="t('dashboard.stats.total_offers')"
                :value="stats.total_offers"
                color="primary"
            />
            <MetricCard
                :icon="CalendarDays"
                :label="t('dashboard.stats.total_reservations')"
                :value="stats.total_reservations"
                color="blue"
            />
            <MetricCard
                :icon="CreditCard"
                :label="t('dashboard.stats.total_revenue')"
                :value="formatCurrency(stats.total_revenue)"
                :sub-value="t('dashboard.stats.revenue_suffix')"
                color="emerald"
            />
            <MetricCard
                :icon="Users"
                :label="t('dashboard.stats.total_users')"
                :value="stats.total_users"
                color="violet"
            />
        </div>

        <!-- Charts Row -->
        <div class="grid gap-6 md:grid-cols-2">
            <!-- Monthly Reservations Chart -->
            <Card>
                <CardHeader>
                    <CardTitle class="text-base font-semibold">
                        {{ t('dashboard.charts.monthly_reservations.title') }}
                    </CardTitle>
                </CardHeader>
                <CardContent>
                    <div class="h-64">
                        <BarChart
                            :labels="monthLabels"
                            :data="monthData"
                            :label="t('dashboard.charts.monthly_reservations.label')"
                            :color="reservationBarColor"
                        />
                    </div>
                </CardContent>
            </Card>

            <!-- Reservation Status Breakdown -->
            <Card>
                <CardHeader>
                    <CardTitle class="text-base font-semibold">
                        {{ t('dashboard.charts.reservation_status.title') }}
                    </CardTitle>
                </CardHeader>
                <CardContent>
                    <div class="h-64">
                        <DoughnutChart
                            :labels="statusLabels"
                            :data="statusData"
                        />
                    </div>
                </CardContent>
            </Card>
        </div>

        <!-- Second Charts Row -->
        <div class="grid gap-6 md:grid-cols-3">
            <!-- Revenue Trend -->
            <Card class="md:col-span-2">
                <CardHeader>
                    <CardTitle class="text-base font-semibold">
                        {{ t('dashboard.charts.revenue_trend.title') }}
                    </CardTitle>
                </CardHeader>
                <CardContent>
                    <div class="h-64">
                        <LineChart
                            :labels="revenueLabels"
                            :data="revenueData"
                            :label="t('dashboard.charts.revenue_trend.label')"
                        />
                    </div>
                </CardContent>
            </Card>

            <!-- Top Offers -->
            <Card>
                <CardHeader>
                    <CardTitle class="text-base font-semibold">
                        {{ t('dashboard.top_offers.title') }}
                    </CardTitle>
                </CardHeader>
                <CardContent>
                    <div v-if="topOffers.length === 0" class="flex flex-col items-center gap-2 py-10 text-center text-sm text-muted-foreground">
                        <Inbox class="h-8 w-8 opacity-50" />
                        {{ t('dashboard.top_offers.empty') }}
                    </div>
                    <div v-else class="space-y-4">
                        <div
                            v-for="(offer, index) in topOffers"
                            :key="index"
                            class="flex items-center gap-3"
                        >
                            <span class="w-5 text-sm font-semibold text-muted-foreground">
                                {{ index + 1 }}
                            </span>
                            <div class="min-w-0 flex-1">
                                <p class="truncate text-sm font-medium text-card-foreground">
                                    {{ offer.title }}
                                </p>
                                <div class="mt-1.5 h-1.5 w-full overflow-hidden rounded-full bg-muted">
                                    <div
                                        class="h-full rounded-full bg-primary transition-all duration-500"
                                        :style="{ width: `${(offer.reservations_count / topOffersMaxCount) * 100}%` }"
                                    />
                                </div>
                            </div>
                            <span class="text-xs font-medium tabular-nums text-muted-foreground">
                                {{ offer.reservations_count }}
                            </span>
                        </div>
                    </div>
                </CardContent>
            </Card>
        </div>

        <!-- Recent Reservations -->
        <Card>
            <CardHeader>
                <CardTitle class="text-base font-semibold">
                    {{ t('dashboard.recent_reservations.title') }}
                </CardTitle>
            </CardHeader>
            <CardContent>
                <div v-if="recentReservations.length === 0" class="flex flex-col items-center gap-2 py-12 text-center text-sm text-muted-foreground">
                    <Inbox class="h-8 w-8 opacity-50" />
                    {{ t('dashboard.recent_reservations.empty') }}
                </div>
                <div v-else class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b border-border bg-muted/40">
                                <th class="px-4 py-3 text-start text-xs font-semibold uppercase tracking-wider text-muted-foreground">{{ t('dashboard.recent_reservations.columns.customer') }}</th>
                                <th class="px-4 py-3 text-start text-xs font-semibold uppercase tracking-wider text-muted-foreground">{{ t('dashboard.recent_reservations.columns.offer') }}</th>
                                <th class="px-4 py-3 text-center text-xs font-semibold uppercase tracking-wider text-muted-foreground">{{ t('dashboard.recent_reservations.columns.travellers') }}</th>
                                <th class="px-4 py-3 text-center text-xs font-semibold uppercase tracking-wider text-muted-foreground">{{ t('dashboard.recent_reservations.columns.status') }}</th>
                                <th class="px-4 py-3 text-end text-xs font-semibold uppercase tracking-wider text-muted-foreground">{{ t('dashboard.recent_reservations.columns.price') }}</th>
                                <th class="px-4 py-3 text-end text-xs font-semibold uppercase tracking-wider text-muted-foreground">{{ t('dashboard.recent_reservations.columns.date') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr
                                v-for="reservation in recentReservations"
                                :key="reservation.id"
                                class="border-b border-border transition-colors last:border-b-0 hover:bg-muted/50"
                            >
                                <td class="px-4 py-3">
                                    <div class="flex items-center gap-3">
                                        <div class="flex h-9 w-9 items-center justify-center rounded-full bg-primary/10">
                                            <Users class="h-4 w-4 text-primary" />
                                        </div>
                                        <p class="font-medium text-card-foreground">{{ reservation.customer }}</p>
                                    </div>
                                </td>
                                <td class="px-4 py-3 text-muted-foreground">
                                    {{ reservation.offer_title }}
                                </td>
                                <td class="px-4 py-3 text-center tabular-nums text-card-foreground">
                                    {{ reservation.travellers_number }}
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <Badge
                                        :class="statusBadgeClass(reservation.status)"
                                        class="gap-1 border"
                                    >
                                        <component :is="statusIcon(reservation.status)" class="h-3 w-3" />
                                        {{ translateStatus(reservation.status) }}
                                    </Badge>
                                </td>
                                <td class="px-4 py-3 text-end font-medium tabular-nums text-card-foreground">
                                    {{ formatCurrency(reservation.total_price) }} {{ t('dashboard.stats.revenue_suffix') }}
                                </td>
                                <td class="px-4 py-3 text-end tabular-nums text-muted-foreground">
                                    {{ formatDate(reservation.created_at) }}
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </CardContent>
        </Card>
    </div>
</template>
