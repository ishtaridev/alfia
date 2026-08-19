<script setup lang="ts">
import { computed, onMounted, onUnmounted, ref, watch } from 'vue';
import { X, ChevronLeft, ChevronRight } from '@lucide/vue';

type Image = {
    id: number;
    url: string;
    order?: number;
};

const props = defineProps<{
    images: Image[];
    initialIndex?: number;
    open: boolean;
}>();

const emit = defineEmits<{
    close: [];
}>();

const currentIndex = ref(props.initialIndex ?? 0);

const currentImage = computed(() => props.images[currentIndex.value]);
const hasPrev = computed(() => currentIndex.value > 0);
const hasNext = computed(() => currentIndex.value < props.images.length - 1);
const showNavigation = computed(() => props.images.length > 1);

const prev = () => {
    if (hasPrev.value) {
        currentIndex.value--;
    }
};

const next = () => {
    if (hasNext.value) {
        currentIndex.value++;
    }
};

const handleKeydown = (e: KeyboardEvent) => {
    if (!props.open) return;

    switch (e.key) {
        case 'Escape':
            emit('close');
            break;
        case 'ArrowLeft':
            prev();
            break;
        case 'ArrowRight':
            next();
            break;
    }
};

const handleBackdropClick = (e: MouseEvent) => {
    if (e.target === e.currentTarget) {
        emit('close');
    }
};

watch(() => props.open, (isOpen) => {
    if (isOpen) {
        currentIndex.value = props.initialIndex ?? 0;
        document.body.style.overflow = 'hidden';
    } else {
        document.body.style.overflow = '';
    }
});

watch(() => props.initialIndex, (index) => {
    if (index !== undefined) {
        currentIndex.value = index;
    }
});

onMounted(() => {
    document.addEventListener('keydown', handleKeydown);
});

onUnmounted(() => {
    document.removeEventListener('keydown', handleKeydown);
    document.body.style.overflow = '';
});
</script>

<template>
    <Teleport to="body">
        <Transition
            enter-active-class="duration-200 ease-out"
            enter-from-class="opacity-0"
            enter-to-class="opacity-100"
            leave-active-class="duration-150 ease-in"
            leave-from-class="opacity-100"
            leave-to-class="opacity-0"
        >
            <div
                v-if="open"
                class="fixed inset-0 z-50 flex items-center justify-center bg-black/80 backdrop-blur-sm"
                @click="handleBackdropClick"
            >
                <!-- Close button -->
                <button
                    class="absolute right-4 top-4 z-10 flex h-10 w-10 items-center justify-center rounded-full bg-white/10 text-white transition-colors hover:bg-white/20"
                    @click="emit('close')"
                >
                    <X class="h-5 w-5" />
                </button>

                <!-- Image counter -->
                <div
                    v-if="showNavigation"
                    class="absolute left-4 top-4 z-10 rounded-full bg-white/10 px-3 py-1 text-sm text-white backdrop-blur-sm"
                >
                    {{ currentIndex + 1 }} / {{ images.length }}
                </div>

                <!-- Previous button -->
                <button
                    v-if="showNavigation && hasPrev"
                    class="absolute left-4 z-10 flex h-12 w-12 items-center justify-center rounded-full bg-white/10 text-white transition-colors hover:bg-white/20"
                    @click.stop="prev"
                >
                    <ChevronLeft class="h-6 w-6" />
                </button>

                <!-- Image -->
                <Transition
                    enter-active-class="duration-200 ease-out"
                    enter-from-class="scale-95 opacity-0"
                    enter-to-class="scale-100 opacity-100"
                    leave-active-class="duration-150 ease-in"
                    leave-from-class="scale-100 opacity-100"
                    leave-to-class="scale-95 opacity-0"
                    mode="out-in"
                >
                    <img
                        :key="currentImage.id"
                        :src="currentImage.url"
                        :alt="`Image ${(currentImage.order ?? currentIndex) + 1}`"
                        class="max-h-[85vh] max-w-[90vw] rounded-lg object-contain shadow-2xl"
                    />
                </Transition>

                <!-- Next button -->
                <button
                    v-if="showNavigation && hasNext"
                    class="absolute right-4 z-10 flex h-12 w-12 items-center justify-center rounded-full bg-white/10 text-white transition-colors hover:bg-white/20"
                    @click.stop="next"
                >
                    <ChevronRight class="h-6 w-6" />
                </button>
            </div>
        </Transition>
    </Teleport>
</template>
