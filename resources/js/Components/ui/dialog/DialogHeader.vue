<script setup>
defineProps({
  class: String
})
</script>

<template>
  <div :class="['flex flex-col space-y-1.5 text-center sm:text-left', $props.class]">
    <slot />
  </div>
</template>
