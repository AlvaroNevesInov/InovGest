<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/Components/ui/card';
import { Button } from '@/Components/ui/button';
import { Input } from '@/Components/ui/input';
import { Label } from '@/Components/ui/label';
import { Textarea } from '@/Components/ui/textarea';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/Components/ui/select';

const form = useForm({
    name: '',
    bank_name: '',
    iban: '',
    swift_bic: '',
    currency: 'EUR',
    initial_balance: 0,
    notes: '',
    active: true,
});

const submit = () => {
    form.post(route('bank-accounts.store'), {
        onSuccess: () => {
            // Redirect handled by controller
        },
    });
};

const currencies = [
    { value: 'EUR', label: 'EUR - Euro' },
    { value: 'USD', label: 'USD - Dólar Americano' },
    { value: 'GBP', label: 'GBP - Libra Esterlina' },
    { value: 'CHF', label: 'CHF - Franco Suíço' },
    { value: 'BRL', label: 'BRL - Real Brasileiro' },
];
</script>

<template>
    <AuthenticatedLayout>
        <Head title="Nova Conta Bancária" />

        <div class="py-12">
            <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
                <Card>
                    <CardHeader>
                        <CardTitle>Nova Conta Bancária</CardTitle>
                        <CardDescription>Adicione uma nova conta bancária ao sistema</CardDescription>
                    </CardHeader>
                    <CardContent>
                        <form @submit.prevent="submit" class="space-y-6">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- Nome da Conta -->
                                <div class="space-y-2">
                                    <Label for="name">Nome da Conta *</Label>
                                    <Input
                                        id="name"
                                        v-model="form.name"
                                        type="text"
                                        placeholder="Ex: Conta Principal"
                                        :class="{ 'border-red-500': form.errors.name }"
                                        required
                                    />
                                    <p v-if="form.errors.name" class="text-sm text-red-500">{{ form.errors.name }}</p>
                                </div>

                                <!-- Nome do Banco -->
                                <div class="space-y-2">
                                    <Label for="bank_name">Nome do Banco *</Label>
                                    <Input
                                        id="bank_name"
                                        v-model="form.bank_name"
                                        type="text"
                                        placeholder="Ex: Banco Exemplo"
                                        :class="{ 'border-red-500': form.errors.bank_name }"
                                        required
                                    />
                                    <p v-if="form.errors.bank_name" class="text-sm text-red-500">{{ form.errors.bank_name }}</p>
                                </div>

                                <!-- IBAN -->
                                <div class="space-y-2">
                                    <Label for="iban">IBAN *</Label>
                                    <Input
                                        id="iban"
                                        v-model="form.iban"
                                        type="text"
                                        placeholder="PT50000000000000000000000"
                                        :class="{ 'border-red-500': form.errors.iban }"
                                        required
                                    />
                                    <p v-if="form.errors.iban" class="text-sm text-red-500">{{ form.errors.iban }}</p>
                                </div>

                                <!-- SWIFT/BIC -->
                                <div class="space-y-2">
                                    <Label for="swift_bic">SWIFT/BIC</Label>
                                    <Input
                                        id="swift_bic"
                                        v-model="form.swift_bic"
                                        type="text"
                                        placeholder="Ex: ABCDEFGH"
                                        :class="{ 'border-red-500': form.errors.swift_bic }"
                                    />
                                    <p v-if="form.errors.swift_bic" class="text-sm text-red-500">{{ form.errors.swift_bic }}</p>
                                </div>

                                <!-- Moeda -->
                                <div class="space-y-2">
                                    <Label for="currency">Moeda *</Label>
                                    <Select v-model="form.currency" required>
                                        <SelectTrigger :class="{ 'border-red-500': form.errors.currency }">
                                            <SelectValue placeholder="Selecione a moeda" />
                                        </SelectTrigger>
                                        <SelectContent>
                                            <SelectItem v-for="currency in currencies" :key="currency.value" :value="currency.value">
                                                {{ currency.label }}
                                            </SelectItem>
                                        </SelectContent>
                                    </Select>
                                    <p v-if="form.errors.currency" class="text-sm text-red-500">{{ form.errors.currency }}</p>
                                </div>

                                <!-- Saldo Inicial -->
                                <div class="space-y-2">
                                    <Label for="initial_balance">Saldo Inicial *</Label>
                                    <Input
                                        id="initial_balance"
                                        v-model.number="form.initial_balance"
                                        type="number"
                                        step="0.01"
                                        placeholder="0.00"
                                        :class="{ 'border-red-500': form.errors.initial_balance }"
                                        required
                                    />
                                    <p v-if="form.errors.initial_balance" class="text-sm text-red-500">{{ form.errors.initial_balance }}</p>
                                </div>
                            </div>

                            <!-- Notas -->
                            <div class="space-y-2">
                                <Label for="notes">Notas</Label>
                                <Textarea
                                    id="notes"
                                    v-model="form.notes"
                                    rows="4"
                                    placeholder="Informações adicionais sobre a conta"
                                    :class="{ 'border-red-500': form.errors.notes }"
                                />
                                <p v-if="form.errors.notes" class="text-sm text-red-500">{{ form.errors.notes }}</p>
                            </div>

                            <!-- Ativa -->
                            <div class="flex items-center gap-2">
                                <input
                                    id="active"
                                    v-model="form.active"
                                    type="checkbox"
                                    class="rounded border-gray-300 text-primary focus:ring-primary"
                                />
                                <Label for="active" class="!mb-0 cursor-pointer">Conta Ativa</Label>
                            </div>

                            <!-- Botões -->
                            <div class="flex items-center justify-end space-x-4 pt-4">
                                <Button
                                    type="button"
                                    variant="outline"
                                    @click="$inertia.visit(route('bank-accounts.index'))"
                                    :disabled="form.processing"
                                >
                                    Cancelar
                                </Button>
                                <Button
                                    type="submit"
                                    :disabled="form.processing"
                                >
                                    <span v-if="form.processing">A criar...</span>
                                    <span v-else>Criar Conta Bancária</span>
                                </Button>
                            </div>
                        </form>
                    </CardContent>
                </Card>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
