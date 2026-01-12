<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, router, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';
import { Button } from '@/Components/ui/button';
import { Input } from '@/Components/ui/input';
import { Label } from '@/Components/ui/label';
import { Textarea } from '@/Components/ui/textarea';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/Components/ui/card';

const props = defineProps({
    company: Object,
    countries: Array
});

const logoPreview = ref(props.company?.logo_path ? `/storage/${props.company.logo_path}` : null);
const fileInput = ref(null);

const form = useForm({
    name: props.company?.name || '',
    nif: props.company?.nif || '',
    address: props.company?.address || '',
    postal_code: props.company?.postal_code || '',
    city: props.company?.city || '',
    country_id: props.company?.country_id || '',
    phone: props.company?.phone || '',
    mobile: props.company?.mobile || '',
    email: props.company?.email || '',
    website: props.company?.website || '',
    logo: null,
    notes: props.company?.notes || '',
});

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
    if (props.company) {
        form.post(route('settings.companies.update', props.company.id), {
            forceFormData: true,
            preserveScroll: true,
            onSuccess: () => {
                form.logo = null;
                if (fileInput.value) {
                    fileInput.value.value = '';
                }
            }
        });
    } else {
        form.post(route('settings.companies.store'), {
            forceFormData: true,
            preserveScroll: true,
            onSuccess: () => {
                form.logo = null;
                if (fileInput.value) {
                    fileInput.value.value = '';
                }
            }
        });
    }
}
</script>

<template>
    <Head title="Empresa" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
                    Configurações - Empresa
                </h2>
            </div>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-4xl sm:px-6 lg:px-8">
                <Card>
                    <CardHeader>
                        <CardTitle>Dados da Empresa</CardTitle>
                        <CardDescription>
                            Configure os dados da sua empresa. Estas informações serão utilizadas nos PDFs e em toda a aplicação.
                        </CardDescription>
                    </CardHeader>
                    <CardContent>
                        <form @submit.prevent="save" class="space-y-6">
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
                                    <div class="flex-1">
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
                                        required
                                    />
                                </div>

                                <div class="grid gap-2">
                                    <Label for="nif">NIF *</Label>
                                    <Input
                                        id="nif"
                                        v-model="form.nif"
                                        type="text"
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
                                    />
                                </div>

                                <div class="grid gap-2">
                                    <Label for="city">Localidade</Label>
                                    <Input
                                        id="city"
                                        v-model="form.city"
                                        type="text"
                                    />
                                </div>

                                <div class="grid gap-2">
                                    <Label for="country">País</Label>
                                    <select
                                        id="country"
                                        v-model="form.country_id"
                                        class="h-10 rounded-md border border-input bg-background px-3 py-2 text-sm"
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
                                    />
                                </div>

                                <div class="grid gap-2">
                                    <Label for="mobile">Telemóvel</Label>
                                    <Input
                                        id="mobile"
                                        v-model="form.mobile"
                                        type="text"
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
                                    />
                                </div>

                                <div class="grid gap-2">
                                    <Label for="website">Website</Label>
                                    <Input
                                        id="website"
                                        v-model="form.website"
                                        type="url"
                                        placeholder="https://"
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
                                />
                            </div>

                            <!-- Botões -->
                            <div class="flex justify-end">
                                <Button type="submit" :disabled="form.processing">
                                    {{ company ? 'Guardar Alterações' : 'Criar Empresa' }}
                                </Button>
                            </div>
                        </form>
                    </CardContent>
                </Card>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
