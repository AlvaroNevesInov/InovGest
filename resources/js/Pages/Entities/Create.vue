<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { Button } from '@/Components/ui/button';
import { Input } from '@/Components/ui/input';
import { Label } from '@/Components/ui/label';
import { Card, CardHeader, CardTitle, CardContent } from '@/Components/ui/card';
import InputError from '@/Components/InputError.vue';


const props = defineProps({
    nextNumber: Number,
    countries: Array
});

const form = useForm({
    type: 'client',
    nif: '',
    name: '',
    address: '',
    postal_code: '',
    city: '',
    country_id: '',
    phone: '',
    mobile: '',
    website: '',
    email: '',
    rgpd_consent: false,
    notes: '',
    active: true
});

const submit = () => {
    form.post(route('entities.store'));
};
</script>

<template>
    <Head title="Nova Entidade" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
                    Nova Entidade
                </h2>
                <Link :href="route('entities.index')">
                    <Button variant="outline">Voltar</Button>
                </Link>
            </div>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-4xl sm:px-6 lg:px-8">
                <Card>
                    <CardHeader>
                        <CardTitle>Dados da Entidade</CardTitle>
                    </CardHeader>
                    <CardContent>
                        <form @submit.prevent="submit" class="space-y-6">
                            <!-- Tipo -->
                            <div>
                                <Label for="type">Tipo *</Label>
                                <select
                                    id="type"
                                    v-model="form.type"
                                    class="mt-1 block w-full h-10 rounded-md border border-input bg-background px-3 py-2 text-sm"
                                    required
                                >
                                    <option value="client">Cliente</option>
                                    <option value="supplier">Fornecedor</option>
                                    <option value="both">Ambos</option>
                                </select>
                                <InputError :message="form.errors.type" class="mt-2" />
                            </div>

                            <!-- Número -->
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

                            <!-- NIF e Nome -->
                            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                                <div>
                                    <Label for="nif">NIF *</Label>
                                    <Input
                                        id="nif"
                                        v-model="form.nif"
                                        type="text"
                                        class="mt-1"
                                        required
                                        placeholder="999999999"
                                    />
                                    <InputError :message="form.errors.nif" class="mt-2" />
                                </div>

                                <div>
                                    <Label for="name">Nome *</Label>
                                    <Input
                                        id="name"
                                        v-model="form.name"
                                        type="text"
                                        class="mt-1"
                                        required
                                    />
                                    <InputError :message="form.errors.name" class="mt-2" />
                                </div>
                            </div>

                            <!-- Morada -->
                            <div>
                                <Label for="address">Morada</Label>
                                <Input
                                    id="address"
                                    v-model="form.address"
                                    type="text"
                                    class="mt-1"
                                />
                                <InputError :message="form.errors.address" class="mt-2" />
                            </div>

                            <!-- Código Postal e Localidade -->
                            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                                <div>
                                    <Label for="postal_code">Código Postal</Label>
                                    <Input
                                        id="postal_code"
                                        v-model="form.postal_code"
                                        type="text"
                                        class="mt-1"
                                        placeholder="0000-000"
                                    />
                                    <InputError :message="form.errors.postal_code" class="mt-2" />
                                </div>

                                <div>
                                    <Label for="city">Localidade</Label>
                                    <Input
                                        id="city"
                                        v-model="form.city"
                                        type="text"
                                        class="mt-1"
                                    />
                                    <InputError :message="form.errors.city" class="mt-2" />
                                </div>
                            </div>

                            <!-- País -->
                            <div>
                                <Label for="country_id">País</Label>
                                <select
                                    id="country_id"
                                    v-model="form.country_id"
                                    class="mt-1 block w-full h-10 rounded-md border border-input bg-background px-3 py-2 text-sm"
                                >
                                    <option value="">Selecione um país</option>
                                    <option v-for="country in countries" :key="country.id" :value="country.id">
                                        {{ country.name }}
                                    </option>
                                </select>
                                <InputError :message="form.errors.country_id" class="mt-2" />
                            </div>

                            <!-- Telefone e Telemóvel -->
                            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                                <div>
                                    <Label for="phone">Telefone</Label>
                                    <Input
                                        id="phone"
                                        v-model="form.phone"
                                        type="text"
                                        class="mt-1"
                                    />
                                    <InputError :message="form.errors.phone" class="mt-2" />
                                </div>

                                <div>
                                    <Label for="mobile">Telemóvel</Label>
                                    <Input
                                        id="mobile"
                                        v-model="form.mobile"
                                        type="text"
                                        class="mt-1"
                                    />
                                    <InputError :message="form.errors.mobile" class="mt-2" />
                                </div>
                            </div>

                            <!-- Email e Website -->
                            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                                <div>
                                    <Label for="email">Email</Label>
                                    <Input
                                        id="email"
                                        v-model="form.email"
                                        type="email"
                                        class="mt-1"
                                    />
                                    <InputError :message="form.errors.email" class="mt-2" />
                                </div>

                                <div>
                                    <Label for="website">Website</Label>
                                    <Input
                                        id="website"
                                        v-model="form.website"
                                        type="url"
                                        class="mt-1"
                                        placeholder="https://"
                                    />
                                    <InputError :message="form.errors.website" class="mt-2" />
                                </div>
                            </div>

                            <!-- Observações -->
                            <div>
                                <Label for="notes">Observações</Label>
                                <textarea
                                    id="notes"
                                    v-model="form.notes"
                                    rows="3"
                                    class="mt-1 block w-full rounded-md border border-input bg-background px-3 py-2 text-sm"
                                />
                                <InputError :message="form.errors.notes" class="mt-2" />
                            </div>

                            <!-- Checkboxes -->
                            <div class="space-y-2">
                                <div class="flex items-center gap-2">
                                    <input
                                        id="rgpd_consent"
                                        v-model="form.rgpd_consent"
                                        type="checkbox"
                                        class="rounded border-gray-300 text-primary focus:ring-primary"
                                    />
                                    <Label for="rgpd_consent" class="!mb-0">Consentimento RGPD</Label>
                                </div>

                                <div class="flex items-center gap-2">
                                    <input
                                        id="active"
                                        v-model="form.active"
                                        type="checkbox"
                                        class="rounded border-gray-300 text-primary focus:ring-primary"
                                    />
                                    <Label for="active" class="!mb-0">Ativo</Label>
                                </div>
                            </div>

                            <!-- Botões -->
                            <div class="flex items-center justify-end gap-4">
                                <Link :href="route('entities.index')">
                                    <Button type="button" variant="outline">Cancelar</Button>
                                </Link>
                                <Button type="submit" :disabled="form.processing">
                                    {{ form.processing ? 'A guardar...' : 'Guardar' }}
                                </Button>
                            </div>
                        </form>
                    </CardContent>
                </Card>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
