<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, router } from '@inertiajs/vue3';
import { ref, watch } from 'vue';
import { Button } from '@/Components/ui/button';
import { Input } from '@/Components/ui/input';
import { Badge } from '@/Components/ui/badge';
import { Table, TableHeader, TableBody, TableRow, TableHead, TableCell } from '@/Components/ui/table';
import { Dialog, DialogContent, DialogHeader, DialogTitle } from '@/Components/ui/dialog';
import { Label } from '@/Components/ui/label';

const props = defineProps({
    vatRates: Object,
    filters: Object
});

const search = ref(props.filters?.search || '');
const activeFilter = ref(props.filters?.active || 'all');
const showDialog = ref(false);
const editingItem = ref(null);
const form = ref({
    name: '',
    rate: 0,
    active: true
});

watch([search, activeFilter], ([newSearch, newActive]) => {
    router.get(route('settings.vat-rates.index'), {
        search: newSearch,
        active: newActive
    }, {
        preserveState: true,
        replace: true
    });
}, { debounce: 300 });

const openCreateDialog = () => {
    editingItem.value = null;
    form.value = {
        name: '',
        rate: 0,
        active: true
    };
    showDialog.value = true;
};

const openEditDialog = (item) => {
    editingItem.value = item;
    form.value = {
        name: item.name,
        rate: item.rate,
        active: item.active
    };
    showDialog.value = true;
};

const submitForm = () => {
    if (editingItem.value) {
        router.put(route('settings.vat-rates.update', editingItem.value.id), form.value, {
            onSuccess: () => {
                showDialog.value = false;
            }
        });
    } else {
        router.post(route('settings.vat-rates.store'), form.value, {
            onSuccess: () => {
                showDialog.value = false;
            }
        });
    }
};

const deleteItem = (id) => {
    if (confirm('Tem a certeza que deseja eliminar esta taxa de IVA?')) {
        router.delete(route('settings.vat-rates.destroy', id));
    }
};
</script>

<template>
    <Head title="Taxas de IVA" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
                    Taxas de IVA
                </h2>
                <Button @click="openCreateDialog">Nova Taxa</Button>
            </div>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg dark:bg-gray-800">
                    <div class="p-6">
                        <!-- Filtros -->
                        <div class="mb-6 flex flex-col gap-4 sm:flex-row">
                            <Input
                                v-model="search"
                                type="text"
                                placeholder="Pesquisar por nome..."
                                class="max-w-md"
                            />
                            <select
                                v-model="activeFilter"
                                class="h-10 rounded-md border border-input bg-background px-3 py-2 text-sm"
                            >
                                <option value="all">Todas</option>
                                <option value="1">Ativas</option>
                                <option value="0">Inativas</option>
                            </select>
                        </div>

                        <!-- Tabela -->
                        <div v-if="vatRates.data && vatRates.data.length > 0">
                            <Table>
                                <TableHeader>
                                    <TableRow>
                                        <TableHead>Nome</TableHead>
                                        <TableHead>Taxa (%)</TableHead>
                                        <TableHead>Estado</TableHead>
                                        <TableHead class="text-right">Ações</TableHead>
                                    </TableRow>
                                </TableHeader>
                                <TableBody>
                                    <TableRow v-for="item in vatRates.data" :key="item.id">
                                        <TableCell class="font-medium">{{ item.name }}</TableCell>
                                        <TableCell>{{ item.rate }}%</TableCell>
                                        <TableCell>
                                            <Badge :variant="item.active ? 'default' : 'secondary'">
                                                {{ item.active ? 'Ativa' : 'Inativa' }}
                                            </Badge>
                                        </TableCell>
                                        <TableCell class="text-right">
                                            <div class="flex justify-end gap-2">
                                                <Button
                                                    variant="ghost"
                                                    size="sm"
                                                    @click="openEditDialog(item)"
                                                >
                                                    Editar
                                                </Button>
                                                <Button
                                                    variant="ghost"
                                                    size="sm"
                                                    @click="deleteItem(item.id)"
                                                >
                                                    Eliminar
                                                </Button>
                                            </div>
                                        </TableCell>
                                    </TableRow>
                                </TableBody>
                            </Table>

                            <!-- Paginação -->
                            <div v-if="vatRates.links && vatRates.links.length > 3" class="mt-6 flex justify-center gap-2">
                                <component
                                    :is="link.url ? 'a' : 'span'"
                                    v-for="(link, index) in vatRates.links"
                                    :key="index"
                                    :href="link.url"
                                    :class="[
                                        'px-4 py-2 text-sm rounded-md',
                                        link.active
                                            ? 'bg-primary text-white'
                                            : link.url
                                            ? 'bg-gray-200 hover:bg-gray-300 text-gray-700'
                                            : 'bg-gray-100 text-gray-400 cursor-not-allowed'
                                    ]"
                                    v-html="link.label"
                                />
                            </div>
                        </div>
                        <div v-else class="py-12 text-center text-gray-500 dark:text-gray-400">
                            Nenhuma taxa de IVA encontrada.
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Dialog for Create/Edit -->
        <Dialog v-model:open="showDialog">
            <DialogContent class="sm:max-w-[425px]">
                <DialogHeader>
                    <DialogTitle>
                        {{ editingItem ? 'Editar Taxa' : 'Nova Taxa' }}
                    </DialogTitle>
                </DialogHeader>
                <form @submit.prevent="submitForm" class="space-y-4">
                    <div>
                        <Label for="name">Nome</Label>
                        <Input
                            id="name"
                            v-model="form.name"
                            required
                            placeholder="Ex: IVA Normal, IVA Reduzido..."
                        />
                    </div>
                    <div>
                        <Label for="rate">Taxa (%)</Label>
                        <Input
                            id="rate"
                            v-model.number="form.rate"
                            type="number"
                            step="0.01"
                            min="0"
                            max="100"
                            required
                            placeholder="Ex: 23"
                        />
                    </div>
                    <div class="flex items-center gap-2">
                        <input
                            id="active"
                            v-model="form.active"
                            type="checkbox"
                            class="rounded border-gray-300 text-primary focus:ring-primary"
                        />
                        <Label for="active" class="!mb-0">Ativa</Label>
                    </div>
                    <div class="flex justify-end gap-2">
                        <Button type="button" variant="outline" @click="showDialog = false">
                            Cancelar
                        </Button>
                        <Button type="submit">
                            {{ editingItem ? 'Atualizar' : 'Criar' }}
                        </Button>
                    </div>
                </form>
            </DialogContent>
        </Dialog>
    </AuthenticatedLayout>
</template>
