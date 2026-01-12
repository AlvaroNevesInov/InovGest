<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, router, usePage } from '@inertiajs/vue3';
import { ref, computed, onMounted } from 'vue';
import { Button } from '@/Components/ui/button';
import { Dialog, DialogContent, DialogHeader, DialogTitle, DialogFooter } from '@/Components/ui/dialog';
import { Input } from '@/Components/ui/input';
import { Label } from '@/Components/ui/label';
import { Textarea } from '@/Components/ui/textarea';
import FullCalendar from '@fullcalendar/vue3';
import dayGridPlugin from '@fullcalendar/daygrid';
import timeGridPlugin from '@fullcalendar/timegrid';
import interactionPlugin from '@fullcalendar/interaction';
import listPlugin from '@fullcalendar/list';

const props = defineProps({
    events: Array,
    types: Array,
    actions: Array,
    entities: Array,
    filters: Object
});

const page = usePage();
const calendarOptions = ref({
    plugins: [dayGridPlugin, timeGridPlugin, interactionPlugin, listPlugin],
    initialView: 'dayGridMonth',
    locale: 'pt',
    headerToolbar: {
        left: 'prev,next today',
        center: 'title',
        right: 'dayGridMonth,timeGridWeek,timeGridDay,listWeek'
    },
    buttonText: {
        today: 'Hoje',
        month: 'Mês',
        week: 'Semana',
        day: 'Dia',
        list: 'Lista'
    },
    events: [],
    editable: true,
    selectable: true,
    selectMirror: true,
    dayMaxEvents: true,
    weekends: true,
    select: handleDateSelect,
    eventClick: handleEventClick,
    eventDrop: handleEventDrop,
    eventResize: handleEventResize,
    height: 'auto',
});

const showDialog = ref(false);
const dialogMode = ref('create'); // 'create' or 'edit'
const selectedEvent = ref(null);

const formData = ref({
    title: '',
    description: '',
    start_date: '',
    end_date: '',
    all_day: false,
    calendar_type_id: '',
    calendar_action_id: '',
    entity_id: '',
    user_id: page.props.auth.user.id,
    color: '#3788d8',
    notes: ''
});

const userFilter = ref(props.filters?.user_id || '');
const typeFilter = ref(props.filters?.type_id || '');

// Convert events to FullCalendar format
const calendarEvents = computed(() => {
    return props.events.map(event => ({
        id: event.id,
        title: event.title,
        start: event.start_date,
        end: event.end_date,
        allDay: event.all_day,
        backgroundColor: event.color || '#3788d8',
        borderColor: event.color || '#3788d8',
        extendedProps: {
            description: event.description,
            calendar_type_id: event.calendar_type_id,
            calendar_action_id: event.calendar_action_id,
            entity_id: event.entity_id,
            user_id: event.user_id,
            notes: event.notes,
            calendarType: event.calendar_type,
            calendarAction: event.calendar_action,
            entity: event.entity,
            user: event.user
        }
    }));
});

onMounted(() => {
    calendarOptions.value.events = calendarEvents.value;
});

function handleDateSelect(selectInfo) {
    dialogMode.value = 'create';
    formData.value = {
        title: '',
        description: '',
        start_date: selectInfo.startStr,
        end_date: selectInfo.endStr || selectInfo.startStr,
        all_day: selectInfo.allDay,
        calendar_type_id: '',
        calendar_action_id: '',
        entity_id: '',
        user_id: page.props.auth.user.id,
        color: '#3788d8',
        notes: ''
    };
    showDialog.value = true;
}

function handleEventClick(clickInfo) {
    dialogMode.value = 'edit';
    selectedEvent.value = clickInfo.event;

    formData.value = {
        title: clickInfo.event.title,
        description: clickInfo.event.extendedProps.description || '',
        start_date: formatDateForInput(clickInfo.event.start),
        end_date: formatDateForInput(clickInfo.event.end || clickInfo.event.start),
        all_day: clickInfo.event.allDay,
        calendar_type_id: clickInfo.event.extendedProps.calendar_type_id,
        calendar_action_id: clickInfo.event.extendedProps.calendar_action_id,
        entity_id: clickInfo.event.extendedProps.entity_id || '',
        user_id: clickInfo.event.extendedProps.user_id,
        color: clickInfo.event.backgroundColor,
        notes: clickInfo.event.extendedProps.notes || ''
    };

    showDialog.value = true;
}

