<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/Components/ui/card';
import { Button } from '@/Components/ui/button';
import { Input } from '@/Components/ui/input';
import { Label } from '@/Components/ui/label';

const form = useForm({
    name: '',
    slug: '',
});

const submit = () => {
    form.post(route('tenants.store'), {
        onSuccess: () => {
            // Redirect handled by controller
        },
    });
};

const updateSlug = () => {
    if (!form.slug || form.slug === '') {
        form.slug = form.name
            .toLowerCase()
            .normalize('NFD')
            .replace(/[\u0300-\u036f]/g, '')
            .replace(/[^a-z0-9]+/g, '-')
            .replace(/^-+|-+$/g, '');
    }
};
</script>

<template>
    <AuthenticatedLayout>
        <Head title="Criar Novo Tenant" />

        <div class="py-12">
            <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
                <Card>
                    <CardHeader>
                        <CardTitle>Criar Novo Tenant</CardTitle>
                        <CardDescription>
                            Crie um novo espaço de trabalho (tenant) para organizar seus dados de forma independente.
                            Você será automaticamente definido como proprietário deste tenant.
                        </CardDescription>
                    </CardHeader>
                    <CardContent>
                        <form @submit.prevent="submit" class="space-y-6">
                            <div class="space-y-6">
                                <!-- Nome do Tenant -->
                                <div class="space-y-2">
                                    <Label for="name">Nome do Tenant *</Label>
                                    <Input
                                        id="name"
                                        v-model="form.name"
                                        type="text"
                                        placeholder="Ex: Minha Empresa"
                                        :class="{ 'border-red-500': form.errors.name }"
                                        @blur="updateSlug"
                                        required
                                    />
                                    <p v-if="form.errors.name" class="text-sm text-red-500">{{ form.errors.name }}</p>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">
                                        O nome que identifica este espaço de trabalho.
                                    </p>
                                </div>

                                <!-- Slug -->
                                <div class="space-y-2">
                                    <Label for="slug">Slug</Label>
                                    <Input
                                        id="slug"
                                        v-model="form.slug"
                                        type="text"
                                        placeholder="Ex: minha-empresa"
                                        :class="{ 'border-red-500': form.errors.slug }"
                                    />
                                    <p v-if="form.errors.slug" class="text-sm text-red-500">{{ form.errors.slug }}</p>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">
                                        Identificador único para o tenant. Será gerado automaticamente se deixado em branco.
                                    </p>
                                </div>
                            </div>

                            <!-- Botões de Ação -->
                            <div class="flex items-center justify-end gap-4 pt-4">
                                <Button
                                    type="button"
                                    variant="outline"
                                    @click="$inertia.visit(route('dashboard'))"
                                    :disabled="form.processing"
                                >
                                    Cancelar
                                </Button>
                                <Button
                                    type="submit"
                                    :disabled="form.processing"
                                    class="bg-blue-600 hover:bg-blue-700 text-white"
                                >
                                    <svg
                                        v-if="form.processing"
                                        class="animate-spin -ml-1 mr-2 h-4 w-4 text-white"
                                        xmlns="http://www.w3.org/2000/svg"
                                        fill="none"
                                        viewBox="0 0 24 24"
                                    >
                                        <circle
                                            class="opacity-25"
                                            cx="12"
                                            cy="12"
                                            r="10"
                                            stroke="currentColor"
                                            stroke-width="4"
                                        ></circle>
                                        <path
                                            class="opacity-75"
                                            fill="currentColor"
                                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
                                        ></path>
                                    </svg>
                                    {{ form.processing ? 'Criando...' : 'Criar Tenant' }}
                                </Button>
                            </div>
                        </form>
                    </CardContent>
                </Card>

                <!-- Informação Adicional -->
                <div class="mt-6 bg-blue-50 dark:bg-blue-900/20 rounded-lg p-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-blue-800 dark:text-blue-200">
                                O que é um Tenant?
                            </h3>
                            <div class="mt-2 text-sm text-blue-700 dark:text-blue-300">
                                <p>
                                    Um tenant é um espaço de trabalho isolado que permite organizar diferentes empresas,
                                    projetos ou departamentos de forma independente. Você pode criar múltiplos tenants
                                    e alternar entre eles facilmente.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
