<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { computed } from 'vue';
import { dashboard, home, login } from '@/routes';
import offers from '@/routes/offers';
import AppLogoIcon from '@/components/AppLogoIcon.vue';
import GoogleTagManager from '@/components/GoogleTagManager.vue';
import { useLocale } from '@/composables/useLocale';

type OfferImage = { url: string };

type OfferVariant = {
    id: number;
    travel_date: string;
    airport: string;
    pricing: {
        collectif_room: number;
        room_of_four: number;
        room_of_three: number;
        room_of_two: number;
        feeding: number;
    } | null;
};

type Offer = {
    id: number;
    title: string;
    code: string;
    description: string | null;
    images: OfferImage[];
    variants: OfferVariant[];
};

const props = defineProps<{
    offers: Offer[];
}>();

const { t, locale, direction, setLocale } = useLocale();

const isRtl = computed(() => direction.value === 'rtl');

const phoneNumber = '035 76 30 33';
const phoneLink = 'tel:035763033';
const whatsappLink = 'https://wa.me/213553013105';

function offerImageUrl(offer: Offer): string {
    return offer.images[0]?.url ?? '/images/alfia.jpeg';
}

function offerLowestPrice(offer: Offer): number | null {
    let lowest: number | null = null;

    for (const variant of offer.variants) {
        if (!variant.pricing) {
            continue;
        }

        const prices = [
            variant.pricing.collectif_room,
            variant.pricing.room_of_four,
            variant.pricing.room_of_three,
            variant.pricing.room_of_two,
        ];

        for (const price of prices) {
            if (price > 0 && (lowest === null || price < lowest)) {
                lowest = price;
            }
        }
    }

    return lowest;
}

function nextVariant(offer: Offer): OfferVariant | null {
    return offer.variants[0] ?? null;
}

function formatDate(dateString: string): string {
    return new Date(dateString).toLocaleDateString(locale.value, {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
    });
}

function formatPrice(price: number): string {
    return price.toLocaleString(locale.value);
}

const languages = [
    { code: 'en', label: 'EN' },
    { code: 'fr', label: 'FR' },
    { code: 'ar', label: 'AR' },
] as const;

