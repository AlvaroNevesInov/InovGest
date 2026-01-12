<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, router } from '@inertiajs/vue3';
import { ref, watch } from 'vue';
import { Button } from '@/Components/ui/button';
import { Input } from '@/Components/ui/input';
import { Badge } from '@/Components/ui/badge';
import { Table, TableHeader, TableBody, TableRow, TableHead, TableCell } from '@/Components/ui/table';
import { Dialog, DialogContent, DialogHeader, DialogTitle, DialogFooter } from '@/Components/ui/dialog';
import { Label } from '@/Components/ui/label';

const props = defineProps({
    calendarActions: Object,
    filters: Object
});

const search = ref(props.filters?.search || '');
const showDialog = ref(false);
const dialogMode = ref('create');
const selectedAction = ref(null);

const formData = ref({
    name: '',
    active: true
});

watch(search, (newSearch) => {
    router.get(route('settings.calendar-actions.index'), {
        search: newSearch
    }, {
        preserveState: true,
        replace: true
    });
}, { debounce: 300 });

function openCreateDialog() {
    dialogMode.value = 'create';
    formData.value = {
        name: '',
        active: true
    };
    showDialog.value = true;
}

function openEditDialog(item) {
    dialogMode.value = 'edit';
    selectedAction.value = item;
    formData.value = {
        name: item.name,
        active: item.active
    };
    showDialog.value = true;
}

function saveAction() {
    if (dialogMode.value === 'create') {
        router.post(route('settings.calendar-actions.store'), formData.value, {
            onSuccess: () => {
                showDialog.value = false;
            }
        });
    } else {
        router.put(route('settings.calendar-actions.update', selectedAction.value.id), formData.value, {
            onSuccess: () => {
                showDialog.value = false;
            }
        });
    }
}

function deleteAction(id) {
    if (confirm('Tem a certeza que deseja eliminar esta ação?')) {
        router.delete(route('settings.calendar-actions.destroy', id));
    }
}
</script>

<template>
    <Head title="Ações de Calendário" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
                    Configurações - Ações de Calendário
                </h2>
                <Button @click="openCreateDialog">Nova Ação</Button>
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
                                placeholder="Pesquisar..."
                                class="max-w-md"
                            />
                        </div>

                        <!-- Tabela -->
                        <div v-if="calendarActions.data.length > 0">
                            <Table>
                                <TableHeader>
                                    <TableRow>
                                        <TableHead>Nome</TableHead>
                                        <TableHead>Estado</TableHead>
                                        <TableHead class="text-right">Ações</TableHead>
                                    </TableRow>
                                </TableHeader>
                                <TableBody>
                                    <TableRow v-for="item in calendarActions.data" :key="item.id">
                                        <TableCell class="font-medium">{{ item.name }}</TableCell>
                                        <TableCell>
                                            <Badge :variant="item.active ? 'default' : 'destructive'">
                                                {{ item.active ? 'Ativo' : 'Inativo' }}
                                            </Badge>
                                        </TableCell>
                                        <TableCell class="text-right">
                                            <div class="flex justify-end gap-2">
                                                <Button variant="outline" size="sm" @click="openEditDialog(item)">
                                                    Editar
                                                </Button>
                                                <Button
                                                    variant="destructive"
                                                    size="sm"
                                                    @click="deleteAction(item.id)"
                                                >
                                                    Eliminar
                                                </Button>
                                            </div>
                                        </TableCell>
                                    </TableRow>
                                </TableBody>
                            </Table>

                            <!-- Paginação -->
                            <div v-if="calendarActions.links.length > 3" class="mt-6 flex items-center justify-between">
                                <div class="text-sm text-gray-700 dark:text-gray-300">
                                    Mostrando <span class="font-medium">{{ calendarActions.from }}</span> a
                                    <span class="font-medium">{{ calendarActions.to }}</span> de
                                    <span class="font-medium">{{ calendarActions.total }}</span> resultados
                                </div>
                            </div>
                        </div>

                        <!-- Mensagem quando não há dados -->
                        <div v-else class="py-12 text-center">
                            <p class="text-gray-500 dark:text-gray-400">
                                Nenhuma ação encontrada.
                            </p>
                            <Button class="mt-4" @click="openCreateDialog">Criar primeira ação</Button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Dialog -->
        <Dialog v-model:open="showDialog">
            <DialogContent class="sm:max-w-[500px]">
                <DialogHeader>
                    <DialogTitle>
                        {{ dialogMode === 'create' ? 'Nova Ação' : 'Editar Ação' }}
                    </DialogTitle>
                </DialogHeader>

                <div class="grid gap-4 py-4">
                    <div class="grid gap-2">
                        <Label for="name">Nome *</Label>
                        <Input
                            id="name"
                            v-model="formData.name"
                            type="text"
                            required
                        />
                    </div>

                    <div class="flex items-center gap-2">
                        <input
                            id="active"
                            v-model="formData.active"
                            type="checkbox"
                            class="h-4 w-4 rounded border-gray-300"
                        />
                        <Label for="active">Ativo</Label>
                    </div>
                </div>

                <DialogFooter>
                    <Button variant="outline" @click="showDialog = false">
                        Cancelar
                    </Button>
                    <Button @click="saveAction">
                        {{ dialogMode === 'create' ? 'Criar' : 'Guardar' }}
                    </Button>
                </DialogFooter>
            </DialogContent>
        </Dialog>
    </AuthenticatedLayout>
</template>
