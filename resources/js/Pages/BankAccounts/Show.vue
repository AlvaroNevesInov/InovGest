<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, router } from '@inertiajs/vue3';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/Components/ui/card';
import { Button } from '@/Components/ui/button';
import { Badge } from '@/Components/ui/badge';

const props = defineProps({
    bankAccount: {
        type: Object,
        required: true,
    },
});

const formatCurrency = (value, currency = 'EUR') => {
    return new Intl.NumberFormat('pt-PT', {
        style: 'currency',
        currency: currency,
    }).format(value);
};

const deleteBankAccount = () => {
    if (confirm('Tem a certeza que deseja eliminar esta conta bancária?')) {
        router.delete(route('bank-accounts.destroy', props.bankAccount.id));
    }
};
</script>

<template>
    <AuthenticatedLayout>
        <Head :title="`Conta Bancária: ${bankAccount.name}`" />

        <div class="py-12">
            <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
                <Card>
                    <CardHeader>
                        <div class="flex items-start justify-between">
                            <div>
                                <CardTitle class="flex items-center gap-3">
                                    {{ bankAccount.name }}
                                    <Badge :variant="bankAccount.active ? 'default' : 'secondary'">
                                        {{ bankAccount.active ? 'Ativa' : 'Inativa' }}
                                    </Badge>
                                </CardTitle>
                                <CardDescription>Detalhes da conta bancária</CardDescription>
                            </div>
                            <div class="flex gap-2">
                                <Button
                                    variant="outline"
                                    size="sm"
                                    @click="$inertia.visit(route('bank-accounts.edit', bankAccount.id))"
                                >
                                    Editar
                                </Button>
                                <Button
                                    variant="destructive"
                                    size="sm"
                                    @click="deleteBankAccount"
                                >
                                    Eliminar
                                </Button>
                            </div>
                        </div>
                    </CardHeader>
                    <CardContent>
                        <div class="space-y-6">
                            <!-- Informações Bancárias -->
                            <div>
                                <h3 class="text-lg font-semibold mb-4">Informações Bancárias</h3>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <p class="text-sm text-gray-500">Nome da Conta</p>
                                        <p class="font-medium">{{ bankAccount.name }}</p>
                                    </div>
                                    <div>
                                        <p class="text-sm text-gray-500">Nome do Banco</p>
                                        <p class="font-medium">{{ bankAccount.bank_name }}</p>
                                    </div>
                                    <div>
                                        <p class="text-sm text-gray-500">IBAN</p>
                                        <p class="font-mono text-sm">{{ bankAccount.iban }}</p>
                                    </div>
                                    <div v-if="bankAccount.swift_bic">
                                        <p class="text-sm text-gray-500">SWIFT/BIC</p>
                                        <p class="font-mono text-sm">{{ bankAccount.swift_bic }}</p>
                                    </div>
                                    <div>
                                        <p class="text-sm text-gray-500">Moeda</p>
                                        <p class="font-medium">{{ bankAccount.currency }}</p>
                                    </div>
                                    <div>
                                        <p class="text-sm text-gray-500">Estado</p>
                                        <p class="font-medium">{{ bankAccount.active ? 'Ativa' : 'Inativa' }}</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Saldos -->
                            <div>
                                <h3 class="text-lg font-semibold mb-4">Saldos</h3>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <p class="text-sm text-gray-500">Saldo Inicial</p>
                                        <p class="text-2xl font-bold">{{ formatCurrency(bankAccount.initial_balance, bankAccount.currency) }}</p>
                                    </div>
                                    <div>
                                        <p class="text-sm text-gray-500">Saldo Atual</p>
                                        <p
                                            class="text-2xl font-bold"
                                            :class="{
                                                'text-green-600': bankAccount.current_balance >= 0,
                                                'text-red-600': bankAccount.current_balance < 0
                                            }"
                                        >
                                            {{ formatCurrency(bankAccount.current_balance, bankAccount.currency) }}
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <!-- Notas -->
                            <div v-if="bankAccount.notes">
                                <h3 class="text-lg font-semibold mb-2">Notas</h3>
                                <p class="text-gray-700 whitespace-pre-wrap">{{ bankAccount.notes }}</p>
                            </div>

                            <!-- Datas -->
                            <div>
                                <h3 class="text-lg font-semibold mb-4">Informações do Sistema</h3>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <p class="text-sm text-gray-500">Data de Criação</p>
                                        <p class="font-medium">{{ new Date(bankAccount.created_at).toLocaleString('pt-PT') }}</p>
                                    </div>
                                    <div>
                                        <p class="text-sm text-gray-500">Última Atualização</p>
                                        <p class="font-medium">{{ new Date(bankAccount.updated_at).toLocaleString('pt-PT') }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Botão Voltar -->
                        <div class="mt-8 pt-6 border-t">
                            <Button
                                variant="outline"
                                @click="$inertia.visit(route('bank-accounts.index'))"
                            >
                                ← Voltar à Lista
                            </Button>
                        </div>
                    </CardContent>
                </Card>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
