<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref, watch } from 'vue';
import { Button } from '@/Components/ui/button';
import { Input } from '@/Components/ui/input';
import { Badge } from '@/Components/ui/badge';
import { Table, TableHeader, TableBody, TableRow, TableHead, TableCell } from '@/Components/ui/table';

const props = defineProps({
    workOrders: Object,
    filters: Object
});

const search = ref(props.filters?.search || '');
const statusFilter = ref(props.filters?.status || 'all');
const priorityFilter = ref(props.filters?.priority || 'all');

watch([search, statusFilter, priorityFilter], ([newSearch, newStatus, newPriority]) => {
    router.get(route('work-orders.index'), {
        search: newSearch,
        status: newStatus,
        priority: newPriority
    }, {
        preserveState: true,
        replace: true
    });
}, { debounce: 300 });

const deleteWorkOrder = (id) => {
    if (confirm('Tem a certeza que deseja eliminar esta ordem de trabalho?')) {
        router.delete(route('work-orders.destroy', id));
    }
};

const formatDate = (date) => {
    return new Date(date).toLocaleDateString('pt-PT');
};

const getStatusBadgeVariant = (status) => {
    switch (status) {
        case 'pending': return 'secondary';
        case 'in_progress': return 'default';
        case 'completed': return 'success';
        case 'cancelled': return 'destructive';
        default: return 'secondary';
    }
};

const getStatusLabel = (status) => {
    switch (status) {
        case 'pending': return 'Pendente';
        case 'in_progress': return 'Em Progresso';
        case 'completed': return 'Completa';
        case 'cancelled': return 'Cancelada';
        default: return status;
    }
};

const getPriorityBadgeVariant = (priority) => {
    switch (priority) {
        case 'low': return 'secondary';
        case 'normal': return 'default';
        case 'high': return 'warning';
        case 'urgent': return 'destructive';
        default: return 'secondary';
    }
};

const getPriorityLabel = (priority) => {
    switch (priority) {
        case 'low': return 'Baixa';
        case 'normal': return 'Normal';
        case 'high': return 'Alta';
        case 'urgent': return 'Urgente';
        default: return priority;
    }
};
</script>

<template>
    <Head title="Ordens de Trabalho" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
                    Ordens de Trabalho
                </h2>
                <Link :href="route('work-orders.create')">
                    <Button>Nova Ordem de Trabalho</Button>
                </Link>
            </div>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg dark:bg-gray-800">
                    <div class="p-6">
                        <!-- Filtros -->
                        <div class="mb-6 flex flex-col gap-4 sm:flex-row">
                            <Input
                                v-model="search"
                                type="text"
                                placeholder="Pesquisar por número ou cliente..."
                                class="max-w-md"
                            />
                            <select
                                v-model="statusFilter"
                                class="h-10 rounded-md border border-input bg-background px-3 py-2 text-sm"
                            >
                                <option value="all">Todos os estados</option>
                                <option value="pending">Pendente</option>
                                <option value="in_progress">Em Progresso</option>
                                <option value="completed">Completa</option>
                                <option value="cancelled">Cancelada</option>
                            </select>
                            <select
                                v-model="priorityFilter"
                                class="h-10 rounded-md border border-input bg-background px-3 py-2 text-sm"
                            >
                                <option value="all">Todas as prioridades</option>
                                <option value="low">Baixa</option>
                                <option value="normal">Normal</option>
                                <option value="high">Alta</option>
                                <option value="urgent">Urgente</option>
                            </select>
                        </div>

                        <!-- Tabela -->
                        <div v-if="workOrders.data.length > 0">
                            <Table>
                                <TableHeader>
                                    <TableRow>
                                        <TableHead>Número</TableHead>
                                        <TableHead>Data Trabalho</TableHead>
                                        <TableHead>Cliente</TableHead>
                                        <TableHead>Encomenda</TableHead>
                                        <TableHead>Prioridade</TableHead>
                                        <TableHead>Estado</TableHead>
                                        <TableHead class="text-right">Ações</TableHead>
                                    </TableRow>
                                </TableHeader>
                                <TableBody>
                                    <TableRow v-for="workOrder in workOrders.data" :key="workOrder.id">
                                        <TableCell class="font-medium">{{ workOrder.number }}</TableCell>
                                        <TableCell>{{ formatDate(workOrder.work_date) }}</TableCell>
                                        <TableCell>{{ workOrder.entity?.name }}</TableCell>
                                        <TableCell>
                                            <span v-if="workOrder.order">
                                                #{{ workOrder.order.number }}
                                            </span>
                                            <span v-else class="text-gray-400">-</span>
                                        </TableCell>
                                        <TableCell>
                                            <Badge :variant="getPriorityBadgeVariant(workOrder.priority)">
                                                {{ getPriorityLabel(workOrder.priority) }}
                                            </Badge>
                                        </TableCell>
                                        <TableCell>
                                            <Badge :variant="getStatusBadgeVariant(workOrder.status)">
                                                {{ getStatusLabel(workOrder.status) }}
                                            </Badge>
                                        </TableCell>
                                        <TableCell class="text-right">
                                            <div class="flex justify-end gap-2">
                                                <Link :href="route('work-orders.show', workOrder.id)">
                                                    <Button variant="ghost" size="sm">Ver</Button>
                                                </Link>
                                                <Link :href="route('work-orders.edit', workOrder.id)">
                                                    <Button variant="ghost" size="sm">Editar</Button>
                                                </Link>
                                                <Button
                                                    variant="ghost"
                                                    size="sm"
                                                    @click="deleteWorkOrder(workOrder.id)"
                                                >
                                                    Eliminar
                                                </Button>
                                            </div>
                                        </TableCell>
                                    </TableRow>
                                </TableBody>
                            </Table>

                            <!-- Paginação -->
                            <div class="mt-4 flex items-center justify-between">
                                <div class="text-sm text-gray-700 dark:text-gray-300">
                                    A mostrar {{ workOrders.from }} a {{ workOrders.to }} de {{ workOrders.total }} resultados
                                </div>
                                <div class="flex gap-2">
                                    <Link
                                        v-for="link in workOrders.links"
                                        :key="link.label"
                                        :href="link.url"
                                        :class="[
                                            'px-3 py-2 text-sm rounded-md',
                                            link.active
                                                ? 'bg-primary text-primary-foreground'
                                                : 'bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-600',
                                            !link.url && 'opacity-50 cursor-not-allowed'
                                        ]"
                                        v-html="link.label"
                                    />
                                </div>
                            </div>
                        </div>
                        <div v-else class="py-12 text-center text-gray-500 dark:text-gray-400">
                            Nenhuma ordem de trabalho encontrada.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
