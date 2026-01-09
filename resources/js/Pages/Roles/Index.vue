<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, router } from '@inertiajs/vue3';
import { ref } from 'vue';
import { Button } from '@/Components/ui/button';
import { Table, TableHeader, TableBody, TableRow, TableHead, TableCell } from '@/Components/ui/table';
import { Dialog, DialogContent, DialogHeader, DialogTitle } from '@/Components/ui/dialog';
import { Label } from '@/Components/ui/label';
import { Input } from '@/Components/ui/input';

const props = defineProps({
    roles: Array,
    permissions: Object
});

const showDialog = ref(false);
const editingRole = ref(null);
const form = ref({
    name: '',
    permissions: []
});

const openCreateDialog = () => {
    editingRole.value = null;
    form.value = {
        name: '',
        permissions: []
    };
    showDialog.value = true;
};

const openEditDialog = (role) => {
    editingRole.value = role;
    form.value = {
        name: role.name,
        permissions: role.permissions?.map(p => p.name) || []
    };
    showDialog.value = true;
};

const submitForm = () => {
    if (editingRole.value) {
        router.put(route('roles.update', editingRole.value.id), form.value, {
            onSuccess: () => {
                showDialog.value = false;
            }
        });
    } else {
        router.post(route('roles.store'), form.value, {
            onSuccess: () => {
                showDialog.value = false;
            }
        });
    }
};

const deleteRole = (id) => {
    if (confirm('Tem a certeza que deseja eliminar este role?')) {
        router.delete(route('roles.destroy', id));
    }
};

const togglePermission = (permissionName) => {
    const index = form.value.permissions.indexOf(permissionName);
    if (index > -1) {
        form.value.permissions.splice(index, 1);
    } else {
        form.value.permissions.push(permissionName);
    }
};

const toggleAllPermissionsInGroup = (group) => {
    const allSelected = group.every(p => form.value.permissions.includes(p.name));
    if (allSelected) {
        // Remove all
        group.forEach(p => {
            const index = form.value.permissions.indexOf(p.name);
            if (index > -1) form.value.permissions.splice(index, 1);
        });
    } else {
        // Add all
        group.forEach(p => {
            if (!form.value.permissions.includes(p.name)) {
                form.value.permissions.push(p.name);
            }
        });
    }
};
</script>

<template>
    <Head title="Grupos de Permissões" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
                    Grupos de Permissões (Roles)
                </h2>
                <Button @click="openCreateDialog">Novo Role</Button>
            </div>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg dark:bg-gray-800">
                    <div class="p-6">
                        <div v-if="roles.length > 0">
                            <Table>
                                <TableHeader>
                                    <TableRow>
                                        <TableHead>Nome</TableHead>
                                        <TableHead>Permissões</TableHead>
                                        <TableHead>Utilizadores</TableHead>
                                        <TableHead class="text-right">Ações</TableHead>
                                    </TableRow>
                                </TableHeader>
                                <TableBody>
                                    <TableRow v-for="role in roles" :key="role.id">
                                        <TableCell class="font-medium">{{ role.name }}</TableCell>
                                        <TableCell>{{ role.permissions?.length || 0 }} permissões</TableCell>
                                        <TableCell>{{ role.users_count || 0 }} utilizadores</TableCell>
                                        <TableCell class="text-right">
                                            <div class="flex justify-end gap-2">
                                                <Button
                                                    variant="ghost"
                                                    size="sm"
                                                    @click="openEditDialog(role)"
                                                >
                                                    Editar
                                                </Button>
                                                <Button
                                                    variant="ghost"
                                                    size="sm"
                                                    @click="deleteRole(role.id)"
                                                >
                                                    Eliminar
                                                </Button>
                                            </div>
                                        </TableCell>
                                    </TableRow>
                                </TableBody>
                            </Table>
                        </div>
                        <div v-else class="py-12 text-center text-gray-500 dark:text-gray-400">
                            Nenhum role encontrado.
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Dialog for Create/Edit -->
        <Dialog v-model:open="showDialog">
            <DialogContent class="sm:max-w-[725px] max-h-[90vh] overflow-y-auto">
                <DialogHeader>
                    <DialogTitle>
                        {{ editingRole ? 'Editar Role' : 'Novo Role' }}
                    </DialogTitle>
                </DialogHeader>
                <form @submit.prevent="submitForm" class="space-y-4">
                    <div>
                        <Label for="name">Nome do Role</Label>
                        <Input
                            id="name"
                            v-model="form.name"
                            required
                        />
                    </div>
                    <div>
                        <Label>Permissões</Label>
                        <div class="mt-4 space-y-6">
                            <div v-for="(group, moduleName) in permissions" :key="moduleName" class="border rounded-lg p-4">
                                <div class="flex items-center justify-between mb-3">
                                    <h4 class="font-semibold text-sm">{{ moduleName }}</h4>
                                    <Button
                                        type="button"
                                        variant="ghost"
                                        size="sm"
                                        @click="toggleAllPermissionsInGroup(group)"
                                    >
                                        Selecionar Todos
                                    </Button>
                                </div>
                                <div class="grid grid-cols-2 gap-2">
                                    <div v-for="permission in group" :key="permission.id" class="flex items-center space-x-2">
                                        <input
                                            :id="'perm-' + permission.id"
                                            type="checkbox"
                                            :checked="form.permissions.includes(permission.name)"
                                            @change="togglePermission(permission.name)"
                                            class="h-4 w-4 rounded border-gray-300"
                                        />
                                        <Label :for="'perm-' + permission.id" class="text-sm">
                                            {{ permission.name.split('.')[1] }}
                                        </Label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="flex justify-end gap-2 pt-4">
                        <Button type="button" variant="outline" @click="showDialog = false">
                            Cancelar
                        </Button>
                        <Button type="submit">
                            {{ editingRole ? 'Atualizar' : 'Criar' }}
                        </Button>
                    </div>
                </form>
            </DialogContent>
        </Dialog>
    </AuthenticatedLayout>
</template>
