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
        default: 'bg-primary text-primary-foreground hover:bg-primary/80',
        secondary: 'bg-secondary text-secondary-foreground hover:bg-secondary/80',
        success: 'bg-green-500 text-white hover:bg-green-600',
        destructive: 'bg-destructive text-destructive-foreground hover:bg-destructive/80',
        outline: 'text-foreground border border-input bg-background hover:bg-accent hover:text-accent-foreground',
        warning: 'bg-orange-500 text-white hover:bg-orange-600',
    };
    return variants[props.variant] || variants.default;
});
</script>

<template>
    <span
        :class="[
            'inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-semibold transition-colors focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2',
            variantClasses
        ]"
    >
        <slot />
    </span>
</template>
