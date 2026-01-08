<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ref, computed, watch } from 'vue';
import { Button } from '@/Components/ui/button';
import { Input } from '@/Components/ui/input';
import { Label } from '@/Components/ui/label';
import { Textarea } from '@/Components/ui/textarea';
import { Card, CardHeader, CardTitle, CardContent } from '@/Components/ui/card';
import { Table, TableHeader, TableBody, TableRow, TableHead, TableCell } from '@/Components/ui/table';
import InputError from '@/Components/InputError.vue';

const props = defineProps({
    proposal: Object,
    clients: Array,
    articles: Array,
    suppliers: Array,
    vatRates: Array
});

const form = useForm({
    proposal_date: props.proposal.proposal_date,
    entity_id: props.proposal.entity_id,
    validity_date: props.proposal.validity_date,
    notes: props.proposal.notes,
    lines: props.proposal.lines.map(line => ({
        article_id: line.article_id,
        article_reference: line.article_reference,
        article_name: line.article_name,
        description: line.description,
        quantity: line.quantity,
        unit_price: line.unit_price,
        discount: line.discount,
        discount_percentage: line.discount_percentage,
        vat_rate_id: line.vat_rate_id,
        vat_rate: line.vat_rate,
        supplier_id: line.supplier_id,
        cost_price: line.cost_price,
        subtotal: line.subtotal,
        tax_amount: line.tax_amount,
        total: line.total
    }))
});

