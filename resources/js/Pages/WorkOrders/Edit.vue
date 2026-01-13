<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { Button } from '@/Components/ui/button';
import { Input } from '@/Components/ui/input';
import { Label } from '@/Components/ui/label';
import { Textarea } from '@/Components/ui/textarea';
import { Card, CardHeader, CardTitle, CardContent } from '@/Components/ui/card';
import InputError from '@/Components/InputError.vue';

const props = defineProps({
    workOrder: Object,
    entities: Array,
    orders: Array
});

const form = useForm({
    work_date: props.workOrder.work_date,
    entity_id: props.workOrder.entity_id,
    order_id: props.workOrder.order_id || '',
    title: props.workOrder.title,
    description: props.workOrder.description || '',
    status: props.workOrder.status,
    priority: props.workOrder.priority,
    start_date: props.workOrder.start_date || '',
    due_date: props.workOrder.due_date || '',
    completed_date: props.workOrder.completed_date || '',
    notes: props.workOrder.notes || ''
});

const submit = () => {
    form.put(route('work-orders.update', props.workOrder.id));
};
</script>

<template>
    <Head title="Editar Ordem de Trabalho" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
                    Editar Ordem de Trabalho #{{ workOrder.number }}
                </h2>
                <Link :href="route('work-orders.index')">
                    <Button variant="outline">Voltar</Button>
                </Link>
            </div>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-4xl sm:px-6 lg:px-8">
                <Card>
                    <CardHeader>
                        <CardTitle>Dados da Ordem de Trabalho</CardTitle>
                    </CardHeader>
                    <CardContent>
                        <form @submit.prevent="submit" class="space-y-6">
                            <!-- Número -->
                            <div>
                                <Label for="number">Número</Label>
                                <Input
                                    id="number"
                                    type="text"
                                    :value="workOrder.number"
                                    disabled
                                    class="mt-1"
                                />
                            </div>

                            <!-- Título e Data -->
                            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                                <div>
                                    <Label for="title">Título *</Label>
                                    <Input
                                        id="title"
                                        v-model="form.title"
                                        type="text"
                                        class="mt-1"
                                        required
                                        placeholder="Ex: Instalação de equipamento"
                                    />
                                    <InputError :message="form.errors.title" class="mt-2" />
                                </div>

                                <div>
                                    <Label for="work_date">Data de Trabalho *</Label>
                                    <Input
                                        id="work_date"
                                        v-model="form.work_date"
                                        type="date"
                                        class="mt-1"
                                        required
                                    />
                                    <InputError :message="form.errors.work_date" class="mt-2" />
                                </div>
                            </div>

                            <!-- Cliente e Encomenda -->
                            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                                <div>
                                    <Label for="entity_id">Cliente *</Label>
                                    <select
                                        id="entity_id"
                                        v-model="form.entity_id"
                                        class="mt-1 block w-full h-10 rounded-md border border-input bg-background px-3 py-2 text-sm"
                                        required
                                    >
                                        <option value="">Selecione um cliente</option>
                                        <option v-for="entity in entities" :key="entity.id" :value="entity.id">
                                            {{ entity.name }}
                                        </option>
                                    </select>
                                    <InputError :message="form.errors.entity_id" class="mt-2" />
                                </div>

                                <div>
                                    <Label for="order_id">Encomenda (Opcional)</Label>
                                    <select
                                        id="order_id"
                                        v-model="form.order_id"
                                        class="mt-1 block w-full h-10 rounded-md border border-input bg-background px-3 py-2 text-sm"
                                    >
                                        <option value="">Sem encomenda associada</option>
                                        <option v-for="order in orders" :key="order.id" :value="order.id">
                                            #{{ order.number }} - {{ order.entity?.name }}
                                        </option>
                                    </select>
                                    <InputError :message="form.errors.order_id" class="mt-2" />
                                </div>
                            </div>

                            <!-- Estado e Prioridade -->
                            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                                <div>
                                    <Label for="status">Estado *</Label>
                                    <select
                                        id="status"
                                        v-model="form.status"
                                        class="mt-1 block w-full h-10 rounded-md border border-input bg-background px-3 py-2 text-sm"
                                        required
                                    >
                                        <option value="pending">Pendente</option>
                                        <option value="in_progress">Em Progresso</option>
                                        <option value="completed">Concluída</option>
                                        <option value="cancelled">Cancelada</option>
                                    </select>
                                    <InputError :message="form.errors.status" class="mt-2" />
                                </div>

                                <div>
                                    <Label for="priority">Prioridade *</Label>
                                    <select
                                        id="priority"
                                        v-model="form.priority"
                                        class="mt-1 block w-full h-10 rounded-md border border-input bg-background px-3 py-2 text-sm"
                                        required
                                    >
                                        <option value="low">Baixa</option>
                                        <option value="normal">Normal</option>
                                        <option value="high">Alta</option>
                                        <option value="urgent">Urgente</option>
                                    </select>
                                    <InputError :message="form.errors.priority" class="mt-2" />
                                </div>
                            </div>

                            <!-- Datas de Início, Conclusão e Completada -->
                            <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
                                <div>
                                    <Label for="start_date">Data de Início</Label>
                                    <Input
                                        id="start_date"
                                        v-model="form.start_date"
                                        type="date"
                                        class="mt-1"
                                    />
                                    <InputError :message="form.errors.start_date" class="mt-2" />
                                </div>

                                <div>
                                    <Label for="due_date">Data de Conclusão Prevista</Label>
                                    <Input
                                        id="due_date"
                                        v-model="form.due_date"
                                        type="date"
                                        class="mt-1"
                                    />
                                    <InputError :message="form.errors.due_date" class="mt-2" />
                                </div>

                                <div>
                                    <Label for="completed_date">Data de Conclusão Real</Label>
                                    <Input
                                        id="completed_date"
                                        v-model="form.completed_date"
                                        type="date"
                                        class="mt-1"
                                    />
                                    <InputError :message="form.errors.completed_date" class="mt-2" />
                                </div>
                            </div>

                            <!-- Descrição -->
                            <div>
                                <Label for="description">Descrição</Label>
                                <Textarea
                                    id="description"
                                    v-model="form.description"
                                    rows="4"
                                    class="mt-1"
                                    placeholder="Descreva o trabalho a ser realizado..."
                                />
                                <InputError :message="form.errors.description" class="mt-2" />
                            </div>

                            <!-- Observações -->
                            <div>
                                <Label for="notes">Observações</Label>
                                <Textarea
                                    id="notes"
                                    v-model="form.notes"
                                    rows="3"
                                    class="mt-1"
                                />
                                <InputError :message="form.errors.notes" class="mt-2" />
                            </div>

                            <!-- Botões -->
                            <div class="flex items-center justify-end gap-4">
                                <Link :href="route('work-orders.index')">
                                    <Button type="button" variant="outline">Cancelar</Button>
                                </Link>
                                <Button type="submit" :disabled="form.processing">
                                    {{ form.processing ? 'A guardar...' : 'Atualizar Ordem de Trabalho' }}
                                </Button>
                            </div>
                        </form>
                    </CardContent>
                </Card>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
