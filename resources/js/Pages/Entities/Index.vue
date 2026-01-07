<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref, watch } from 'vue';
import { Button } from '@/Components/ui/button';
import { Input } from '@/Components/ui/input';
import {Badge} from '@/Components/ui/badge';
import {Table} from '@/Components/ui/table';
import {TableHeader} from '@/Components/ui/table';
import {TableBody} from '@/Components/ui/table';
import {TableRow} from '@/Components/ui/table';
import {TableHead} from '@/Components/ui/table';
import {TableCell} from '@/Components/ui/table';

const props = defineProps({
    entities: Object,
    filters: Object
});

const search = ref(props.filters?.search || '');
const typeFilter = ref(props.filters?.type || 'all');

watch([search, typeFilter], ([newSearch, newType]) => {
    router.get(route('entities.index'), {
        search: newSearch,
        type: newType
    }, {
        preserveState: true,
        replace: true
    });
}, { debounce: 300 });

const getTypeLabel = (type) => {
    const labels = {
        client: 'Cliente',
        supplier: 'Fornecedor',
        both: 'Ambos'
    };
    return labels[type] || type;
};

const getTypeBadgeVariant = (type) => {
    const variants = {
        client: 'default',
        supplier: 'secondary',
        both: 'outline'
    };
    return variants[type] || 'default';
};

const deleteEntity = (id) => {
    if (confirm('Tem a certeza que deseja eliminar esta entidade?')) {
        router.delete(route('entities.destroy', id));
    }
};
</script>

<template>
    <Head title="Entidades" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
                    Entidades (Clientes/Fornecedores)
                </h2>
                <Link :href="route('entities.create')">
                    <Button>Nova Entidade</Button>
                </Link>
            </div>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg dark:bg-gray-800">
                    <div class="p-6">
                        <!-- Filtros -->
                        <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                            <div class="flex flex-1 gap-4">
                                <Input
                                    v-model="search"
                                    type="text"
                                    placeholder="Pesquisar por nome, NIF ou email..."
                                    class="max-w-md"
                                />

                                <select
                                    v-model="typeFilter"
                                    class="h-10 rounded-md border border-input bg-background px-3 py-2 text-sm"
                                >
                                    <option value="all">Todos</option>
                                    <option value="client">Clientes</option>
                                    <option value="supplier">Fornecedores</option>
                                </select>
                            </div>
                        </div>

                        <!-- Tabela -->
                        <div v-if="entities.data.length > 0">
                            <Table>
                                <TableHeader>
                                    <TableRow>
                                        <TableHead>Número</TableHead>
                                        <TableHead>Tipo</TableHead>
                                        <TableHead>Nome</TableHead>
                                        <TableHead>NIF</TableHead>
                                        <TableHead>Email</TableHead>
                                        <TableHead>Telefone</TableHead>
                                        <TableHead>País</TableHead>
                                        <TableHead>Estado</TableHead>
                                        <TableHead class="text-right">Ações</TableHead>
                                    </TableRow>
                                </TableHeader>
                                <TableBody>
                                    <TableRow v-for="entity in entities.data" :key="entity.id">
                                        <TableCell class="font-medium">{{ entity.number }}</TableCell>
                                        <TableCell>
                                            <Badge :variant="getTypeBadgeVariant(entity.type)">
                                                {{ getTypeLabel(entity.type) }}
                                            </Badge>
                                        </TableCell>
                                        <TableCell>{{ entity.name }}</TableCell>
                                        <TableCell>{{ entity.nif }}</TableCell>
                                        <TableCell>{{ entity.email || '-' }}</TableCell>
                                        <TableCell>{{ entity.phone || entity.mobile || '-' }}</TableCell>
                                        <TableCell>{{ entity.country?.name || '-' }}</TableCell>
                                        <TableCell>
                                            <Badge :variant="entity.active ? 'default' : 'destructive'">
                                                {{ entity.active ? 'Ativo' : 'Inativo' }}
                                            </Badge>
                                        </TableCell>
                                        <TableCell class="text-right">
                                            <div class="flex justify-end gap-2">
                                                <Link :href="route('entities.show', entity.id)">
                                                    <Button variant="outline" size="sm">Ver</Button>
                                                </Link>
                                                <Link :href="route('entities.edit', entity.id)">
                                                    <Button variant="outline" size="sm">Editar</Button>
                                                </Link>
                                                <Button
                                                    variant="destructive"
                                                    size="sm"
                                                    @click="deleteEntity(entity.id)"
                                                >
                                                    Eliminar
                                                </Button>
                                            </div>
                                        </TableCell>
                                    </TableRow>
                                </TableBody>
                            </Table>

                            <!-- Paginação -->
                            <div v-if="entities.links.length > 3" class="mt-6 flex items-center justify-between">
                                <div class="text-sm text-gray-700 dark:text-gray-300">
                                    Mostrando <span class="font-medium">{{ entities.from }}</span> a
                                    <span class="font-medium">{{ entities.to }}</span> de
                                    <span class="font-medium">{{ entities.total }}</span> resultados
                                </div>
                                <div class="flex gap-1">
                                    <Link
                                        v-for="(link, index) in entities.links"
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
                                Nenhuma entidade encontrada.
                            </p>
                            <Link :href="route('entities.create')" class="mt-4 inline-block">
                                <Button>Criar primeira entidade</Button>
                            </Link>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
