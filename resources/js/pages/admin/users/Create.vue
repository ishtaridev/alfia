<script setup lang="ts">
import { Form, Head, Link } from '@inertiajs/vue3';
import { ArrowLeft, Send } from '@lucide/vue';
import Heading from '@/components/Heading.vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { useLocale } from '@/composables/useLocale';
import { index as usersIndex } from '@/routes/users';

const { t } = useLocale();

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Users',
                href: usersIndex().url,
            },
            {
                title: 'Create User',
                href: '#',
            },
        ],
    },
});
</script>

<template>
    <Head :title="t('users.create.title')" />

    <div class="flex h-full flex-1 flex-col gap-6 overflow-x-auto rounded-xl p-4">
        <div class="flex items-center justify-between">
            <Heading
                :title="t('users.create.title')"
                :description="t('users.create.description')"
            />
            <Button variant="outline" size="sm" as-child>
                <Link :href="usersIndex().url">
                    <ArrowLeft class="mr-2 h-4 w-4" />
                    {{ t('common.back') }}
                </Link>
            </Button>
        </div>

        <div class="mx-auto w-full max-w-2xl">
            <Card>
                <CardHeader>
                    <CardTitle>{{ t('users.create.form_title') }}</CardTitle>
                    <CardDescription>{{ t('users.create.form_description') }}</CardDescription>
                </CardHeader>
                <CardContent>
                    <Form action="/users" method="post" class="space-y-6" v-slot="{ errors, processing }">
                        <div class="space-y-2">
                            <Label for="name">{{ t('users.fields.name') }}</Label>
                            <Input
                                id="name"
                                name="name"
                                type="text"
                                required
                                autocomplete="name"
                                :placeholder="t('users.fields.name_placeholder')"
                            />
                            <InputError :message="errors.name" />
                        </div>

                        <div class="space-y-2">
                            <Label for="email">{{ t('users.fields.email') }}</Label>
                            <Input
                                id="email"
                                name="email"
                                type="email"
                                required
                                autocomplete="email"
                                :placeholder="t('users.fields.email_placeholder')"
                            />
                            <InputError :message="errors.email" />
                        </div>

                        <div class="grid gap-6 sm:grid-cols-2">
                            <div class="space-y-2">
                                <Label for="password">{{ t('users.fields.password') }}</Label>
                                <Input
                                    id="password"
                                    name="password"
                                    type="password"
                                    required
                                    autocomplete="new-password"
                                />
                                <InputError :message="errors.password" />
                            </div>

                            <div class="space-y-2">
                                <Label for="password_confirmation">{{ t('users.fields.confirm_password') }}</Label>
                                <Input
                                    id="password_confirmation"
                                    name="password_confirmation"
                                    type="password"
                                    required
                                    autocomplete="new-password"
                                />
                                <InputError :message="errors.password_confirmation" />
                            </div>
                        </div>

                        <div class="space-y-2">
                            <Label for="role">{{ t('users.fields.role') }}</Label>
                            <Select name="role" required>
                                <SelectTrigger class="w-full">
                                    <SelectValue :placeholder="t('users.fields.role_placeholder')" />
                                </SelectTrigger>
                                <SelectContent>
                                    <SelectItem value="admin">{{ t('users.role.admin') }}</SelectItem>
                                    <SelectItem value="superadmin">{{ t('users.role.superadmin') }}</SelectItem>
                                </SelectContent>
                            </Select>
                            <InputError :message="errors.role" />
                        </div>

                        <div class="flex justify-end border-t border-border pt-4">
                            <Button type="submit" :disabled="processing">
                                <Send class="mr-2 h-4 w-4" />
                                {{ t('common.create') }}
                            </Button>
                        </div>
                    </Form>
                </CardContent>
            </Card>
        </div>
    </div>
</template>
