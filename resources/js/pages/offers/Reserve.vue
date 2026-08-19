<script setup lang="ts">
import { Head, router } from '@inertiajs/vue3';
import {
    Send,
    Calendar,
    MapPin,
    Wallet,
    Users,
    User,
    Phone,
    Utensils,
    BedDouble,
    ArrowRight,
    ShieldCheck,
    Clock,
    Headphones,
    AlertCircle,
} from '@lucide/vue';
import { ref, computed, watch, nextTick } from 'vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent } from '@/components/ui/card';
import { Checkbox } from '@/components/ui/checkbox';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import { Separator } from '@/components/ui/separator';
import { useLocale } from '@/composables/useLocale';
import { formatDate } from '@/lib/formatDate';

const { t } = useLocale();

type Pricing = {
    collectif_room: number;
    room_of_four: number;
    room_of_three: number;
    room_of_two: number;
    feeding: number;
};

type Variant = {
    id: number;
    travel_date: string;
    airport: string;
    pricing: Pricing | null;
};

type Offer = {
    id: number;
    title: string;
    code: string;
    description: string | null;
    images: Array<{ id: number; url: string; order: number }>;
    variants: Variant[];
};

const props = defineProps<{
    offer: Offer;
}>();

const selectedVariantId = ref(
    props.offer.variants.length > 0
        ? props.offer.variants[0].id.toString()
        : '',
);
const customer = ref('');
const phone = ref('');
const travellers_number = ref(1);
const wilaya = ref('');
const room_type = ref('collectif');
const include_feeding = ref(false);

const errors = ref<Record<string, string>>({});
const processing = ref(false);
const pricePulse = ref(false);

const selectedVariant = computed(() => {
    return (
        props.offer.variants.find(
            (v) => v.id.toString() === selectedVariantId.value,
        ) || null
    );
});

const roomPricing = computed(() => {
    const variant = selectedVariant.value;

    if (!variant || !variant.pricing) {
        return null;
    }

    return {
        collectif: variant.pricing.collectif_room,
        room_of_four: variant.pricing.room_of_four,
        room_of_three: variant.pricing.room_of_three,
        room_of_two: variant.pricing.room_of_two,
        feeding: variant.pricing.feeding,
    };
});

const currentRoomPrice = computed(() => {
    if (!roomPricing.value) {
        return 0;
    }

    switch (room_type.value) {
        case 'collectif':
            return roomPricing.value.collectif;
        case 'room_of_four':
            return roomPricing.value.room_of_four;
        case 'room_of_three':
            return roomPricing.value.room_of_three;
        case 'room_of_two':
            return roomPricing.value.room_of_two;
        default:
            return 0;
    }
});

const feedingPrice = computed(() => {
    return include_feeding.value && roomPricing.value
        ? roomPricing.value.feeding
        : 0;
});

const roomTotal = computed(
    () => currentRoomPrice.value * travellers_number.value,
);
const feedingTotal = computed(
    () => feedingPrice.value * travellers_number.value,
);
const calculatePrice = computed(() => roomTotal.value + feedingTotal.value);

watch(
    [
        () => room_type.value,
        () => include_feeding.value,
        () => travellers_number.value,
        () => selectedVariantId.value,
    ],
    () => {
        pricePulse.value = true;
        nextTick(() => {
            setTimeout(() => {
                pricePulse.value = false;
            }, 250);
        });
    },
);

const roomOptions = [
    {
        value: 'collectif',
        icon: Users,
        occupancyKey: 'reservations.room_type.collectif',
    },
    {
        value: 'room_of_four',
        icon: Users,
        occupancyKey: 'reservations.room_type.room_of_four',
    },
    {
        value: 'room_of_three',
        icon: User,
        occupancyKey: 'reservations.room_type.room_of_three',
    },
    {
        value: 'room_of_two',
        icon: User,
        occupancyKey: 'reservations.room_type.room_of_two',
    },
];

