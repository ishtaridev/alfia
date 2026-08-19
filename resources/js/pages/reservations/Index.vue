<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import {
    Plus,
    Pencil,
    Trash2,
    Calendar,
    MapPin,
    Eye,
    ChevronLeft,
    ChevronRight,
    Users,
    CreditCard,
    Clock,
    CheckCircle,
    XCircle,
    MoreHorizontal,
    Inbox,
    Wallet,
} from '@lucide/vue';
import { ref, computed } from 'vue';
import Heading from '@/components/Heading.vue';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent } from '@/components/ui/card';
import { Dialog, DialogContent, DialogDescription, DialogFooter, DialogHeader, DialogTitle } from '@/components/ui/dialog';
import { Sheet, SheetContent, SheetHeader, SheetTitle, SheetDescription } from '@/components/ui/sheet';
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuItem,
    DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu';
import { Separator } from '@/components/ui/separator';
import { useLocale } from '@/composables/useLocale';
import { formatDate } from '@/lib/formatDate';
import { index as offersIndex } from '@/routes/offers';
import {
    index as reservationsIndex,
    create as reservationsCreate,
    edit as reservationsEdit,
    destroy as reservationsDestroy,
} from '@/routes/offer-variants/reservations';

const { t } = useLocale();

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Offers',
                href: offersIndex().url,
            },
            {
                title: 'Reservations',
                href: '#',
            },
        ],
    },
});

type Pricing = {
    collectif_room: number;
    room_of_four: number;
    room_of_three: number;
    room_of_two: number;
    feeding: number;
};

type OfferVariant = {
    id: number;
    travel_date: string;
    airport: string;
    offer: {
        id: number;
        title: string;
        code: string;
    };
    pricing: Pricing | null;
};

type Reservation = {
    id: number;
    customer: string;
    phone: string;
    travellers_number: number;
    wilaya: string;
    room_type: string;
    status: string;
    include_feeding: boolean;
    total_price: number;
};

type PaginatedReservations = {
    data: Reservation[];
    current_page: number;
    last_page: number;
    per_page: number;
    total: number;
};

type Stats = {
    total_reservations: number;
    total_travellers: number;
    total_revenue: number;
    confirmed_count: number;
    pending_count: number;
    cancelled_count: number;
};

type Filters = {
    status: string | null;
};

const props = defineProps<{
    offerVariant: OfferVariant;
    reservations: PaginatedReservations;
    stats: Stats;
    filters: Filters;
}>();

const showDeleteDialog = ref(false);
const reservationToDelete = ref<Reservation | null>(null);
const selectedReservation = ref<Reservation | null>(null);
const showViewSheet = ref(false);



