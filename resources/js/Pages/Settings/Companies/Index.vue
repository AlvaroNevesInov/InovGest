<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, router, useForm } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import { Button } from '@/Components/ui/button';
import { Input } from '@/Components/ui/input';
import { Label } from '@/Components/ui/label';
import { Textarea } from '@/Components/ui/textarea';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/Components/ui/card';
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogHeader,
    DialogTitle,
} from '@/Components/ui/dialog';
import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from '@/Components/ui/table';

const props = defineProps({
    companies: Array,
    countries: Array
});

const isDialogOpen = ref(false);
const editingCompany = ref(null);
const viewMode = ref(false);
const logoPreview = ref(null);
const fileInput = ref(null);

const form = useForm({
    name: '',
    nif: '',
    address: '',
    postal_code: '',
    city: '',
    country_id: '',
    phone: '',
    mobile: '',
    email: '',
    website: '',
    logo: null,
    notes: '',
    _method: 'PUT',
});

const dialogTitle = computed(() => {
    if (viewMode.value) return 'Detalhes da Empresa';
    return editingCompany.value ? 'Editar Empresa' : 'Nova Empresa';
});

const dialogDescription = computed(() => {
    if (viewMode.value) return 'Visualizar os dados da empresa.';
    return editingCompany.value ? 'Edite os dados da empresa.' : 'Preencha os dados para criar uma nova empresa.';
});

function openCreateDialog() {
    editingCompany.value = null;
    viewMode.value = false;
    resetForm();
    isDialogOpen.value = true;
}

function openViewDialog(company) {
    editingCompany.value = company;
    viewMode.value = true;
    loadCompanyData(company);
    isDialogOpen.value = true;
}

function openEditDialog(company) {
    editingCompany.value = company;
    viewMode.value = false;
    loadCompanyData(company);
    isDialogOpen.value = true;
}

function loadCompanyData(company) {
    form.name = company.name || '';
    form.nif = company.nif || '';
    form.address = company.address || '';
    form.postal_code = company.postal_code || '';
    form.city = company.city || '';
    form.country_id = company.country_id || '';
    form.phone = company.phone || '';
    form.mobile = company.mobile || '';
    form.email = company.email || '';
    form.website = company.website || '';
    form.notes = company.notes || '';
    form.logo = null;
    logoPreview.value = company.logo_path ? `/storage/${company.logo_path}` : null;
}

function resetForm() {
    form.reset();
    form.clearErrors();
    logoPreview.value = null;
    if (fileInput.value) {
        fileInput.value.value = '';
    }
}

function handleLogoChange(event) {
    const file = event.target.files[0];
    if (file) {
        form.logo = file;
        const reader = new FileReader();
        reader.onload = (e) => {
            logoPreview.value = e.target.result;
        };
        reader.readAsDataURL(file);
    }
}

function save() {
    if (editingCompany.value) {
        // Update existing company
        form.post(route('settings.companies.update', editingCompany.value.id), {
            forceFormData: true,
            preserveScroll: true,
            onSuccess: () => {
                isDialogOpen.value = false;
                resetForm();
            }
        });
    } else {
        // Create new company
        const { _method, ...dataWithoutMethod } = form.data();
        useForm(dataWithoutMethod).post(route('settings.companies.store'), {
            forceFormData: true,
            preserveScroll: true,
            onSuccess: () => {
                isDialogOpen.value = false;
                resetForm();
            }
        });
    }
}

function deleteCompany(company) {
    if (confirm(`Tem a certeza que deseja remover a empresa "${company.name}"?`)) {
        router.delete(route('settings.companies.destroy', company.id), {
            preserveScroll: true,
        });
    }
}
</script>

