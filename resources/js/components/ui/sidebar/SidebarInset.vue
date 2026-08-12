<script setup lang="ts">
import type { HTMLAttributes } from "vue"
import { computed } from "vue"
import { cn } from "@/lib/utils"
import { useLocale } from "@/composables/useLocale"

const props = defineProps<{
  class?: HTMLAttributes["class"]
}>()

const { locale } = useLocale()
const isRtl = computed(() => locale.value === 'ar')
</script>

<template>
  <main
    data-slot="sidebar-inset"
    :class="cn(
      'bg-background relative flex w-full flex-1 flex-col',
      'md:peer-data-[variant=inset]:m-2 md:peer-data-[variant=inset]:rounded-xl md:peer-data-[variant=inset]:shadow-sm',
      isRtl
        ? 'md:peer-data-[variant=inset]:mr-0 md:peer-data-[variant=inset]:peer-data-[state=collapsed]:mr-2'
        : 'md:peer-data-[variant=inset]:ml-0 md:peer-data-[variant=inset]:peer-data-[state=collapsed]:ml-2',
      props.class,
    )"
  >
    <slot />
  </main>
</template>
