<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref, watch } from 'vue';
import { Input } from '@/Components/ui/input';
import { Badge } from '@/Components/ui/badge';
import { Table, TableHeader, TableBody, TableRow, TableHead, TableCell } from '@/Components/ui/table';
import { Label } from '@/Components/ui/label';

const props = defineProps({
    activities: Object,
    subjectTypes: Array,
    filters: Object
});

const search = ref(props.filters?.search || '');
const subjectTypeFilter = ref(props.filters?.subject_type || '');
const eventFilter = ref(props.filters?.event || '');
const dateFrom = ref(props.filters?.date_from || '');
const dateTo = ref(props.filters?.date_to || '');

watch([search, subjectTypeFilter, eventFilter, dateFrom, dateTo], ([newSearch, newSubjectType, newEvent, newDateFrom, newDateTo]) => {
    router.get(route('activity-log.index'), {
        search: newSearch,
        subject_type: newSubjectType,
        event: newEvent,
        date_from: newDateFrom,
        date_to: newDateTo
    }, {
        preserveState: true,
        replace: true
    });
}, { debounce: 300 });

const formatDate = (date) => {
    return new Date(date).toLocaleString('pt-PT');
};

const getEventBadgeVariant = (event) => {
    switch (event) {
        case 'created': return 'success';
        case 'updated': return 'default';
        case 'deleted': return 'destructive';
        default: return 'secondary';
    }
};

const getEventLabel = (event) => {
    switch (event) {
        case 'created': return 'Criado';
        case 'updated': return 'Atualizado';
        case 'deleted': return 'Eliminado';
        default: return event;
    }
};
</script>

<template>
    <Head title="Logs de Atividade" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
                Logs de Atividade
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg dark:bg-gray-800">
                    <div class="p-6">
                        <!-- Filtros -->
                        <div class="mb-6 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
                            <div>
                                <Label for="search">Pesquisar</Label>
                                <Input
                                    id="search"
                                    v-model="search"
                                    type="text"
                                    placeholder="Pesquisar na descrição..."
                                />
                            </div>
                            <div>
                                <Label for="subject-type">Tipo</Label>
                                <select
                                    id="subject-type"
                                    v-model="subjectTypeFilter"
                                    class="h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm"
                                >
                                    <option value="">Todos os tipos</option>
                                    <option v-for="type in subjectTypes" :key="type.value" :value="type.value">
                                        {{ type.label }}
                                    </option>
                                </select>
                            </div>
                            <div>
                                <Label for="event">Evento</Label>
                                <select
                                    id="event"
                                    v-model="eventFilter"
                                    class="h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm"
                                >
                                    <option value="">Todos os eventos</option>
                                    <option value="created">Criado</option>
                                    <option value="updated">Atualizado</option>
                                    <option value="deleted">Eliminado</option>
                                </select>
                            </div>
                            <div>
                                <Label for="date-from">Data Início</Label>
                                <Input
                                    id="date-from"
                                    v-model="dateFrom"
                                    type="date"
                                />
                            </div>
                            <div>
                                <Label for="date-to">Data Fim</Label>
                                <Input
                                    id="date-to"
                                    v-model="dateTo"
                                    type="date"
                                />
                            </div>
                        </div>

                        <!-- Tabela -->
                        <div v-if="activities.data.length > 0">
                            <Table>
                                <TableHeader>
                                    <TableRow>
                                        <TableHead>Data/Hora</TableHead>
                                        <TableHead>Utilizador</TableHead>
                                        <TableHead>Evento</TableHead>
                                        <TableHead>Tipo</TableHead>
                                        <TableHead>Descrição</TableHead>
                                    </TableRow>
                                </TableHeader>
                                <TableBody>
                                    <TableRow v-for="activity in activities.data" :key="activity.id">
                                        <TableCell class="whitespace-nowrap">{{ formatDate(activity.created_at) }}</TableCell>
                                        <TableCell>{{ activity.causer?.name || 'Sistema' }}</TableCell>
                                        <TableCell>
                                            <Badge :variant="getEventBadgeVariant(activity.event)">
                                                {{ getEventLabel(activity.event) }}
                                            </Badge>
                                        </TableCell>
                                        <TableCell>
                                            <code class="text-xs bg-gray-100 dark:bg-gray-700 px-2 py-1 rounded">
                                                {{ activity.subject_type?.split('\\').pop() }}
                                            </code>
                                        </TableCell>
                                        <TableCell>{{ activity.description }}</TableCell>
                                    </TableRow>
                                </TableBody>
                            </Table>

                            <!-- Paginação -->
                            <div class="mt-4 flex items-center justify-between">
                                <div class="text-sm text-gray-700 dark:text-gray-300">
                                    A mostrar {{ activities.from }} a {{ activities.to }} de {{ activities.total }} resultados
                                </div>
                                <div class="flex gap-2">
                                    <Link
                                        v-for="link in activities.links"
                                        :key="link.label"
                                        :href="link.url"
                                        :class="[
                                            'px-3 py-2 text-sm rounded-md',
                                            link.active
                                                ? 'bg-primary text-primary-foreground'
                                                : 'bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-600',
                                            !link.url && 'opacity-50 cursor-not-allowed'
                                        ]"
                                        v-html="link.label"
                                    />
                                </div>
                            </div>
                        </div>
                        <div v-else class="py-12 text-center text-gray-500 dark:text-gray-400">
                            Nenhum log de atividade encontrado.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
