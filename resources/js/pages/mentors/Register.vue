<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import { User, Phone, MapPin, Send } from '@lucide/vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import { useLocale } from '@/composables/useLocale';
import { store as mentorsStore } from '@/routes/mentors/register';

const { t } = useLocale();

defineProps<{
    communes: string[];
}>();

const form = useForm({
    full_name: '',
    phone: '',
    commune: '',
});

const submit = () => {
    form.post(mentorsStore().url);
};
</script>

<template>
    <Head :title="t('mentors.register.page_title')" />

    <div class="mx-auto w-full max-w-2xl">
        <div class="mb-8 text-center">
            <div
                class="mb-3 inline-flex items-center gap-2 rounded-full bg-primary/10 px-4 py-1.5 text-sm font-medium text-primary"
            >
                <User class="h-4 w-4" />
                <span>{{ t('mentors.register.badge') }}</span>
            </div>
            <h1 class="text-2xl font-bold tracking-tight sm:text-3xl">
                {{ t('mentors.register.title') }}
            </h1>
            <p class="mt-2 text-sm text-muted-foreground">
                {{ t('mentors.register.description') }}
            </p>
        </div>

        <Card class="overflow-hidden border-0 shadow-xl">
            <CardContent class="p-6 sm:p-8">
                <form class="space-y-6" @submit.prevent="submit">
                    <div class="space-y-2">
                        <Label for="full_name" class="text-sm font-medium">
                            {{ t('mentors.register.full_name') }}
                        </Label>
                        <div class="relative">
                            <User
                                class="absolute start-3 top-1/2 h-5 w-5 -translate-y-1/2 text-muted-foreground"
                            />
                            <Input
                                id="full_name"
                                v-model="form.full_name"
                                type="text"
                                :placeholder="t('mentors.register.full_name_placeholder')"
                                class="h-12 rounded-xl ps-10"
                            />
                        </div>
                        <InputError :message="form.errors.full_name" />
                    </div>

                    <div class="space-y-2">
                        <Label for="phone" class="text-sm font-medium">
                            {{ t('mentors.register.phone') }}
                        </Label>
                        <div class="relative">
                            <Phone
                                class="absolute start-3 top-1/2 h-5 w-5 -translate-y-1/2 text-muted-foreground"
                            />
                            <Input
                                id="phone"
                                v-model="form.phone"
                                type="tel"
                                dir="ltr"
                                :placeholder="t('mentors.register.phone_placeholder')"
                                class="h-12 rounded-xl ps-10"
                            />
                        </div>
                        <InputError :message="form.errors.phone" />
                    </div>

                    <div class="space-y-2">
                        <Label for="commune" class="text-sm font-medium">
                            {{ t('mentors.register.commune') }}
                        </Label>
                        <Select v-model="form.commune">
                            <SelectTrigger
                                id="commune"
                                class="h-12 w-full rounded-xl border-border bg-card px-4"
                            >
                                <div class="flex items-center gap-2">
                                    <MapPin class="h-4 w-4 text-muted-foreground" />
                                    <SelectValue
                                        :placeholder="t('mentors.register.select_commune')"
                                    />
                                </div>
                            </SelectTrigger>
                            <SelectContent>
                                <SelectItem
                                    v-for="commune in communes"
                                    :key="commune"
                                    :value="commune"
                                >
                                    {{ commune }}
                                </SelectItem>
                            </SelectContent>
                        </Select>
                        <InputError :message="form.errors.commune" />
                    </div>

                    <Button
                        type="submit"
                        class="h-12 w-full rounded-xl text-base"
                        :disabled="form.processing"
                    >
                        <Send v-if="!form.processing" class="me-2 h-4 w-4" />
                        {{ form.processing ? t('common.loading') : t('mentors.register.submit') }}
                    </Button>
                </form>
            </CardContent>
        </Card>
    </div>
</template>
