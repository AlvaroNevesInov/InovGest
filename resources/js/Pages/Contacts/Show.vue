<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { Button } from '@/Components/ui/button';
import { Card, CardHeader, CardTitle, CardContent } from '@/Components/ui/card';
import { Badge } from '@/Components/ui/badge';

const props = defineProps({
    contact: Object
});
</script>

<template>
    <Head :title="`Contacto: ${contact.name}`" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
                    Detalhes do Contacto
                </h2>
                <div class="flex gap-2">
                    <Link :href="route('contacts.edit', contact.id)">
                        <Button variant="outline">Editar</Button>
                    </Link>
                    <Link :href="route('contacts.index')">
                        <Button variant="outline">Voltar</Button>
                    </Link>
                </div>
            </div>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-4xl sm:px-6 lg:px-8">
                <Card>
                    <CardHeader>
                        <div class="flex items-center justify-between">
                            <CardTitle>{{ contact.name }} {{ contact.surname }}</CardTitle>
                            <Badge :variant="contact.active ? 'default' : 'secondary'">
                                {{ contact.active ? 'Ativo' : 'Inativo' }}
                            </Badge>
                        </div>
                    </CardHeader>
                    <CardContent class="space-y-6">
                        <!-- Informação Geral -->
                        <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                            <div>
                                <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">Número</h3>
                                <p class="mt-1 text-gray-900 dark:text-gray-100">{{ contact.number }}</p>
                            </div>

                            <div>
                                <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">Entidade</h3>
                                <Link
                                    :href="route('entities.show', contact.entity.id)"
                                    class="mt-1 text-primary hover:underline"
                                >
                                    {{ contact.entity.name }}
                                </Link>
                            </div>

                            <div>
                                <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">Nome</h3>
                                <p class="mt-1 text-gray-900 dark:text-gray-100">{{ contact.name }}</p>
                            </div>

                            <div v-if="contact.surname">
                                <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">Apelido</h3>
                                <p class="mt-1 text-gray-900 dark:text-gray-100">{{ contact.surname }}</p>
                            </div>

                            <div v-if="contact.contact_function">
                                <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">Função</h3>
                                <p class="mt-1 text-gray-900 dark:text-gray-100">{{ contact.contact_function.name }}</p>
                            </div>
                        </div>

                        <!-- Contactos -->
                        <div class="border-t pt-6">
                            <h3 class="mb-4 text-lg font-semibold">Contactos</h3>
                            <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                                <div v-if="contact.phone">
                                    <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">Telefone</h3>
                                    <p class="mt-1 text-gray-900 dark:text-gray-100">{{ contact.phone }}</p>
                                </div>

                                <div v-if="contact.mobile">
                                    <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">Telemóvel</h3>
                                    <p class="mt-1 text-gray-900 dark:text-gray-100">{{ contact.mobile }}</p>
                                </div>

                                <div v-if="contact.email">
                                    <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">Email</h3>
                                    <a :href="`mailto:${contact.email}`" class="mt-1 text-primary hover:underline">
                                        {{ contact.email }}
                                    </a>
                                </div>
                            </div>
                        </div>

                        <!-- RGPD -->
                        <div class="border-t pt-6">
                            <h3 class="mb-4 text-lg font-semibold">RGPD</h3>
                            <div>
                                <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">Consentimento RGPD</h3>
                                <Badge :variant="contact.rgpd_consent ? 'default' : 'secondary'" class="mt-1">
                                    {{ contact.rgpd_consent ? 'Sim' : 'Não' }}
                                </Badge>
                            </div>
                        </div>

                        <!-- Observações -->
                        <div v-if="contact.notes" class="border-t pt-6">
                            <h3 class="mb-2 text-lg font-semibold">Observações</h3>
                            <p class="text-gray-900 dark:text-gray-100 whitespace-pre-wrap">{{ contact.notes }}</p>
                        </div>

                        <!-- Metadata -->
                        <div class="border-t pt-6">
                            <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                                <div>
                                    <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">Criado em</h3>
                                    <p class="mt-1 text-gray-900 dark:text-gray-100">
                                        {{ new Date(contact.created_at).toLocaleString('pt-PT') }}
                                    </p>
                                </div>

                                <div>
                                    <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">Atualizado em</h3>
                                    <p class="mt-1 text-gray-900 dark:text-gray-100">
                                        {{ new Date(contact.updated_at).toLocaleString('pt-PT') }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </CardContent>
                </Card>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
