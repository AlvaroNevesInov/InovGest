<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { Button } from '@/Components/ui/button';
import { Badge } from '@/Components/ui/badge';
import { Card, CardHeader, CardTitle, CardContent } from '@/Components/ui/card';
import { Table, TableHeader, TableBody, TableRow, TableHead, TableCell } from '@/Components/ui/table';

const props = defineProps({
    order: Object
});

const formatPrice = (price) => {
    return new Intl.NumberFormat('pt-PT', {
        style: 'currency',
        currency: 'EUR'
    }).format(price);
};

const formatDate = (date) => {
    return new Date(date).toLocaleDateString('pt-PT');
};

const closeOrder = () => {
    if (confirm('Tem a certeza que deseja fechar esta encomenda?')) {
        router.post(route('orders.close', props.order.id));
    }
};


const deleteOrder = () => {
    if (confirm('Tem a certeza que deseja eliminar esta encomenda?')) {
        router.delete(route('orders.destroy', props.order.id));
    }
};
</script>

<template>
    <Head title="Detalhes da Encomenda" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
                    Encomenda #{{ order.number }}
                </h2>
                <div class="flex gap-2">
                    <Link v-if="order.status === 'draft'" :href="route('orders.edit', order.id)">
                        <Button>Editar</Button>
                    </Link>
                    <Link :href="route('orders.index')">
                        <Button variant="outline">Voltar</Button>
                    </Link>
                </div>
            </div>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-6xl sm:px-6 lg:px-8 space-y-6">
                <!-- Informação do Cabeçalho -->
                <Card>
                    <CardHeader>
                        <div class="flex items-center justify-between">
                            <CardTitle>Informação da Encomenda</CardTitle>
                            <Badge :variant="order.status === 'closed' ? 'default' : 'secondary'">
                                {{ order.status === 'closed' ? 'Fechada' : 'Rascunho' }}
                            </Badge>
                        </div>
                    </CardHeader>
                    <CardContent>
                        <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                            <div>
                                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Número</p>
                                <p class="mt-1 text-lg font-semibold">#{{ order.number }}</p>
                            </div>

                            <div>
                                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Data da Encomenda</p>
                                <p class="mt-1 text-base">{{ formatDate(order.order_date) }}</p>
                            </div>

                            <div>
                                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Cliente</p>
                                <p class="mt-1 text-base">{{ order.entity?.name }}</p>
                            </div>

                            <div v-if="order.notes" class="md:col-span-2">
                                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Observações</p>
                                <p class="mt-1 text-base">{{ order.notes }}</p>
                            </div>
                        </div>
                    </CardContent>
                </Card>

                <!-- Linhas da Encomenda -->
                <Card>
                    <CardHeader>
                        <CardTitle>Artigos</CardTitle>
                    </CardHeader>
                    <CardContent>
                        <Table>
                            <TableHeader>
                                <TableRow>
                                    <TableHead>Referência</TableHead>
                                    <TableHead>Artigo</TableHead>
                                    <TableHead class="text-right">Qtd</TableHead>
                                    <TableHead class="text-right">Preço Unit.</TableHead>
                                    <TableHead class="text-right">Desconto</TableHead>
                                    <TableHead class="text-right">IVA</TableHead>
                                    <TableHead class="text-right">Total</TableHead>
                                </TableRow>
                            </TableHeader>
                            <TableBody>
                                <TableRow v-for="line in order.lines" :key="line.id">
                                    <TableCell>{{ line.article_reference }}</TableCell>
                                    <TableCell>
                                        <div>
                                            <p class="font-medium">{{ line.article_name }}</p>
                                            <p v-if="line.description" class="text-sm text-gray-500">{{ line.description }}</p>
                                        </div>
                                    </TableCell>
                                    <TableCell class="text-right">{{ line.quantity }}</TableCell>
                                    <TableCell class="text-right">{{ formatPrice(line.unit_price) }}</TableCell>
                                    <TableCell class="text-right">{{ line.discount_percentage }}%</TableCell>
                                    <TableCell class="text-right">{{ line.vat_rate }}%</TableCell>
                                    <TableCell class="text-right font-semibold">{{ formatPrice(line.total) }}</TableCell>
                                </TableRow>
                            </TableBody>
                        </Table>
                    </CardContent>
                </Card>

                <!-- Totais -->
                <Card>
                    <CardHeader>
                        <CardTitle>Resumo</CardTitle>
                    </CardHeader>
                    <CardContent>
                        <div class="space-y-2">
                            <div class="flex justify-between">
                                <span class="text-gray-600 dark:text-gray-400">Subtotal:</span>
                                <span class="font-medium">{{ formatPrice(order.subtotal) }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600 dark:text-gray-400">IVA:</span>
                                <span class="font-medium">{{ formatPrice(order.tax_total) }}</span>
                            </div>
                            <div class="flex justify-between border-t pt-2">
                                <span class="text-lg font-semibold">Total:</span>
                                <span class="text-lg font-bold">{{ formatPrice(order.total) }}</span>
                            </div>
                        </div>
                    </CardContent>
                </Card>

                <!-- Ações -->
                <Card>
                    <CardHeader>
                        <CardTitle>Ações</CardTitle>
                    </CardHeader>
                    <CardContent>
                        <div class="flex flex-wrap gap-4">
                            <Link :href="route('orders.pdf', order.id)">
                                <Button variant="default">
                                    Descarregar PDF
                                </Button>
                            </Link>

                            <Link v-if="order.status === 'draft'" :href="route('orders.edit', order.id)">
                                <Button variant="outline">Editar Encomenda</Button>
                            </Link>

                            <Button
                                v-if="order.status === 'draft'"
                                variant="secondary"
                                @click="closeOrder"
                            >
                                Fechar Encomenda
                            </Button>

                            <Button
                                v-if="order.status === 'draft'"
                                variant="destructive"
                                @click="deleteOrder"
                            >
                                Eliminar Encomenda
                            </Button>
                        </div>
                    </CardContent>
                </Card>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
