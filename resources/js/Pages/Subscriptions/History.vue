<template>
    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Histórico de Subscrição</h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
                <!-- Credits Summary -->
                <div v-if="creditsSummary.total_balance > 0" class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Créditos Disponíveis</h3>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div class="bg-green-50 p-4 rounded-lg">
                                <p class="text-sm text-gray-600">Saldo Total</p>
                                <p class="text-2xl font-bold text-green-600">
                                    €{{ creditsSummary.total_balance.toFixed(2) }}
                                </p>
                            </div>

                            <div class="bg-blue-50 p-4 rounded-lg">
                                <p class="text-sm text-gray-600">Créditos Ativos</p>
                                <p class="text-2xl font-bold text-blue-600">
                                    {{ creditsSummary.available_credits_count }}
                                </p>
                            </div>

                            <div v-if="creditsSummary.expiring_soon > 0" class="bg-yellow-50 p-4 rounded-lg">
                                <p class="text-sm text-gray-600">A Expirar em 30 dias</p>
                                <p class="text-2xl font-bold text-yellow-600">
                                    €{{ creditsSummary.expiring_soon.toFixed(2) }}
                                </p>
                            </div>
                        </div>

                        <!-- Credits List -->
                        <div v-if="creditsSummary.credits.length > 0" class="mt-6">
                            <h4 class="text-sm font-medium text-gray-700 mb-3">Detalhes dos Créditos</h4>
                            <div class="space-y-2">
                                <div
                                    v-for="credit in creditsSummary.credits"
                                    :key="credit.id"
                                    class="flex justify-between items-center p-3 bg-gray-50 rounded"
                                >
                                    <div>
                                        <p class="text-sm font-medium">{{ credit.description }}</p>
                                        <p class="text-xs text-gray-500">
                                            {{ formatDate(credit.created_at) }}
                                            <span v-if="credit.expires_at">
                                                · Expira em {{ formatDate(credit.expires_at) }}
                                            </span>
                                        </p>
                                    </div>
                                    <div class="text-right">
                                        <p class="text-sm font-bold text-green-600">€{{ credit.amount.toFixed(2) }}</p>
                                        <p class="text-xs text-gray-500">{{ getTypeLabel(credit.type) }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Audit Logs -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Histórico de Alterações</h3>

                        <div v-if="auditLogs.length === 0" class="text-center py-8 text-gray-500">
                            Ainda não existem alterações registadas.
                        </div>

                        <div v-else class="space-y-4">
                            <div
                                v-for="log in auditLogs"
                                :key="log.id"
                                class="border-l-4 pl-4 py-3"
                                :class="getEventColor(log.event)"
                            >
                                <div class="flex justify-between items-start">
                                    <div class="flex-1">
                                        <div class="flex items-center space-x-2">
                                            <span
                                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                                                :class="getEventBadgeClass(log.event)"
                                            >
                                                {{ getEventLabel(log.event) }}
                                            </span>
                                            <span class="text-xs text-gray-500">
                                                {{ formatDate(log.created_at) }}
                                            </span>
                                        </div>

                                        <p class="mt-1 text-sm text-gray-700">{{ log.description }}</p>

                                        <div v-if="log.previous_plan || log.new_plan" class="mt-2 text-xs text-gray-600">
                                            <span v-if="log.previous_plan">
                                                De: <strong>{{ log.previous_plan.name }}</strong>
                                            </span>
                                            <span v-if="log.previous_plan && log.new_plan"> → </span>
                                            <span v-if="log.new_plan">
                                                Para: <strong>{{ log.new_plan.name }}</strong>
                                            </span>
                                        </div>

                                        <div v-if="log.amount || log.prorated_amount || log.credit_amount" class="mt-2 flex space-x-4 text-xs">
                                            <span v-if="log.amount" class="text-gray-600">
                                                Valor: <strong class="text-gray-900">€{{ log.amount }}</strong>
                                            </span>
                                            <span v-if="log.prorated_amount" class="text-blue-600">
                                                Pró-rata: <strong>€{{ log.prorated_amount }}</strong>
                                            </span>
                                            <span v-if="log.credit_amount" class="text-green-600">
                                                Crédito: <strong>€{{ log.credit_amount }}</strong>
                                            </span>
                                        </div>

                                        <p v-if="log.user" class="mt-1 text-xs text-gray-500">
                                            Por: {{ log.user.name }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Back Button -->
                <div class="flex justify-between">
                    <Link
                        :href="route('subscriptions.index')"
                        class="inline-flex items-center px-4 py-2 bg-gray-300 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150"
                    >
                        Voltar
                    </Link>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { computed } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Link } from '@inertiajs/vue3';

const props = defineProps({
    tenant: Object,
    auditLogs: Array,
    creditsSummary: Object,
});

const formatDate = (dateString) => {
    return new Date(dateString).toLocaleString('pt-PT', {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
    });
};

const getEventLabel = (event) => {
    const labels = {
        'created': 'Criada',
        'upgraded': 'Upgrade',
        'downgraded': 'Downgrade',
        'downgrade_scheduled': 'Downgrade Agendado',
        'downgrade_canceled': 'Downgrade Cancelado',
        'canceled': 'Cancelada',
        'canceled_immediately': 'Cancelada Imediatamente',
        'resumed': 'Retomada',
        'renewed': 'Renovada',
        'trial_started': 'Teste Iniciado',
        'trial_converted': 'Teste Convertido',
        'trial_expired': 'Teste Expirado',
        'expired': 'Expirada',
    };
    return labels[event] || event;
};

const getEventColor = (event) => {
    const colors = {
        'created': 'border-blue-500',
        'upgraded': 'border-green-500',
        'downgraded': 'border-yellow-500',
        'downgrade_scheduled': 'border-yellow-400',
        'downgrade_canceled': 'border-gray-500',
        'canceled': 'border-red-500',
        'canceled_immediately': 'border-red-600',
        'resumed': 'border-green-400',
        'renewed': 'border-blue-400',
        'trial_started': 'border-purple-500',
        'trial_converted': 'border-green-500',
        'trial_expired': 'border-red-400',
        'expired': 'border-gray-600',
    };
    return colors[event] || 'border-gray-300';
};

const getEventBadgeClass = (event) => {
    const classes = {
        'created': 'bg-blue-100 text-blue-800',
        'upgraded': 'bg-green-100 text-green-800',
        'downgraded': 'bg-yellow-100 text-yellow-800',
        'downgrade_scheduled': 'bg-yellow-50 text-yellow-700',
        'downgrade_canceled': 'bg-gray-100 text-gray-800',
        'canceled': 'bg-red-100 text-red-800',
        'canceled_immediately': 'bg-red-200 text-red-900',
        'resumed': 'bg-green-50 text-green-700',
        'renewed': 'bg-blue-50 text-blue-700',
        'trial_started': 'bg-purple-100 text-purple-800',
        'trial_converted': 'bg-green-100 text-green-800',
        'trial_expired': 'bg-red-100 text-red-800',
        'expired': 'bg-gray-200 text-gray-900',
    };
    return classes[event] || 'bg-gray-100 text-gray-800';
};

const getTypeLabel = (type) => {
    const labels = {
        'refund': 'Reembolso',
        'cancellation_credit': 'Crédito de Cancelamento',
        'promotional': 'Promocional',
        'adjustment': 'Ajuste',
        'usage': 'Utilização',
    };
    return labels[type] || type;
};
</script>