const addLine = () => {
    form.lines.push({
        article_id: '',
        article_reference: '',
        article_name: '',
        description: '',
        quantity: 1,
        unit_price: 0,
        discount: 0,
        discount_percentage: 0,
        vat_rate_id: '',
        vat_rate: 0,
        supplier_id: '',
        cost_price: 0,
        subtotal: 0,
        tax_amount: 0,
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
        line.article_reference = article.reference;
        line.article_name = article.name;
        line.description = article.description;
        line.unit_price = article.price;
        line.vat_rate_id = article.vat_rate_id;
        line.vat_rate = article.vat_rate?.rate || 0;
        calculateLineTotal(line);
    }
};

const calculateLineTotal = (line) => {
    const subtotalBeforeDiscount = line.quantity * line.unit_price;

    if (line.discount_percentage > 0) {
        line.discount = subtotalBeforeDiscount * (line.discount_percentage / 100);
    }

    line.subtotal = subtotalBeforeDiscount - line.discount;
    line.tax_amount = line.subtotal * (line.vat_rate / 100);
    line.total = line.subtotal + line.tax_amount;
};

// Watch para recalcular quando mudar quantidade, preço, desconto ou IVA
watch(() => form.lines, () => {
    form.lines.forEach(line => calculateLineTotal(line));
}, { deep: true });

const totals = computed(() => {
    const subtotal = form.lines.reduce((sum, line) => sum + parseFloat(line.subtotal || 0), 0);
    const tax_total = form.lines.reduce((sum, line) => sum + parseFloat(line.tax_amount || 0), 0);
    const total = form.lines.reduce((sum, line) => sum + parseFloat(line.total || 0), 0);

    return { subtotal, tax_total, total };
});

const formatPrice = (price) => {
    return new Intl.NumberFormat('pt-PT', {
        style: 'currency',
        currency: 'EUR'
    }).format(price);
};

const submit = () => {
    form.put(route('proposals.update', props.proposal.id));
};
</script>

<template>
    <Head title="Editar Proposta" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
                    Editar Proposta #{{ proposal.number }}
                </h2>
                <Link :href="route('proposals.index')">
                    <Button variant="outline">Voltar</Button>
                </Link>
            </div>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <form @submit.prevent="submit" class="space-y-6">
                    <!-- Cabeçalho da Proposta -->
                    <Card>
                        <CardHeader>
                            <CardTitle>Dados da Proposta</CardTitle>
                        </CardHeader>
                        <CardContent>
                            <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                                <!-- Número -->
                                <div>
                                    <Label>Número</Label>
                                    <Input
                                        type="text"
                                        :value="'#' + proposal.number"
                                        disabled
                                        class="mt-1"
                                    />
                                </div>

                                <!-- Data -->
                                <div>
                                    <Label for="proposal_date">Data da Proposta *</Label>
                                    <Input
                                        id="proposal_date"
                                        v-model="form.proposal_date"
                                        type="date"
                                        class="mt-1"
                                        required
                                    />
                                    <InputError :message="form.errors.proposal_date" class="mt-2" />
                                </div>

                                <!-- Cliente -->
                                <div>
                                    <Label for="entity_id">Cliente *</Label>
                                    <select
                                        id="entity_id"
                                        v-model="form.entity_id"
                                        class="mt-1 block w-full h-10 rounded-md border border-input bg-background px-3 py-2 text-sm"
                                        required
                                    >
                                        <option value="">Selecione...</option>
                                        <option v-for="client in clients" :key="client.id" :value="client.id">
                                            {{ client.name }}
                                        </option>
                                    </select>
                                    <InputError :message="form.errors.entity_id" class="mt-2" />
                                </div>

                                <!-- Validade -->
                                <div>
                                    <Label for="validity_date">Data de Validade *</Label>
                                    <Input
                                        id="validity_date"
                                        v-model="form.validity_date"
                                        type="date"
                                        class="mt-1"
                                        required
                                    />
                                    <InputError :message="form.errors.validity_date" class="mt-2" />
                                </div>

                                <!-- Observações -->
                                <div class="md:col-span-2">
                                    <Label for="notes">Observações</Label>
                                    <Textarea
                                        id="notes"
                                        v-model="form.notes"
                                        class="mt-1"
                                        rows="3"
                                    />
                                    <InputError :message="form.errors.notes" class="mt-2" />
                                </div>
                            </div>
                        </CardContent>
                    </Card>

                    <!-- Linhas da Proposta -->
                    <Card>
                        <CardHeader>
                            <div class="flex items-center justify-between">
                                <CardTitle>Artigos</CardTitle>
                                <Button type="button" @click="addLine" size="sm">
                                    + Adicionar Linha
                                </Button>
                            </div>
                        </CardHeader>
                        <CardContent>
                            <div class="overflow-x-auto">
                                <Table>
                                    <TableHeader>
                                        <TableRow>
                                            <TableHead class="w-[200px]">Artigo</TableHead>
                                            <TableHead class="w-[80px]">Qtd</TableHead>
                                            <TableHead class="w-[100px]">Preço</TableHead>
                                            <TableHead class="w-[80px]">Desc %</TableHead>
                                            <TableHead class="w-[120px]">IVA</TableHead>
                                            <TableHead class="w-[150px]">Fornecedor</TableHead>
                                            <TableHead class="w-[100px]">P. Custo</TableHead>
                                            <TableHead class="w-[100px]">Total</TableHead>
                                            <TableHead class="w-[50px]"></TableHead>
                                        </TableRow>
                                    </TableHeader>
                                    <TableBody>
                                        <TableRow v-for="(line, index) in form.lines" :key="index">
                                            <!-- Artigo -->
                                            <TableCell>
                                                <select
                                                    v-model="line.article_id"
                                                    @change="selectArticle(line, line.article_id)"
                                                    class="w-full h-9 rounded-md border border-input bg-background px-2 text-sm"
                                                >
                                                    <option value="">Selecione...</option>
                                                    <option v-for="article in articles" :key="article.id" :value="article.id">
                                                        {{ article.reference }} - {{ article.name }}
                                                    </option>
                                                </select>
                                            </TableCell>

                                            <!-- Quantidade -->
                                            <TableCell>
                                                <Input
                                                    v-model.number="line.quantity"
                                                    type="number"
                                                    step="0.01"
                                                    min="0.01"
                                                    class="w-full"
                                                />
                                            </TableCell>

                                            <!-- Preço Unitário -->
                                            <TableCell>
                                                <Input
                                                    v-model.number="line.unit_price"
                                                    type="number"
                                                    step="0.01"
                                                    min="0"
                                                    class="w-full"
                                                />
                                            </TableCell>

                                            <!-- Desconto % -->
                                            <TableCell>
                                                <Input
                                                    v-model.number="line.discount_percentage"
                                                    type="number"
                                                    step="0.01"
                                                    min="0"
                                                    max="100"
                                                    class="w-full"
                                                />
                                            </TableCell>

                                            <!-- IVA -->
                                            <TableCell>
                                                <select
                                                    v-model="line.vat_rate_id"
                                                    @change="line.vat_rate = vatRates.find(v => v.id == line.vat_rate_id)?.rate || 0"
                                                    class="w-full h-9 rounded-md border border-input bg-background px-2 text-sm"
                                                >
                                                    <option value="">Selecione...</option>
                                                    <option v-for="vat in vatRates" :key="vat.id" :value="vat.id">
                                                        {{ vat.name }}
                                                    </option>
                                                </select>
                                            </TableCell>

                                            <!-- Fornecedor -->
                                            <TableCell>
                                                <select
                                                    v-model="line.supplier_id"
                                                    class="w-full h-9 rounded-md border border-input bg-background px-2 text-sm"
                                                >
                                                    <option value="">Nenhum</option>
                                                    <option v-for="supplier in suppliers" :key="supplier.id" :value="supplier.id">
                                                        {{ supplier.name }}
                                                    </option>
                                                </select>
                                            </TableCell>

                                            <!-- Preço de Custo -->
                                            <TableCell>
                                                <Input
                                                    v-model.number="line.cost_price"
                                                    type="number"
                                                    step="0.01"
                                                    min="0"
                                                    class="w-full"
                                                />
                                            </TableCell>

                                            <!-- Total -->
                                            <TableCell class="font-semibold">
                                                {{ formatPrice(line.total) }}
                                            </TableCell>

                                            <!-- Remover -->
                                            <TableCell>
                                                <Button
                                                    type="button"
                                                    variant="destructive"
                                                    size="sm"
                                                    @click="removeLine(index)"
                                                    :disabled="form.lines.length === 1"
                                                >
                                                    ×
                                                </Button>
                                            </TableCell>
                                        </TableRow>
                                    </TableBody>
                                </Table>
                            </div>
                        </CardContent>
                    </Card>

                    <!-- Totais -->
                    <Card>
                        <CardHeader>
                            <CardTitle>Resumo</CardTitle>
                        </CardHeader>
                        <CardContent>
                            <div class="space-y-2 max-w-md ml-auto">
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
                    <Card>
                        <CardContent class="pt-6">
                            <div class="flex justify-end gap-4">
                                <Link :href="route('proposals.index')">
                                    <Button type="button" variant="outline">Cancelar</Button>
                                </Link>
                                <Button type="submit" :disabled="form.processing || form.lines.length === 0">
                                    {{ form.processing ? 'Guardando...' : 'Guardar Alterações' }}
                                </Button>
                            </div>
                        </CardContent>
                    </Card>
                </form>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