const facebookComments = [
    {
        name: 'Sabi Nafissa Djema',
        avatar: 'https://scontent.fqsf1-1.fna.fbcdn.net/v/t39.30808-1/518374103_1783971572518740_6492545234124785641_n.jpg?stp=cp0_dst-jpg_tt6&cstp=mx480x478&ctp=s40x40&_nc_cat=105&ccb=1-7&_nc_sid=e99d92&_nc_ohc=yf2n1-DCdj8Q7kNvwEFauZr&_nc_oc=AdopWiSXx_HOENTu8Ztt_ssGcgLQE7VP5CVxPuYQmsjQU_9zBod9r8gcUVcy6jiZ8t8&_nc_zt=24&_nc_ht=scontent.fqsf1-1.fna&_nc_gid=L-HKoziBbeneA3j-V4Acqw&_nc_ss=7b2a8&oh=00_AQEPEE1LGVi1cVvNqmzbxdvoHXJgXYG7R9yHJ2bek6ZZ7Q&oe=6A8B5777',
        body: 'بدون مجاملة اعتمرت معكم في 2019 فندق انجم مع المرشد السيد الخلوق شريف وكانت رحلة ممتعة',
    },
    {
        name: 'Tulipe Taqwa',
        avatar: 'https://scontent.fqsf1-1.fna.fbcdn.net/v/t39.30808-1/489531033_977817561092661_1041869638892551891_n.jpg?stp=cp0_dst-jpg_tt6&cstp=mx536x531&ctp=p40x40&_nc_cat=102&ccb=1-7&_nc_sid=e99d92&_nc_ohc=t6dB7KqALNwQ7kNvwG-Cz4k&_nc_oc=AdoKbspSBidBRSk_e8mOjZ-JSJ8bqphmv8RmHjJd9X4lrS0vPLWm4mZZ1K_23aKn1ys&_nc_zt=24&_nc_ht=scontent.fqsf1-1.fna&_nc_gid=L-HKoziBbeneA3j-V4Acqw&_nc_ss=7b2a8&oh=00_AQEr1wjZD1tx-dk_VJwfjhgK1dI7T7hs9Gw7nDmnI8FXWA&oe=6A8B7345',
        body: 'رحنا معاكم قبل عام ونصف كانت رحلة جميلة جدا وخاصة اختيار الفنادق والتنظيم 👌🏻 ، الله يرزقنا العودة 😢',
    },
    {
        name: 'Samira Samirab',
        avatar: 'https://scontent.fqsf1-2.fna.fbcdn.net/v/t39.30808-1/492553429_2722779091260409_8601223917656287712_n.jpg?stp=dst-jpg_tt6&cstp=mx736x735&ctp=s100x100&_nc_cat=106&ccb=1-7&_nc_sid=e99d92&_nc_ohc=27bMjzwh6AsQ7kNvwG2PO84&_nc_oc=AdqVsd0tY-eq9UCgXhgfdRnd2gmm_pjIE5EypGFbJbI2Mp6fnRIPKGj2J0eNWQONfn0&_nc_zt=24&_nc_ht=scontent.fqsf1-2.fna&_nc_gid=PM4EiE2bbISyAJO9OuhUSg&_nc_ss=7b2a8&oh=00_AQFqwWmeEQWr8kD6ikaYQjKSbm2ImMlG21_rAG1ji5-mog&oe=6A8B480B',
        body: 'وكالة الأليفة من بكري معروفة بصدقها وطيبة اهلها والعاملين لي فيها قمة في الاخلاق والتواضع ربي يوفق',
    },
    {
        name: 'الحمد الله',
        avatar: 'https://scontent.fqsf1-2.fna.fbcdn.net/v/t39.30808-1/752845733_122106237333397908_7847580169119069157_n.jpg?stp=dst-jpg_tt6&cstp=mx736x732&ctp=s100x100&_nc_cat=107&ccb=1-7&_nc_sid=e99d92&_nc_ohc=S0GjnJT8RgIQ7kNvwFtD3dq&_nc_oc=AdowEmuK9jfsUxUMmi2ZEprtBM_bRXU5ZsCjqzlTlQu8oWvykx3jPX8J8MmXy3Zuvbw&_nc_zt=24&_nc_ht=scontent.fqsf1-2.fna&_nc_gid=yb2Y_MvOXr8ZaSofOEomeA&_nc_ss=7b2a8&oh=00_AQEtCMllS1ALmTmK2qRhiCJHcyga5H4qAAfiFUG0eHg9OA&oe=6A8B48FB',
        body: 'وكالة في القمة ماشاء الله تنظيم إحترافية وأخلاق ربي يوفقكم ويبارك فيكم وفي خدمتكم لضيوف الرحمان وربي يبارك في المرشدين ويكتبلكم الأجر والثواب وراح نكون ان شاء الله بين أيديكم في عمرة أخرى ❤️🕋',
    },
    {
        name: 'Rabah Ammari',
        avatar: 'https://scontent.fqsf1-2.fna.fbcdn.net/v/t39.30808-1/515371883_703983429148110_729023542988990592_n.jpg?stp=c0.108.720.720a_cp0_dst-jpg_tt6&cstp=mx720x720&ctp=s40x40&_nc_cat=107&ccb=1-7&_nc_sid=e99d92&_nc_ohc=6fgptcWIL98Q7kNvwEpLsAd&_nc_oc=AdofGGvTGrK0ZbxY5wCHA2vT3akhNu04G0FAtJeSsXuFEs8BQodpu6QWAzYmjJxFL7c&_nc_zt=24&_nc_ht=scontent.fqsf1-2.fna&_nc_gid=WkX52_xphZu1q0B5rPZpFg&_nc_ss=7b2a8&oh=00_AQG5qTGKrBdGKSsWMORY1H7JZE3MHtoPuPwX07eJ1f2ljw&oe=6A8B7152',
        body: 'والله غير وكاله الألفية مشاء الله بيدون مجملة',
    },
    {
        name: 'Walid Booyka',
        avatar: 'https://scontent.fqsf1-1.fna.fbcdn.net/v/t39.30808-1/351327571_1020609182261009_3543011170771563675_n.jpg?stp=cp0_dst-jpg_tt6&cstp=mx950x960&ctp=s40x40&_nc_cat=111&ccb=1-7&_nc_sid=1d2534&_nc_ohc=CnoRuewLFZ0Q7kNvwHrjhkQ&_nc_oc=Adp-Flbh7PD-Fa3hfvkQCFbceWDyxt7CSNqQxXnq_QlZl5MzZ7ATAfyd5gv-mfBsNbI&_nc_zt=24&_nc_ht=scontent.fqsf1-1.fna&_nc_gid=rzYI4OBXPQYuQHliuimc6g&_nc_ss=7b2a8&oh=00_AQG8sFbdwMbSZaG6g8O6b8t-zupf-0UaLUiHCAObfb4j6g&oe=6A8B4D7C',
        body: 'رحت في هاذ الرحلة كانت افضل رحلة في حياتي زيارة البقاع المقدسة وكانت مجمموعة مشاء الله كنا عايلة نتمنى نعاود نولى',
    },
];

