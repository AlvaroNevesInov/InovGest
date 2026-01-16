<template>
    <div class="min-h-screen bg-gray-50 py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-3xl mx-auto">
            <!-- Progress Bar -->
            <div class="mb-8">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-sm font-medium text-gray-700">
                        Passo {{ currentStep }} de {{ totalSteps }}
                    </span>
                    <span class="text-sm text-gray-500">
                        {{ Math.round((currentStep / totalSteps) * 100) }}% completo
                    </span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-2">
                    <div
                        class="bg-blue-600 h-2 rounded-full transition-all duration-300"
                        :style="{ width: `${(currentStep / totalSteps) * 100}%` }"
                    ></div>
                </div>
            </div>

            <!-- Steps Indicator -->
            <div class="flex items-center justify-between mb-8">
                <div
                    v-for="step in steps"
                    :key="step.number"
                    class="flex items-center"
                    :class="{ 'flex-1': step.number < totalSteps }"
                >
                    <div
                        class="flex items-center justify-center w-10 h-10 rounded-full border-2 transition-all"
                        :class="
                            step.number < currentStep
                                ? 'bg-blue-600 border-blue-600 text-white'
                                : step.number === currentStep
                                ? 'bg-white border-blue-600 text-blue-600'
                                : 'bg-white border-gray-300 text-gray-400'
                        "
                    >
                        <svg
                            v-if="step.number < currentStep"
                            class="w-6 h-6"
                            fill="currentColor"
                            viewBox="0 0 20 20"
                        >
                            <path
                                fill-rule="evenodd"
                                d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                clip-rule="evenodd"
                            />
                        </svg>
                        <span v-else>{{ step.number }}</span>
                    </div>
                    <div
                        v-if="step.number < totalSteps"
                        class="flex-1 h-0.5 mx-2"
                        :class="
                            step.number < currentStep ? 'bg-blue-600' : 'bg-gray-300'
                        "
                    ></div>
                </div>
            </div>

            <!-- Content Card -->
            <div class="bg-white shadow-md rounded-lg p-8">
                <h2 class="text-2xl font-bold text-gray-900 mb-2">
                    {{ title }}
                </h2>
                <p class="text-gray-600 mb-6">
                    {{ description }}
                </p>

                <slot />

                <!-- Navigation Buttons -->
                <div class="flex items-center justify-between mt-8 pt-6 border-t">
                    <button
                        v-if="showBackButton"
                        @click="$emit('back')"
                        type="button"
                        class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50 transition-colors"
                    >
                        Voltar
                    </button>
                    <div v-else></div>

                    <div class="flex items-center gap-4">
                        <button
                            v-if="showSkipButton"
                            @click="$emit('skip')"
                            type="button"
                            class="px-4 py-2 text-gray-600 hover:text-gray-900 transition-colors"
                        >
                            Saltar por agora
                        </button>
                        <button
                            @click="$emit('next')"
                            type="button"
                            class="px-6 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors"
                        >
                            {{ nextButtonText }}
                        </button>
                    </div>
                </div>
            </div>

            <!-- Help Text -->
            <div class="mt-4 text-center text-sm text-gray-500">
                Precisa de ajuda? <a href="#" class="text-blue-600 hover:underline">Contacte o suporte</a>
            </div>
        </div>
    </div>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
    currentStep: {
        type: Number,
        required: true,
    },
    totalSteps: {
        type: Number,
        default: 3,
    },
    title: {
        type: String,
        required: true,
    },
    description: {
        type: String,
        required: true,
    },
    showBackButton: {
        type: Boolean,
        default: true,
    },
    showSkipButton: {
        type: Boolean,
        default: false,
    },
    nextButtonText: {
        type: String,
        default: 'Continuar',
    },
});

defineEmits(['next', 'back', 'skip']);

const steps = computed(() => {
    return Array.from({ length: props.totalSteps }, (_, i) => ({
        number: i + 1,
    }));
});
</script>
