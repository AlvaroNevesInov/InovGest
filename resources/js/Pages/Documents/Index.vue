<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref, watch } from 'vue';
import { Button } from '@/Components/ui/button';
import { Badge } from '@/Components/ui/badge';
import { Card, CardHeader, CardTitle, CardContent } from '@/Components/ui/card';
import { Input } from '@/Components/ui/input';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/Components/ui/select';
import { Table, TableHeader, TableBody, TableRow, TableHead, TableCell } from '@/Components/ui/table';

const props = defineProps({
    proposals: Array,
    orders: Array,
    filters: Object,
});

const search = ref(props.filters.search || '');
const type = ref(props.filters.type || 'all');

watch([search, type], ([searchValue, typeValue]) => {
    router.get(route('documents.index'), {
        search: searchValue,
        type: typeValue
    }, {
        preserveState: true,
        replace: true,
    });
});

const formatDate = (date) => {
    return new Date(date).toLocaleDateString('pt-PT');
};

const formatPrice = (price) => {
    return new Intl.NumberFormat('pt-PT', {
        style: 'currency',
        currency: 'EUR'
    }).format(price);
};
</script>

<template>
    <Head title="Documentos" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
                Documentos
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <Card>
                    <CardHeader>
                        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                            <CardTitle>Lista de Documentos</CardTitle>
                            <div class="flex flex-col gap-2 sm:flex-row sm:items-center">
                                <div class="w-full sm:w-48">
                                    <Select v-model="type">
                                        <SelectTrigger>
                                            <SelectValue placeholder="Filtrar por tipo" />
                                        </SelectTrigger>
                                        <SelectContent>
                                            <SelectItem value="all">Todos</SelectItem>
                                            <SelectItem value="proposals">Propostas</SelectItem>
                                            <SelectItem value="orders">Encomendas</SelectItem>
                                        </SelectContent>
                                    </Select>
                                </div>
                                <div class="w-full sm:w-72">
                                    <Input
                                        v-model="search"
                                        type="search"
                                        placeholder="Pesquisar por número ou cliente..."
                                    />
                                </div>
                            </div>
                        </div>
                    </CardHeader>
                    <CardContent>
                        <div v-if="proposals.length > 0 || orders.length > 0">
                            <!-- Propostas -->
                            <div v-if="proposals.length > 0" class="mb-8">
                                <h3 class="mb-4 text-lg font-semibold text-gray-800 dark:text-gray-200">Propostas</h3>
                                <Table>
                                    <TableHeader>
                                        <TableRow>
                                            <TableHead>Número</TableHead>
                                            <TableHead>Cliente</TableHead>
                                            <TableHead>Data</TableHead>
                                            <TableHead>Validade</TableHead>
                                            <TableHead class="text-right">Total</TableHead>
                                            <TableHead>Estado</TableHead>
                                            <TableHead class="text-right">Ações</TableHead>
                                        </TableRow>
                                    </TableHeader>
                                    <TableBody>
                                        <TableRow v-for="proposal in proposals" :key="proposal.id">
                                            <TableCell>
                                                <Link
                                                    :href="route('proposals.show', proposal.id)"
                                                    class="font-medium text-primary hover:underline"
                                                >
                                                    #{{ proposal.number }}
                                                </Link>
                                            </TableCell>
                                            <TableCell>{{ proposal.entity?.name }}</TableCell>
                                            <TableCell>{{ formatDate(proposal.proposal_date) }}</TableCell>
                                            <TableCell>{{ formatDate(proposal.validity_date) }}</TableCell>
                                            <TableCell class="text-right">{{ formatPrice(proposal.total) }}</TableCell>
                                            <TableCell>
                                                <Badge :variant="proposal.status === 'closed' ? 'default' : 'secondary'">
                                                    {{ proposal.status === 'closed' ? 'Fechada' : 'Rascunho' }}
                                                </Badge>
                                            </TableCell>
                                            <TableCell class="text-right">
                                                <div class="flex justify-end gap-2">
                                                    <a
                                                        :href="route('proposals.pdf', proposal.id)"
                                                        class="inline-flex items-center justify-center whitespace-nowrap rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 border border-input bg-background hover:bg-accent hover:text-accent-foreground h-9 px-3"
                                                    >
                                                        PDF
                                                    </a>
                                                    <Link :href="route('proposals.show', proposal.id)">
                                                        <Button variant="secondary" size="sm">
                                                            Ver
                                                        </Button>
                                                    </Link>
                                                </div>
                                            </TableCell>
                                        </TableRow>
                                    </TableBody>
                                </Table>
                            </div>

                            <!-- Encomendas -->
                            <div v-if="orders.length > 0">
                                <h3 class="mb-4 text-lg font-semibold text-gray-800 dark:text-gray-200">Encomendas</h3>
                                <Table>
                                    <TableHeader>
                                        <TableRow>
                                            <TableHead>Número</TableHead>
                                            <TableHead>Cliente</TableHead>
                                            <TableHead>Data</TableHead>
                                            <TableHead class="text-right">Total</TableHead>
                                            <TableHead>Estado</TableHead>
                                            <TableHead class="text-right">Ações</TableHead>
                                        </TableRow>
                                    </TableHeader>
                                    <TableBody>
                                        <TableRow v-for="order in orders" :key="order.id">
                                            <TableCell>
                                                <Link
                                                    :href="route('orders.show', order.id)"
                                                    class="font-medium text-primary hover:underline"
                                                >
                                                    #{{ order.number }}
                                                </Link>
                                            </TableCell>
                                            <TableCell>{{ order.entity?.name }}</TableCell>
                                            <TableCell>{{ formatDate(order.order_date) }}</TableCell>
                                            <TableCell class="text-right">{{ formatPrice(order.total) }}</TableCell>
                                            <TableCell>
                                                <Badge :variant="order.status === 'closed' ? 'default' : 'secondary'">
                                                    {{ order.status === 'closed' ? 'Fechada' : 'Rascunho' }}
                                                </Badge>
                                            </TableCell>
                                            <TableCell class="text-right">
                                                <div class="flex justify-end gap-2">
                                                    <a
                                                        :href="route('orders.pdf', order.id)"
                                                        class="inline-flex items-center justify-center whitespace-nowrap rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 border border-input bg-background hover:bg-accent hover:text-accent-foreground h-9 px-3"
                                                    >
                                                        PDF
                                                    </a>
                                                    <Link :href="route('orders.show', order.id)">
                                                        <Button variant="secondary" size="sm">
                                                            Ver
                                                        </Button>
                                                    </Link>
                                                </div>
                                            </TableCell>
                                        </TableRow>
                                    </TableBody>
                                </Table>
                            </div>
                        </div>

                        <div v-else class="py-12 text-center text-gray-500">
                            <p>Nenhum documento encontrado</p>
                        </div>
                    </CardContent>
                </Card>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
