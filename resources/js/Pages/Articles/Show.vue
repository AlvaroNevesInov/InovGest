<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { Button } from '@/Components/ui/button';
import { Badge } from '@/Components/ui/badge';
import { Card, CardHeader, CardTitle, CardContent } from '@/Components/ui/card';

const props = defineProps({
    article: Object
});

const formatPrice = (price) => {
    return new Intl.NumberFormat('pt-PT', {
        style: 'currency',
        currency: 'EUR'
    }).format(price);
};

const deleteArticle = () => {
    if (confirm('Tem a certeza que deseja eliminar este artigo?')) {
        router.delete(route('settings.articles.destroy', props.article.id));
    }
};
</script>

<template>
    <Head title="Detalhes do Artigo" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
                    Detalhes do Artigo
                </h2>
                <div class="flex gap-2">
                    <Link :href="route('settings.articles.edit', article.id)">
                        <Button>Editar</Button>
                    </Link>
                    <Link :href="route('settings.articles.index')">
                        <Button variant="outline">Voltar</Button>
                    </Link>
                </div>
            </div>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-4xl sm:px-6 lg:px-8 space-y-6">
                <!-- Informação Principal -->
                <Card>
                    <CardHeader>
                        <div class="flex items-center justify-between">
                            <CardTitle>Informação do Artigo</CardTitle>
                            <Badge :variant="article.active ? 'default' : 'destructive'">
                                {{ article.active ? 'Ativo' : 'Inativo' }}
                            </Badge>
                        </div>
                    </CardHeader>
                    <CardContent>
                        <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                            <div>
                                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Referência</p>
                                <p class="mt-1 text-base">{{ article.reference }}</p>
                            </div>

                            <div>
                                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Nome</p>
                                <p class="mt-1 text-base">{{ article.name }}</p>
                            </div>

                            <div class="md:col-span-2">
                                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Descrição</p>
                                <p class="mt-1 text-base">{{ article.description || '-' }}</p>
                            </div>

                            <div>
                                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Preço (sem IVA)</p>
                                <p class="mt-1 text-lg font-semibold">{{ formatPrice(article.price) }}</p>
                            </div>

                            <div>
                                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Taxa de IVA</p>
                                <p class="mt-1 text-base">{{ article.vat_rate?.name || '-' }}</p>
                            </div>

                            <div v-if="article.notes" class="md:col-span-2">
                                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Observações</p>
                                <p class="mt-1 text-base">{{ article.notes }}</p>
                            </div>
                        </div>
                    </CardContent>
                </Card>

                <!-- Foto -->
                <Card v-if="article.photo">
                    <CardHeader>
                        <CardTitle>Foto</CardTitle>
                    </CardHeader>
                    <CardContent>
                        <img
                            :src="`/storage/${article.photo}`"
                            :alt="article.name"
                            class="max-w-md rounded-lg shadow-md"
                        >
                    </CardContent>
                </Card>

                <!-- Ações -->
                <Card>
                    <CardHeader>
                        <CardTitle>Ações</CardTitle>
                    </CardHeader>
                    <CardContent>
                        <div class="flex gap-4">
                            <Link :href="route('settings.articles.edit', article.id)">
                                <Button>Editar Artigo</Button>
                            </Link>
                            <Button variant="destructive" @click="deleteArticle">
                                Eliminar Artigo
                            </Button>
                        </div>
                    </CardContent>
                </Card>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
