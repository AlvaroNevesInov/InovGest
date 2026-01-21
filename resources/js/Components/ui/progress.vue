<script setup>
/**
 * Progress Bar Component
 * Displays a progress bar with customizable value and max
 */
import { computed } from 'vue';

const props = defineProps({
    value: {
        type: Number,
        default: 0
    },
    max: {
        type: Number,
        default: 100
    }
});

const percentage = computed(() => {
    return Math.min(Math.max((props.value / props.max) * 100, 0), 100);
});
</script>

<template>
    <div
        class="relative h-2 w-full overflow-hidden rounded-full bg-gray-200 dark:bg-gray-800"
        role="progressbar"
        :aria-valuenow="value"
        :aria-valuemax="max"
    >
        <div
            class="h-full bg-primary transition-all duration-300 ease-in-out"
            :style="{ width: `${percentage}%` }"
        />
    </div>
</template>