function handleEventDrop(info) {
    updateEvent(info.event.id, {
        start_date: formatDateForInput(info.event.start),
        end_date: formatDateForInput(info.event.end || info.event.start),
    });
}

function handleEventResize(info) {
    updateEvent(info.event.id, {
        start_date: formatDateForInput(info.event.start),
        end_date: formatDateForInput(info.event.end),
    });
}

function formatDateForInput(date) {
    if (!date) return '';
    const d = new Date(date);
    const year = d.getFullYear();
    const month = String(d.getMonth() + 1).padStart(2, '0');
    const day = String(d.getDate()).padStart(2, '0');
    const hours = String(d.getHours()).padStart(2, '0');
    const minutes = String(d.getMinutes()).padStart(2, '0');
    return `${year}-${month}-${day}T${hours}:${minutes}`;
}

function saveEvent() {
    if (dialogMode.value === 'create') {
        router.post(route('calendar.store'), formData.value, {
            onSuccess: () => {
                showDialog.value = false;
                resetForm();
            }
        });
    } else {
        router.put(route('calendar.update', selectedEvent.value.id), formData.value, {
            onSuccess: () => {
                showDialog.value = false;
                resetForm();
            }
        });
    }
}

function updateEvent(eventId, data) {
    router.put(route('calendar.update', eventId), {
        ...formData.value,
        ...data
    }, {
        preserveScroll: true
    });
}

function deleteEvent() {
    if (confirm('Tem a certeza que deseja eliminar este evento?')) {
        router.delete(route('calendar.destroy', selectedEvent.value.id), {
            onSuccess: () => {
                showDialog.value = false;
                resetForm();
            }
        });
    }
}

function resetForm() {
    formData.value = {
        title: '',
        description: '',
        start_date: '',
        end_date: '',
        all_day: false,
        calendar_type_id: '',
        calendar_action_id: '',
        entity_id: '',
        user_id: page.props.auth.user.id,
        color: '#3788d8',
        notes: ''
    };
    selectedEvent.value = null;
}

function applyFilters() {
    router.get(route('calendar.index'), {
        user_id: userFilter.value,
        type_id: typeFilter.value
    }, {
        preserveState: true,
        replace: true
    });
}
</script>

