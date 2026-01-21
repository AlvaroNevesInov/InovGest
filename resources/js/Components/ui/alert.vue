<script setup>
import { computed } from 'vue';

const props = defineProps({
    variant: {
        type: String,
        default: 'default'
    }
});

const variantClasses = computed(() => {
    const variants = {
        default: 'bg-background text-foreground border-gray-200 dark:border-gray-800',
        destructive: 'border-destructive/50 text-destructive dark:border-destructive [&>svg]:text-destructive',
    };
    return variants[props.variant] || variants.default;
});
</script>

<template>
    <div
        :class="[
            'relative w-full rounded-lg border p-4',
            variantClasses
        ]"
        role="alert"
    >
        <slot />
    </div>
</template>
