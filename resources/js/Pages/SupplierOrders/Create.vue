<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import { Button } from '@/Components/ui/button';
import { Input } from '@/Components/ui/input';
import { Label } from '@/Components/ui/label';
import { Textarea } from '@/Components/ui/textarea';
import { Card, CardHeader, CardTitle, CardContent } from '@/Components/ui/card';
import { Table, TableHeader, TableBody, TableRow, TableHead, TableCell } from '@/Components/ui/table';
import InputError from '@/Components/InputError.vue';

const props = defineProps({
    nextNumber: String,
    suppliers: Array,
    articles: Array
});

const form = useForm({
    order_date: new Date().toISOString().split('T')[0],
    supplier_id: '',
    notes: '',
    lines: []
});

const addLine = () => {
    form.lines.push({
        article_id: '',
        article_name: '',
        quantity: 1,
        unit_price: 0,
        tax_rate: 23,
        subtotal: 0,
        total: 0
    });
};

const removeLine = (index) => {
    form.lines.splice(index, 1);
};

const selectArticle = (line, articleId) => {
    const article = props.articles.find(a => a.id == articleId);
    if (article) {
        line.article_id = article.id;
        line.article_name = article.name;
        line.unit_price = article.price || 0;
        line.tax_rate = article.vat_rate?.rate || 23;
        calculateLineTotal(line);
    }
};

const calculateLineTotal = (line) => {
    line.subtotal = line.quantity * line.unit_price;
    const taxAmount = line.subtotal * (line.tax_rate / 100);
    line.total = line.subtotal + taxAmount;
};

const totals = computed(() => {
    const subtotal = form.lines.reduce((sum, line) => {
        calculateLineTotal(line);
        return sum + parseFloat(line.subtotal || 0);
    }, 0);

    const tax_total = form.lines.reduce((sum, line) => {
        const taxAmount = parseFloat(line.subtotal || 0) * (parseFloat(line.tax_rate || 0) / 100);
        return sum + taxAmount;
    }, 0);

    const total = subtotal + tax_total;

    return { subtotal, tax_total, total };
});

const formatPrice = (price) => {
    return new Intl.NumberFormat('pt-PT', {
        style: 'currency',
        currency: 'EUR'
    }).format(price || 0);
};

const submit = () => {
    form.post(route('supplier-orders.store'));
};
</script>