const duplicatedComments = computed(() => [
    ...facebookComments,
    ...facebookComments,
]);

const heroTitleWords = computed(() =>
    String(t('welcome.hero_title')).trim().split(/\s+/),
);

const scrollAnimationClass = computed(() =>
    direction.value === 'rtl'
        ? 'animate-[marquee-rtl_35s_linear_infinite]'
        : 'animate-[marquee_35s_linear_infinite]',
);

const startFadeClass = computed(() =>
    direction.value === 'rtl'
        ? 'bg-gradient-to-l from-[hsl(205,64%,22%)] to-transparent'
        : 'bg-gradient-to-r from-[hsl(205,64%,22%)] to-transparent',
);

const endFadeClass = computed(() =>
    direction.value === 'rtl'
        ? 'bg-gradient-to-r from-[hsl(205,64%,22%)] to-transparent'
        : 'bg-gradient-to-l from-[hsl(205,64%,22%)] to-transparent',
);
</script>

<template>

    <Head :title="t('welcome.meta_title')">
        <meta name="description" :content="t('welcome.hero_subtitle')" />
    </Head>

    <GoogleTagManager />

    <div class="min-h-screen bg-background text-foreground antialiased selection:bg-white selection:text-[#184a6d]"
        :dir="direction">
        <!-- Navigation -->
        <header class="sticky top-0 z-50 border-b border-white/10 bg-background/90 backdrop-blur">
            <div class="mx-auto flex max-w-7xl items-center justify-between px-6 py-4 lg:px-8">
                <Link :href="home()" class="flex items-center gap-3">
                    <AppLogoIcon class="h-10 w-10 rounded-md object-cover shadow-sm" />
                </Link>

                <nav class="flex items-center gap-6">
                    <a href="#offers"
                        class="hidden text-sm font-medium text-white/80 transition-colors hover:text-white sm:inline-block">
                        {{ t('welcome.nav_offers') }}
                    </a>
                    <a href="#contact"
                        class="hidden text-sm font-medium text-white/80 transition-colors hover:text-white sm:inline-block">
                        {{ t('welcome.nav_contact') }}
                    </a>

                    <Link v-if="$page.props.auth.user" :href="dashboard()"
                        class="rounded-full border border-white px-4 py-1.5 text-sm font-medium text-white transition-colors hover:bg-white hover:text-[#184a6d]">
                        {{ t('welcome.nav_dashboard') }}
                    </Link>
                    <Link v-else :href="login()"
                        class="rounded-full border border-white px-4 py-1.5 text-sm font-medium text-white transition-colors hover:bg-white hover:text-[#184a6d]">
                        {{ t('welcome.nav_login') }}
                    </Link>

                    <div class="flex items-center rounded-full bg-white/10 p-1">
                        <button v-for="lang in languages" :key="lang.code" type="button"
                            :aria-label="t('language_switcher.' + lang.code)"
                            class="rounded-full px-2.5 py-1 text-xs font-semibold transition-all" :class="locale === lang.code
                                ? 'bg-white text-[#184a6d] shadow-sm'
                                : 'text-white/70 hover:text-white'
                                " @click="setLocale(lang.code)">
                            {{ lang.label }}
                        </button>
                    </div>
                </nav>
            </div>
        </header>

        <!-- Hero -->
        <section class="relative overflow-hidden bg-background px-6 pt-20 pb-24 lg:px-8 lg:pt-28">
            <div class="absolute inset-0 opacity-40">
                <div
                    class="absolute -end-32 -top-32 h-96 w-96 animate-[hero-float_8s_ease-in-out_infinite] rounded-full bg-white/10 blur-3xl" />
                <div
                    class="absolute -start-32 -bottom-32 h-96 w-96 animate-[hero-float_10s_ease-in-out_infinite_reverse] rounded-full bg-white/5 blur-3xl" />
            </div>

            <div class="relative mx-auto max-w-5xl text-center">
                <h1 class="text-4xl leading-[1.1] font-bold tracking-tight text-balance text-white sm:text-5xl lg:text-6xl"
                    :aria-label="t('welcome.hero_title')">
                    <span v-for="(word, index) in heroTitleWords" :key="index"
                        class="inline-block animate-[hero-word-reveal_0.8s_ease-out_forwards] opacity-0"
                        :style="{ animationDelay: `${index * 90}ms` }" aria-hidden="true">
                        {{ word
                        }}{{
                            index < heroTitleWords.length - 1 ? '\u00A0' : '' }} </span>
                </h1>
                <p
                    class="mx-auto mt-6 max-w-2xl animate-[hero-fade-up_0.9s_ease-out_0.7s_forwards] text-lg leading-relaxed text-balance text-white/80 opacity-0">
                    {{ t('welcome.hero_subtitle') }}
                </p>

                <div
                    class="mt-10 flex animate-[hero-fade-up_0.9s_ease-out_0.95s_forwards] flex-col items-center justify-center gap-4 opacity-0 sm:flex-row">
                    <a href="#offers"
                        class="inline-flex min-w-[12rem] items-center justify-center rounded-full bg-white px-7 py-3.5 text-base font-semibold text-[#184a6d] shadow-lg shadow-black/20 transition-all hover:-translate-y-0.5 hover:bg-white/90 hover:shadow-xl hover:shadow-black/25 focus-visible:ring-2 focus-visible:ring-white focus-visible:ring-offset-2 focus-visible:ring-offset-[#184a6d]">
                        {{ t('welcome.cta_offers') }}
                    </a>
                    <div class="flex items-center gap-4 flex-col md:flex-row">
                        <a :href="phoneLink"
                            class="inline-flex min-w-[12rem] items-center justify-center gap-2 rounded-full border-2 border-white bg-transparent px-7 py-3.5 text-base font-semibold text-white transition-all hover:-translate-y-0.5 hover:bg-white/10 focus-visible:ring-2 focus-visible:ring-white focus-visible:ring-offset-2 focus-visible:ring-offset-[#184a6d]">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                class="h-5 w-5">
                                <path
                                    d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z" />
                            </svg>
                            {{ t('welcome.cta_call') }}
                        </a>
                        <a :href="whatsappLink" target="_blank" rel="noopener noreferrer"
                            class="inline-flex h-12 w-12 items-center justify-center rounded-full bg-[#25d366] text-white shadow-md transition-all hover:-translate-y-0.5 hover:scale-110 focus-visible:ring-2 focus-visible:ring-[#25d366] focus-visible:ring-offset-2 focus-visible:ring-offset-[#184a6d]"
                            :aria-label="t('welcome.cta_whatsapp')">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"
                                class="h-6 w-6">
                                <path
                                    d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413" />
                            </svg>
                        </a>
                    </div>
                </div>

                <!-- Trust signals -->
                <div
                    class="mx-auto mt-16 flex max-w-3xl animate-[hero-fade-up_0.9s_ease-out_1.15s_forwards] flex-col items-center justify-center gap-8 border-t border-white/10 pt-10 opacity-0 sm:flex-row sm:gap-12">
                    <div class="text-center">
                        <div class="text-3xl font-bold text-white">2000</div>
                        <div class="mt-1 text-sm text-white/70">
                            {{ t('welcome.trust_since') }}
                        </div>
                    </div>
                    <div class="hidden h-10 w-px bg-white/20 sm:block" />
                    <div class="text-center">
                        <div class="text-3xl font-bold text-white">4</div>
                        <div class="mt-1 text-sm text-white/70">
                            {{ t('welcome.trust_offices') }}
                        </div>
                    </div>
                    <div class="hidden h-10 w-px bg-white/20 sm:block" />
                    <div class="text-center">
                        <div class="text-3xl font-bold text-white">24/7</div>
                        <div class="mt-1 text-sm text-white/70">
                            {{ t('welcome.trust_support') }}
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Featured offers -->
        <section id="offers" class="px-6 py-24 lg:px-8">
            <div class="mx-auto max-w-7xl">
                <div class="mb-12 max-w-2xl">
                    <h2 class="text-3xl font-bold tracking-tight text-white sm:text-4xl">
                        {{ t('welcome.offers_title') }}
                    </h2>
                    <p class="mt-4 text-lg text-white/80">
                        {{ t('welcome.offers_subtitle') }}
                    </p>
                </div>

                <div v-if="props.offers.length > 0" class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                    <article v-for="offer in props.offers" :key="offer.id"
                        class="group flex flex-col overflow-hidden rounded-2xl border border-white/10 bg-white shadow-sm transition-all hover:-translate-y-1 hover:shadow-lg">
                        <div class="relative aspect-[4/3] overflow-hidden bg-slate-100">
                            <img :src="offerImageUrl(offer)" :alt="offer.title"
                                class="h-full w-full object-cover transition-transform duration-500 group-hover:scale-105"
                                loading="lazy" />
                            <div
                                class="absolute inset-0 bg-gradient-to-t from-black/40 to-transparent opacity-0 transition-opacity duration-300 group-hover:opacity-100" />
                        </div>
                        <div class="flex flex-1 flex-col p-6">
                            <h3 class="text-xl font-semibold text-[#1b1b18]" :class="isRtl ? 'leading-normal' : 'leading-tight'
                                ">
                                {{ offer.title }}
                            </h3>
                            <p v-if="offer.description" class="mt-2 line-clamp-2 text-sm text-[#6b6b64]"
                                v-html="offer.description"></p>

                            <div class="mt-4 space-y-2 border-t border-slate-100 pt-4 text-sm">
                                <div v-if="nextVariant(offer)" class="flex items-center justify-between">
                                    <span class="text-[#6b6b64]">
                                        {{ t('welcome.offers_next_departure') }}
                                    </span>
                                    <span class="font-medium text-[#1b1b18]">
                                        {{
                                            formatDate(
                                                nextVariant(offer)!.travel_date,
                                            )
                                        }}
                                    </span>
                                </div>
                                <div v-if="offerLowestPrice(offer)" class="flex items-center justify-between">
                                    <span class="text-[#6b6b64]">
                                        {{ t('welcome.offers_from') }}
                                    </span>
                                    <span class="text-lg font-bold text-[#184a6d]">
                                        {{
                                            formatPrice(
                                                offerLowestPrice(offer)!,
                                            )
                                        }}
                                        DZD
                                    </span>
                                </div>
                            </div>

                            <div class="mt-auto pt-6">
                                <Link :href="offers.reserve({ offer: offer.code })
                                    "
                                    class="inline-flex w-full items-center justify-center rounded-xl bg-[#184a6d] px-5 py-3 text-sm font-semibold text-white transition-colors hover:bg-[#123a55] focus-visible:ring-2 focus-visible:ring-[#184a6d] focus-visible:ring-offset-2">
                                    {{ t('welcome.offers_view') }}
                                </Link>
                            </div>
                        </div>
                    </article>
                </div>

                <div v-else class="rounded-2xl border border-dashed border-white/20 bg-white/5 p-12 text-center">
                    <p class="text-lg text-white/80">
                        {{ t('welcome.offers_empty') }}
                    </p>
                    <a :href="phoneLink" dir="ltr"
                        class="mt-4 inline-flex items-center gap-2 font-semibold text-white hover:underline">
                        {{ phoneNumber }}
                    </a>
                </div>
            </div>
        </section>

        <!-- Testimonials -->
        <section class="bg-[hsl(205,64%,22%)] px-6 py-24 lg:px-8">
            <div class="mx-auto max-w-7xl">
                <div class="mb-12 max-w-2xl">
                    <h2 class="text-3xl font-bold tracking-tight text-white sm:text-4xl">
                        {{ t('welcome.testimonials_title') }}
                    </h2>
                    <p class="mt-4 text-lg text-white/80">
                        {{ t('welcome.testimonials_subtitle') }}
                    </p>
                </div>

                <div class="relative -mx-6 overflow-hidden lg:-mx-8">
                    <div class="pointer-events-none absolute inset-y-0 start-0 z-10 w-16" :class="startFadeClass" />
                    <div class="pointer-events-none absolute inset-y-0 end-0 z-10 w-16" :class="endFadeClass" />

                    <div class="flex w-max gap-4 py-2 will-change-transform" :class="scrollAnimationClass"
                        :dir="direction">
                        <div v-for="(comment, index) in duplicatedComments" :key="index"
                            class="w-[22rem] shrink-0 rounded-xl bg-white p-4 shadow-sm">
                            <div class="flex items-start gap-3">
                                <img :src="comment.avatar" :alt="comment.name"
                                    class="h-10 w-10 shrink-0 rounded-full border border-black/5 object-cover"
                                    loading="lazy" />
                                <div class="min-w-0 flex-1">
                                    <div class="inline-block max-w-full rounded-2xl bg-[#f0f2f5] px-3.5 py-2">
                                        <p class="text-sm font-bold text-[#050505]">
                                            {{ comment.name }}
                                        </p>
                                        <p class="mt-0.5 text-sm leading-relaxed break-words text-[#050505]">
                                            {{ comment.body }}
                                        </p>
                                    </div>
                                    <div class="mt-1 flex items-center gap-2 px-1 text-xs font-bold text-[#65676b]">
                                        <button type="button" class="hover:underline">
                                            {{ t('welcome.testimonials_like') }}
                                        </button>
                                        <span>·</span>
                                        <button type="button" class="hover:underline">
                                            {{
                                                t('welcome.testimonials_reply')
                                            }}
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Contact -->
        <section id="contact" class="px-6 py-24 lg:px-8">
            <div class="mx-auto max-w-7xl">
                <div class="grid gap-12 lg:grid-cols-2">
                    <div>
                        <h2 class="text-3xl font-bold tracking-tight text-white sm:text-4xl">
                            {{ t('welcome.contact_title') }}
                        </h2>
                        <p class="mt-4 text-lg text-white/80">
                            {{ t('welcome.contact_subtitle') }}
                        </p>

                        <div class="mt-10">
                            <h3 class="text-sm font-semibold tracking-wide text-white/70 uppercase">
                                {{ t('welcome.contact_phone') }}
                            </h3>
                            <a :href="phoneLink" dir="ltr"
                                class="mt-2 inline-flex items-center gap-3 text-3xl font-bold text-white transition-colors hover:text-white/80">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                                    stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round" class="h-7 w-7">
                                    <path
                                        d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z" />
                                </svg>
                                {{ phoneNumber }}
                            </a>
                        </div>
                    </div>

                    <div class="rounded-2xl border border-white/10 bg-white p-8 shadow-sm">
                        <h3 class="text-lg font-semibold text-[#1b1b18]">
                            {{ t('welcome.contact_address') }}
                        </h3>
                        <ul class="mt-6 space-y-5">
                            <li class="flex items-start gap-4">
                                <span
                                    class="mt-1 flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-[#184a6d]/10 text-[#184a6d]">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                                        stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                        stroke-linejoin="round" class="h-4 w-4">
                                        <path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z" />
                                        <circle cx="12" cy="10" r="3" />
                                    </svg>
                                </span>
                                <span class="text-[#4a4a45]">
                                    {{ t('welcome.office_algiers') }}
                                </span>
                            </li>
                            <li class="flex items-start gap-4">
                                <span
                                    class="mt-1 flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-[#184a6d]/10 text-[#184a6d]">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                                        stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                        stroke-linejoin="round" class="h-4 w-4">
                                        <path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z" />
                                        <circle cx="12" cy="10" r="3" />
                                    </svg>
                                </span>
                                <span class="text-[#4a4a45]">
                                    {{ t('welcome.office_oran') }}
                                </span>
                            </li>
                            <li class="flex items-start gap-4">
                                <span
                                    class="mt-1 flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-[#184a6d]/10 text-[#184a6d]">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                                        stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                        stroke-linejoin="round" class="h-4 w-4">
                                        <path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z" />
                                        <circle cx="12" cy="10" r="3" />
                                    </svg>
                                </span>
                                <span class="text-[#4a4a45]">
                                    {{ t('welcome.office_bordj') }}
                                </span>
                            </li>
                            <li class="flex items-start gap-4">
                                <span
                                    class="mt-1 flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-[#184a6d]/10 text-[#184a6d]">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                                        stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                        stroke-linejoin="round" class="h-4 w-4">
                                        <circle cx="12" cy="12" r="10" />
                                        <polyline points="12 6 12 12 16 14" />
                                    </svg>
                                </span>
                                <span class="text-[#6b6b64]">
                                    {{ t('welcome.office_setif') }}
                                </span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </section>

        <!-- Footer -->
        <footer class="border-t border-white/10 bg-[hsl(205,64%,22%)] px-6 py-10 lg:px-8">
            <div class="mx-auto flex max-w-7xl flex-col items-center justify-between gap-4 sm:flex-row">
                <div class="flex items-center gap-3">
                    <AppLogoIcon class="h-8 w-8 rounded-md object-cover" />
                    <span class="text-sm font-semibold text-white">
                        Alfia
                    </span>
                </div>
                <p class="text-center text-sm text-white/70">
                    © {{ new Date().getFullYear() }} Alfia.
                    {{ t('welcome.footer_rights') }}
                </p>
                <p class="text-sm text-white/70">
                    {{ t('welcome.footer_tagline') }}
                </p>
            </div>
        </footer>
    </div>
</template>
