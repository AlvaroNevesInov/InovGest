<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref, watch } from 'vue';
import { Button } from '@/Components/ui/button';
import { Input } from '@/Components/ui/input';
import { Badge } from '@/Components/ui/badge';
import { Table, TableHeader, TableBody, TableRow, TableHead, TableCell } from '@/Components/ui/table';

const props = defineProps({
    orders: Object,
    filters: Object
});

const search = ref(props.filters?.search || '');
const statusFilter = ref(props.filters?.status || '');

watch([search, statusFilter], ([newSearch, newStatus]) => {
    router.get(route('orders.index'), {
        search: newSearch,
        status: newStatus
    }, {
        preserveState: true,
        replace: true
    });
}, { debounce: 300 });

const deleteOrder = (id) => {
    if (confirm('Tem a certeza que deseja eliminar esta encomenda?')) {
        router.delete(route('orders.destroy', id));
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
    return status === 'closed' ? 'default' : 'secondary';
};

const getStatusLabel = (status) => {
    return status === 'closed' ? 'Fechada' : 'Rascunho';
};
</script>

<template>
    <Head title="Encomendas" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
                    Encomendas
                </h2>
                <Link :href="route('orders.create')">
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
                                placeholder="Pesquisar por número ou cliente..."
                                class="max-w-md"
                            />
                            <select
                                v-model="statusFilter"
                                class="h-10 rounded-md border border-input bg-background px-3 py-2 text-sm"
                            >
                                <option value="">Todos os estados</option>
                                <option value="draft">Rascunho</option>
                                <option value="closed">Fechadas</option>
                            </select>
                        </div>

                        <!-- Tabela -->
                        <div v-if="orders.data.length > 0">
                            <Table>
                                <TableHeader>
                                    <TableRow>
                                        <TableHead>Número</TableHead>
                                        <TableHead>Data</TableHead>
                                        <TableHead>Cliente</TableHead>
                                        <TableHead>Proposta</TableHead>
                                        <TableHead>Total</TableHead>
                                        <TableHead>Estado</TableHead>
                                        <TableHead class="text-right">Ações</TableHead>
                                    </TableRow>
                                </TableHeader>
                                <TableBody>
                                    <TableRow v-for="order in orders.data" :key="order.id">
                                        <TableCell class="font-medium">#{{ order.number }}</TableCell>
                                        <TableCell>{{ formatDate(order.order_date) }}</TableCell>
                                        <TableCell>{{ order.entity?.name }}</TableCell>
                                        <TableCell>
                                            <span v-if="order.proposal_id">#{{ order.proposal?.number }}</span>
                                            <span v-else class="text-gray-400">-</span>
                                        </TableCell>
                                        <TableCell class="font-semibold">{{ formatPrice(order.total) }}</TableCell>
                                        <TableCell>
                                            <Badge :variant="getStatusBadgeVariant(order.status)">
                                                {{ getStatusLabel(order.status) }}
                                            </Badge>
                                        </TableCell>
                                        <TableCell class="text-right">
                                            <div class="flex justify-end gap-2">
                                                <Link :href="route('orders.show', order.id)">
                                                    <Button variant="outline" size="sm">Ver</Button>
                                                </Link>
                                                <Link v-if="order.status === 'draft'" :href="route('orders.edit', order.id)">
                                                    <Button variant="outline" size="sm">Editar</Button>
                                                </Link>
                                                <Button
                                                    v-if="order.status === 'draft'"
                                                    variant="destructive"
                                                    size="sm"
                                                    @click="deleteOrder(order.id)"
                                                >
                                                    Eliminar
                                                </Button>
                                            </div>
                                        </TableCell>
                                    </TableRow>
                                </TableBody>
                            </Table>

                            <!-- Paginação -->
                            <div v-if="orders.links.length > 3" class="mt-6 flex items-center justify-between">
                                <div class="text-sm text-gray-700 dark:text-gray-300">
                                    Mostrando <span class="font-medium">{{ orders.from }}</span> a
                                    <span class="font-medium">{{ orders.to }}</span> de
                                    <span class="font-medium">{{ orders.total }}</span> resultados
                                </div>
                                <div class="flex gap-1">
                                    <Link
                                        v-for="(link, index) in orders.links"
                                        :key="index"
                                        :href="link.url || '#'"
                                        :class="[
                                            'px-3 py-2 text-sm rounded-md',
                                            link.active
                                                ? 'bg-primary text-primary-foreground'
                                                : 'bg-secondary text-secondary-foreground hover:bg-secondary/80',
                                            !link.url && 'opacity-50 cursor-not-allowed'
                                        ]"
                                        :preserve-state="true"
                                        v-html="link.label"
                                    />
                                </div>
                            </div>
                        </div>

                        <!-- Mensagem quando não há dados -->
                        <div v-else class="py-12 text-center">
                            <p class="text-gray-500 dark:text-gray-400">
                                Nenhuma encomenda encontrada.
                            </p>
                            <Link :href="route('orders.create')" class="mt-4 inline-block">
                                <Button>Criar primeira encomenda</Button>
                            </Link>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
