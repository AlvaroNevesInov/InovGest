<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref, watch } from 'vue';
import { Button } from '@/Components/ui/button';
import { Input } from '@/Components/ui/input';
import { Badge } from '@/Components/ui/badge';
import { Table, TableHeader, TableBody, TableRow, TableHead, TableCell } from '@/Components/ui/table';

const props = defineProps({
    supplierOrders: Object,
    filters: Object
});

const search = ref(props.filters?.search || '');
const statusFilter = ref(props.filters?.status || '');

watch([search, statusFilter], ([newSearch, newStatus]) => {
    router.get(route('supplier-orders.index'), {
        search: newSearch,
        status: newStatus
    }, {
        preserveState: true,
        replace: true
    });
}, { debounce: 300 });

const deleteSupplierOrder = (id) => {
    if (confirm('Tem a certeza que deseja eliminar esta encomenda a fornecedor?')) {
        router.delete(route('supplier-orders.destroy', id));
    }
};

const sendSupplierOrder = (id) => {
    if (confirm('Tem a certeza que deseja enviar esta encomenda ao fornecedor?')) {
        router.post(route('supplier-orders.send', id));
    }
};

const formatPrice = (price) => {
    return new Intl.NumberFormat('pt-PT', {
        style: 'currency',
        currency: 'EUR'
    }).format(price);
};

const formatDate = (date) => {
    return new Date(date).toLocaleDateString('pt-PT');
};

const getStatusBadgeVariant = (status) => {
    switch (status) {
        case 'pending': return 'secondary';
        case 'sent': return 'default';
        case 'received': return 'success';
        default: return 'secondary';
    }
};

const getStatusLabel = (status) => {
    switch (status) {
        case 'pending': return 'Pendente';
        case 'sent': return 'Enviada';
        case 'received': return 'Recebida';
        default: return status;
    }
};
</script>

<template>
    <Head title="Encomendas a Fornecedores" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
                    Encomendas a Fornecedores
                </h2>
                <Link :href="route('supplier-orders.create')">
                    <Button>Nova Encomenda</Button>
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
                                placeholder="Pesquisar por número ou fornecedor..."
                                class="max-w-md"
                            />
                            <select
                                v-model="statusFilter"
                                class="h-10 rounded-md border border-input bg-background px-3 py-2 text-sm"
                            >
                                <option value="">Todos os estados</option>
                                <option value="pending">Pendente</option>
                                <option value="sent">Enviada</option>
                                <option value="received">Recebida</option>
                            </select>
                        </div>

                        <!-- Tabela -->
                        <div v-if="supplierOrders.data.length > 0">
                            <Table>
                                <TableHeader>
                                    <TableRow>
                                        <TableHead>Número</TableHead>
                                        <TableHead>Data</TableHead>
                                        <TableHead>Fornecedor</TableHead>
                                        <TableHead>Encomenda Cliente</TableHead>
                                        <TableHead>Total</TableHead>
                                        <TableHead>Estado</TableHead>
                                        <TableHead class="text-right">Ações</TableHead>
                                    </TableRow>
                                </TableHeader>
                                <TableBody>
                                    <TableRow v-for="supplierOrder in supplierOrders.data" :key="supplierOrder.id">
                                        <TableCell class="font-medium">{{ supplierOrder.number }}</TableCell>
                                        <TableCell>{{ formatDate(supplierOrder.order_date) }}</TableCell>
                                        <TableCell>{{ supplierOrder.supplier?.name }}</TableCell>
                                        <TableCell>
                                            <span v-if="supplierOrder.order">
                                                #{{ supplierOrder.order.number }}
                                            </span>
                                            <span v-else class="text-gray-400">-</span>
                                        </TableCell>
                                        <TableCell>{{ formatPrice(supplierOrder.total) }}</TableCell>
                                        <TableCell>
                                            <Badge :variant="getStatusBadgeVariant(supplierOrder.status)">
                                                {{ getStatusLabel(supplierOrder.status) }}
                                            </Badge>
                                        </TableCell>
                                        <TableCell class="text-right">
                                            <div class="flex justify-end gap-2">
                                                <Link :href="route('supplier-orders.show', supplierOrder.id)">
                                                    <Button variant="ghost" size="sm">Ver</Button>
                                                </Link>
                                                <Link :href="route('supplier-orders.edit', supplierOrder.id)">
                                                    <Button variant="ghost" size="sm">Editar</Button>
                                                </Link>
                                                <a :href="route('supplier-orders.pdf', supplierOrder.id)" target="_blank">
                                                    <Button variant="ghost" size="sm">PDF</Button>
                                                </a>
                                                <Button
                                                    v-if="supplierOrder.status === 'pending'"
                                                    variant="ghost"
                                                    size="sm"
                                                    @click="sendSupplierOrder(supplierOrder.id)"
                                                >
                                                    Enviar
                                                </Button>
                                                <Button
                                                    variant="ghost"
                                                    size="sm"
                                                    @click="deleteSupplierOrder(supplierOrder.id)"
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
                                    A mostrar {{ supplierOrders.from }} a {{ supplierOrders.to }} de {{ supplierOrders.total }} resultados
                                </div>
                                <div class="flex gap-2">
                                    <Link
                                        v-for="link in supplierOrders.links"
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
                            Nenhuma encomenda a fornecedor encontrada.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
