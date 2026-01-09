<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, router } from '@inertiajs/vue3';
import { ref } from 'vue';
import { Button } from '@/Components/ui/button';
import { Input } from '@/Components/ui/input';
import { Table, TableHeader, TableBody, TableRow, TableHead, TableCell } from '@/Components/ui/table';
import { Dialog, DialogContent, DialogHeader, DialogTitle, DialogTrigger } from '@/Components/ui/dialog';
import { Label } from '@/Components/ui/label';

const props = defineProps({
    bankAccounts: Array
});

const showDialog = ref(false);
const editingAccount = ref(null);
const form = ref({
    bank_name: '',
    account_number: '',
    iban: '',
    swift: '',
    initial_balance: 0
});

const openCreateDialog = () => {
    editingAccount.value = null;
    form.value = {
        bank_name: '',
        account_number: '',
        iban: '',
        swift: '',
        initial_balance: 0
    };
    showDialog.value = true;
};

const openEditDialog = (account) => {
    editingAccount.value = account;
    form.value = {
        bank_name: account.bank_name,
        account_number: account.account_number,
        iban: account.iban,
        swift: account.swift || '',
        initial_balance: account.initial_balance
    };
    showDialog.value = true;
};

const submitForm = () => {
    if (editingAccount.value) {
        router.put(route('bank-accounts.update', editingAccount.value.id), form.value, {
            onSuccess: () => {
                showDialog.value = false;
            }
        });
    } else {
        router.post(route('bank-accounts.store'), form.value, {
            onSuccess: () => {
                showDialog.value = false;
            }
        });
    }
};

const deleteAccount = (id) => {
    if (confirm('Tem a certeza que deseja eliminar esta conta bancária?')) {
        router.delete(route('bank-accounts.destroy', id));
    }
};

const formatPrice = (price) => {
    return new Intl.NumberFormat('pt-PT', {
        style: 'currency',
        currency: 'EUR'
    }).format(price);
};
</script>

<template>
    <Head title="Contas Bancárias" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
                    Contas Bancárias
                </h2>
                <Button @click="openCreateDialog">Nova Conta Bancária</Button>
            </div>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg dark:bg-gray-800">
                    <div class="p-6">
                        <div v-if="bankAccounts.length > 0">
                            <Table>
                                <TableHeader>
                                    <TableRow>
                                        <TableHead>Banco</TableHead>
                                        <TableHead>Número Conta</TableHead>
                                        <TableHead>IBAN</TableHead>
                                        <TableHead>SWIFT</TableHead>
                                        <TableHead>Saldo Inicial</TableHead>
                                        <TableHead>Saldo Atual</TableHead>
                                        <TableHead class="text-right">Ações</TableHead>
                                    </TableRow>
                                </TableHeader>
                                <TableBody>
                                    <TableRow v-for="account in bankAccounts" :key="account.id">
                                        <TableCell class="font-medium">{{ account.bank_name }}</TableCell>
                                        <TableCell>{{ account.account_number }}</TableCell>
                                        <TableCell>{{ account.iban }}</TableCell>
                                        <TableCell>{{ account.swift || '-' }}</TableCell>
                                        <TableCell>{{ formatPrice(account.initial_balance) }}</TableCell>
                                        <TableCell>{{ formatPrice(account.current_balance) }}</TableCell>
                                        <TableCell class="text-right">
                                            <div class="flex justify-end gap-2">
                                                <Button
                                                    variant="ghost"
                                                    size="sm"
                                                    @click="openEditDialog(account)"
                                                >
                                                    Editar
                                                </Button>
                                                <Button
                                                    variant="ghost"
                                                    size="sm"
                                                    @click="deleteAccount(account.id)"
                                                >
                                                    Eliminar
                                                </Button>
                                            </div>
                                        </TableCell>
                                    </TableRow>
                                </TableBody>
                            </Table>
                        </div>
                        <div v-else class="py-12 text-center text-gray-500 dark:text-gray-400">
                            Nenhuma conta bancária encontrada.
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Dialog for Create/Edit -->
        <Dialog v-model:open="showDialog">
            <DialogContent class="sm:max-w-[525px]">
                <DialogHeader>
                    <DialogTitle>
                        {{ editingAccount ? 'Editar Conta Bancária' : 'Nova Conta Bancária' }}
                    </DialogTitle>
                </DialogHeader>
                <form @submit.prevent="submitForm" class="space-y-4">
                    <div>
                        <Label for="bank_name">Nome do Banco</Label>
                        <Input
                            id="bank_name"
                            v-model="form.bank_name"
                            required
                        />
                    </div>
                    <div>
                        <Label for="account_number">Número da Conta</Label>
                        <Input
                            id="account_number"
                            v-model="form.account_number"
                            required
                        />
                    </div>
                    <div>
                        <Label for="iban">IBAN</Label>
                        <Input
                            id="iban"
                            v-model="form.iban"
                            required
                        />
                    </div>
                    <div>
                        <Label for="swift">SWIFT (opcional)</Label>
                        <Input
                            id="swift"
                            v-model="form.swift"
                        />
                    </div>
                    <div>
                        <Label for="initial_balance">Saldo Inicial</Label>
                        <Input
                            id="initial_balance"
                            v-model="form.initial_balance"
                            type="number"
                            step="0.01"
                            required
                        />
                    </div>
                    <div class="flex justify-end gap-2">
                        <Button type="button" variant="outline" @click="showDialog = false">
                            Cancelar
                        </Button>
                        <Button type="submit">
                            {{ editingAccount ? 'Atualizar' : 'Criar' }}
                        </Button>
                    </div>
                </form>
            </DialogContent>
        </Dialog>
    </AuthenticatedLayout>
</template>
