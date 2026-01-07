<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { Button } from '@/Components/ui/button';
import { Input } from '@/Components/ui/input';
import { Badge } from '@/Components/ui/badge';
import { Table, TableHeader, TableBody, TableRow, TableHead, TableCell } from '@/Components/ui/table';
import { ref, watch } from 'vue';

const props = defineProps({
    contacts: Object,
    filters: Object
});

const search = ref(props.filters.search || '');
const status = ref(props.filters.status || '');

watch([search, status], () => {
    router.get(route('contacts.index'), {
        search: search.value,
        status: status.value,
    }, {
        preserveState: true,
        replace: true,
    });
}, { debounce: 300 });

const deleteContact = (contact) => {
    if (confirm(`Tem a certeza que deseja eliminar o contacto ${contact.name}?`)) {
        router.delete(route('contacts.destroy', contact.id));
    }
};
</script>

<template>
    <Head title="Contactos" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
                    Contactos
                </h2>
                <Link :href="route('contacts.create')">
                    <Button>Novo Contacto</Button>
                </Link>
            </div>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <!-- Filters -->
                <div class="mb-6 flex gap-4">
                    <div class="flex-1">
                        <Input
                            v-model="search"
                            type="text"
                            placeholder="Pesquisar por nome, email ou telefone..."
                        />
                    </div>
                    <select
                        v-model="status"
                        class="h-10 rounded-md border border-input bg-background px-3 py-2 text-sm"
                    >
                        <option value="">Todos</option>
                        <option value="active">Ativos</option>
                        <option value="inactive">Inativos</option>
                    </select>
                </div>

                <!-- Table -->
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg dark:bg-gray-800">
                    <Table>
                        <TableHeader>
                            <TableRow>
                                <TableHead>Nº</TableHead>
                                <TableHead>Nome</TableHead>
                                <TableHead>Entidade</TableHead>
                                <TableHead>Função</TableHead>
                                <TableHead>Email</TableHead>
                                <TableHead>Telefone</TableHead>
                                <TableHead>Estado</TableHead>
                                <TableHead>Ações</TableHead>
                            </TableRow>
                        </TableHeader>
                        <TableBody>
                            <TableRow v-for="contact in contacts.data" :key="contact.id">
                                <TableCell>{{ contact.number }}</TableCell>
                                <TableCell class="font-medium">
                                    {{ contact.name }} {{ contact.surname }}
                                </TableCell>
                                <TableCell>
                                    <Link
                                        :href="route('entities.show', contact.entity.id)"
                                        class="text-primary hover:underline"
                                    >
                                        {{ contact.entity.name }}
                                    </Link>
                                </TableCell>
                                <TableCell>
                                    {{ contact.contact_function?.name || '-' }}
                                </TableCell>
                                <TableCell>{{ contact.email || '-' }}</TableCell>
                                <TableCell>{{ contact.mobile || contact.phone || '-' }}</TableCell>
                                <TableCell>
                                    <Badge :variant="contact.active ? 'default' : 'secondary'">
                                        {{ contact.active ? 'Ativo' : 'Inativo' }}
                                    </Badge>
                                </TableCell>
                                <TableCell>
                                    <div class="flex gap-2">
                                        <Link :href="route('contacts.show', contact.id)">
                                            <Button variant="outline" size="sm">Ver</Button>
                                        </Link>
                                        <Link :href="route('contacts.edit', contact.id)">
                                            <Button variant="outline" size="sm">Editar</Button>
                                        </Link>
                                        <Button
                                            variant="destructive"
                                            size="sm"
                                            @click="deleteContact(contact)"
                                        >
                                            Eliminar
                                        </Button>
                                    </div>
                                </TableCell>
                            </TableRow>
                            <TableRow v-if="contacts.data.length === 0">
                                <TableCell colspan="8" class="text-center">
                                    Nenhum contacto encontrado.
                                </TableCell>
                            </TableRow>
                        </TableBody>
                    </Table>

                    <!-- Pagination -->
                    <div v-if="contacts.links.length > 3" class="border-t p-4">
                        <div class="flex justify-center gap-2">
                            <Link
                                v-for="link in contacts.links"
                                :key="link.label"
                                :href="link.url"
                                :class="[
                                    'px-3 py-1 rounded border',
                                    link.active ? 'bg-primary text-white' : 'bg-white hover:bg-gray-50',
                                    !link.url ? 'opacity-50 cursor-not-allowed' : ''
                                ]"
                                v-html="link.label"
                            />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
