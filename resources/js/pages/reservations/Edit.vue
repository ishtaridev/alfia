<script setup lang="ts">
import { Head, router } from '@inertiajs/vue3';
import { ArrowLeft, Send, Calendar, MapPin, Wallet } from '@lucide/vue';
import { ref, computed } from 'vue';
import Heading from '@/components/Heading.vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Checkbox } from '@/components/ui/checkbox';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Separator } from '@/components/ui/separator';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { useLocale } from '@/composables/useLocale';
import { index as offersIndex } from '@/routes/offers';
import { index as reservationsIndex, update as reservationsUpdate } from '@/routes/offer-variants/reservations';

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

const props = defineProps<{
    offerVariant: OfferVariant;
    reservation: Reservation;
}>();

const customer = ref(props.reservation.customer);
const phone = ref(props.reservation.phone);
const travellers_number = ref(props.reservation.travellers_number);
const wilaya = ref(props.reservation.wilaya);
const room_type = ref(props.reservation.room_type);
const status = ref(props.reservation.status);
const include_feeding = ref(props.reservation.include_feeding);

const errors = ref<Record<string, string>>({});
const processing = ref(false);

const calculatePrice = computed(() => {
    const pricing = props.offerVariant.pricing;
    if (!pricing) {
        return 0;
    }

    const roomPrice = (() => {
        switch (room_type.value) {
            case 'collectif': return pricing.collectif_room;
            case 'room_of_four': return pricing.room_of_four;
            case 'room_of_three': return pricing.room_of_three;
            case 'room_of_two': return pricing.room_of_two;
            default: return 0;
        }
    })();

    let total = roomPrice * travellers_number.value;

    if (include_feeding.value) {
        total += pricing.feeding * travellers_number.value;
    }

    return total;
});

const submit = () => {
    processing.value = true;
    errors.value = {};

    router.put(reservationsUpdate([props.offerVariant.id, props.reservation.id]).url, {
        customer: customer.value,
        phone: phone.value,
        travellers_number: travellers_number.value,
        wilaya: wilaya.value,
        room_type: room_type.value,
        status: status.value,
        include_feeding: include_feeding.value,
    }, {
        onFinish: () => {
            processing.value = false;
        },
        onError: (errs) => {
            errors.value = errs;
        },
    });
};
</script>

