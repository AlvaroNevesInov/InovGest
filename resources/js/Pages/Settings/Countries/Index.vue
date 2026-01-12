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
    countries: Object,
    filters: Object
});

const search = ref(props.filters?.search || '');
const showDialog = ref(false);
const dialogMode = ref('create');
const selectedCountry = ref(null);

const formData = ref({
    name: '',
    code: '',
    active: true
});

watch(search, (newSearch) => {
    router.get(route('settings.countries.index'), {
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
        code: '',
        active: true
    };
    showDialog.value = true;
}

function openEditDialog(country) {
    dialogMode.value = 'edit';
    selectedCountry.value = country;
    formData.value = {
        name: country.name,
        code: country.code,
        active: country.active
    };
    showDialog.value = true;
}

function saveCountry() {
    if (dialogMode.value === 'create') {
        router.post(route('settings.countries.store'), formData.value, {
            onSuccess: () => {
                showDialog.value = false;
            }
        });
    } else {
        router.put(route('settings.countries.update', selectedCountry.value.id), formData.value, {
            onSuccess: () => {
                showDialog.value = false;
            }
        });
    }
}

function deleteCountry(id) {
    if (confirm('Tem a certeza que deseja eliminar este país?')) {
        router.delete(route('settings.countries.destroy', id));
    }
}
</script>

<template>
    <Head title="Países" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
                    Configurações - Países
                </h2>
                <Button @click="openCreateDialog">Novo País</Button>
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
                                placeholder="Pesquisar por nome ou código..."
                                class="max-w-md"
                            />
                        </div>

                        <!-- Tabela -->
                        <div v-if="countries.data.length > 0">
                            <Table>
                                <TableHeader>
                                    <TableRow>
                                        <TableHead>Nome</TableHead>
                                        <TableHead>Código</TableHead>
                                        <TableHead>Estado</TableHead>
                                        <TableHead class="text-right">Ações</TableHead>
                                    </TableRow>
                                </TableHeader>
                                <TableBody>
                                    <TableRow v-for="country in countries.data" :key="country.id">
                                        <TableCell class="font-medium">{{ country.name }}</TableCell>
                                        <TableCell>{{ country.code }}</TableCell>
                                        <TableCell>
                                            <Badge :variant="country.active ? 'default' : 'destructive'">
                                                {{ country.active ? 'Ativo' : 'Inativo' }}
                                            </Badge>
                                        </TableCell>
                                        <TableCell class="text-right">
                                            <div class="flex justify-end gap-2">
                                                <Button variant="outline" size="sm" @click="openEditDialog(country)">
                                                    Editar
                                                </Button>
                                                <Button
                                                    variant="destructive"
                                                    size="sm"
                                                    @click="deleteCountry(country.id)"
                                                >
                                                    Eliminar
                                                </Button>
                                            </div>
                                        </TableCell>
                                    </TableRow>
                                </TableBody>
                            </Table>

                            <!-- Paginação -->
                            <div v-if="countries.links.length > 3" class="mt-6 flex items-center justify-between">
                                <div class="text-sm text-gray-700 dark:text-gray-300">
                                    Mostrando <span class="font-medium">{{ countries.from }}</span> a
                                    <span class="font-medium">{{ countries.to }}</span> de
                                    <span class="font-medium">{{ countries.total }}</span> resultados
                                </div>
                            </div>
                        </div>

                        <!-- Mensagem quando não há dados -->
                        <div v-else class="py-12 text-center">
                            <p class="text-gray-500 dark:text-gray-400">
                                Nenhum país encontrado.
                            </p>
                            <Button class="mt-4" @click="openCreateDialog">Criar primeiro país</Button>
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
                        {{ dialogMode === 'create' ? 'Novo País' : 'Editar País' }}
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

                    <div class="grid gap-2">
                        <Label for="code">Código (ISO 2 letras) *</Label>
                        <Input
                            id="code"
                            v-model="formData.code"
                            type="text"
                            maxlength="2"
                            placeholder="PT"
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
                    <Button @click="saveCountry">
                        {{ dialogMode === 'create' ? 'Criar' : 'Guardar' }}
                    </Button>
                </DialogFooter>
            </DialogContent>
        </Dialog>
    </AuthenticatedLayout>
</template>
