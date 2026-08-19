<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { Eye, Pencil, Trash2, Copy, Check, Link2, ImageIcon } from '@lucide/vue';
import { ref } from 'vue';
import { toast } from 'vue-sonner';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardFooter, CardHeader, CardTitle } from '@/components/ui/card';
import { useLocale } from '@/composables/useLocale';
import { formatDate } from '@/lib/formatDate';
import { show as offersShow, edit as offersEdit } from '@/routes/offers';

type Image = {
    id: number;
    url: string;
    order: number;
};

type Variant = {
    id: number;
    travel_date: string;
    airport: string;
};

type Offer = {
    id: number;
    title: string;
    code: string;
    description: string | null;
    images: Image[];
    variants: Variant[];
};

const props = defineProps<{
    offer: Offer;
}>();

const emit = defineEmits<{
    delete: [offer: Offer];
}>();

const { t } = useLocale();

const copied = ref(false);

const copyToClipboard = async (text: string): Promise<boolean> => {
    if (navigator.clipboard && window.isSecureContext) {
        try {
            await navigator.clipboard.writeText(text);
            return true;
        } catch {
            // Fall through to fallback
        }
    }

    // Fallback for non-secure contexts (HTTP, older browsers)
    const textarea = document.createElement('textarea');
    textarea.value = text;
    textarea.style.position = 'fixed';
    textarea.style.left = '-9999px';
    textarea.style.top = '0';
    document.body.appendChild(textarea);
    textarea.focus();
    textarea.select();

    try {
        const successful = document.execCommand('copy');
        document.body.removeChild(textarea);
        return successful;
    } catch {
        document.body.removeChild(textarea);
        return false;
    }
};

const copyPublicUrl = async () => {
    const url = `${window.location.origin}/offers/${props.offer.code}/reserve`;
    const success = await copyToClipboard(url);

    if (success) {
        copied.value = true;
        toast.success(t('offer_components.copied'), {
            description: url,
            duration: 2500,
        });
        setTimeout(() => {
            copied.value = false;
        }, 2000);
    } else {
        toast.error(t('errors.something_went_wrong'), {
            description: 'Could not copy to clipboard.',
            duration: 3000,
        });
    }
};


</script>

<template>
    <Card class="overflow-hidden transition-shadow duration-200 hover:shadow-md">
        <div class="relative aspect-video overflow-hidden bg-muted">
            <img
                v-if="offer.images.length > 0"
                :src="offer.images[0].url"
                :alt="offer.title"
                class="h-full w-full object-cover"
            />
            <div
                v-else
                class="flex h-full w-full flex-col items-center justify-center gap-2 text-muted-foreground"
            >
                <ImageIcon class="h-10 w-10 opacity-40" />
                <span class="text-sm">{{ t('offer_components.no_image') }}</span>
            </div>
            <Badge
                variant="secondary"
                class="absolute top-2 right-2 text-xs"
            >
                {{ t('offer_components.variants_count', { count: offer.variants.length }) }}
            </Badge>
        </div>

        <CardHeader class="pb-3">
            <div class="flex items-start justify-between gap-3">
                <CardTitle class="line-clamp-1 text-lg text-card-foreground">{{ offer.title }}</CardTitle>
                <Badge variant="outline" class="shrink-0 text-xs">
                    {{ offer.code }}
                </Badge>
            </div>
        </CardHeader>

        <CardContent class="pb-3">
            <p
                v-if="offer.description"
                class="line-clamp-2 text-sm text-muted-foreground"
                v-html="offer.description"
            />
            <p
                v-if="offer.variants.length > 0"
                class="mt-2 text-xs text-muted-foreground"
            >
                {{ t('offer_components.next') }} {{ formatDate(offer.variants[0].travel_date) }} - {{ offer.variants[0].airport }}
            </p>
        </CardContent>

        <CardFooter class="flex flex-wrap gap-2 border-t pt-3">
            <Button variant="outline" size="sm" as-child>
                <Link :href="offersShow(offer.code).url">
                    <Eye class="me-1 h-3.5 w-3.5" />
                    {{ t('offer_components.view') }}
                </Link>
            </Button>
            <Button variant="outline" size="sm" as-child>
                <Link :href="offersEdit(offer.code).url">
                    <Pencil class="me-1 h-3.5 w-3.5" />
                    {{ t('common.edit') }}
                </Link>
            </Button>
            <Button
                variant="outline"
                size="sm"
                class="transition-colors duration-200"
                :class="copied
                    ? 'border-emerald-500/50 bg-emerald-500/10 text-emerald-700 hover:bg-emerald-500/20 hover:text-emerald-800 dark:border-emerald-500/30 dark:text-emerald-400'
                    : ''
                "
                @click="copyPublicUrl"
            >
                <Check v-if="copied" class="me-1 h-3.5 w-3.5" />
                <Link2 v-else class="me-1 h-3.5 w-3.5" />
                {{ copied ? t('offer_components.copied') : t('offer_components.copy_url') }}
            </Button>
            <Button
                variant="outline"
                size="sm"
                class="ms-auto text-destructive hover:bg-destructive hover:text-destructive-foreground"
                :aria-label="t('common.delete')"
                @click="emit('delete', offer)">
                <Trash2 class="h-3.5 w-3.5" />
            </Button>
        </CardFooter>
    </Card>
</template>
