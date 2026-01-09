<script setup>
import { DialogTrigger } from 'radix-vue'

defineProps({
  asChild: Boolean,
  as: String
})
</script>

<template>
  <DialogTrigger :as-child="asChild" :as="as">
    <slot />
  </DialogTrigger>
</template>