<template>
    <Head title="Nova Encomenda a Fornecedor" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
                    Nova Encomenda a Fornecedor
                </h2>
                <Link :href="route('supplier-orders.index')">
                    <Button variant="outline">Voltar</Button>
                </Link>
            </div>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-6xl sm:px-6 lg:px-8 space-y-6">
                <form @submit.prevent="submit" class="space-y-6">
                    <!-- Dados da Encomenda -->
                    <Card>
                        <CardHeader>
                            <CardTitle>Dados da Encomenda</CardTitle>
                        </CardHeader>
                        <CardContent class="space-y-4">
                            <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
                                <div>
                                    <Label for="number">Número</Label>
                                    <Input
                                        id="number"
                                        type="text"
                                        :value="nextNumber"
                                        disabled
                                        class="mt-1"
                                    />
                                    <p class="mt-1 text-sm text-gray-500">Gerado automaticamente</p>
                                </div>

                                <div>
                                    <Label for="order_date">Data da Encomenda *</Label>
                                    <Input
                                        id="order_date"
                                        v-model="form.order_date"
                                        type="date"
                                        class="mt-1"
                                        required
                                    />
                                    <InputError :message="form.errors.order_date" class="mt-2" />
                                </div>

                                <div>
                                    <Label for="supplier_id">Fornecedor *</Label>
                                    <select
                                        id="supplier_id"
                                        v-model="form.supplier_id"
                                        class="mt-1 block w-full h-10 rounded-md border border-input bg-background px-3 py-2 text-sm"
                                        required
                                    >
                                        <option value="">Selecione um fornecedor</option>
                                        <option v-for="supplier in suppliers" :key="supplier.id" :value="supplier.id">
                                            {{ supplier.name }}
                                        </option>
                                    </select>
                                    <InputError :message="form.errors.supplier_id" class="mt-2" />
                                </div>
                            </div>

                            <div>
                                <Label for="notes">Observações</Label>
                                <Textarea
                                    id="notes"
                                    v-model="form.notes"
                                    rows="3"
                                    class="mt-1"
                                />
                                <InputError :message="form.errors.notes" class="mt-2" />
                            </div>
                        </CardContent>
                    </Card>

                    <!-- Linhas da Encomenda -->
                    <Card>
                        <CardHeader>
                            <div class="flex items-center justify-between">
                                <CardTitle>Artigos</CardTitle>
                                <Button type="button" @click="addLine" variant="outline">
                                    Adicionar Linha
                                </Button>
                            </div>
                        </CardHeader>
                        <CardContent>
                            <div v-if="form.lines.length === 0" class="text-center py-8 text-gray-500">
                                Nenhuma linha adicionada. Clique em "Adicionar Linha" para começar.
                            </div>

                            <Table v-else>
                                <TableHeader>
                                    <TableRow>
                                        <TableHead>Artigo</TableHead>
                                        <TableHead class="text-right">Qtd</TableHead>
                                        <TableHead class="text-right">Preço Unit.</TableHead>
                                        <TableHead class="text-right">IVA %</TableHead>
                                        <TableHead class="text-right">Total</TableHead>
                                        <TableHead class="text-right">Ações</TableHead>
                                    </TableRow>
                                </TableHeader>
                                <TableBody>
                                    <TableRow v-for="(line, index) in form.lines" :key="index">
                                        <TableCell>
                                            <select
                                                v-model="line.article_id"
                                                @change="selectArticle(line, line.article_id)"
                                                class="w-full rounded-md border border-input bg-background px-3 py-2 text-sm"
                                                required
                                            >
                                                <option value="">Selecione um artigo</option>
                                                <option v-for="article in articles" :key="article.id" :value="article.id">
                                                    {{ article.reference }} - {{ article.name }}
                                                </option>
                                            </select>
                                            <InputError :message="form.errors[`lines.${index}.article_id`]" class="mt-1" />
                                        </TableCell>
                                        <TableCell class="text-right">
                                            <Input
                                                v-model.number="line.quantity"
                                                type="number"
                                                min="1"
                                                @input="calculateLineTotal(line)"
                                                class="w-24 text-right"
                                                required
                                            />
                                        </TableCell>
                                        <TableCell class="text-right">
                                            <Input
                                                v-model.number="line.unit_price"
                                                type="number"
                                                step="0.01"
                                                min="0"
                                                @input="calculateLineTotal(line)"
                                                class="w-32 text-right"
                                                required
                                            />
                                        </TableCell>
                                        <TableCell class="text-right">
                                            <Input
                                                v-model.number="line.tax_rate"
                                                type="number"
                                                step="0.01"
                                                min="0"
                                                max="100"
                                                @input="calculateLineTotal(line)"
                                                class="w-24 text-right"
                                                required
                                            />
                                        </TableCell>
                                        <TableCell class="text-right font-semibold">
                                            {{ formatPrice(line.total) }}
                                        </TableCell>
                                        <TableCell class="text-right">
                                            <Button
                                                type="button"
                                                variant="destructive"
                                                size="sm"
                                                @click="removeLine(index)"
                                            >
                                                Remover
                                            </Button>
                                        </TableCell>
                                    </TableRow>
                                </TableBody>
                            </Table>

                            <InputError :message="form.errors.lines" class="mt-2" />
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
                                    <span class="font-medium">{{ formatPrice(totals.subtotal) }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600 dark:text-gray-400">IVA:</span>
                                    <span class="font-medium">{{ formatPrice(totals.tax_total) }}</span>
                                </div>
                                <div class="flex justify-between border-t pt-2">
                                    <span class="text-lg font-semibold">Total:</span>
                                    <span class="text-lg font-bold">{{ formatPrice(totals.total) }}</span>
                                </div>
                            </div>
                        </CardContent>
                    </Card>

                    <!-- Botões -->
                    <div class="flex items-center justify-end gap-4">
                        <Link :href="route('supplier-orders.index')">
                            <Button type="button" variant="outline">Cancelar</Button>
                        </Link>
                        <Button type="submit" :disabled="form.processing || form.lines.length === 0">
                            {{ form.processing ? 'A guardar...' : 'Criar Encomenda' }}
                        </Button>
                    </div>
                </form>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
