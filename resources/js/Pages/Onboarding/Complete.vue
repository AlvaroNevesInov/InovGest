<template>
    <Head title="Configuração Concluída" />

    <div class="min-h-screen bg-gradient-to-br from-green-50 to-blue-100 py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-3xl mx-auto">
            <!-- Success Card -->
            <div class="bg-white shadow-xl rounded-lg overflow-hidden">
                <!-- Success Header -->
                <div class="bg-gradient-to-r from-green-600 to-blue-600 px-8 py-12 text-center">
                    <div class="flex justify-center mb-4">
                        <div class="w-20 h-20 bg-white rounded-full flex items-center justify-center">
                            <svg class="w-12 h-12 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                        </div>
                    </div>
                    <h1 class="text-4xl font-bold text-white mb-4">
                        Configuração Concluída!
                    </h1>
                    <p class="text-xl text-blue-100">
                        A sua conta está pronta para começar a usar
                    </p>
                </div>

                <!-- Content -->
                <div class="px-8 py-8">
                    <!-- Progress Summary -->
                    <div class="mb-8">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4 text-center">
                            Resumo da Configuração
                        </h3>
                        <div class="grid grid-cols-3 gap-4">
                            <div class="text-center p-4 bg-green-50 rounded-lg border border-green-200">
                                <div class="text-3xl font-bold text-green-600 mb-1">
                                    {{ progress.completed_tasks }}
                                </div>
                                <div class="text-xs text-gray-600">
                                    Tarefas Completas
                                </div>
                            </div>
                            <div class="text-center p-4 bg-blue-50 rounded-lg border border-blue-200">
                                <div class="text-3xl font-bold text-blue-600 mb-1">
                                    {{ progress.completion_percentage }}%
                                </div>
                                <div class="text-xs text-gray-600">
                                    Progresso
                                </div>
                            </div>
                            <div class="text-center p-4 bg-purple-50 rounded-lg border border-purple-200">
                                <div class="text-3xl font-bold text-purple-600 mb-1">
                                    {{ progress.total_tasks - progress.completed_tasks }}
                                </div>
                                <div class="text-xs text-gray-600">
                                    Tarefas Opcionais
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Next Steps -->
                    <div class="mb-8">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">
                            Próximos Passos Sugeridos
                        </h3>
                        <div class="space-y-3">
                            <div class="flex items-start gap-3 p-4 bg-gray-50 rounded-lg border border-gray-200">
                                <div class="flex-shrink-0 w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center text-blue-600 font-semibold text-sm">
                                    1
                                </div>
                                <div class="flex-1">
                                    <h4 class="text-sm font-semibold text-gray-900">Criar Primeira Entidade</h4>
                                    <p class="text-xs text-gray-600 mt-1">
                                        Adicione a sua primeira entidade comercial (cliente ou fornecedor)
                                    </p>
                                </div>
                                <Link
                                    :href="route('entities.index')"
                                    class="text-sm text-blue-600 hover:text-blue-700 font-medium"
                                >
                                    Ir
                                </Link>
                            </div>

                            <div class="flex items-start gap-3 p-4 bg-gray-50 rounded-lg border border-gray-200">
                                <div class="flex-shrink-0 w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center text-blue-600 font-semibold text-sm">
                                    2
                                </div>
                                <div class="flex-1">
                                    <h4 class="text-sm font-semibold text-gray-900">Configurar Artigos</h4>
                                    <p class="text-xs text-gray-600 mt-1">
                                        Adicione os seus produtos ou serviços ao catálogo
                                    </p>
                                </div>
                                <Link
                                    :href="route('settings.articles.index')"
                                    class="text-sm text-blue-600 hover:text-blue-700 font-medium"
                                >
                                    Ir
                                </Link>
                            </div>

                            <div class="flex items-start gap-3 p-4 bg-gray-50 rounded-lg border border-gray-200">
                                <div class="flex-shrink-0 w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center text-blue-600 font-semibold text-sm">
                                    3
                                </div>
                                <div class="flex-1">
                                    <h4 class="text-sm font-semibold text-gray-900">Ver Checklist Completa</h4>
                                    <p class="text-xs text-gray-600 mt-1">
                                        Aceda à checklist completa com todas as tarefas recomendadas
                                    </p>
                                </div>
                                <Link
                                    :href="route('onboarding.checklist')"
                                    class="text-sm text-blue-600 hover:text-blue-700 font-medium"
                                >
                                    Ver
                                </Link>
                            </div>
                        </div>
                    </div>

                    <!-- CTA Buttons -->
                    <div class="flex items-center justify-center gap-4 pt-6 border-t">
                        <Link
                            :href="route('dashboard')"
                            class="px-8 py-3 bg-blue-600 text-white rounded-lg font-semibold hover:bg-blue-700 transition-colors shadow-md hover:shadow-lg"
                        >
                            Ir para Dashboard
                        </Link>
                        <Link
                            :href="route('onboarding.checklist')"
                            class="px-8 py-3 bg-white border-2 border-gray-300 text-gray-700 rounded-lg font-semibold hover:bg-gray-50 transition-colors"
                        >
                            Ver Checklist
                        </Link>
                    </div>
                </div>
            </div>

            <!-- Celebration Animation (optional) -->
            <div class="mt-8 text-center text-gray-600">
                <p class="text-sm">
                    Obrigado por escolher o InovGest! 🎉
                </p>
            </div>
        </div>
    </div>
</template>

<script setup>
import { Head, Link } from '@inertiajs/vue3';

defineProps({
    tenant: Object,
    progress: Object,
});
</script>
