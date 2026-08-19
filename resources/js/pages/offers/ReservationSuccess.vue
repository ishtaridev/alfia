<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import {
    Check,
    Calendar,
    MapPin,
    Wallet,
    ArrowLeft,
    User,
    Phone,
    Users,
    BedDouble,
    Utensils,
} from '@lucide/vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent } from '@/components/ui/card';
import { Separator } from '@/components/ui/separator';
import { useLocale } from '@/composables/useLocale';
import { formatDate } from '@/lib/formatDate';

const { t } = useLocale();

type Reservation = {
    id: number;
    customer: string;
    phone: string;
    travellers_number: number;
    wilaya: string;
    room_type: string;
    include_feeding: boolean;
    total_price: number;
    variant_id: number;
    status: string;
};

type Offer = {
    id: number;
    title: string;
    code: string;
    description: string | null;
    images: Array<{ id: number; url: string; order: number }>;
};

type Variant = {
    id: number;
    travel_date: string;
    airport: string;
};

defineProps<{
    offer: Offer;
    reservation: Reservation;
    variant: Variant;
}>();

const formatCurrency = (amount: number): string => amount.toLocaleString();
</script>

<template>
    <Head :title="t('public_reserve.success_title')" />

    <div class="mx-auto w-full max-w-2xl">
        <!-- Success Header -->
        <div class="mb-8 text-center">
            <div
                class="mx-auto mb-4 flex h-20 w-20 items-center justify-center rounded-full bg-emerald-100 text-emerald-600 dark:bg-emerald-900/30 dark:text-emerald-400"
            >
                <Check class="h-10 w-10" />
            </div>
            <h1 class="text-2xl font-bold tracking-tight sm:text-3xl">
                {{ t('public_reserve.success_heading') }}
            </h1>
            <p class="mt-2 text-muted-foreground">
                {{ t('public_reserve.success_description') }}
            </p>
        </div>

        <!-- Offer Card -->
        <Card class="mb-6 overflow-hidden border-0 shadow-xl">
            <div class="relative h-40 overflow-hidden">
                <img
                    v-if="offer.images.length > 0"
                    :src="offer.images[0].url"
                    :alt="offer.title"
                    class="h-full w-full object-cover"
                />
                <div
                    v-else
                    class="flex h-full w-full items-center justify-center bg-muted"
                >
                    <BedDouble class="h-12 w-12 text-muted-foreground/40" />
                </div>
                <div
                    class="absolute inset-0 bg-gradient-to-t from-black/60 via-black/20 to-transparent"
                />
                <div class="absolute start-0 bottom-0 p-5">
                    <h3 class="text-lg font-bold text-white">
                        {{ offer.title }}
                    </h3>
                    <p class="text-xs text-white/80">{{ offer.code }}</p>
                </div>
            </div>

            <CardContent class="p-6">
                <h2 class="mb-4 flex items-center gap-2 text-lg font-semibold">
                    <Wallet class="h-5 w-5 text-primary" />
                    {{ t('public_reserve.reservation_summary') }}
                </h2>

                <div class="mb-5 space-y-3 rounded-xl border p-4">
                    <div class="flex items-center gap-3">
                        <Calendar class="h-5 w-5 text-primary" />
                        <div>
                            <p class="text-xs text-muted-foreground">
                                {{ t('reservations.index.travel_date') }}
                            </p>
                            <p class="font-medium">
                                {{ formatDate(variant.travel_date) }}
                            </p>
                        </div>
                    </div>
                    <div class="flex items-center gap-3">
                        <MapPin class="h-5 w-5 text-primary" />
                        <div>
                            <p class="text-xs text-muted-foreground">
                                {{ t('reservations.index.airport') }}
                            </p>
                            <p class="font-medium">{{ variant.airport }}</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-3">
                        <BedDouble class="h-5 w-5 text-primary" />
                        <div>
                            <p class="text-xs text-muted-foreground">
                                {{ t('reservations.index.room_type') }}
                            </p>
                            <p class="font-medium">
                                {{
                                    t(
                                        'reservations.room_type.' +
                                            reservation.room_type,
                                    )
                                }}
                            </p>
                        </div>
                    </div>
                    <div
                        v-if="reservation.include_feeding"
                        class="flex items-center gap-3"
                    >
                        <Utensils class="h-5 w-5 text-primary" />
                        <div>
                            <p class="text-xs text-muted-foreground">
                                {{ t('reservations.index.include_feeding') }}
                            </p>
                            <p class="font-medium">{{ t('common.yes') }}</p>
                        </div>
                    </div>
                </div>

                <div class="mb-5 grid gap-4 sm:grid-cols-2">
                    <div
                        class="flex items-center gap-3 rounded-xl bg-muted/50 p-4"
                    >
                        <User class="h-5 w-5 text-primary" />
                        <div>
                            <p class="text-xs text-muted-foreground">
                                {{ t('reservations.index.customer') }}
                            </p>
                            <p class="font-medium">
                                {{ reservation.customer }}
                            </p>
                        </div>
                    </div>
                    <div
                        class="flex items-center gap-3 rounded-xl bg-muted/50 p-4"
                    >
                        <Phone class="h-5 w-5 text-primary" />
                        <div>
                            <p class="text-xs text-muted-foreground">
                                {{ t('reservations.index.phone') }}
                            </p>
                            <p class="font-medium" dir="ltr">
                                {{ reservation.phone }}
                            </p>
                        </div>
                    </div>
                    <div
                        class="flex items-center gap-3 rounded-xl bg-muted/50 p-4"
                    >
                        <MapPin class="h-5 w-5 text-primary" />
                        <div>
                            <p class="text-xs text-muted-foreground">
                                {{ t('reservations.index.wilaya') }}
                            </p>
                            <p class="font-medium">{{ reservation.wilaya }}</p>
                        </div>
                    </div>
                    <div
                        class="flex items-center gap-3 rounded-xl bg-muted/50 p-4"
                    >
                        <Users class="h-5 w-5 text-primary" />
                        <div>
                            <p class="text-xs text-muted-foreground">
                                {{ t('reservations.index.travellers') }}
                            </p>
                            <p class="font-medium">
                                {{ reservation.travellers_number }}
                            </p>
                        </div>
                    </div>
                </div>

                <Separator class="my-4" />

                <div
                    class="flex items-end justify-between rounded-2xl bg-primary/5 p-5"
                >
                    <span class="text-sm font-medium">{{
                        t('public_reserve.total')
                    }}</span>
                    <span class="text-3xl font-bold text-primary">
                        {{ formatCurrency(reservation.total_price) }}
                        <span class="text-sm font-normal text-muted-foreground"
                            >DZD</span
                        >
                    </span>
                </div>
            </CardContent>
        </Card>

        <div class="flex justify-center">
            <Button
                variant="outline"
                size="lg"
                class="rounded-xl px-8"
                as-child
            >
                <Link :href="`/offers/${offer.code}/reserve`">
                    <ArrowLeft class="me-2 h-4 w-4" />
                    {{ t('public_reserve.back_to_offer') }}
                </Link>
            </Button>
        </div>
    </div>
</template>