<template>
    <Head title="Empresas" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
                    Configurações - Empresas
                </h2>
                <Button @click="openCreateDialog">
                    Nova Empresa
                </Button>
            </div>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <Card>
                    <CardHeader>
                        <CardTitle>Gestão de Empresas</CardTitle>
                        <CardDescription>
                            Gerir todas as empresas a que tem acesso. Pode criar novas empresas e editar ou remover as existentes.
                        </CardDescription>
                    </CardHeader>
                    <CardContent>
                        <div v-if="companies && companies.length > 0" class="rounded-md border">
                            <Table>
                                <TableHeader>
                                    <TableRow>
                                        <TableHead>Nome</TableHead>
                                        <TableHead>NIF</TableHead>
                                        <TableHead>Localidade</TableHead>
                                        <TableHead>País</TableHead>
                                        <TableHead class="text-right">Ações</TableHead>
                                    </TableRow>
                                </TableHeader>
                                <TableBody>
                                    <TableRow v-for="company in companies" :key="company.id">
                                        <TableCell class="font-medium">
                                            <div class="flex items-center gap-2">
                                                <img
                                                    v-if="company.logo_path"
                                                    :src="`/storage/${company.logo_path}`"
                                                    :alt="company.name"
                                                    class="h-8 w-8 rounded object-contain"
                                                />
                                                {{ company.name }}
                                            </div>
                                        </TableCell>
                                        <TableCell>{{ company.nif }}</TableCell>
                                        <TableCell>{{ company.city || '-' }}</TableCell>
                                        <TableCell>{{ company.country?.name || '-' }}</TableCell>
                                        <TableCell class="text-right">
                                            <div class="flex justify-end gap-2">
                                                <Button
                                                    variant="ghost"
                                                    size="sm"
                                                    @click="openViewDialog(company)"
                                                >
                                                    Ver
                                                </Button>
                                                <Button
                                                    variant="outline"
                                                    size="sm"
                                                    @click="openEditDialog(company)"
                                                >
                                                    Editar
                                                </Button>
                                                <Button
                                                    variant="destructive"
                                                    size="sm"
                                                    @click="deleteCompany(company)"
                                                >
                                                    Remover
                                                </Button>
                                            </div>
                                        </TableCell>
                                    </TableRow>
                                </TableBody>
                            </Table>
                        </div>
                        <div v-else class="text-center py-12 text-gray-500">
                            <p>Não tem empresas disponíveis.</p>
                            <Button @click="openCreateDialog" class="mt-4">
                                Criar Primeira Empresa
                            </Button>
                        </div>
                    </CardContent>
                </Card>
            </div>
        </div>

        <!-- Dialog para criar/editar empresa -->
        <Dialog v-model:open="isDialogOpen">
            <DialogContent class="max-w-3xl max-h-[90vh] overflow-y-auto">
                <DialogHeader>
                    <DialogTitle>{{ dialogTitle }}</DialogTitle>
                    <DialogDescription>
                        {{ dialogDescription }}
                    </DialogDescription>
                </DialogHeader>

                <form @submit.prevent="save" class="space-y-6 mt-4">
                    <!-- Logo -->
                    <div class="grid gap-2">
                        <Label>Logotipo</Label>
                        <div class="flex items-center gap-4">
                            <div v-if="logoPreview" class="h-24 w-24 overflow-hidden rounded-lg border">
                                <img :src="logoPreview" alt="Logo" class="h-full w-full object-contain" />
                            </div>
                            <div v-else class="flex h-24 w-24 items-center justify-center rounded-lg border bg-gray-100 text-gray-400 dark:bg-gray-800">
                                <svg class="h-12 w-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                            </div>
                            <div v-if="!viewMode" class="flex-1">
                                <input
                                    ref="fileInput"
                                    type="file"
                                    accept="image/jpeg,image/png,image/svg+xml"
                                    @change="handleLogoChange"
                                    class="hidden"
                                />
                                <Button type="button" variant="outline" @click="fileInput.click()">
                                    Escolher Imagem
                                </Button>
                                <p class="mt-1 text-sm text-gray-500">JPG, PNG ou SVG. Máximo 2MB.</p>
                            </div>
                        </div>
                    </div>

                    <!-- Nome e NIF -->
                    <div class="grid gap-4 md:grid-cols-2">
                        <div class="grid gap-2">
                            <Label for="name">Nome *</Label>
                            <Input
                                id="name"
                                v-model="form.name"
                                type="text"
                                :disabled="viewMode"
                                required
                            />
                        </div>

                        <div class="grid gap-2">
                            <Label for="nif">NIF *</Label>
                            <Input
                                id="nif"
                                v-model="form.nif"
                                type="text"
                                :disabled="viewMode"
                                required
                            />
                        </div>
                    </div>

                    <!-- Morada -->
                    <div class="grid gap-2">
                        <Label for="address">Morada</Label>
                        <Input
                            id="address"
                            v-model="form.address"
                            type="text"
                            :disabled="viewMode"
                        />
                    </div>

                    <!-- Código Postal, Localidade e País -->
                    <div class="grid gap-4 md:grid-cols-3">
                        <div class="grid gap-2">
                            <Label for="postal-code">Código Postal</Label>
                            <Input
                                id="postal-code"
                                v-model="form.postal_code"
                                type="text"
                                placeholder="XXXX-XXX"
                                :disabled="viewMode"
                            />
                        </div>

                        <div class="grid gap-2">
                            <Label for="city">Localidade</Label>
                            <Input
                                id="city"
                                v-model="form.city"
                                type="text"
                                :disabled="viewMode"
                            />
                        </div>

                        <div class="grid gap-2">
                            <Label for="country">País</Label>
                            <select
                                id="country"
                                v-model="form.country_id"
                                class="h-10 rounded-md border border-input bg-background px-3 py-2 text-sm"
                                :disabled="viewMode"
                            >
                                <option value="">Selecione...</option>
                                <option v-for="country in countries" :key="country.id" :value="country.id">
                                    {{ country.name }}
                                </option>
                            </select>
                        </div>
                    </div>

                    <!-- Telefone e Telemóvel -->
                    <div class="grid gap-4 md:grid-cols-2">
                        <div class="grid gap-2">
                            <Label for="phone">Telefone</Label>
                            <Input
                                id="phone"
                                v-model="form.phone"
                                type="text"
                                :disabled="viewMode"
                            />
                        </div>

                        <div class="grid gap-2">
                            <Label for="mobile">Telemóvel</Label>
                            <Input
                                id="mobile"
                                v-model="form.mobile"
                                type="text"
                                :disabled="viewMode"
                            />
                        </div>
                    </div>

                    <!-- Email e Website -->
                    <div class="grid gap-4 md:grid-cols-2">
                        <div class="grid gap-2">
                            <Label for="email">Email</Label>
                            <Input
                                id="email"
                                v-model="form.email"
                                type="email"
                                :disabled="viewMode"
                            />
                        </div>

                        <div class="grid gap-2">
                            <Label for="website">Website</Label>
                            <Input
                                id="website"
                                v-model="form.website"
                                type="url"
                                placeholder="https://"
                                :disabled="viewMode"
                            />
                        </div>
                    </div>

                    <!-- Observações -->
                    <div class="grid gap-2">
                        <Label for="notes">Observações</Label>
                        <Textarea
                            id="notes"
                            v-model="form.notes"
                            rows="4"
                            :disabled="viewMode"
                        />
                    </div>

                    <!-- Botões -->
                    <div class="flex justify-end gap-2">
                        <Button v-if="viewMode" type="button" @click="isDialogOpen = false">
                            Fechar
                        </Button>
                        <template v-else>
                            <Button type="button" variant="outline" @click="isDialogOpen = false">
                                Cancelar
                            </Button>
                            <Button type="submit" :disabled="form.processing">
                                {{ editingCompany ? 'Guardar Alterações' : 'Criar Empresa' }}
                            </Button>
                        </template>
                    </div>
                </form>
            </DialogContent>
        </Dialog>
    </AuthenticatedLayout>
</template>