const getRoomPrice = (type: string): number => {
    if (!roomPricing.value) {
        return 0;
    }

    switch (type) {
        case 'collectif':
            return roomPricing.value.collectif;
        case 'room_of_four':
            return roomPricing.value.room_of_four;
        case 'room_of_three':
            return roomPricing.value.room_of_three;
        case 'room_of_two':
            return roomPricing.value.room_of_two;
        default:
            return 0;
    }
};

const formatCurrency = (amount: number): string => {
    return amount.toLocaleString();
};

const submit = () => {
    processing.value = true;
    errors.value = {};

    router.post(
        `/offers/${props.offer.code}/reserve`,
        {
            variant_id: parseInt(selectedVariantId.value),
            customer: customer.value,
            phone: phone.value,
            travellers_number: travellers_number.value,
            wilaya: wilaya.value,
            room_type: room_type.value,
            include_feeding: include_feeding.value,
        },
        {
            onFinish: () => {
                processing.value = false;
            },
            onError: (errs) => {
                errors.value = errs;
            },
        },
    );
};
</script>

<template>
    <Head :title="offer.title" />

    <div class="mx-auto w-full max-w-5xl">
        <!-- Page Header -->
        <div class="mb-8 text-center lg:mb-10">
            <div
                class="mb-3 inline-flex items-center gap-2 rounded-full bg-primary/10 px-4 py-1.5 text-sm font-medium text-primary"
            >
                <Calendar class="h-4 w-4" />
                <span>{{ t('public_reserve.page_title') }}</span>
            </div>
            <h1 class="text-2xl font-bold tracking-tight sm:text-3xl">
                {{ offer.title }}
            </h1>
            <p class="mt-1 text-sm text-muted-foreground">{{ offer.code }}</p>
        </div>

        <!-- Empty State -->
        <Card v-if="offer.variants.length === 0" class="overflow-hidden">
            <CardContent
                class="flex flex-col items-center justify-center gap-4 py-16 text-center"
            >
                <div
                    class="flex h-16 w-16 items-center justify-center rounded-full bg-muted"
                >
                    <AlertCircle class="h-8 w-8 text-muted-foreground" />
                </div>
                <div class="max-w-sm space-y-1">
                    <h3 class="text-lg font-semibold">
                        {{ t('public_reserve.no_variants_title') }}
                    </h3>
                    <p class="text-sm text-muted-foreground">
                        {{ t('public_reserve.no_variants_description') }}
                    </p>
                </div>
            </CardContent>
        </Card>

        <!-- Booking Layout -->
        <div v-else class="grid gap-6 lg:grid-cols-12 lg:gap-8">
            <!-- Main Form -->
            <div class="order-2 lg:order-1 lg:col-span-7">
                <Card class="overflow-hidden border-0 shadow-xl">
                    <CardContent class="p-0">
                        <!-- Trip Details Section -->
                        <div class="border-b bg-muted/30 p-6 sm:p-8">
                            <div class="mb-5 flex items-center gap-3">
                                <div
                                    class="flex h-10 w-10 items-center justify-center rounded-xl bg-primary/10"
                                >
                                    <Calendar class="h-5 w-5 text-primary" />
                                </div>
                                <div>
                                    <h2 class="text-lg font-semibold">
                                        {{ t('public_reserve.trip_details') }}
                                    </h2>
                                    <p class="text-sm text-muted-foreground">
                                        {{
                                            t('public_reserve.form_description')
                                        }}
                                    </p>
                                </div>
                            </div>

                            <div class="space-y-4">
                                <div class="space-y-2">
                                    <Label
                                        for="variant"
                                        class="text-sm font-medium"
                                        >{{
                                            t('public_reserve.variant')
                                        }}</Label
                                    >
                                    <Select v-model="selectedVariantId">
                                        <SelectTrigger
                                            class="h-12 w-full rounded-xl border-border bg-card px-4"
                                        >
                                            <SelectValue
                                                :placeholder="
                                                    t(
                                                        'public_reserve.select_variant',
                                                    )
                                                "
                                            />
                                        </SelectTrigger>
                                        <SelectContent>
                                            <SelectItem
                                                v-for="variant in offer.variants"
                                                :key="variant.id"
                                                :value="variant.id.toString()"
                                            >
                                                <div
                                                    class="flex items-center gap-3"
                                                >
                                                    <span class="font-medium">{{
                                                        formatDate(
                                                            variant.travel_date,
                                                        )
                                                    }}</span>
                                                    <span
                                                        class="text-muted-foreground"
                                                        >—</span
                                                    >
                                                    <span
                                                        class="flex items-center gap-1 text-muted-foreground"
                                                    >
                                                        <MapPin
                                                            class="h-3.5 w-3.5"
                                                        />
                                                        {{ variant.airport }}
                                                    </span>
                                                </div>
                                            </SelectItem>
                                        </SelectContent>
                                    </Select>
                                    <InputError :message="errors.variant_id" />
                                </div>

                                <div
                                    v-if="selectedVariant"
                                    class="flex flex-wrap items-center gap-3"
                                >
                                    <div
                                        class="inline-flex items-center gap-2 rounded-full bg-primary/10 px-4 py-2 text-sm font-medium text-primary"
                                    >
                                        <Calendar class="h-4 w-4" />
                                        {{
                                            formatDate(
                                                selectedVariant.travel_date,
                                            )
                                        }}
                                    </div>
                                    <div
                                        class="inline-flex items-center gap-2 rounded-full border px-4 py-2 text-sm font-medium"
                                    >
                                        <MapPin class="h-4 w-4 text-primary" />
                                        {{ selectedVariant.airport }}
                                    </div>
                                </div>
                            </div>
                        </div>

                        <form @submit.prevent="submit" class="p-6 sm:p-8">
                            <!-- Room Type -->
                            <div class="mb-8 space-y-4">
                                <div class="flex items-center gap-3">
                                    <div
                                        class="flex h-10 w-10 items-center justify-center rounded-xl bg-primary/10"
                                    >
                                        <BedDouble
                                            class="h-5 w-5 text-primary"
                                        />
                                    </div>
                                    <div>
                                        <h2 class="text-lg font-semibold">
                                            {{
                                                t(
                                                    'reservations.index.room_type',
                                                )
                                            }}
                                        </h2>
                                        <p
                                            class="text-sm text-muted-foreground"
                                        >
                                            {{
                                                t(
                                                    'public_reserve.room_type_label',
                                                )
                                            }}
                                        </p>
                                    </div>
                                </div>

                                <div
                                    class="grid grid-cols-2 gap-3 sm:grid-cols-4"
                                >
                                    <button
                                        v-for="option in roomOptions"
                                        :key="option.value"
                                        type="button"
                                        class="group relative flex flex-col items-center gap-2 rounded-xl border-2 p-4 transition-all duration-200 focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 focus-visible:outline-none"
                                        :class="
                                            room_type === option.value
                                                ? 'border-primary bg-primary/5 text-primary'
                                                : 'border-border bg-card text-card-foreground hover:border-primary/30 hover:bg-muted/50'
                                        "
                                        @click="room_type = option.value"
                                    >
                                        <component
                                            :is="option.icon"
                                            class="h-6 w-6 transition-transform duration-200 group-hover:scale-110"
                                        />
                                        <span
                                            class="text-center text-xs leading-tight font-medium"
                                            >{{ t(option.occupancyKey) }}</span
                                        >
                                        <span
                                            v-if="selectedVariant?.pricing"
                                            class="text-[10px] opacity-70"
                                        >
                                            {{
                                                formatCurrency(
                                                    getRoomPrice(option.value),
                                                )
                                            }}
                                            DZD
                                        </span>
                                        <div
                                            v-if="room_type === option.value"
                                            class="absolute -end-2 -top-2 flex h-5 w-5 items-center justify-center rounded-full bg-primary text-primary-foreground shadow-sm"
                                        >
                                            <ArrowRight class="h-3 w-3" />
                                        </div>
                                    </button>
                                </div>
                                <InputError :message="errors.room_type" />
                            </div>

                            <!-- Travellers & Feeding -->
                            <div class="mb-8 grid gap-6 sm:grid-cols-2">
                                <div class="space-y-2">
                                    <Label
                                        for="travellers_number"
                                        class="text-sm font-medium"
                                        >{{
                                            t('reservations.index.travellers')
                                        }}</Label
                                    >
                                    <div class="relative">
                                        <Users
                                            class="absolute start-3 top-1/2 h-4 w-4 -translate-y-1/2 text-muted-foreground"
                                        />
                                        <Input
                                            id="travellers_number"
                                            v-model.number="travellers_number"
                                            type="number"
                                            min="1"
                                            required
                                            class="h-12 rounded-xl ps-10"
                                        />
                                    </div>
                                    <InputError
                                        :message="errors.travellers_number"
                                    />
                                </div>

                                <div class="space-y-2">
                                    <Label class="text-sm font-medium">{{
                                        t('reservations.index.include_feeding')
                                    }}</Label>
                                    <button
                                        type="button"
                                        class="flex h-12 w-full items-center justify-between rounded-xl border-2 px-4 transition-all duration-200"
                                        :class="
                                            include_feeding
                                                ? 'border-primary bg-primary/5 text-primary'
                                                : 'border-border bg-card hover:border-primary/30 hover:bg-muted/50'
                                        "
                                        @click="
                                            include_feeding = !include_feeding
                                        "
                                    >
                                        <span
                                            class="flex items-center gap-2 text-sm font-medium"
                                        >
                                            <Utensils class="h-4 w-4" />
                                            {{
                                                t(
                                                    'reservations.index.include_feeding',
                                                )
                                            }}
                                        </span>
                                        <Checkbox
                                            id="include_feeding"
                                            v-model="include_feeding"
                                            class="pointer-events-none bg-white"
                                        />
                                    </button>
                                    <InputError
                                        :message="errors.include_feeding"
                                    />
                                </div>
                            </div>

                            <!-- Personal Details -->
                            <div class="mb-8 space-y-4">
                                <div class="flex items-center gap-3">
                                    <div
                                        class="flex h-10 w-10 items-center justify-center rounded-xl bg-primary/10"
                                    >
                                        <User class="h-5 w-5 text-primary" />
                                    </div>
                                    <div>
                                        <h2 class="text-lg font-semibold">
                                            {{
                                                t('public_reserve.your_details')
                                            }}
                                        </h2>
                                        <p
                                            class="text-sm text-muted-foreground"
                                        >
                                            {{
                                                t(
                                                    'public_reserve.form_description',
                                                )
                                            }}
                                        </p>
                                    </div>
                                </div>

                                <div class="grid gap-5">
                                    <div class="space-y-2">
                                        <Label
                                            for="customer"
                                            class="text-sm font-medium"
                                            >{{
                                                t('reservations.index.customer')
                                            }}</Label
                                        >
                                        <div class="relative">
                                            <User
                                                class="absolute start-3 top-1/2 h-4 w-4 -translate-y-1/2 text-muted-foreground"
                                            />
                                            <Input
                                                id="customer"
                                                v-model="customer"
                                                :placeholder="
                                                    t(
                                                        'public_reserve.customer_placeholder',
                                                    )
                                                "
                                                required
                                                class="h-12 rounded-xl ps-10"
                                            />
                                        </div>
                                        <InputError
                                            :message="errors.customer"
                                        />
                                    </div>

                                    <div class="grid gap-5 sm:grid-cols-2">
                                        <div class="space-y-2">
                                            <Label
                                                for="phone"
                                                class="text-sm font-medium"
                                                >{{
                                                    t(
                                                        'reservations.index.phone',
                                                    )
                                                }}</Label
                                            >
                                            <div class="relative">
                                                <Phone
                                                    class="absolute start-3 top-1/2 h-4 w-4 -translate-y-1/2 text-muted-foreground"
                                                />
                                                <Input
                                                    id="phone"
                                                    v-model="phone"
                                                    :placeholder="
                                                        t(
                                                            'public_reserve.phone_placeholder',
                                                        )
                                                    "
                                                    required
                                                    class="h-12 rounded-xl ps-10"
                                                />
                                            </div>
                                            <InputError
                                                :message="errors.phone"
                                            />
                                        </div>

                                        <div class="space-y-2">
                                            <Label
                                                for="wilaya"
                                                class="text-sm font-medium"
                                                >{{
                                                    t(
                                                        'reservations.index.wilaya',
                                                    )
                                                }}</Label
                                            >
                                            <Select v-model="wilaya">
                                                <SelectTrigger
                                                    class="h-12 w-full rounded-xl border-border bg-card px-4"
                                                >
                                                    <SelectValue
                                                        :placeholder="
                                                            t(
                                                                'public_reserve.select_wilaya',
                                                            )
                                                        "
                                                    />
                                                </SelectTrigger>
                                                <SelectContent>
                                                    <SelectItem
                                                        v-for="w in $page.props
                                                            .wilayas"
                                                        :key="w"
                                                        :value="w"
                                                        >{{ w }}</SelectItem
                                                    >
                                                </SelectContent>
                                            </Select>
                                            <InputError
                                                :message="errors.wilaya"
                                            />
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Mobile Price Summary -->
                            <div class="mb-6 lg:hidden">
                                <div
                                    class="rounded-2xl border-2 border-primary/20 bg-primary/5 p-5"
                                >
                                    <div
                                        class="mb-4 flex items-center justify-between"
                                    >
                                        <span
                                            class="text-sm font-medium text-muted-foreground"
                                            >{{
                                                t('public_reserve.total')
                                            }}</span
                                        >
                                        <span
                                            class="text-3xl font-bold text-primary transition-transform duration-200"
                                            :class="{ 'scale-105': pricePulse }"
                                        >
                                            {{ formatCurrency(calculatePrice) }}
                                            <span
                                                class="text-sm font-normal text-muted-foreground"
                                                >DZD</span
                                            >
                                        </span>
                                    </div>
                                    <Button
                                        type="submit"
                                        size="lg"
                                        class="w-full rounded-xl"
                                        :disabled="processing"
                                    >
                                        <Send
                                            v-if="!processing"
                                            class="me-2 h-4 w-4"
                                        />
                                        {{
                                            processing
                                                ? t('common.loading')
                                                : t('public_reserve.submit')
                                        }}
                                    </Button>
                                </div>
                            </div>

                            <!-- Desktop Submit -->
                            <div class="hidden lg:block">
                                <Button
                                    type="submit"
                                    size="lg"
                                    class="w-full rounded-xl py-6 text-base"
                                    :disabled="processing"
                                >
                                    <Send
                                        v-if="!processing"
                                        class="me-2 h-5 w-5"
                                    />
                                    {{
                                        processing
                                            ? t('common.loading')
                                            : t('public_reserve.submit')
                                    }}
                                </Button>
                            </div>
                        </form>
                    </CardContent>
                </Card>
            </div>

            <!-- Sticky Summary -->
            <div class="order-1 lg:order-2 lg:col-span-5">
                <div class="sticky top-6 space-y-6">
                    <!-- Offer Preview Card -->
                    <Card class="overflow-hidden border-0 shadow-xl">
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
                                <BedDouble
                                    class="h-12 w-12 text-muted-foreground/40"
                                />
                            </div>
                            <div
                                class="absolute inset-0 bg-gradient-to-t from-black/60 via-black/20 to-transparent"
                            />
                            <div class="absolute start-0 bottom-0 p-5">
                                <h3 class="text-lg font-bold text-white">
                                    {{ offer.title }}
                                </h3>
                                <p class="text-xs text-white/80">
                                    {{ offer.code }}
                                </p>
                            </div>
                        </div>
                        <CardContent class="p-5">
                            <div v-if="selectedVariant" class="space-y-3">
                                <div class="flex items-center gap-3">
                                    <div
                                        class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-primary/10"
                                    >
                                        <Calendar
                                            class="h-5 w-5 text-primary"
                                        />
                                    </div>
                                    <div>
                                        <p
                                            class="text-xs text-muted-foreground"
                                        >
                                            {{
                                                t(
                                                    'reservations.index.travel_date',
                                                )
                                            }}
                                        </p>
                                        <p class="font-medium">
                                            {{
                                                formatDate(
                                                    selectedVariant.travel_date,
                                                )
                                            }}
                                        </p>
                                    </div>
                                </div>
                                <div class="flex items-center gap-3">
                                    <div
                                        class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-primary/10"
                                    >
                                        <MapPin class="h-5 w-5 text-primary" />
                                    </div>
                                    <div>
                                        <p
                                            class="text-xs text-muted-foreground"
                                        >
                                            {{
                                                t('reservations.index.airport')
                                            }}
                                        </p>
                                        <p class="font-medium">
                                            {{ selectedVariant.airport }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </CardContent>
                    </Card>

                    <!-- Pricing Breakdown -->
                    <Card class="overflow-hidden border-0 shadow-xl">
                        <CardContent class="p-5">
                            <h3
                                class="mb-4 flex items-center gap-2 text-base font-semibold"
                            >
                                <Wallet class="h-5 w-5 text-primary" />
                                {{ t('public_reserve.pricing_breakdown') }}
                            </h3>

                            <div
                                v-if="selectedVariant?.pricing"
                                class="space-y-3"
                            >
                                <div
                                    class="flex items-center justify-between text-sm"
                                >
                                    <span class="text-muted-foreground">
                                        {{
                                            t(
                                                'reservations.room_type.' +
                                                    room_type,
                                            )
                                        }}
                                        <span class="text-xs"
                                            >× {{ travellers_number }}</span
                                        >
                                    </span>
                                    <span class="font-medium tabular-nums"
                                        >{{
                                            formatCurrency(roomTotal)
                                        }}
                                        DZD</span
                                    >
                                </div>
                                <div
                                    v-if="include_feeding"
                                    class="flex items-center justify-between text-sm"
                                >
                                    <span class="text-muted-foreground">
                                        {{ t('reservations.index.feeding') }}
                                        <span class="text-xs"
                                            >× {{ travellers_number }}</span
                                        >
                                    </span>
                                    <span class="font-medium tabular-nums"
                                        >{{
                                            formatCurrency(feedingTotal)
                                        }}
                                        DZD</span
                                    >
                                </div>

                                <Separator />

                                <div class="flex items-end justify-between">
                                    <span class="text-sm font-medium">{{
                                        t('public_reserve.total')
                                    }}</span>
                                    <span
                                        class="text-3xl font-bold text-primary transition-transform duration-200"
                                        :class="{ 'scale-105': pricePulse }"
                                    >
                                        {{ formatCurrency(calculatePrice) }}
                                        <span
                                            class="text-sm font-normal text-muted-foreground"
                                            >DZD</span
                                        >
                                    </span>
                                </div>
                                <p class="text-xs text-muted-foreground">
                                    {{ t('public_reserve.per_person') }}:
                                    {{
                                        formatCurrency(
                                            currentRoomPrice + feedingPrice,
                                        )
                                    }}
                                    DZD
                                </p>
                            </div>

                            <div
                                v-else
                                class="py-6 text-center text-sm text-muted-foreground"
                            >
                                {{ t('public_reserve.select_variant') }}
                            </div>
                        </CardContent>
                    </Card>

                    <!-- Trust Signals -->
                    <div class="grid grid-cols-3 gap-3">
                        <div
                            class="flex flex-col items-center gap-2 rounded-xl bg-card/50 p-3 text-center text-xs text-muted-foreground"
                        >
                            <ShieldCheck class="h-5 w-5 text-primary" />
                            <span>{{ t('public_reserve.trust_secure') }}</span>
                        </div>
                        <div
                            class="flex flex-col items-center gap-2 rounded-xl bg-card/50 p-3 text-center text-xs text-muted-foreground"
                        >
                            <Clock class="h-5 w-5 text-primary" />
                            <span>{{ t('public_reserve.trust_instant') }}</span>
                        </div>
                        <div
                            class="flex flex-col items-center gap-2 rounded-xl bg-card/50 p-3 text-center text-xs text-muted-foreground"
                        >
                            <Headphones class="h-5 w-5 text-primary" />
                            <span>{{ t('public_reserve.trust_support') }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
