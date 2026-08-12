<script setup lang="ts">
import type { HTMLAttributes } from "vue"
import { Loader2Icon } from "@lucide/vue"
import { cn } from "@/lib/utils"
import { useLocale } from "@/composables/useLocale"

const props = defineProps<{
  class?: HTMLAttributes["class"]
}>()

const { t } = useLocale()
</script>

<template>
  <Loader2Icon
    role="status"
    :aria-label="t('accessibility.show_password')"
    :class="cn('size-4 animate-spin', props.class)"
  />
</template>
