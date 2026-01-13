<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, router } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import { Button } from '@/Components/ui/button';
import { Input } from '@/Components/ui/input';
import { Badge } from '@/Components/ui/badge';
import { Table, TableHeader, TableBody, TableRow, TableHead, TableCell } from '@/Components/ui/table';
import { Dialog, DialogContent, DialogHeader, DialogTitle } from '@/Components/ui/dialog';
import { Label } from '@/Components/ui/label';

const props = defineProps({
    movements: Object,
    clients: Array,
    filters: Object
});


const showDialog = ref(false);
const entityFilter = ref(props.filters?.entity_id || '');
const form = ref({
    entity_id: '',
    movement_date: new Date().toISOString().split('T')[0],
    type: 'debit',
    amount: 0,
    description: '',
    document_number: ''
});

const openCreateDialog = () => {
    form.value = {
        entity_id: entityFilter.value || '',
        movement_date: new Date().toISOString().split('T')[0],
        type: 'debit',
        amount: 0,
        description: '',
        document_number: ''
    };
    showDialog.value = true;
};

const submitForm = () => {
    router.post(route('client-accounts.store'), form.value, {
        onSuccess: () => {
            showDialog.value = false;
        }
    });
};

const deleteMovement = (id) => {
    if (confirm('Tem a certeza que deseja eliminar este movimento?')) {
        router.delete(route('client-accounts.destroy', id));
    }
};

const filterByEntity = (entityId) => {
    router.get(route('client-accounts.index'), {
        entity_id: entityId
    }, {
        preserveState: true,
        replace: true
    });
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

const getTypeBadgeVariant = (type) => {
    return type === 'debit' ? 'destructive' : 'success';
};

const getTypeLabel = (type) => {
    return type === 'debit' ? 'Débito' : 'Crédito';
};
</script>

<template>
    <Head title="Conta Corrente Clientes" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
                    Conta Corrente Clientes
                </h2>
                <Button @click="openCreateDialog">Novo Movimento</Button>
            </div>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg dark:bg-gray-800">
                    <div class="p-6">
                        <!-- Filtros -->
                        <div class="mb-6">
                            <Label for="entity-filter">Filtrar por Cliente</Label>
                            <select
                                id="entity-filter"
                                v-model="entityFilter"
                                @change="filterByEntity(entityFilter)"
                                class="h-10 w-full max-w-md rounded-md border border-input bg-background px-3 py-2 text-sm"
                            >
                                <option value="">Todos os clientes</option>
                                <option v-for="client in clients" :key="client.id" :value="client.id">
                                    {{ client.name }}
                                </option>
                            </select>
                        </div>

                        <!-- Tabela -->
                        <div v-if="movements.data.length > 0">
                            <Table>
                                <TableHeader>
                                    <TableRow>
                                        <TableHead>Data</TableHead>
                                        <TableHead>Cliente</TableHead>
                                        <TableHead>Tipo</TableHead>
                                        <TableHead>Descrição</TableHead>
                                        <TableHead>Documento</TableHead>
                                        <TableHead>Valor</TableHead>
                                        <TableHead>Saldo</TableHead>
                                        <TableHead class="text-right">Ações</TableHead>
                                    </TableRow>
                                </TableHeader>
                                <TableBody>
                                    <TableRow v-for="movement in movements.data" :key="movement.id">
                                        <TableCell>{{ formatDate(movement.movement_date) }}</TableCell>
                                        <TableCell class="font-medium">{{ movement.entity?.name }}</TableCell>
                                        <TableCell>
                                            <Badge :variant="getTypeBadgeVariant(movement.type)">
                                                {{ getTypeLabel(movement.type) }}
                                            </Badge>
                                        </TableCell>
                                        <TableCell>{{ movement.description }}</TableCell>
                                        <TableCell>{{ movement.document_number || '-' }}</TableCell>
                                        <TableCell>{{ formatPrice(movement.amount) }}</TableCell>
                                        <TableCell :class="movement.balance >= 0 ? 'text-green-600' : 'text-red-600'">
                                            {{ formatPrice(movement.balance) }}
                                        </TableCell>
                                        <TableCell class="text-right">
                                            <Button
                                                variant="ghost"
                                                size="sm"
                                                @click="deleteMovement(movement.id)"
                                            >
                                                Eliminar
                                            </Button>
                                        </TableCell>
                                    </TableRow>
                                </TableBody>
                            </Table>

                            <!-- Paginação -->
                            <div class="mt-4 flex items-center justify-between">
                                <div class="text-sm text-gray-700 dark:text-gray-300">
                                    A mostrar {{ movements.from }} a {{ movements.to }} de {{ movements.total }} resultados
                                </div>
                                <div class="flex gap-2">
                                    <a
                                        v-for="link in movements.links"
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
                            Nenhum movimento encontrado.
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Dialog for Create -->
        <Dialog v-model:open="showDialog">
            <DialogContent class="sm:max-w-[525px]">
                <DialogHeader>
                    <DialogTitle>Novo Movimento</DialogTitle>
                </DialogHeader>
                <form @submit.prevent="submitForm" class="space-y-4">
                    <div>
                        <Label for="entity_id">Cliente</Label>
                        <select
                            id="entity_id"
                            v-model="form.entity_id"
                            required
                            class="h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm"
                        >
                            <option value="">Selecione um cliente</option>
                            <option v-for="client in clients" :key="client.id" :value="client.id">
                                {{ client.name }}
                            </option>
                        </select>
                    </div>
                    <div>
                        <Label for="movement_date">Data</Label>
                        <Input
                            id="movement_date"
                            v-model="form.movement_date"
                            type="date"
                            required
                        />
                    </div>
                    <div>
                        <Label for="type">Tipo</Label>
                        <select
                            id="type"
                            v-model="form.type"
                            required
                            class="h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm"
                        >
                            <option value="debit">Débito (Cliente deve)</option>
                            <option value="credit">Crédito (Cliente pagou)</option>
                        </select>
                    </div>
                    <div>
                        <Label for="amount">Valor</Label>
                        <Input
                            id="amount"
                            v-model="form.amount"
                            type="number"
                            step="0.01"
                            required
                        />
                    </div>
                    <div>
                        <Label for="description">Descrição</Label>
                        <Input
                            id="description"
                            v-model="form.description"
                            required
                        />
                    </div>
                    <div>
                        <Label for="document_number">Número Documento (opcional)</Label>
                        <Input
                            id="document_number"
                            v-model="form.document_number"
                        />
                    </div>
                    <div class="flex justify-end gap-2">
                        <Button type="button" variant="outline" @click="showDialog = false">
                            Cancelar
                        </Button>
                        <Button type="submit">Criar</Button>
                    </div>
                </form>
            </DialogContent>
        </Dialog>
    </AuthenticatedLayout>
</template>