<template>
    <Head title="Calendário" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
                    Calendário
                </h2>
            </div>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg dark:bg-gray-800">
                    <div class="p-6">
                        <!-- Filtros -->
                        <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-end">
                            <div class="flex-1">
                                <Label for="user-filter">Filtrar por Utilizador</Label>
                                <select
                                    id="user-filter"
                                    v-model="userFilter"
                                    class="mt-1 h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm"
                                    @change="applyFilters"
                                >
                                    <option value="">Todos os utilizadores</option>
                                    <option :value="page.props.auth.user.id">{{ page.props.auth.user.name }}</option>
                                </select>
                            </div>

                            <div class="flex-1">
                                <Label for="type-filter">Filtrar por Tipo</Label>
                                <select
                                    id="type-filter"
                                    v-model="typeFilter"
                                    class="mt-1 h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm"
                                    @change="applyFilters"
                                >
                                    <option value="">Todos os tipos</option>
                                    <option v-for="type in types" :key="type.id" :value="type.id">
                                        {{ type.name }}
                                    </option>
                                </select>
                            </div>
                        </div>

                        <!-- Calendar -->
                        <div class="calendar-wrapper">
                            <FullCalendar :options="calendarOptions" />
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Event Dialog -->
        <Dialog v-model:open="showDialog">
            <DialogContent class="sm:max-w-[600px]">
                <DialogHeader>
                    <DialogTitle>
                        {{ dialogMode === 'create' ? 'Novo Evento' : 'Editar Evento' }}
                    </DialogTitle>
                </DialogHeader>

                <div class="grid gap-4 py-4">
                    <div class="grid gap-2">
                        <Label for="title">Título *</Label>
                        <Input
                            id="title"
                            v-model="formData.title"
                            type="text"
                            required
                        />
                    </div>

                    <div class="grid gap-2">
                        <Label for="description">Descrição</Label>
                        <Textarea
                            id="description"
                            v-model="formData.description"
                            rows="3"
                        />
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div class="grid gap-2">
                            <Label for="start-date">Data Início *</Label>
                            <Input
                                id="start-date"
                                v-model="formData.start_date"
                                type="datetime-local"
                                required
                            />
                        </div>

                        <div class="grid gap-2">
                            <Label for="end-date">Data Fim *</Label>
                            <Input
                                id="end-date"
                                v-model="formData.end_date"
                                type="datetime-local"
                                required
                            />
                        </div>
                    </div>

                    <div class="flex items-center gap-2">
                        <input
                            id="all-day"
                            v-model="formData.all_day"
                            type="checkbox"
                            class="h-4 w-4 rounded border-gray-300"
                        />
                        <Label for="all-day">Dia inteiro</Label>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div class="grid gap-2">
                            <Label for="type">Tipo *</Label>
                            <select
                                id="type"
                                v-model="formData.calendar_type_id"
                                class="h-10 rounded-md border border-input bg-background px-3 py-2 text-sm"
                                required
                            >
                                <option value="">Selecione...</option>
                                <option v-for="type in types" :key="type.id" :value="type.id">
                                    {{ type.name }}
                                </option>
                            </select>
                        </div>

                        <div class="grid gap-2">
                            <Label for="action">Ação *</Label>
                            <select
                                id="action"
                                v-model="formData.calendar_action_id"
                                class="h-10 rounded-md border border-input bg-background px-3 py-2 text-sm"
                                required
                            >
                                <option value="">Selecione...</option>
                                <option v-for="action in actions" :key="action.id" :value="action.id">
                                    {{ action.name }}
                                </option>
                            </select>
                        </div>
                    </div>

                    <div class="grid gap-2">
                        <Label for="entity">Entidade</Label>
                        <select
                            id="entity"
                            v-model="formData.entity_id"
                            class="h-10 rounded-md border border-input bg-background px-3 py-2 text-sm"
                        >
                            <option value="">Nenhuma</option>
                            <option v-for="entity in entities" :key="entity.id" :value="entity.id">
                                {{ entity.name }}
                            </option>
                        </select>
                    </div>

                    <div class="grid gap-2">
                        <Label for="color">Cor</Label>
                        <Input
                            id="color"
                            v-model="formData.color"
                            type="color"
                            class="h-10 w-20"
                        />
                    </div>

                    <div class="grid gap-2">
                        <Label for="notes">Observações</Label>
                        <Textarea
                            id="notes"
                            v-model="formData.notes"
                            rows="3"
                        />
                    </div>
                </div>

                <DialogFooter>
                    <Button
                        v-if="dialogMode === 'edit'"
                        variant="destructive"
                        @click="deleteEvent"
                    >
                        Eliminar
                    </Button>
                    <Button variant="outline" @click="showDialog = false">
                        Cancelar
                    </Button>
                    <Button @click="saveEvent">
                        {{ dialogMode === 'create' ? 'Criar' : 'Guardar' }}
                    </Button>
                </DialogFooter>
            </DialogContent>
        </Dialog>
    </AuthenticatedLayout>
</template>

<style>
/* FullCalendar custom styles */
.calendar-wrapper {
    background: white;
    border-radius: 0.5rem;
}

.fc {
    --fc-border-color: #e5e7eb;
    --fc-button-bg-color: #3788d8;
    --fc-button-border-color: #3788d8;
    --fc-button-hover-bg-color: #2563eb;
    --fc-button-hover-border-color: #2563eb;
    --fc-button-active-bg-color: #1d4ed8;
    --fc-button-active-border-color: #1d4ed8;
}

.fc .fc-button {
    padding: 0.5rem 1rem;
    font-size: 0.875rem;
    text-transform: capitalize;
}

.fc .fc-toolbar-title {
    font-size: 1.5rem;
    font-weight: 600;
}

.fc-event {
    cursor: pointer;
}

.dark .calendar-wrapper {
    background: #1f2937;
}

.dark .fc {
    --fc-border-color: #374151;
    --fc-neutral-bg-color: #1f2937;
    --fc-page-bg-color: #1f2937;
}

.dark .fc-theme-standard td,
.dark .fc-theme-standard th {
    border-color: #374151;
}

.dark .fc-theme-standard .fc-scrollgrid {
    border-color: #374151;
}

.dark .fc .fc-col-header-cell {
    background: #111827;
}

.dark .fc .fc-daygrid-day-number {
    color: #e5e7eb;
}
</style>
