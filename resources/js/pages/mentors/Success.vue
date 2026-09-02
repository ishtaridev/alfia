<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { CheckCircle, User, Phone, MapPin, ArrowRight } from '@lucide/vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent } from '@/components/ui/card';
import { useLocale } from '@/composables/useLocale';
import { register as mentorsRegister } from '@/routes/mentors';

const { t } = useLocale();

type Mentor = {
    id: number;
    full_name: string;
    phone: string;
    commune: string;
};

defineProps<{
    mentor: Mentor;
}>();
</script>

<template>
    <Head :title="t('mentors.register.success_title')" />

    <div class="mx-auto w-full max-w-2xl">
        <Card class="overflow-hidden border-0 shadow-xl">
            <CardContent class="p-6 text-center sm:p-10">
                <div
                    class="mx-auto flex h-20 w-20 items-center justify-center rounded-full bg-green-100 dark:bg-green-900/30"
                >
                    <CheckCircle class="h-10 w-10 text-green-600 dark:text-green-400" />
                </div>

                <h1 class="mt-6 text-2xl font-bold tracking-tight sm:text-3xl">
                    {{ t('mentors.register.success_heading') }}
                </h1>
                <p class="mt-2 text-muted-foreground">
                    {{ t('mentors.register.success_description') }}
                </p>

                <div class="mt-8 rounded-xl border bg-muted/30 p-6 text-start">
                    <h2 class="mb-4 text-sm font-semibold uppercase tracking-wide text-muted-foreground">
                        {{ t('mentors.register.summary_title') }}
                    </h2>
                    <div class="space-y-3">
                        <div class="flex items-center gap-3">
                            <User class="h-5 w-5 text-muted-foreground" />
                            <span>{{ mentor.full_name }}</span>
                        </div>
                        <div class="flex items-center gap-3" dir="ltr">
                            <Phone class="h-5 w-5 text-muted-foreground" />
                            <span>{{ mentor.phone }}</span>
                        </div>
                        <div class="flex items-center gap-3">
                            <MapPin class="h-5 w-5 text-muted-foreground" />
                            <span>{{ mentor.commune }}</span>
                        </div>
                    </div>
                </div>

                <Button as-child class="mt-8 h-12 rounded-xl px-8">
                    <Link :href="mentorsRegister().url">
                        {{ t('mentors.register.back_to_form') }}
                        <ArrowRight class="ms-2 h-4 w-4" />
                    </Link>
                </Button>
            </CardContent>
        </Card>
    </div>
</template>
