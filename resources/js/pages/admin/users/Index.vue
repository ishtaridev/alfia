<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import { Plus, Pencil, Trash2, Users } from '@lucide/vue';
import { ref } from 'vue';
import Heading from '@/components/Heading.vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent } from '@/components/ui/card';
import { Dialog, DialogContent, DialogDescription, DialogFooter, DialogHeader, DialogTitle } from '@/components/ui/dialog';
import { useLocale } from '@/composables/useLocale';
import { index as usersIndex, create as usersCreate, edit as usersEdit, destroy as usersDestroy } from '@/routes/users';

const { t } = useLocale();

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Users',
                href: usersIndex().url,
            },
        ],
    },
});

type PaginatedUsers = {
    data: Array<{
        id: number;
        name: string;
        email: string;
        role: string;
        created_at: string;
    }>;
    current_page: number;
    last_page: number;
    per_page: number;
    total: number;
};

defineProps<{
    users: PaginatedUsers;
}>();

const showDeleteDialog = ref(false);
const userToDelete = ref<{ id: number; name: string } | null>(null);

const confirmDelete = (user: { id: number; name: string }) => {
    userToDelete.value = user;
    showDeleteDialog.value = true;
};

const deleteUser = () => {
    if (! userToDelete.value) {
        return;
    }

    router.delete(usersDestroy(userToDelete.value.id).url, {
        onSuccess: () => {
            showDeleteDialog.value = false;
            userToDelete.value = null;
        },
    });
};
</script>

<template>
    <Head :title="t('users.index.title')" />

    <div class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
        <div class="flex items-center justify-between">
            <Heading
                :title="t('users.index.title')"
                :description="t('users.index.description')"
            />
            <Button as-child>
                <Link :href="usersCreate().url">
                    <Plus class="mr-2 h-4 w-4" />
                    {{ t('users.index.create_button') }}
                </Link>
            </Button>
        </div>

        <div v-if="users.data.length === 0" class="flex flex-col items-center justify-center py-16 text-center">
            <Users class="h-12 w-12 text-muted-foreground/50" />
            <p class="mt-4 text-lg font-medium text-muted-foreground">{{ t('users.index.empty_title') }}</p>
            <p class="mt-1 text-sm text-muted-foreground/70">{{ t('users.index.empty_description') }}</p>
            <Button class="mt-4" as-child>
                <Link :href="usersCreate().url">
                    <Plus class="mr-2 h-4 w-4" />
                    {{ t('users.index.create_button') }}
                </Link>
            </Button>
        </div>

        <div v-else class="space-y-4">
            <Card v-for="user in users.data" :key="user.id" class="overflow-hidden">
                <CardContent class="flex items-center justify-between p-4">
                    <div class="flex items-center gap-4">
                        <div class="flex h-10 w-10 items-center justify-center rounded-full bg-primary/10">
                            <Users class="h-5 w-5 text-primary" />
                        </div>
                        <div>
                            <p class="font-medium">{{ user.name }}</p>
                            <p class="text-sm text-muted-foreground">{{ user.email }}</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-4">
                        <span
                            :class="[
                                'inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium',
                                user.role === 'superadmin'
                                    ? 'bg-purple-100 text-purple-800 dark:bg-purple-900/30 dark:text-purple-300'
                                    : 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300',
                            ]"
                        >
                            {{ user.role === 'superadmin' ? t('users.role.superadmin') : t('users.role.admin') }}
                        </span>
                        <div class="flex items-center gap-2">
                            <Button variant="ghost" size="icon" as-child>
                                <Link :href="usersEdit(user.id).url">
                                    <Pencil class="h-4 w-4" />
                                </Link>
                            </Button>
                            <Button variant="ghost" size="icon" @click="confirmDelete(user)">
                                <Trash2 class="h-4 w-4 text-destructive" />
                            </Button>
                        </div>
                    </div>
                </CardContent>
            </Card>
        </div>

        <div v-if="users.last_page > 1" class="mt-4 flex justify-center gap-2">
            <Button
                v-for="page in users.last_page"
                :key="page"
                variant="outline"
                size="sm"
                :class="{ 'bg-primary text-primary-foreground': page === users.current_page }"
                as-child
            >
                <Link :href="usersIndex({ query: { page: String(page) } }).url">
                    {{ page }}
                </Link>
            </Button>
        </div>
    </div>

    <Dialog v-model:open="showDeleteDialog">
        <DialogContent>
            <DialogHeader>
                <DialogTitle>{{ t('users.index.delete_title') }}</DialogTitle>
                <DialogDescription>
                    {{ t('users.index.delete_confirmation', { name: userToDelete?.name }) }}
                </DialogDescription>
            </DialogHeader>
            <DialogFooter>
                <Button variant="outline" @click="showDeleteDialog = false">{{ t('common.cancel') }}</Button>
                <Button variant="destructive" @click="deleteUser">{{ t('common.delete') }}</Button>
            </DialogFooter>
        </DialogContent>
    </Dialog>
</template>
