<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref, watch } from 'vue';
import { Button } from '@/Components/ui/button';
import { Input } from '@/Components/ui/input';
import { Badge } from '@/Components/ui/badge';
import { Table, TableHeader, TableBody, TableRow, TableHead, TableCell } from '@/Components/ui/table';

const props = defineProps({
    articles: Object,
    filters: Object
});

const search = ref(props.filters?.search || '');

watch(search, (newSearch) => {
    router.get(route('settings.articles.index'), {
        search: newSearch
    }, {
        preserveState: true,
        replace: true
    });
}, { debounce: 300 });

const deleteArticle = (id) => {
    if (confirm('Tem a certeza que deseja eliminar este artigo?')) {
        router.delete(route('settings.articles.destroy', id));
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
    <Head title="Artigos" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
                    Artigos
                </h2>
                <Link :href="route('settings.articles.create')">
                    <Button>Novo Artigo</Button>
                </Link>
            </div>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg dark:bg-gray-800">
                    <div class="p-6">
                        <!-- Filtros -->
                        <div class="mb-6">
                            <Input
                                v-model="search"
                                type="text"
                                placeholder="Pesquisar por referência, nome ou descrição..."
                                class="max-w-md"
                            />
                        </div>

                        <!-- Tabela -->
                        <div v-if="articles.data.length > 0">
                            <Table>
                                <TableHeader>
                                    <TableRow>
                                        <TableHead>Referência</TableHead>
                                        <TableHead>Nome</TableHead>
                                        <TableHead>Descrição</TableHead>
                                        <TableHead>Preço</TableHead>
                                        <TableHead>IVA</TableHead>
                                        <TableHead>Estado</TableHead>
                                        <TableHead class="text-right">Ações</TableHead>
                                    </TableRow>
                                </TableHeader>
                                <TableBody>
                                    <TableRow v-for="article in articles.data" :key="article.id">
                                        <TableCell class="font-medium">{{ article.reference }}</TableCell>
                                        <TableCell>{{ article.name }}</TableCell>
                                        <TableCell class="max-w-xs truncate">
                                            {{ article.description || '-' }}
                                        </TableCell>
                                        <TableCell>{{ formatPrice(article.price) }}</TableCell>
                                        <TableCell>{{ article.vat_rate?.name || '-' }}</TableCell>
                                        <TableCell>
                                            <Badge :variant="article.active ? 'default' : 'destructive'">
                                                {{ article.active ? 'Ativo' : 'Inativo' }}
                                            </Badge>
                                        </TableCell>
                                        <TableCell class="text-right">
                                            <div class="flex justify-end gap-2">
                                                <Link :href="route('settings.articles.show', article.id)">
                                                    <Button variant="outline" size="sm">Ver</Button>
                                                </Link>
                                                <Link :href="route('settings.articles.edit', article.id)">
                                                    <Button variant="outline" size="sm">Editar</Button>
                                                </Link>
                                                <Button
                                                    variant="destructive"
                                                    size="sm"
                                                    @click="deleteArticle(article.id)"
                                                >
                                                    Eliminar
                                                </Button>
                                            </div>
                                        </TableCell>
                                    </TableRow>
                                </TableBody>
                            </Table>

                            <!-- Paginação -->
                            <div v-if="articles.links.length > 3" class="mt-6 flex items-center justify-between">
                                <div class="text-sm text-gray-700 dark:text-gray-300">
                                    Mostrando <span class="font-medium">{{ articles.from }}</span> a
                                    <span class="font-medium">{{ articles.to }}</span> de
                                    <span class="font-medium">{{ articles.total }}</span> resultados
                                </div>
                                <div class="flex gap-1">
                                    <Link
                                        v-for="(link, index) in articles.links"
                                        :key="index"
                                        :href="link.url || '#'"
                                        :class="[
                                            'px-3 py-2 text-sm rounded-md',
                                            link.active
                                                ? 'bg-primary text-primary-foreground'
                                                : 'bg-secondary text-secondary-foreground hover:bg-secondary/80',
                                            !link.url && 'opacity-50 cursor-not-allowed'
                                        ]"
                                        :preserve-state="true"
                                        v-html="link.label"
                                    />
                                </div>
                            </div>
                        </div>

                        <!-- Mensagem quando não há dados -->
                        <div v-else class="py-12 text-center">
                            <p class="text-gray-500 dark:text-gray-400">
                                Nenhum artigo encontrado.
                            </p>
                            <Link :href="route('settings.articles.create')" class="mt-4 inline-block">
                                <Button>Criar primeiro artigo</Button>
                            </Link>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
