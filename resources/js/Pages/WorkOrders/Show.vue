<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { Button } from '@/Components/ui/button';
import { Badge } from '@/Components/ui/badge';
import { Card, CardHeader, CardTitle, CardContent } from '@/Components/ui/card';

const props = defineProps({
    workOrder: Object
});

const formatDate = (date) => {
    if (!date) return '-';
    return new Date(date).toLocaleDateString('pt-PT');
};

const getStatusLabel = (status) => {
    const labels = {
        'pending': 'Pendente',
        'in_progress': 'Em Progresso',
        'completed': 'Concluída',
        'cancelled': 'Cancelada'
    };
    return labels[status] || status;
};

const getPriorityLabel = (priority) => {
    const labels = {
        'low': 'Baixa',
        'normal': 'Normal',
        'high': 'Alta',
        'urgent': 'Urgente'
    };
    return labels[priority] || priority;
};

const getStatusVariant = (status) => {
    if (status === 'completed') return 'default';
    if (status === 'in_progress') return 'secondary';
    if (status === 'cancelled') return 'destructive';
    return 'outline';
};

const getPriorityVariant = (priority) => {
    if (priority === 'urgent') return 'destructive';
    if (priority === 'high') return 'secondary';
    return 'outline';
};

const deleteWorkOrder = () => {
    if (confirm('Tem a certeza que deseja eliminar esta ordem de trabalho?')) {
        router.delete(route('work-orders.destroy', props.workOrder.id));
    }
};
</script>

<template>
    <Head title="Detalhes da Ordem de Trabalho" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
                    Ordem de Trabalho #{{ workOrder.number }}
                </h2>
                <div class="flex gap-2">
                    <Link :href="route('work-orders.edit', workOrder.id)">
                        <Button>Editar</Button>
                    </Link>
                    <Link :href="route('work-orders.index')">
                        <Button variant="outline">Voltar</Button>
                    </Link>
                </div>
            </div>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-4xl sm:px-6 lg:px-8 space-y-6">
                <!-- Informação do Cabeçalho -->
                <Card>
                    <CardHeader>
                        <div class="flex items-center justify-between">
                            <CardTitle>{{ workOrder.title }}</CardTitle>
                            <div class="flex gap-2">
                                <Badge :variant="getStatusVariant(workOrder.status)">
                                    {{ getStatusLabel(workOrder.status) }}
                                </Badge>
                                <Badge :variant="getPriorityVariant(workOrder.priority)">
                                    {{ getPriorityLabel(workOrder.priority) }}
                                </Badge>
                            </div>
                        </div>
                    </CardHeader>
                    <CardContent>
                        <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                            <div>
                                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Número</p>
                                <p class="mt-1 text-lg font-semibold">#{{ workOrder.number }}</p>
                            </div>

                            <div>
                                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Data de Trabalho</p>
                                <p class="mt-1 text-base">{{ formatDate(workOrder.work_date) }}</p>
                            </div>

                            <div>
                                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Cliente</p>
                                <Link :href="route('entities.show', workOrder.entity.id)" class="mt-1 text-base text-blue-600 hover:underline">
                                    {{ workOrder.entity?.name }}
                                </Link>
                            </div>

                            <div v-if="workOrder.order">
                                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Encomenda Associada</p>
                                <Link :href="route('orders.show', workOrder.order.id)" class="mt-1 text-base text-blue-600 hover:underline">
                                    #{{ workOrder.order.number }}
                                </Link>
                            </div>

                            <div>
                                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Data de Início</p>
                                <p class="mt-1 text-base">{{ formatDate(workOrder.start_date) }}</p>
                            </div>

                            <div>
                                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Data Prevista de Conclusão</p>
                                <p class="mt-1 text-base">{{ formatDate(workOrder.due_date) }}</p>
                            </div>

                            <div v-if="workOrder.completed_date">
                                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Data de Conclusão</p>
                                <p class="mt-1 text-base">{{ formatDate(workOrder.completed_date) }}</p>
                            </div>
                        </div>
                    </CardContent>
                </Card>

                <!-- Descrição -->
                <Card v-if="workOrder.description">
                    <CardHeader>
                        <CardTitle>Descrição</CardTitle>
                    </CardHeader>
                    <CardContent>
                        <p class="text-base whitespace-pre-wrap">{{ workOrder.description }}</p>
                    </CardContent>
                </Card>

                <!-- Observações -->
                <Card v-if="workOrder.notes">
                    <CardHeader>
                        <CardTitle>Observações</CardTitle>
                    </CardHeader>
                    <CardContent>
                        <p class="text-base whitespace-pre-wrap">{{ workOrder.notes }}</p>
                    </CardContent>
                </Card>

                <!-- Ações -->
                <Card>
                    <CardHeader>
                        <CardTitle>Ações</CardTitle>
                    </CardHeader>
                    <CardContent>
                        <div class="flex flex-wrap gap-4">
                            <Link :href="route('work-orders.edit', workOrder.id)">
                                <Button variant="outline">Editar Ordem de Trabalho</Button>
                            </Link>

                            <Button
                                variant="destructive"
                                @click="deleteWorkOrder"
                            >
                                Eliminar Ordem de Trabalho
                            </Button>
                        </div>
                    </CardContent>
                </Card>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
