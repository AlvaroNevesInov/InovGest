<script setup>
import { DialogTitle } from 'radix-vue'

defineProps({
  class: String,
  asChild: Boolean,
  as: String
})
</script>

<template>
  <DialogTitle
    :class="['text-lg font-semibold leading-none tracking-tight', $props.class]"
    :as-child="asChild"
    :as="as"
  >
    <slot />
  </DialogTitle>
</template>
