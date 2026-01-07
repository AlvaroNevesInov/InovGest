<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { Button } from '@/Components/ui/button';
import {Badge} from '@/Components/ui/badge';
import {Card} from '@/Components/ui/card';
import {CardHeader} from '@/Components/ui/card';
import {CardTitle} from '@/Components/ui/card';
import {CardContent} from '@/Components/ui/card';

const props = defineProps({
    entity: Object
});

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
</script>

<template>
    <Head :title="`Entidade #${entity.number}`" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
                    Entidade #{{ entity.number }}
                </h2>
                <div class="flex gap-2">
                    <Link :href="route('entities.edit', entity.id)">
                        <Button>Editar</Button>
                    </Link>
                    <Link :href="route('entities.index')">
                        <Button variant="outline">Voltar</Button>
                    </Link>
                </div>
            </div>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-4xl sm:px-6 lg:px-8">
                <Card>
                    <CardHeader>
                        <div class="flex items-start justify-between">
                            <div>
                                <CardTitle>{{ entity.name }}</CardTitle>
                                <p class="mt-1 text-sm text-muted-foreground">NIF: {{ entity.nif }}</p>
                            </div>
                            <div class="flex gap-2">
                                <Badge :variant="getTypeBadgeVariant(entity.type)">
                                    {{ getTypeLabel(entity.type) }}
                                </Badge>
                                <Badge :variant="entity.active ? 'default' : 'destructive'">
                                    {{ entity.active ? 'Ativo' : 'Inativo' }}
                                </Badge>
                            </div>
                        </div>
                    </CardHeader>
                    <CardContent>
                        <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                            <!-- Informações de Contacto -->
                            <div class="space-y-4">
                                <h3 class="font-semibold">Contacto</h3>

                                <div v-if="entity.phone">
                                    <p class="text-sm text-muted-foreground">Telefone</p>
                                    <p class="font-medium">{{ entity.phone }}</p>
                                </div>

                                <div v-if="entity.mobile">
                                    <p class="text-sm text-muted-foreground">Telemóvel</p>
                                    <p class="font-medium">{{ entity.mobile }}</p>
                                </div>

                                <div v-if="entity.email">
                                    <p class="text-sm text-muted-foreground">Email</p>
                                    <a :href="`mailto:${entity.email}`" class="font-medium text-primary hover:underline">
                                        {{ entity.email }}
                                    </a>
                                </div>

                                <div v-if="entity.website">
                                    <p class="text-sm text-muted-foreground">Website</p>
                                    <a :href="entity.website" target="_blank" class="font-medium text-primary hover:underline">
                                        {{ entity.website }}
                                    </a>
                                </div>
                            </div>

                            <!-- Morada -->
                            <div class="space-y-4">
                                <h3 class="font-semibold">Morada</h3>

                                <div v-if="entity.address">
                                    <p class="text-sm text-muted-foreground">Morada</p>
                                    <p class="font-medium">{{ entity.address }}</p>
                                </div>

                                <div class="grid grid-cols-2 gap-4">
                                    <div v-if="entity.postal_code">
                                        <p class="text-sm text-muted-foreground">Código Postal</p>
                                        <p class="font-medium">{{ entity.postal_code }}</p>
                                    </div>

                                    <div v-if="entity.city">
                                        <p class="text-sm text-muted-foreground">Localidade</p>
                                        <p class="font-medium">{{ entity.city }}</p>
                                    </div>
                                </div>

                                <div v-if="entity.country">
                                    <p class="text-sm text-muted-foreground">País</p>
                                    <p class="font-medium">{{ entity.country.name }}</p>
                                </div>
                            </div>

                            <!-- Outras Informações -->
                            <div class="space-y-4 md:col-span-2">
                                <h3 class="font-semibold">Outras Informações</h3>

                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <p class="text-sm text-muted-foreground">Consentimento RGPD</p>
                                        <Badge :variant="entity.rgpd_consent ? 'default' : 'secondary'">
                                            {{ entity.rgpd_consent ? 'Sim' : 'Não' }}
                                        </Badge>
                                    </div>
                                </div>

                                <div v-if="entity.notes">
                                    <p class="text-sm text-muted-foreground">Observações</p>
                                    <p class="mt-1 whitespace-pre-wrap">{{ entity.notes }}</p>
                                </div>
                            </div>
                        </div>
                    </CardContent>
                </Card>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
