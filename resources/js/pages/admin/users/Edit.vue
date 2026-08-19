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
import { index as usersIndex, update as usersUpdate } from '@/routes/users';

const { t } = useLocale();

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Users',
                href: usersIndex().url,
            },
            {
                title: 'Edit User',
                href: '#',
            },
        ],
    },
});

const props = defineProps<{
    user: {
        id: number;
        name: string;
        email: string;
        role: string;
    };
}>();
</script>

<template>
    <Head :title="t('users.edit.title')" />

    <div class="flex h-full flex-1 flex-col gap-6 overflow-x-auto rounded-xl p-4">
        <div class="flex items-center justify-between">
            <Heading
                :title="t('users.edit.title')"
                :description="t('users.edit.description', { name: props.user.name })"
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
                    <CardTitle>{{ t('users.edit.form_title') }}</CardTitle>
                    <CardDescription>{{ t('users.edit.form_description', { name: props.user.name }) }}</CardDescription>
                </CardHeader>
                <CardContent>
                    <Form :action="usersUpdate(props.user.id).url" method="put" class="space-y-6" v-slot="{ errors, processing }">
                        <div class="space-y-2">
                            <Label for="name">{{ t('users.fields.name') }}</Label>
                            <Input
                                id="name"
                                name="name"
                                type="text"
                                :default-value="props.user.name"
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
                                :default-value="props.user.email"
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
                                    autocomplete="new-password"
                                    :placeholder="t('users.fields.password_optional')"
                                />
                                <InputError :message="errors.password" />
                            </div>

                            <div class="space-y-2">
                                <Label for="password_confirmation">{{ t('users.fields.confirm_password') }}</Label>
                                <Input
                                    id="password_confirmation"
                                    name="password_confirmation"
                                    type="password"
                                    autocomplete="new-password"
                                />
                                <InputError :message="errors.password_confirmation" />
                            </div>
                        </div>

                        <div class="space-y-2">
                            <Label for="role">{{ t('users.fields.role') }}</Label>
                            <Select name="role" :default-value="props.user.role" required>
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
                                {{ t('common.update') }}
                            </Button>
                        </div>
                    </Form>
                </CardContent>
            </Card>
        </div>
    </div>
</template>
