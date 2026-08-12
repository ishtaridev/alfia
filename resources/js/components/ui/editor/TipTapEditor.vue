<script setup lang="ts">
import { useEditor, EditorContent } from '@tiptap/vue-3';
import StarterKit from '@tiptap/starter-kit';
import Underline from '@tiptap/extension-underline';
import Link from '@tiptap/extension-link';
import Placeholder from '@tiptap/extension-placeholder';
import {
    Bold,
    Italic,
    Underline as UnderlineIcon,
    List,
    ListOrdered,
    Heading2,
    Heading3,
    Link as LinkIcon,
    Undo,
    Redo,
} from '@lucide/vue';
import { useLocale } from '@/composables/useLocale';

type Props = {
    modelValue: string | null;
    placeholder?: string;
};

const props = withDefaults(defineProps<Props>(), {
    placeholder: '',
});

const { t } = useLocale();
const displayPlaceholder = props.placeholder || t('editor.placeholder');

const emit = defineEmits<{
    'update:modelValue': [value: string];
}>();

const editor = useEditor({
    content: props.modelValue ?? '',
    extensions: [
        StarterKit.configure({
            heading: {
                levels: [2, 3],
            },
        }),
        Underline,
        Link.configure({
            openOnClick: false,
        }),
        Placeholder.configure({
            placeholder: displayPlaceholder,
        }),
    ],
    onUpdate: ({ editor: e }) => {
        emit('update:modelValue', e.getHTML());
    },
    editorProps: {
        attributes: {
            class: 'tiptap min-h-[200px] w-full rounded-md bg-background px-3 py-2 text-sm ring-offset-background focus-visible:outline-none disabled:cursor-not-allowed disabled:opacity-50',
        },
    },
});

const setLink = () => {
    const url = window.prompt(t('editor.url_prompt'));
    if (editor.value && url !== null) {
        editor.value.chain().focus().setLink({ href: url }).run();
    }
};
</script>

<template>
    <div class="rounded-md border border-input ring-offset-background">
        <div
            v-if="editor"
            class="flex flex-wrap items-center gap-1 border-b border-input p-1"
        >
            <button
                type="button"
                class="inline-flex h-8 w-8 items-center justify-center rounded-sm bg-background text-muted-foreground hover:bg-accent hover:text-accent-foreground"
                :class="{ 'bg-accent text-accent-foreground': editor.isActive('bold') }"
                @click="editor.chain().focus().toggleBold().run()"
            >
                <Bold class="h-4 w-4" />
            </button>
            <button
                type="button"
                class="inline-flex h-8 w-8 items-center justify-center rounded-sm bg-background text-muted-foreground hover:bg-accent hover:text-accent-foreground"
                :class="{ 'bg-accent text-accent-foreground': editor.isActive('italic') }"
                @click="editor.chain().focus().toggleItalic().run()"
            >
                <Italic class="h-4 w-4" />
            </button>
            <button
                type="button"
                class="inline-flex h-8 w-8 items-center justify-center rounded-sm bg-background text-muted-foreground hover:bg-accent hover:text-accent-foreground"
                :class="{ 'bg-accent text-accent-foreground': editor.isActive('underline') }"
                @click="editor.chain().focus().toggleUnderline().run()"
            >
                <UnderlineIcon class="h-4 w-4" />
            </button>

            <div class="mx-1 h-6 w-px bg-border" />

            <button
                type="button"
                class="inline-flex h-8 w-8 items-center justify-center rounded-sm bg-background text-muted-foreground hover:bg-accent hover:text-accent-foreground"
                :class="{ 'bg-accent text-accent-foreground': editor.isActive('heading', { level: 2 }) }"
                @click="editor.chain().focus().toggleHeading({ level: 2 }).run()"
            >
                <Heading2 class="h-4 w-4" />
            </button>
            <button
                type="button"
                class="inline-flex h-8 w-8 items-center justify-center rounded-sm bg-background text-muted-foreground hover:bg-accent hover:text-accent-foreground"
                :class="{ 'bg-accent text-accent-foreground': editor.isActive('heading', { level: 3 }) }"
                @click="editor.chain().focus().toggleHeading({ level: 3 }).run()"
            >
                <Heading3 class="h-4 w-4" />
            </button>

            <div class="mx-1 h-6 w-px bg-border" />

            <button
                type="button"
                class="inline-flex h-8 w-8 items-center justify-center rounded-sm bg-background text-muted-foreground hover:bg-accent hover:text-accent-foreground"
                :class="{ 'bg-accent text-accent-foreground': editor.isActive('bulletList') }"
                @click="editor.chain().focus().toggleBulletList().run()"
            >
                <List class="h-4 w-4" />
            </button>
            <button
                type="button"
                class="inline-flex h-8 w-8 items-center justify-center rounded-sm bg-background text-muted-foreground hover:bg-accent hover:text-accent-foreground"
                :class="{ 'bg-accent text-accent-foreground': editor.isActive('orderedList') }"
                @click="editor.chain().focus().toggleOrderedList().run()"
            >
                <ListOrdered class="h-4 w-4" />
            </button>

            <div class="mx-1 h-6 w-px bg-border" />

            <button
                type="button"
                class="inline-flex h-8 w-8 items-center justify-center rounded-sm bg-background text-muted-foreground hover:bg-accent hover:text-accent-foreground"
                :class="{ 'bg-accent text-accent-foreground': editor.isActive('link') }"
                @click="setLink"
            >
                <LinkIcon class="h-4 w-4" />
            </button>

            <div class="mx-1 h-6 w-px bg-border" />

            <button
                type="button"
                class="inline-flex h-8 w-8 items-center justify-center rounded-sm bg-background text-muted-foreground hover:bg-accent hover:text-accent-foreground disabled:opacity-50"
                :disabled="!editor.can().undo()"
                @click="editor.chain().focus().undo().run()"
            >
                <Undo class="h-4 w-4" />
            </button>
            <button
                type="button"
                class="inline-flex h-8 w-8 items-center justify-center rounded-sm bg-background text-muted-foreground hover:bg-accent hover:text-accent-foreground disabled:opacity-50"
                :disabled="!editor.can().redo()"
                @click="editor.chain().focus().redo().run()"
            >
                <Redo class="h-4 w-4" />
            </button>
        </div>

        <EditorContent :editor="editor" />
    </div>
</template>