const statusBadgeClass = (status: string): string => {
    switch (status) {
        case 'confirmed':
            return 'bg-primary/10 text-primary border-primary/20';
        case 'pending':
            return 'bg-amber-500/10 text-amber-700 border-amber-500/20';
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

const confirmDelete = (reservation: Reservation) => {
    reservationToDelete.value = reservation;
    showDeleteDialog.value = true;
};

const deleteReservation = (offerVariantId: number) => {
    if (!reservationToDelete.value) {
        return;
    }

    router.delete(reservationsDestroy([offerVariantId, reservationToDelete.value.id]).url, {
        onSuccess: () => {
            showDeleteDialog.value = false;
            reservationToDelete.value = null;
        },
    });
};

const viewReservation = (reservation: Reservation) => {
    selectedReservation.value = reservation;
    showViewSheet.value = true;
};

const getRoomPrice = (roomType: string): number => {
    const pricing = props.offerVariant.pricing;
    if (!pricing) {
        return 0;
    }
    switch (roomType) {
        case 'collectif': return pricing.collectif_room;
        case 'room_of_four': return pricing.room_of_four;
        case 'room_of_three': return pricing.room_of_three;
        case 'room_of_two': return pricing.room_of_two;
        default: return 0;
    }
};

const priceBreakdown = computed(() => {
    if (!selectedReservation.value || !props.offerVariant.pricing) {
        return null;
    }

    const r = selectedReservation.value;
    const pricing = props.offerVariant.pricing;
    const roomPricePerPerson = getRoomPrice(r.room_type);
    const roomTotal = roomPricePerPerson * r.travellers_number;
    const feedingTotal = r.include_feeding ? pricing.feeding * r.travellers_number : 0;

    return {
        roomPricePerPerson,
        roomTotal,
        feedingPerPerson: pricing.feeding,
        feedingTotal,
        total: roomTotal + feedingTotal,
    };
});

const filterStatus = (status: string | null) => {
    router.get(
        reservationsIndex(props.offerVariant.id).url,
        status ? { status } : {},
        { preserveState: true, preserveScroll: true },
    );
};

const activeFilter = computed(() => props.filters.status);

const statusFilters = [
    { key: null, label: 'all' },
    { key: 'confirmed', label: 'confirmed' },
    { key: 'pending', label: 'pending' },
    { key: 'cancelled', label: 'cancelled' },
];
</script>

<template>

    <Head :title="t('reservations.index.title')" />

    <div class="flex h-full flex-1 flex-col gap-6 overflow-x-auto rounded-xl p-4">
        <!-- Header -->
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <Heading :title="t('reservations.index.title')" :description="offerVariant.offer.title" />
            <Button as-child>
                <Link :href="reservationsCreate(offerVariant.id).url">
                    <Plus class="mr-2 h-4 w-4" />
                    {{ t('reservations.index.create_button') }}
                </Link>
            </Button>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-2 gap-3 lg:grid-cols-4">
            <Card class="overflow-hidden border-l-4 border-l-primary">
                <CardContent class="flex items-center gap-3 p-4">
                    <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-primary/10">
                        <Users class="h-4 w-4 text-primary" />
                    </div>
                    <div>
                        <p class="text-xs font-medium text-muted-foreground">{{
                            t('reservations.index.stats.total_reservations') }}</p>
                        <p class="text-xl font-bold">{{ stats.total_reservations }}</p>
                    </div>
                </CardContent>
            </Card>

            <Card class="overflow-hidden border-l-4 border-l-blue-500">
                <CardContent class="flex items-center gap-3 p-4">
                    <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-blue-500/10">
                        <Wallet class="h-4 w-4 text-blue-500" />
                    </div>
                    <div>
                        <p class="text-xs font-medium text-muted-foreground">{{
                            t('reservations.index.stats.total_travellers') }}</p>
                        <p class="text-xl font-bold">{{ stats.total_travellers }}</p>
                    </div>
                </CardContent>
            </Card>

            <Card class="overflow-hidden border-l-4 border-l-emerald-500">
                <CardContent class="flex items-center gap-3 p-4">
                    <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-emerald-500/10">
                        <CreditCard class="h-4 w-4 text-emerald-500" />
                    </div>
                    <div>
                        <p class="text-xs font-medium text-muted-foreground">{{
                            t('reservations.index.stats.total_revenue') }}</p>
                        <p class="text-xl font-bold">{{ stats.total_revenue.toLocaleString() }} <span
                                class="text-xs font-normal text-muted-foreground">DZD</span></p>
                    </div>
                </CardContent>
            </Card>

            <Card class="overflow-hidden border-l-4 border-l-amber-500">
                <CardContent class="flex items-center gap-3 p-4">
                    <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-amber-500/10">
                        <Clock class="h-4 w-4 text-amber-500" />
                    </div>
                    <div class="min-w-0">
                        <p class="text-xs font-medium text-muted-foreground">{{ t('reservations.index.stats.pending') }}
                        </p>
                        <p class="text-xl font-bold">{{ stats.pending_count }}</p>
                    </div>
                </CardContent>
            </Card>
        </div>

        <!-- Variant Info Card -->
        <Card class="overflow-hidden">
            <CardContent class="p-4">
                <div class="flex flex-wrap items-center gap-x-6 gap-y-3">
                    <div class="flex items-center gap-2">
                        <div class="flex h-8 w-8 items-center justify-center rounded-full bg-primary/10">
                            <Calendar class="h-4 w-4 text-primary" />
                        </div>
                        <div>
                            <p class="text-xs text-muted-foreground">{{ t('reservations.index.travel_date') }}</p>
                            <p class="text-sm font-medium">{{ formatDate(offerVariant.travel_date) }}</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-2">
                        <div class="flex h-8 w-8 items-center justify-center rounded-full bg-primary/10">
                            <MapPin class="h-4 w-4 text-primary" />
                        </div>
                        <div>
                            <p class="text-xs text-muted-foreground">{{ t('reservations.index.airport') }}</p>
                            <p class="text-sm font-medium">{{ offerVariant.airport }}</p>
                        </div>
                    </div>
                    <template v-if="offerVariant.pricing">
                        <Separator orientation="vertical" class="hidden h-8 sm:block" />
                        <div class="flex flex-wrap gap-3">
                            <div class="rounded-md bg-muted/50 px-3 py-1.5 text-center">
                                <p class="text-[10px] uppercase tracking-wider text-muted-foreground">{{
                                    t('reservations.index.collectif_room') }}</p>
                                <p class="text-sm font-semibold">{{ offerVariant.pricing.collectif_room }} <span
                                        class="text-[10px] font-normal text-muted-foreground">DZD</span></p>
                            </div>
                            <div class="rounded-md bg-muted/50 px-3 py-1.5 text-center">
                                <p class="text-[10px] uppercase tracking-wider text-muted-foreground">{{
                                    t('reservations.index.room_of_four') }}</p>
                                <p class="text-sm font-semibold">{{ offerVariant.pricing.room_of_four }} <span
                                        class="text-[10px] font-normal text-muted-foreground">DZD</span></p>
                            </div>
                            <div class="rounded-md bg-muted/50 px-3 py-1.5 text-center">
                                <p class="text-[10px] uppercase tracking-wider text-muted-foreground">{{
                                    t('reservations.index.room_of_three') }}</p>
                                <p class="text-sm font-semibold">{{ offerVariant.pricing.room_of_three }} <span
                                        class="text-[10px] font-normal text-muted-foreground">DZD</span></p>
                            </div>
                            <div class="rounded-md bg-muted/50 px-3 py-1.5 text-center">
                                <p class="text-[10px] uppercase tracking-wider text-muted-foreground">{{
                                    t('reservations.index.room_of_two') }}</p>
                                <p class="text-sm font-semibold">{{ offerVariant.pricing.room_of_two }} <span
                                        class="text-[10px] font-normal text-muted-foreground">DZD</span></p>
                            </div>
                            <div class="rounded-md bg-muted/50 px-3 py-1.5 text-center">
                                <p class="text-[10px] uppercase tracking-wider text-muted-foreground">{{
                                    t('reservations.index.feeding') }}</p>
                                <p class="text-sm font-semibold">{{ offerVariant.pricing.feeding }} <span
                                        class="text-[10px] font-normal text-muted-foreground">DZD</span></p>
                            </div>
                        </div>
                    </template>
                </div>
            </CardContent>
        </Card>

        <!-- Status Filters -->
        <div class="flex flex-wrap gap-2">
            <Button v-for="filter in statusFilters" :key="filter.label" size="sm"
                :variant="activeFilter === filter.key ? 'default' : 'outline'" @click="filterStatus(filter.key)">
                {{ t(`reservations.index.filters.${filter.label}`) }}
                <span v-if="filter.key"
                    class="ml-1.5 inline-flex h-5 min-w-[1.25rem] items-center justify-center rounded-full px-1 text-[10px] font-bold"
                    :class="activeFilter === filter.key ? 'bg-primary-foreground/20 text-primary-foreground' : 'bg-muted text-muted-foreground'">
                    {{ stats[`${filter.key}_count` as keyof Stats] }}
                </span>
            </Button>
        </div>

        <!-- Empty State -->
        <div v-if="reservations.data.length === 0"
            class="flex flex-col items-center justify-center rounded-xl border border-dashed border-border py-16 text-center">
            <div class="flex h-12 w-12 items-center justify-center rounded-full bg-muted">
                <Inbox class="h-6 w-6 text-muted-foreground" />
            </div>
            <p class="mt-4 text-lg font-medium text-muted-foreground">{{ t('reservations.index.empty_title') }}</p>
            <p class="mt-1 text-sm text-muted-foreground/70">{{ t('reservations.index.empty_description') }}</p>
            <Button class="mt-4" as-child>
                <Link :href="reservationsCreate(offerVariant.id).url">
                    <Plus class="mr-2 h-4 w-4" />
                    {{ t('reservations.index.create_button') }}
                </Link>
            </Button>
        </div>

        <!-- Table -->
        <template v-else>
            <div class="overflow-hidden rounded-lg border border-border">
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b border-border bg-muted/50">
                                <th class="px-4 py-3 text-center font-medium text-muted-foreground">{{
                                    t('reservations.index.customer') }}</th>
                                <th class="px-4 py-3 text-center font-medium text-muted-foreground">{{
                                    t('reservations.index.phone') }}</th>
                                <th class="px-4 py-3 text-center font-medium text-muted-foreground">{{
                                    t('reservations.index.travellers') }}</th>
                                <th class="px-4 py-3 text-center font-medium text-muted-foreground">{{
                                    t('reservations.index.room_type') }}</th>
                                <th class="px-4 py-3 text-center font-medium text-muted-foreground">{{
                                    t('reservations.index.wilaya') }}</th>
                                <th class="px-4 py-3 text-center font-medium text-muted-foreground">{{
                                    t('reservations.index.status') }}</th>
                                <th class="px-4 py-3 text-center font-medium text-muted-foreground">{{
                                    t('reservations.index.price') }}</th>
                                <th class="px-4 py-3 text-center font-medium text-muted-foreground">{{
                                    t('reservations.index.actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="reservation in reservations.data" :key="reservation.id"
                                class="border-b border-border transition-colors hover:bg-muted/40">
                                <td class="px-4 py-3 text-center">
                                    <button class="font-medium text-foreground hover:text-primary hover:underline"
                                        @click="viewReservation(reservation)">
                                        {{ reservation.customer }}
                                    </button>
                                </td>
                                <td class="px-4 py-3 text-muted-foreground text-center">{{ reservation.phone }}</td>
                                <td class="px-4 py-3 text-center">{{ reservation.travellers_number }}</td>
                                <td class="px-4 py-3 text-muted-foreground text-center">{{
                                    t(`reservations.room_type.${reservation.room_type}`) }}</td>
                                <td class="px-4 py-3 text-muted-foreground text-center">{{ reservation.wilaya }}</td>
                                <td class="px-4 py-3 text-center">
                                    <Badge :class="statusBadgeClass(reservation.status)" class="gap-1 border">
                                        <component :is="statusIcon(reservation.status)" class="h-3 w-3" />
                                        {{ t(`reservations.status.${reservation.status}`) }}
                                    </Badge>
                                </td>
                                <td class="px-4 py-3 text-center font-medium">
                                    {{ reservation.total_price.toLocaleString() }} <span
                                        class="text-xs font-normal text-muted-foreground">DZD</span>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <DropdownMenu>
                                        <DropdownMenuTrigger as-child>
                                            <Button variant="ghost" size="icon" class="h-8 w-8">
                                                <MoreHorizontal class="h-4 w-4" />
                                            </Button>
                                        </DropdownMenuTrigger>
                                        <DropdownMenuContent align="end" class="w-40">
                                            <DropdownMenuItem @click="viewReservation(reservation)">
                                                <Eye class="mr-2 h-4 w-4" />
                                                {{ t('reservations.index.view') }}
                                            </DropdownMenuItem>
                                            <DropdownMenuItem as-child>
                                                <Link :href="reservationsEdit([offerVariant.id, reservation.id]).url">
                                                    <Pencil class="mr-2 h-4 w-4" />
                                                    {{ t('common.edit') }}
                                                </Link>
                                            </DropdownMenuItem>
                                            <DropdownMenuItem class="text-destructive focus:text-destructive"
                                                @click="confirmDelete(reservation)">
                                                <Trash2 class="mr-2 h-4 w-4" />
                                                {{ t('common.delete') }}
                                            </DropdownMenuItem>
                                        </DropdownMenuContent>
                                    </DropdownMenu>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Pagination -->
            <div v-if="reservations.last_page > 1" class="flex items-center justify-center gap-2">
                <Button variant="outline" size="sm" :disabled="reservations.current_page === 1" as-child>
                    <Link
                        :href="reservationsIndex(offerVariant.id, { query: { page: String(reservations.current_page - 1), ...(activeFilter ? { status: activeFilter } : {}) } }).url">
                        <ChevronLeft class="mr-1 h-4 w-4" />
                        {{ t('common.previous') }}
                    </Link>
                </Button>

                <div class="flex items-center gap-1">
                    <Button v-for="page in reservations.last_page" :key="page" variant="outline" size="sm"
                        :class="{ 'bg-primary text-primary-foreground hover:bg-primary/90': page === reservations.current_page }"
                        as-child>
                        <Link
                            :href="reservationsIndex(offerVariant.id, { query: { page: String(page), ...(activeFilter ? { status: activeFilter } : {}) } }).url">
                            {{ page }}
                        </Link>
                    </Button>
                </div>

                <Button variant="outline" size="sm" :disabled="reservations.current_page === reservations.last_page"
                    as-child>
                    <Link
                        :href="reservationsIndex(offerVariant.id, { query: { page: String(reservations.current_page + 1), ...(activeFilter ? { status: activeFilter } : {}) } }).url">
                        {{ t('common.next') }}
                        <ChevronRight class="ml-1 h-4 w-4" />
                    </Link>
                </Button>
            </div>
        </template>
    </div>

    <!-- Delete Dialog -->
    <Dialog v-model:open="showDeleteDialog">
        <DialogContent>
            <DialogHeader>
                <DialogTitle>{{ t('reservations.index.delete_title') }}</DialogTitle>
                <DialogDescription>
                    {{ t('reservations.index.delete_confirmation', { customer: reservationToDelete?.customer }) }}
                </DialogDescription>
            </DialogHeader>
            <DialogFooter>
                <Button variant="outline" @click="showDeleteDialog = false">{{ t('common.cancel') }}</Button>
                <Button variant="destructive" @click="deleteReservation(offerVariant.id)">{{ t('common.delete')
                    }}</Button>
            </DialogFooter>
        </DialogContent>
    </Dialog>

    <!-- View Reservation Sheet -->
    <Sheet v-model:open="showViewSheet">
        <SheetContent class="flex flex-col gap-0 overflow-y-auto sm:max-w-md">
            <SheetHeader class="space-y-2 pb-4">
                <SheetTitle>{{ t('reservations.index.reservation_details') }}</SheetTitle>
                <SheetDescription>
                    {{ t('reservations.index.view_details') }}
                </SheetDescription>
            </SheetHeader>

            <div v-if="selectedReservation" class="flex flex-col gap-6">
                <!-- Customer Info -->
                <div class="rounded-lg border border-border bg-muted/30 p-4">
                    <div class="flex items-center gap-3">
                        <div class="flex h-10 w-10 items-center justify-center rounded-full bg-primary/10">
                            <Users class="h-5 w-5 text-primary" />
                        </div>
                        <div>
                            <p class="text-lg font-semibold">{{ selectedReservation.customer }}</p>
                            <p class="text-sm text-muted-foreground">{{ selectedReservation.phone }} · {{
                                selectedReservation.wilaya }}</p>
                        </div>
                    </div>
                </div>

                <!-- Details Grid -->
                <div class="grid grid-cols-2 gap-3">
                    <div class="rounded-lg border border-border p-3">
                        <p class="text-xs text-muted-foreground">{{ t('reservations.index.travellers') }}</p>
                        <p class="text-lg font-semibold">{{ selectedReservation.travellers_number }}</p>
                    </div>
                    <div class="rounded-lg border border-border p-3">
                        <p class="text-xs text-muted-foreground">{{ t('reservations.index.room_type') }}</p>
                        <p class="text-lg font-semibold">{{ t(`reservations.room_type.${selectedReservation.room_type}`)
                            }}
                        </p>
                    </div>
                    <div class="rounded-lg border border-border p-3">
                        <p class="text-xs text-muted-foreground">{{ t('reservations.index.status') }}</p>
                        <Badge :class="statusBadgeClass(selectedReservation.status)" class="mt-1 gap-1 border">
                            <component :is="statusIcon(selectedReservation.status)" class="h-3 w-3" />
                            {{ t(`reservations.status.${selectedReservation.status}`) }}
                        </Badge>
                    </div>
                    <div class="rounded-lg border border-border p-3">
                        <p class="text-xs text-muted-foreground">{{ t('reservations.index.include_feeding') }}</p>
                        <p class="text-lg font-semibold">{{ selectedReservation.include_feeding ? t('common.yes') :
                            t('common.no') }}</p>
                    </div>
                </div>

                <!-- Pricing Breakdown -->
                <div v-if="priceBreakdown" class="rounded-lg border border-border p-4">
                    <h4 class="mb-3 text-sm font-semibold">{{ t('reservations.index.pricing_breakdown') }}</h4>
                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between">
                            <span class="text-muted-foreground">{{ t('reservations.index.room_price') }}</span>
                            <span class="font-medium">{{ priceBreakdown.roomPricePerPerson.toLocaleString() }} DZD × {{
                                selectedReservation.travellers_number }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-muted-foreground">{{ t('reservations.index.room_price') }} {{
                                t('reservations.index.total') }}</span>
                            <span class="font-medium">{{ priceBreakdown.roomTotal.toLocaleString() }} DZD</span>
                        </div>
                        <template v-if="selectedReservation.include_feeding">
                            <div class="flex justify-between">
                                <span class="text-muted-foreground">{{ t('reservations.index.feeding_price') }}</span>
                                <span class="font-medium">{{ priceBreakdown.feedingPerPerson.toLocaleString() }} DZD ×
                                    {{ selectedReservation.travellers_number }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-muted-foreground">{{ t('reservations.index.feeding_price') }} {{
                                    t('reservations.index.total') }}</span>
                                <span class="font-medium">{{ priceBreakdown.feedingTotal.toLocaleString() }} DZD</span>
                            </div>
                        </template>
                        <Separator />
                        <div class="flex justify-between text-base font-bold">
                            <span>{{ t('reservations.index.price') }}</span>
                            <span class="text-primary">{{ priceBreakdown.total.toLocaleString() }} DZD</span>
                        </div>
                    </div>
                </div>

                <!-- Actions -->
                <div class="mt-auto flex gap-2 pt-2">
                    <Button variant="outline" class="flex-1" as-child>
                        <Link :href="reservationsEdit([offerVariant.id, selectedReservation.id]).url">
                            <Pencil class="mr-2 h-4 w-4" />
                            {{ t('common.edit') }}
                        </Link>
                    </Button>
                    <Button variant="destructive" class="flex-1"
                        @click="confirmDelete(selectedReservation); showViewSheet = false">
                        <Trash2 class="mr-2 h-4 w-4" />
                        {{ t('common.delete') }}
                    </Button>
                </div>
            </div>
        </SheetContent>
    </Sheet>
</template>