<template>
    <Head :title="t('reservations.edit.title')" />

    <div class="flex h-full flex-1 flex-col gap-6 overflow-x-auto rounded-xl p-4">
        <div class="flex items-center justify-between">
            <Heading :title="t('reservations.edit.title')" :description="offerVariant.offer.title" />
            <Button variant="outline" size="sm" as-child>
                <a :href="reservationsIndex(offerVariant.id).url">
                    <ArrowLeft class="mr-2 h-4 w-4" />
                    {{ t('common.back') }}
                </a>
            </Button>
        </div>

        <div class="mx-auto w-full max-w-2xl space-y-6">
            <!-- Pricing Info Card -->
            <Card v-if="offerVariant.pricing" class="overflow-hidden">
                <CardHeader class="pb-3">
                    <CardTitle class="flex items-center gap-2 text-base">
                        <Wallet class="h-4 w-4 text-primary" />
                        {{ t('reservations.index.variant_details') }}
                    </CardTitle>
                </CardHeader>
                <CardContent class="space-y-4">
                    <div class="flex flex-wrap items-center gap-x-6 gap-y-2">
                        <div class="flex items-center gap-2">
                            <div class="flex h-7 w-7 items-center justify-center rounded-full bg-primary/10">
                                <Calendar class="h-3.5 w-3.5 text-primary" />
                            </div>
                            <span class="text-sm font-medium">{{ offerVariant.travel_date }}</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <div class="flex h-7 w-7 items-center justify-center rounded-full bg-primary/10">
                                <MapPin class="h-3.5 w-3.5 text-primary" />
                            </div>
                            <span class="text-sm text-muted-foreground">{{ offerVariant.airport }}</span>
                        </div>
                    </div>
                    <Separator />
                    <div class="grid grid-cols-2 gap-3 sm:grid-cols-3">
                        <div class="rounded-md bg-muted/50 px-3 py-2 text-center">
                            <p class="text-[10px] uppercase tracking-wider text-muted-foreground">{{ t('reservations.index.collectif_room') }}</p>
                            <p class="text-sm font-semibold">{{ offerVariant.pricing.collectif_room }} <span class="text-[10px] font-normal text-muted-foreground">DZD</span></p>
                        </div>
                        <div class="rounded-md bg-muted/50 px-3 py-2 text-center">
                            <p class="text-[10px] uppercase tracking-wider text-muted-foreground">{{ t('reservations.index.room_of_four') }}</p>
                            <p class="text-sm font-semibold">{{ offerVariant.pricing.room_of_four }} <span class="text-[10px] font-normal text-muted-foreground">DZD</span></p>
                        </div>
                        <div class="rounded-md bg-muted/50 px-3 py-2 text-center">
                            <p class="text-[10px] uppercase tracking-wider text-muted-foreground">{{ t('reservations.index.room_of_three') }}</p>
                            <p class="text-sm font-semibold">{{ offerVariant.pricing.room_of_three }} <span class="text-[10px] font-normal text-muted-foreground">DZD</span></p>
                        </div>
                        <div class="rounded-md bg-muted/50 px-3 py-2 text-center">
                            <p class="text-[10px] uppercase tracking-wider text-muted-foreground">{{ t('reservations.index.room_of_two') }}</p>
                            <p class="text-sm font-semibold">{{ offerVariant.pricing.room_of_two }} <span class="text-[10px] font-normal text-muted-foreground">DZD</span></p>
                        </div>
                        <div class="rounded-md bg-muted/50 px-3 py-2 text-center">
                            <p class="text-[10px] uppercase tracking-wider text-muted-foreground">{{ t('reservations.index.feeding') }}</p>
                            <p class="text-sm font-semibold">{{ offerVariant.pricing.feeding }} <span class="text-[10px] font-normal text-muted-foreground">DZD</span></p>
                        </div>
                    </div>
                </CardContent>
            </Card>

            <!-- Reservation Form -->
            <Card>
                <CardHeader>
                    <CardTitle>{{ t('reservations.edit.form_title') }}</CardTitle>
                    <CardDescription>{{ t('reservations.edit.form_description', { customer: reservation.customer }) }}</CardDescription>
                </CardHeader>
                <CardContent>
                    <form @submit.prevent="submit" class="space-y-6">
                        <div class="grid gap-6 sm:grid-cols-2">
                            <div class="space-y-2">
                                <Label for="customer">{{ t('reservations.index.customer') }}</Label>
                                <Input id="customer" v-model="customer"
                                    :placeholder="t('reservations.create.customer_placeholder')" required />
                                <InputError :message="errors.customer" />
                            </div>

                            <div class="space-y-2">
                                <Label for="phone">{{ t('reservations.index.phone') }}</Label>
                                <Input id="phone" v-model="phone"
                                    :placeholder="t('reservations.create.phone_placeholder')" required />
                                <InputError :message="errors.phone" />
                            </div>

                            <div class="space-y-2">
                                <Label for="wilaya">{{ t('reservations.index.wilaya') }}</Label>
                                <Select v-model="wilaya">
                                    <SelectTrigger class="w-full">
                                        <SelectValue :placeholder="t('reservations.create.select_wilaya')" />
                                    </SelectTrigger>
                                    <SelectContent>
                                        <SelectItem v-for="w in $page.props.wilayas" :key="w" :value="w">{{ w }}</SelectItem>
                                    </SelectContent>
                                </Select>
                                <InputError :message="errors.wilaya" />
                            </div>

                            <div class="space-y-2">
                                <Label for="travellers_number">{{ t('reservations.index.travellers') }}</Label>
                                <Input id="travellers_number" v-model.number="travellers_number" type="number" min="1"
                                    required />
                                <InputError :message="errors.travellers_number" />
                            </div>

                            <div class="space-y-2">
                                <Label for="room_type">{{ t('reservations.index.room_type') }}</Label>
                                <Select v-model="room_type">
                                    <SelectTrigger class="w-full">
                                        <SelectValue :placeholder="t('reservations.create.room_type_placeholder')" />
                                    </SelectTrigger>
                                    <SelectContent>
                                        <SelectItem value="collectif">{{ t('reservations.room_type.collectif') }}
                                        </SelectItem>
                                        <SelectItem value="room_of_four">{{ t('reservations.room_type.room_of_four') }}
                                        </SelectItem>
                                        <SelectItem value="room_of_three">{{ t('reservations.room_type.room_of_three')
                                            }}</SelectItem>
                                        <SelectItem value="room_of_two">{{ t('reservations.room_type.room_of_two') }}
                                        </SelectItem>
                                    </SelectContent>
                                </Select>
                                <InputError :message="errors.room_type" />
                            </div>

                            <div class="space-y-2">
                                <Label for="status">{{ t('reservations.index.status') }}</Label>
                                <Select v-model="status">
                                    <SelectTrigger class="w-full">
                                        <SelectValue :placeholder="t('reservations.create.status_placeholder')" />
                                    </SelectTrigger>
                                    <SelectContent>
                                        <SelectItem value="pending">{{ t('reservations.status.pending') }}</SelectItem>
                                        <SelectItem value="confirmed">{{ t('reservations.status.confirmed') }}
                                        </SelectItem>
                                        <SelectItem value="cancelled">{{ t('reservations.status.cancelled') }}
                                        </SelectItem>
                                    </SelectContent>
                                </Select>
                                <InputError :message="errors.status" />
                            </div>
                        </div>

                        <div class="flex items-center gap-3">
                            <Checkbox id="include_feeding" v-model="include_feeding" />
                            <Label for="include_feeding" class="cursor-pointer">{{
                                t('reservations.index.include_feeding') }}</Label>
                        </div>

                        <!-- Price Calculator -->
                        <div class="rounded-lg border-2 border-primary/20 bg-primary/5 p-5">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-2">
                                    <Wallet class="h-5 w-5 text-primary" />
                                    <span class="text-sm font-medium">{{ t('reservations.index.price') }}</span>
                                </div>
                                <span class="text-2xl font-bold text-primary">{{ calculatePrice.toLocaleString() }} <span class="text-sm font-normal text-muted-foreground">DZD</span></span>
                            </div>
                        </div>

                        <div class="flex justify-end border-t border-border pt-4">
                            <Button type="submit" :disabled="processing">
                                <Send class="mr-2 h-4 w-4" />
                                {{ t('common.save') }}
                            </Button>
                        </div>
                    </form>
                </CardContent>
            </Card>
        </div>
    </div>
</template>
