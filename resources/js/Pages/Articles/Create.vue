<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { Button } from '@/Components/ui/button';
import { Input } from '@/Components/ui/input';
import { Label } from '@/Components/ui/label';
import { Textarea } from '@/Components/ui/textarea';
import { Card, CardHeader, CardTitle, CardContent } from '@/Components/ui/card';
import InputError from '@/Components/InputError.vue';

const props = defineProps({
    vatRates: Array
});

const form = useForm({
    reference: '',
    name: '',
    description: '',
    price: '',
    vat_rate_id: '',
    photo: null,
    notes: '',
    active: true
});

const submit = () => {
    form.post(route('settings.articles.store'));
};

const handlePhotoChange = (event) => {
    form.photo = event.target.files[0];
};
</script>

<template>
    <Head title="Novo Artigo" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
                    Novo Artigo
                </h2>
                <Link :href="route('settings.articles.index')">
                    <Button variant="outline">Voltar</Button>
                </Link>
            </div>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-4xl sm:px-6 lg:px-8">
                <Card>
                    <CardHeader>
                        <CardTitle>Dados do Artigo</CardTitle>
                    </CardHeader>
                    <CardContent>
                        <form @submit.prevent="submit" class="space-y-6">
                            <!-- Referência -->
                            <div>
                                <Label for="reference">Referência *</Label>
                                <Input
                                    id="reference"
                                    v-model="form.reference"
                                    type="text"
                                    class="mt-1"
                                    required
                                    placeholder="Ex: ART-00001"
                                />
                                <InputError :message="form.errors.reference" class="mt-2" />
                            </div>

                            <!-- Nome -->
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

                            <!-- Descrição -->
                            <div>
                                <Label for="description">Descrição</Label>
                                <Textarea
                                    id="description"
                                    v-model="form.description"
                                    class="mt-1"
                                    rows="3"
                                />
                                <InputError :message="form.errors.description" class="mt-2" />
                            </div>

                            <!-- Preço e IVA -->
                            <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                                <div>
                                    <Label for="price">Preço (sem IVA) *</Label>
                                    <Input
                                        id="price"
                                        v-model="form.price"
                                        type="number"
                                        step="0.01"
                                        min="0"
                                        class="mt-1"
                                        required
                                    />
                                    <InputError :message="form.errors.price" class="mt-2" />
                                </div>

                                <div>
                                    <Label for="vat_rate_id">Taxa de IVA *</Label>
                                    <select
                                        id="vat_rate_id"
                                        v-model="form.vat_rate_id"
                                        class="mt-1 block w-full h-10 rounded-md border border-input bg-background px-3 py-2 text-sm"
                                        required
                                    >
                                        <option value="">Selecione...</option>
                                        <option v-for="vat in vatRates" :key="vat.id" :value="vat.id">
                                            {{ vat.name }}
                                        </option>
                                    </select>
                                    <InputError :message="form.errors.vat_rate_id" class="mt-2" />
                                </div>
                            </div>

                            <!-- Foto -->
                            <div>
                                <Label for="photo">Foto</Label>
                                <Input
                                    id="photo"
                                    type="file"
                                    accept="image/*"
                                    class="mt-1"
                                    @change="handlePhotoChange"
                                />
                                <p class="mt-1 text-sm text-gray-500">Máximo 2MB</p>
                                <InputError :message="form.errors.photo" class="mt-2" />
                            </div>

                            <!-- Observações -->
                            <div>
                                <Label for="notes">Observações</Label>
                                <Textarea
                                    id="notes"
                                    v-model="form.notes"
                                    class="mt-1"
                                    rows="2"
                                />
                                <InputError :message="form.errors.notes" class="mt-2" />
                            </div>

                            <!-- Estado -->
                            <div class="flex items-center space-x-2">
                                <input
                                    id="active"
                                    v-model="form.active"
                                    type="checkbox"
                                    class="h-4 w-4 rounded border-gray-300"
                                />
                                <Label for="active" class="!mb-0">Ativo</Label>
                            </div>

                            <!-- Botões -->
                            <div class="flex justify-end gap-4">
                                <Link :href="route('settings.articles.index')">
                                    <Button type="button" variant="outline">Cancelar</Button>
                                </Link>
                                <Button type="submit" :disabled="form.processing">
                                    {{ form.processing ? 'Guardando...' : 'Guardar' }}
                                </Button>
                            </div>
                        </form>
                    </CardContent>
                </Card>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
