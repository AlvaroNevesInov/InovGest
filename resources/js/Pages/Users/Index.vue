<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref } from 'vue';
import { Button } from '@/Components/ui/button';
import { Badge } from '@/Components/ui/badge';
import { Table, TableHeader, TableBody, TableRow, TableHead, TableCell } from '@/Components/ui/table';
import { Dialog, DialogContent, DialogHeader, DialogTitle } from '@/Components/ui/dialog';
import { Label } from '@/Components/ui/label';
import { Input } from '@/Components/ui/input';

const props = defineProps({
    users: Object,
    roles: Array,
    companies: Array,
    filters: Object
});

const showDialog = ref(false);
const editingUser = ref(null);
const form = ref({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
    roles: [],
    companies: [],
    company_owners: []
});

const openCreateDialog = () => {
    editingUser.value = null;
    form.value = {
        name: '',
        email: '',
        password: '',
        password_confirmation: '',
        roles: [],
        companies: [],
        company_owners: []
    };
    showDialog.value = true;
};

const openEditDialog = (user) => {
    editingUser.value = user;
    form.value = {
        name: user.name,
        email: user.email,
        password: '',
        password_confirmation: '',
        roles: user.roles?.map(r => r.name) || [],
        companies: user.companies?.map(c => c.id) || [],
        company_owners: user.companies?.filter(c => c.pivot.is_owner).map(c => c.id) || []
    };
    showDialog.value = true;
};

const submitForm = () => {
    if (editingUser.value) {
        router.put(route('users.update', editingUser.value.id), form.value, {
            onSuccess: () => {
                showDialog.value = false;
            }
        });
    } else {
        router.post(route('users.store'), form.value, {
            onSuccess: () => {
                showDialog.value = false;
            }
        });
    }
};

const deleteUser = (id) => {
    if (confirm('Tem a certeza que deseja eliminar este utilizador?')) {
        router.delete(route('users.destroy', id));
    }
};

const toggleRole = (roleName) => {
    const index = form.value.roles.indexOf(roleName);
    if (index > -1) {
        form.value.roles.splice(index, 1);
    } else {
        form.value.roles.push(roleName);
    }
};

const toggleCompany = (companyId) => {
    const index = form.value.companies.indexOf(companyId);
    if (index > -1) {
        form.value.companies.splice(index, 1);
        // Remove from owners if unchecked
        const ownerIndex = form.value.company_owners.indexOf(companyId);
        if (ownerIndex > -1) {
            form.value.company_owners.splice(ownerIndex, 1);
        }
    } else {
        form.value.companies.push(companyId);
    }
};

const toggleCompanyOwner = (companyId) => {
    const index = form.value.company_owners.indexOf(companyId);
    if (index > -1) {
        form.value.company_owners.splice(index, 1);
    } else {
        form.value.company_owners.push(companyId);
    }
};
</script>

