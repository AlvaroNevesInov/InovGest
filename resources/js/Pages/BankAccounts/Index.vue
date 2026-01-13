<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref } from 'vue';
import { Button } from '@/Components/ui/button';
import { Input } from '@/Components/ui/input';
import { Card, CardHeader, CardTitle, CardContent } from '@/Components/ui/card';
import { Table, TableHeader, TableBody, TableRow, TableHead, TableCell } from '@/Components/ui/table';
import { Badge } from '@/Components/ui/badge';

const props = defineProps({
    bankAccounts: Object,
    filters: Object
});

const searchTerm = ref(props.filters?.search || '');
const activeFilter = ref(props.filters?.active || '');

const search = () => {
    router.get(route('bank-accounts.index'), {
        search: searchTerm.value,
        active: activeFilter.value
    }, {
        preserveState: true,
        replace: true
    });
};

const deleteAccount = (id) => {
    if (confirm('Tem a certeza que deseja eliminar esta conta bancária?')) {
        router.delete(route('bank-accounts.destroy', id));
    }
};

const formatCurrency = (value, currency = 'EUR') => {
    return new Intl.NumberFormat('pt-PT', {
        style: 'currency',
        currency: currency
    }).format(value);
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
                <Link :href="route('bank-accounts.create')">
                    <Button>Nova Conta Bancária</Button>
                </Link>
            </div>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <Card>
                    <CardHeader>
                        <CardTitle>Lista de Contas Bancárias</CardTitle>
                    </CardHeader>
                    <CardContent>
                        <!-- Filtros -->
                        <div class="mb-6 flex gap-4">
                            <div class="flex-1">
                                <Input
                                    v-model="searchTerm"
                                    type="text"
                                    placeholder="Pesquisar por nome, banco ou IBAN..."
                                    @keyup.enter="search"
                                />
                            </div>
                            <select
                                v-model="activeFilter"
                                class="rounded-md border border-input bg-background px-3 py-2 text-sm"
                                @change="search"
                            >
                                <option value="">Todas</option>
                                <option value="1">Ativas</option>
                                <option value="0">Inativas</option>
                            </select>
                            <Button @click="search">Pesquisar</Button>
                        </div>

                        <!-- Tabela -->
                        <div v-if="bankAccounts.data && bankAccounts.data.length > 0">
                            <div class="overflow-x-auto">
                                <Table>
                                    <TableHeader>
                                        <TableRow>
                                            <TableHead>Nome</TableHead>
                                            <TableHead>Banco</TableHead>
                                            <TableHead>IBAN</TableHead>
                                            <TableHead>Moeda</TableHead>
                                            <TableHead>Saldo Atual</TableHead>
                                            <TableHead>Estado</TableHead>
                                            <TableHead class="text-right">Ações</TableHead>
                                        </TableRow>
                                    </TableHeader>
                                    <TableBody>
                                        <TableRow v-for="account in bankAccounts.data" :key="account.id">
                                            <TableCell class="font-medium">
                                                <Link
                                                    :href="route('bank-accounts.show', account.id)"
                                                    class="text-blue-600 hover:underline"
                                                >
                                                    {{ account.name }}
                                                </Link>
                                            </TableCell>
                                            <TableCell>{{ account.bank_name }}</TableCell>
                                            <TableCell class="font-mono text-sm">{{ account.iban }}</TableCell>
                                            <TableCell>{{ account.currency }}</TableCell>
                                            <TableCell
                                                :class="{
                                                    'text-green-600 font-semibold': account.current_balance >= 0,
                                                    'text-red-600 font-semibold': account.current_balance < 0
                                                }"
                                            >
                                                {{ formatCurrency(account.current_balance, account.currency) }}
                                            </TableCell>
                                            <TableCell>
                                                <Badge :variant="account.active ? 'default' : 'secondary'">
                                                    {{ account.active ? 'Ativa' : 'Inativa' }}
                                                </Badge>
                                            </TableCell>
                                            <TableCell class="text-right">
                                                <div class="flex justify-end gap-2">
                                                    <Link :href="route('bank-accounts.show', account.id)">
                                                        <Button variant="ghost" size="sm">
                                                            Ver
                                                        </Button>
                                                    </Link>
                                                    <Link :href="route('bank-accounts.edit', account.id)">
                                                        <Button variant="ghost" size="sm">
                                                            Editar
                                                        </Button>
                                                    </Link>
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

                            <!-- Paginação -->
                            <div v-if="bankAccounts.links && bankAccounts.links.length > 3" class="mt-6 flex justify-center gap-2">
                                <Link
                                    v-for="(link, index) in bankAccounts.links"
                                    :key="index"
                                    :href="link.url"
                                    :class="[
                                        'px-4 py-2 text-sm rounded-md',
                                        link.active
                                            ? 'bg-primary text-white'
                                            : link.url
                                            ? 'bg-gray-200 hover:bg-gray-300 text-gray-700'
                                            : 'bg-gray-100 text-gray-400 cursor-not-allowed'
                                    ]"
                                    v-html="link.label"
                                />
                            </div>
                        </div>

                        <!-- Estado Vazio -->
                        <div v-else class="py-12 text-center text-gray-500 dark:text-gray-400">
                            <p class="mb-4">Nenhuma conta bancária encontrada.</p>
                            <Link :href="route('bank-accounts.create')">
                                <Button>Criar Primeira Conta</Button>
                            </Link>
                        </div>
                    </CardContent>
                </Card>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
