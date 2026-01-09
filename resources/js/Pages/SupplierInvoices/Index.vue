<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref, watch } from 'vue';
import { Button } from '@/Components/ui/button';
import { Input } from '@/Components/ui/input';
import { Badge } from '@/Components/ui/badge';
import { Table, TableHeader, TableBody, TableRow, TableHead, TableCell } from '@/Components/ui/table';
import { Dialog, DialogContent, DialogHeader, DialogTitle } from '@/Components/ui/dialog';
import { Label } from '@/Components/ui/label';

const props = defineProps({
    invoices: Object,
    filters: Object
});

const search = ref(props.filters?.search || '');
const statusFilter = ref(props.filters?.status || 'all');
const showPaymentDialog = ref(false);
const selectedInvoice = ref(null);
const paymentForm = ref({
    payment_date: new Date().toISOString().split('T')[0],
    payment_proof: null,
    send_email: false
});

watch([search, statusFilter], ([newSearch, newStatus]) => {
    router.get(route('supplier-invoices.index'), {
        search: newSearch,
        status: newStatus
    }, {
        preserveState: true,
        replace: true
    });
}, { debounce: 300 });

const openPaymentDialog = (invoice) => {
    selectedInvoice.value = invoice;
    paymentForm.value = {
        payment_date: new Date().toISOString().split('T')[0],
        payment_proof: null,
        send_email: false
    };
    showPaymentDialog.value = true;
};

const submitPayment = () => {
    const formData = new FormData();
    formData.append('payment_date', paymentForm.value.payment_date);
    formData.append('send_email', paymentForm.value.send_email ? '1' : '0');
    if (paymentForm.value.payment_proof) {
        formData.append('payment_proof', paymentForm.value.payment_proof);
    }

    router.post(route('supplier-invoices.markAsPaid', selectedInvoice.value.id), formData, {
        onSuccess: () => {
            showPaymentDialog.value = false;
        }
    });
};

const deleteInvoice = (id) => {
    if (confirm('Tem a certeza que deseja eliminar esta fatura?')) {
        router.delete(route('supplier-invoices.destroy', id));
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
    return status === 'paid' ? 'success' : 'secondary';
};

const getStatusLabel = (status) => {
    return status === 'paid' ? 'Paga' : 'Pendente';
};

const handleFileChange = (event) => {
    paymentForm.value.payment_proof = event.target.files[0];
};
</script>

<template>
    <Head title="Faturas Fornecedores" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
                    Faturas Fornecedores
                </h2>
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
                                <option value="all">Todos os estados</option>
                                <option value="pending">Pendente</option>
                                <option value="paid">Paga</option>
                            </select>
                        </div>

                        <!-- Tabela -->
                        <div v-if="invoices.data.length > 0">
                            <Table>
                                <TableHeader>
                                    <TableRow>
                                        <TableHead>Número</TableHead>
                                        <TableHead>Data Fatura</TableHead>
                                        <TableHead>Vencimento</TableHead>
                                        <TableHead>Fornecedor</TableHead>
                                        <TableHead>Total</TableHead>
                                        <TableHead>Estado</TableHead>
                                        <TableHead class="text-right">Ações</TableHead>
                                    </TableRow>
                                </TableHeader>
                                <TableBody>
                                    <TableRow v-for="invoice in invoices.data" :key="invoice.id">
                                        <TableCell class="font-medium">{{ invoice.number }}</TableCell>
                                        <TableCell>{{ formatDate(invoice.invoice_date) }}</TableCell>
                                        <TableCell>{{ formatDate(invoice.due_date) }}</TableCell>
                                        <TableCell>{{ invoice.supplier?.name }}</TableCell>
                                        <TableCell>{{ formatPrice(invoice.total_amount) }}</TableCell>
                                        <TableCell>
                                            <Badge :variant="getStatusBadgeVariant(invoice.status)">
                                                {{ getStatusLabel(invoice.status) }}
                                            </Badge>
                                        </TableCell>
                                        <TableCell class="text-right">
                                            <div class="flex justify-end gap-2">
                                                <a
                                                    v-if="invoice.document_path"
                                                    :href="route('supplier-invoices.download', { supplierInvoice: invoice.id, type: 'document' })"
                                                    target="_blank"
                                                >
                                                    <Button variant="ghost" size="sm">Doc</Button>
                                                </a>
                                                <Button
                                                    v-if="invoice.status === 'pending'"
                                                    variant="ghost"
                                                    size="sm"
                                                    @click="openPaymentDialog(invoice)"
                                                >
                                                    Pagar
                                                </Button>
                                                <a
                                                    v-if="invoice.payment_proof_path"
                                                    :href="route('supplier-invoices.download', { supplierInvoice: invoice.id, type: 'payment_proof' })"
                                                    target="_blank"
                                                >
                                                    <Button variant="ghost" size="sm">Comprov.</Button>
                                                </a>
                                                <Button
                                                    variant="ghost"
                                                    size="sm"
                                                    @click="deleteInvoice(invoice.id)"
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
                                    A mostrar {{ invoices.from }} a {{ invoices.to }} de {{ invoices.total }} resultados
                                </div>
                                <div class="flex gap-2">
                                    <Link
                                        v-for="link in invoices.links"
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
                            Nenhuma fatura encontrada.
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Payment Dialog -->
        <Dialog v-model:open="showPaymentDialog">
            <DialogContent class="sm:max-w-[525px]">
                <DialogHeader>
                    <DialogTitle>Marcar Fatura como Paga</DialogTitle>
                </DialogHeader>
                <form @submit.prevent="submitPayment" class="space-y-4">
                    <div>
                        <Label for="payment_date">Data de Pagamento</Label>
                        <Input
                            id="payment_date"
                            v-model="paymentForm.payment_date"
                            type="date"
                            required
                        />
                    </div>
                    <div>
                        <Label for="payment_proof">Comprovativo de Pagamento (opcional)</Label>
                        <Input
                            id="payment_proof"
                            type="file"
                            accept=".pdf,.jpg,.jpeg,.png"
                            @change="handleFileChange"
                        />
                    </div>
                    <div class="flex items-center space-x-2">
                        <input
                            id="send_email"
                            v-model="paymentForm.send_email"
                            type="checkbox"
                            class="h-4 w-4 rounded border-gray-300"
                        />
                        <Label for="send_email">Enviar email ao fornecedor com comprovativo</Label>
                    </div>
                    <div class="flex justify-end gap-2">
                        <Button type="button" variant="outline" @click="showPaymentDialog = false">
                            Cancelar
                        </Button>
                        <Button type="submit">Confirmar Pagamento</Button>
                    </div>
                </form>
            </DialogContent>
        </Dialog>
    </AuthenticatedLayout>
</template>