<template>
    <Head title="Utilizadores" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
                    Utilizadores
                </h2>
                <Button @click="openCreateDialog">Novo Utilizador</Button>
            </div>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg dark:bg-gray-800">
                    <div class="p-6">
                        <div v-if="users.data && users.data.length > 0">
                            <Table>
                                <TableHeader>
                                    <TableRow>
                                        <TableHead>Nome</TableHead>
                                        <TableHead>Email</TableHead>
                                        <TableHead>Roles</TableHead>
                                        <TableHead>Empresas</TableHead>
                                        <TableHead>2FA</TableHead>
                                        <TableHead class="text-right">Ações</TableHead>
                                    </TableRow>
                                </TableHeader>
                                <TableBody>
                                    <TableRow v-for="user in users.data" :key="user.id">
                                        <TableCell class="font-medium">{{ user.name }}</TableCell>
                                        <TableCell>{{ user.email }}</TableCell>
                                        <TableCell>
                                            <div class="flex flex-wrap gap-1">
                                                <Badge v-for="role in user.roles" :key="role.id" variant="secondary">
                                                    {{ role.name }}
                                                </Badge>
                                            </div>
                                        </TableCell>
                                        <TableCell>
                                            <div class="flex flex-wrap gap-1">
                                                <Badge v-for="company in user.companies" :key="company.id" :variant="company.pivot.is_owner ? 'default' : 'outline'">
                                                    {{ company.name }}
                                                    <span v-if="company.pivot.is_owner" class="ml-1">★</span>
                                                </Badge>
                                                <span v-if="!user.companies || user.companies.length === 0" class="text-sm text-gray-500">-</span>
                                            </div>
                                        </TableCell>
                                        <TableCell>
                                            <Badge :variant="user.two_factor_enabled ? 'success' : 'secondary'">
                                                {{ user.two_factor_enabled ? 'Ativo' : 'Inativo' }}
                                            </Badge>
                                        </TableCell>
                                        <TableCell class="text-right">
                                            <div class="flex justify-end gap-2">
                                                <Button
                                                    variant="ghost"
                                                    size="sm"
                                                    @click="openEditDialog(user)"
                                                >
                                                    Editar
                                                </Button>
                                                <Button
                                                    variant="ghost"
                                                    size="sm"
                                                    @click="deleteUser(user.id)"
                                                >
                                                    Eliminar
                                                </Button>
                                            </div>
                                        </TableCell>
                                    </TableRow>
                                </TableBody>
                            </Table>

                            <!-- Paginação -->
                            <div v-if="users.links && users.links.length > 3" class="mt-6 flex justify-center gap-2">
                                <Link
                                    v-for="(link, index) in users.links"
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
                            Nenhum utilizador encontrado.
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Dialog for Create/Edit -->
        <Dialog v-model:open="showDialog">
            <DialogContent class="sm:max-w-[525px]">
                <DialogHeader>
                    <DialogTitle>
                        {{ editingUser ? 'Editar Utilizador' : 'Novo Utilizador' }}
                    </DialogTitle>
                </DialogHeader>
                <form @submit.prevent="submitForm" class="space-y-4">
                    <div>
                        <Label for="name">Nome</Label>
                        <Input
                            id="name"
                            v-model="form.name"
                            required
                        />
                    </div>
                    <div>
                        <Label for="email">Email</Label>
                        <Input
                            id="email"
                            v-model="form.email"
                            type="email"
                            required
                        />
                    </div>
                    <div>
                        <Label for="password">Palavra-passe {{ editingUser ? '(deixe vazio para manter)' : '' }}</Label>
                        <Input
                            id="password"
                            v-model="form.password"
                            type="password"
                            :required="!editingUser"
                        />
                    </div>
                    <div>
                        <Label for="password_confirmation">Confirmar Palavra-passe</Label>
                        <Input
                            id="password_confirmation"
                            v-model="form.password_confirmation"
                            type="password"
                            :required="!editingUser && form.password"
                        />
                    </div>
                    <div>
                        <Label>Roles</Label>
                        <div class="space-y-2 mt-2">
                            <div v-for="role in roles" :key="role.id" class="flex items-center space-x-2">
                                <input
                                    :id="'role-' + role.id"
                                    type="checkbox"
                                    :checked="form.roles.includes(role.name)"
                                    @change="toggleRole(role.name)"
                                    class="h-4 w-4 rounded border-gray-300"
                                />
                                <Label :for="'role-' + role.id">{{ role.name }}</Label>
                            </div>
                        </div>
                    </div>
                    <div>
                        <Label>Acesso a Empresas (Tenants)</Label>
                        <div class="space-y-2 mt-2 max-h-48 overflow-y-auto border rounded-md p-3">
                            <div v-if="companies && companies.length > 0">
                                <div v-for="company in companies" :key="company.id" class="flex items-center justify-between space-x-2 py-1">
                                    <div class="flex items-center space-x-2">
                                        <input
                                            :id="'company-' + company.id"
                                            type="checkbox"
                                            :checked="form.companies.includes(company.id)"
                                            @change="toggleCompany(company.id)"
                                            class="h-4 w-4 rounded border-gray-300"
                                        />
                                        <Label :for="'company-' + company.id" class="font-normal">{{ company.name }}</Label>
                                    </div>
                                    <div v-if="form.companies.includes(company.id)" class="flex items-center space-x-2">
                                        <input
                                            :id="'owner-' + company.id"
                                            type="checkbox"
                                            :checked="form.company_owners.includes(company.id)"
                                            @change="toggleCompanyOwner(company.id)"
                                            class="h-4 w-4 rounded border-gray-300"
                                        />
                                        <Label :for="'owner-' + company.id" class="text-xs text-gray-600">Owner</Label>
                                    </div>
                                </div>
                            </div>
                            <div v-else class="text-sm text-gray-500 text-center py-2">
                                Não tem empresas disponíveis
                            </div>
                        </div>
                        <p class="text-xs text-gray-500 mt-1">★ Owner tem permissões completas para editar/remover a empresa</p>
                    </div>
                    <div class="flex justify-end gap-2">
                        <Button type="button" variant="outline" @click="showDialog = false">
                            Cancelar
                        </Button>
                        <Button type="submit">
                            {{ editingUser ? 'Atualizar' : 'Criar' }}
                        </Button>
                    </div>
                </form>
            </DialogContent>
        </Dialog>
    </AuthenticatedLayout>
</template>
