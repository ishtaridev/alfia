<script setup lang="ts">
import { Form, Head } from '@inertiajs/vue3';
import InputError from '@/components/InputError.vue';
import PasswordInput from '@/components/PasswordInput.vue';
import TextLink from '@/components/TextLink.vue';
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Spinner } from '@/components/ui/spinner';
import { useLocale } from '@/composables/useLocale';
import { store } from '@/routes/login';
import { request } from '@/routes/password';
import AuthLayout from '@/layouts/AuthLayout.vue';

const { t } = useLocale();

defineOptions({
    layout: (h, page) => h(
        AuthLayout,
        {},
        () => page
    ),
})

defineProps<{
    status?: string;
    canResetPassword: boolean;
}>();
</script>

<template>

    <Head :title="t('auth.login.submit')" />

    <div v-if="status" class="mb-4 text-center text-sm font-medium text-green-600">
        {{ status }}
    </div>

    <Form v-bind="store.form()" :reset-on-success="['password']" v-slot="{ errors, processing }"
        class="flex flex-col gap-6">
        <div class="grid gap-6">
            <div class="grid gap-2">
                <Label for="email">{{ t('auth.login.email') }}</Label>
                <Input id="email" type="email" name="email" required autofocus :tabindex="1" autocomplete="email"
                    :placeholder="t('auth.login.email_placeholder')" />
                <InputError :message="errors.email" />
            </div>

            <div class="grid gap-2">
                <div class="flex items-center justify-between">
                    <Label for="password">{{ t('auth.login.password') }}</Label>
                    <TextLink v-if="canResetPassword" :href="request()" class="text-sm" :tabindex="5">
                        {{ t('auth.login.forgot_password') }}
                    </TextLink>
                </div>
                <PasswordInput id="password" name="password" required :tabindex="2" autocomplete="current-password"
                    :placeholder="t('auth.login.password')" />
                <InputError :message="errors.password" />
            </div>

            <div class="flex items-center justify-between">
                <Label for="remember" class="flex items-center space-x-3">
                    <Checkbox id="remember" name="remember" :tabindex="3" class="bg-white" />
                    <span>{{ t('auth.login.remember_me') }}</span>
                </Label>
            </div>

            <Button type="submit" class="mt-4 w-full" :tabindex="4" :disabled="processing" data-test="login-button">
                <Spinner v-if="processing" />
                {{ t('auth.login.submit') }}
            </Button>
        </div>

    </Form>
</template>
