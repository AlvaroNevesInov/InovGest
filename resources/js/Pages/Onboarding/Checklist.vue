<template>
    <Head title="Checklist de Configuração" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Checklist de Primeira Configuração
                </h2>
                <div class="flex items-center gap-2">
                    <span class="text-sm text-gray-600">
                        {{ progress.completed_tasks }} de {{ progress.total_tasks }} completas
                    </span>
                    <div class="w-32 h-2 bg-gray-200 rounded-full overflow-hidden">
                        <div
                            class="h-full bg-green-600 transition-all duration-500"
                            :style="{ width: `${progress.completion_percentage}%` }"
                        ></div>
                    </div>
                </div>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
                <!-- Progress Card -->
                <div class="bg-white shadow-md rounded-lg p-6 mb-6">
                    <div class="flex items-center justify-between mb-4">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900">
                                Progresso Geral
                            </h3>
                            <p class="text-sm text-gray-600 mt-1">
                                Complete todas as tarefas para configurar totalmente o sistema
                            </p>
                        </div>
                        <div class="text-right">
                            <div class="text-3xl font-bold text-gray-900">
                                {{ progress.completion_percentage }}%
                            </div>
                            <div class="text-xs text-gray-500 mt-1">
                                Completo
                            </div>
                        </div>
                    </div>

                    <!-- Progress Bar -->
                    <div class="w-full bg-gray-200 rounded-full h-4 overflow-hidden">
                        <div
                            class="h-full bg-gradient-to-r from-blue-600 to-green-600 transition-all duration-500"
                            :style="{ width: `${progress.completion_percentage}%` }"
                        ></div>
                    </div>

                    <!-- Quick Stats -->
                    <div class="grid grid-cols-3 gap-4 mt-6">
                        <div class="text-center p-3 bg-blue-50 rounded-lg">
                            <div class="text-2xl font-bold text-blue-600">
                                {{ progress.tasks.filter(t => t.is_required && !t.is_completed).length }}
                            </div>
                            <div class="text-xs text-gray-600 mt-1">
                                Obrigatórias Pendentes
                            </div>
                        </div>
                        <div class="text-center p-3 bg-green-50 rounded-lg">
                            <div class="text-2xl font-bold text-green-600">
                                {{ progress.completed_tasks }}
                            </div>
                            <div class="text-xs text-gray-600 mt-1">
                                Completadas
                            </div>
                        </div>
                        <div class="text-center p-3 bg-gray-50 rounded-lg">
                            <div class="text-2xl font-bold text-gray-600">
                                {{ progress.tasks.filter(t => !t.is_required && !t.is_completed).length }}
                            </div>
                            <div class="text-xs text-gray-600 mt-1">
                                Opcionais Pendentes
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Required Tasks -->
                <div class="bg-white shadow-md rounded-lg p-6 mb-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center gap-2">
                        <span class="flex items-center justify-center w-6 h-6 bg-red-100 rounded-full text-red-600 text-xs font-bold">
                            !
                        </span>
                        Tarefas Obrigatórias
                    </h3>
                    <div class="space-y-3">
                        <div
                            v-for="task in requiredTasks"
                            :key="task.id"
                            class="flex items-start gap-4 p-4 border rounded-lg transition-all"
                            :class="task.is_completed ? 'bg-green-50 border-green-200' : 'bg-white border-gray-200 hover:border-blue-300'"
                        >
                            <button
                                @click="toggleTask(task.id)"
                                class="flex-shrink-0 mt-1"
                                :disabled="processing"
                            >
                                <div
                                    class="w-6 h-6 rounded-full border-2 flex items-center justify-center transition-all"
                                    :class="task.is_completed
                                        ? 'bg-green-600 border-green-600'
                                        : 'bg-white border-gray-300 hover:border-blue-500'"
                                >
                                    <svg
                                        v-if="task.is_completed"
                                        class="w-4 h-4 text-white"
                                        fill="currentColor"
                                        viewBox="0 0 20 20"
                                    >
                                        <path
                                            fill-rule="evenodd"
                                            d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                            clip-rule="evenodd"
                                        />
                                    </svg>
                                </div>
                            </button>
                            <div class="flex-1">
                                <h4
                                    class="text-sm font-semibold transition-all"
                                    :class="task.is_completed ? 'text-green-900 line-through' : 'text-gray-900'"
                                >
                                    {{ task.title }}
                                </h4>
                                <p class="text-xs text-gray-600 mt-1">
                                    {{ task.description }}
                                </p>
                                <div v-if="task.completed_at" class="text-xs text-green-600 mt-2">
                                    Completada em {{ formatDate(task.completed_at) }}
                                </div>
                            </div>
                            <span
                                class="flex-shrink-0 px-2 py-1 text-xs font-medium rounded-full"
                                :class="task.is_completed ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'"
                            >
                                {{ task.is_completed ? 'Completa' : 'Obrigatória' }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Optional Tasks -->
                <div class="bg-white shadow-md rounded-lg p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center gap-2">
                        <span class="flex items-center justify-center w-6 h-6 bg-blue-100 rounded-full text-blue-600 text-xs font-bold">
                            +
                        </span>
                        Tarefas Opcionais
                    </h3>
                    <div class="space-y-3">
                        <div
                            v-for="task in optionalTasks"
                            :key="task.id"
                            class="flex items-start gap-4 p-4 border rounded-lg transition-all"
                            :class="task.is_completed ? 'bg-green-50 border-green-200' : 'bg-white border-gray-200 hover:border-blue-300'"
                        >
                            <button
                                @click="toggleTask(task.id)"
                                class="flex-shrink-0 mt-1"
                                :disabled="processing"
                            >
                                <div
                                    class="w-6 h-6 rounded-full border-2 flex items-center justify-center transition-all"
                                    :class="task.is_completed
                                        ? 'bg-green-600 border-green-600'
                                        : 'bg-white border-gray-300 hover:border-blue-500'"
                                >
                                    <svg
                                        v-if="task.is_completed"
                                        class="w-4 h-4 text-white"
                                        fill="currentColor"
                                        viewBox="0 0 20 20"
                                    >
                                        <path
                                            fill-rule="evenodd"
                                            d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                            clip-rule="evenodd"
                                        />
                                    </svg>
                                </div>
                            </button>
                            <div class="flex-1">
                                <h4
                                    class="text-sm font-semibold transition-all"
                                    :class="task.is_completed ? 'text-green-900 line-through' : 'text-gray-900'"
                                >
                                    {{ task.title }}
                                </h4>
                                <p class="text-xs text-gray-600 mt-1">
                                    {{ task.description }}
                                </p>
                                <div v-if="task.completed_at" class="text-xs text-green-600 mt-2">
                                    Completada em {{ formatDate(task.completed_at) }}
                                </div>
                            </div>
                            <span
                                class="flex-shrink-0 px-2 py-1 text-xs font-medium rounded-full"
                                :class="task.is_completed ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-600'"
                            >
                                {{ task.is_completed ? 'Completa' : 'Opcional' }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Action Button -->
                <div class="mt-6 text-center">
                    <Link
                        :href="route('dashboard')"
                        class="inline-flex items-center gap-2 px-6 py-3 bg-blue-600 text-white rounded-lg font-semibold hover:bg-blue-700 transition-colors"
                    >
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        Voltar ao Dashboard
                    </Link>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { ref, computed } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

const props = defineProps({
    tenant: Object,
    progress: Object,
});

const processing = ref(false);

const requiredTasks = computed(() => {
    return props.progress.tasks.filter(task => task.is_required);
});

const optionalTasks = computed(() => {
    return props.progress.tasks.filter(task => !task.is_required);
});

const toggleTask = (taskId) => {
    if (processing.value) return;

    processing.value = true;

    router.post(
        route('onboarding.task.toggle', taskId),
        {},
        {
            preserveState: true,
            preserveScroll: true,
            onFinish: () => {
                processing.value = false;
            },
        }
    );
};

const formatDate = (dateString) => {
    if (!dateString) return '';
    const date = new Date(dateString);
    return date.toLocaleDateString('pt-PT', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
    });
};
</script>
