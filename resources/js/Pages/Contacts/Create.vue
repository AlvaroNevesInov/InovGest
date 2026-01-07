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
    entities: Array,
    contactFunctions: Array,
    entityId: [Number, String]
});

const form = useForm({
    entity_id: props.entityId || '',
    name: '',
    surname: '',
    contact_function_id: '',
    phone: '',
    mobile: '',
    email: '',
    rgpd_consent: false,
    notes: '',
    active: true
});

const submit = () => {
    form.post(route('contacts.store'));
};
</script>

<template>
    <Head title="Novo Contacto" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
                    Novo Contacto
                </h2>
                <Link :href="route('contacts.index')">
                    <Button variant="outline">Voltar</Button>
                </Link>
            </div>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-4xl sm:px-6 lg:px-8">
                <Card>
                    <CardHeader>
                        <CardTitle>Dados do Contacto</CardTitle>
                    </CardHeader>
                    <CardContent>
                        <form @submit.prevent="submit" class="space-y-6">
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

                            <!-- Entidade -->
                            <div>
                                <Label for="entity_id">Entidade *</Label>
                                <select
                                    id="entity_id"
                                    v-model="form.entity_id"
                                    class="mt-1 block w-full h-10 rounded-md border border-input bg-background px-3 py-2 text-sm"
                                    required
                                >
                                    <option value="">Selecione uma entidade</option>
                                    <option v-for="entity in entities" :key="entity.id" :value="entity.id">
                                        {{ entity.name }} ({{ entity.type === 'client' ? 'Cliente' : entity.type === 'supplier' ? 'Fornecedor' : 'Ambos' }})
                                    </option>
                                </select>
                                <InputError :message="form.errors.entity_id" class="mt-2" />
                            </div>

                            <!-- Nome e Apelido -->
                            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
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

                                <div>
                                    <Label for="surname">Apelido</Label>
                                    <Input
                                        id="surname"
                                        v-model="form.surname"
                                        type="text"
                                        class="mt-1"
                                    />
                                    <InputError :message="form.errors.surname" class="mt-2" />
                                </div>
                            </div>

                            <!-- Função -->
                            <div>
                                <Label for="contact_function_id">Função</Label>
                                <select
                                    id="contact_function_id"
                                    v-model="form.contact_function_id"
                                    class="mt-1 block w-full h-10 rounded-md border border-input bg-background px-3 py-2 text-sm"
                                >
                                    <option value="">Selecione uma função</option>
                                    <option v-for="func in contactFunctions" :key="func.id" :value="func.id">
                                        {{ func.name }}
                                    </option>
                                </select>
                                <InputError :message="form.errors.contact_function_id" class="mt-2" />
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

                            <!-- Email -->
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
                                <Link :href="route('contacts.index')">
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
