<script setup lang="ts">
import { X, Upload } from '@lucide/vue';
import { ref } from 'vue';
import { Button } from '@/components/ui/button';
import { useLocale } from '@/composables/useLocale';

type ExistingImage = {
    id: number;
    path: string;
    url: string;
    order: number;
};

type Props = {
    existingImages?: ExistingImage[];
    modelValue: File[];
};

const props = withDefaults(defineProps<Props>(), {
    existingImages: () => [],
});

const emit = defineEmits<{
    'update:modelValue': [files: File[]];
    deleteExisting: [id: number];
}>();

const { t } = useLocale();
const fileInput = ref<HTMLInputElement | null>(null);
const previewUrls = ref<Map<File, string>>(new Map());

const handleFiles = (event: Event) => {
    const input = event.target as HTMLInputElement;

    if (!input.files) {
        return;
    }

    const newFiles = Array.from(input.files);

    for (const file of newFiles) {
        previewUrls.value.set(file, URL.createObjectURL(file));
    }

    emit('update:modelValue', [...props.modelValue, ...newFiles]);
    input.value = '';
};

const removeFile = (index: number) => {
    const file = props.modelValue[index];
    const url = previewUrls.value.get(file);

    if (url) {
        URL.revokeObjectURL(url);
        previewUrls.value.delete(file);
    }

    const newFiles = [...props.modelValue];
    newFiles.splice(index, 1);
    emit('update:modelValue', newFiles);
};

const getPreviewUrl = (file: File): string => {
    return previewUrls.value.get(file) ?? URL.createObjectURL(file);
};

const handleDrop = (event: DragEvent) => {
    const files = event.dataTransfer?.files;

    if (files) {
        const newFiles = Array.from(files);

        for (const file of newFiles) {
            previewUrls.value.set(file, URL.createObjectURL(file));
        }

        emit('update:modelValue', [...props.modelValue, ...newFiles]);
    }
};
</script>

<template>
    <div class="space-y-4">
        <div v-if="existingImages.length > 0 || modelValue.length > 0"
            class="grid grid-cols-2 gap-3 sm:grid-cols-3 lg:grid-cols-4">
            <div v-for="image in existingImages" :key="`existing-${image.id}`"
                class="group relative aspect-square overflow-hidden rounded-lg border border-border">
                <img :src="image.url" :alt="t('offer_components.offer_image', { order: image.order })" class="h-full w-full object-cover" />
                <div
                    class="absolute inset-0 flex items-center justify-center bg-black/50 opacity-0 transition-opacity group-hover:opacity-100">
                    <Button type="button" variant="destructive" size="sm" class="h-8 w-8 p-0"
                        @click="emit('deleteExisting', image.id)">
                        <X class="h-4 w-4" />
                    </Button>
                </div>
            </div>

            <div v-for="(file, index) in modelValue" :key="`new-${index}`"
                class="group relative aspect-square overflow-hidden rounded-lg border border-border">
                <img :src="getPreviewUrl(file)" :alt="t('offer_components.upload_preview', { number: index + 1 })"
                    class="h-full w-full object-cover" />
                <div
                    class="absolute inset-0 flex items-center justify-center bg-black/50 opacity-0 transition-opacity group-hover:opacity-100">
                    <Button type="button" variant="destructive" size="sm" class="h-8 w-8 p-0"
                        @click="removeFile(index)">
                        <X class="h-4 w-4" />
                    </Button>
                </div>
            </div>
        </div>

        <div class="flex cursor-pointer flex-col items-center justify-center rounded-xl border-2 border-dashed border-border bg-card p-8 transition-colors hover:border-primary/50 hover:bg-accent/50"
            @click="fileInput?.click()" @dragover.prevent @drop.prevent="handleDrop">
            <div class="flex h-12 w-12 items-center justify-center rounded-full bg-muted">
                <Upload class="h-6 w-6 text-muted-foreground" />
            </div>
            <p class="mt-3 text-sm font-medium text-card-foreground">
                {{ t('offer_components.upload_prompt') }}
            </p>
            <p class="mt-1 text-xs text-muted-foreground">
                {{ t('offer_components.upload_formats') }}
            </p>
        </div>

        <input ref="fileInput" type="file" multiple accept="image/png,image/jpeg,image/webp" class="hidden"
            @change="handleFiles" />
    </div>
</template>
