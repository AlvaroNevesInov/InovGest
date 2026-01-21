<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm, router } from '@inertiajs/vue3';
import { Card, CardContent, CardDescription, CardFooter, CardHeader, CardTitle } from '@/Components/ui/card';
import { Button } from '@/Components/ui/button';
import Badge from '@/Components/ui/badge.vue';
import { Check } from 'lucide-vue-next';

const props = defineProps({
    plans: Array,
    currentSubscription: Object,
    usageSummary: Array,
});

const subscribe = (plan) => {
    if (!props.currentSubscription) {
        router.post(route('subscriptions.subscribe', plan.id), {
            tenant_id: window.currentTenantId || sessionStorage.getItem('current_tenant_id'),
        });
    } else {
        // Determine if upgrade or downgrade
        const currentPrice = props.currentSubscription.plan.price;
        const newPrice = plan.price;

        if (newPrice > currentPrice) {
            // Upgrade
            router.post(route('subscriptions.upgrade', plan.id));
        } else if (newPrice < currentPrice) {
            // Downgrade
            router.post(route('subscriptions.downgrade', plan.id));
        }
    }
};

const isCurrentPlan = (plan) => {
    return props.currentSubscription && props.currentSubscription.plan_id === plan.id;
};

const isScheduledPlan = (plan) => {
    return props.currentSubscription &&
           props.currentSubscription.scheduled_plan_id === plan.id;
};

const canChangeTo = (plan) => {
    if (!props.currentSubscription) return true;
    return props.currentSubscription.plan_id !== plan.id;
};

const getButtonText = (plan) => {
    if (isCurrentPlan(plan)) return 'Plano Atual';
    if (isScheduledPlan(plan)) return 'Agendado';
    if (!props.currentSubscription) return 'Começar Teste Grátis';

    const currentPrice = props.currentSubscription.plan.price;
    const newPrice = plan.price;

    if (newPrice > currentPrice) return 'Fazer Upgrade';
    if (newPrice < currentPrice) return 'Fazer Downgrade';
    return 'Selecionar';
};

const formatPrice = (price) => {
    return new Intl.NumberFormat('pt-PT', {
        style: 'currency',
        currency: 'EUR'
    }).format(price);
};

const getFeatureName = (feature) => {
    const names = {
        calendar: 'Calendário',
        contacts: 'Contactos',
        articles: 'Artigos',
        basic_invoicing: 'Faturação Básica',
        invoicing: 'Faturação Completa',
        proposals: 'Propostas',
        work_orders: 'Ordens de Trabalho',
        advanced_reports: 'Relatórios Avançados',
        email_support: 'Suporte por Email',
        priority_support: 'Suporte Prioritário',
        custom_branding: 'Branding Personalizado',
        api_access: 'Acesso à API',
        advanced_permissions: 'Permissões Avançadas',
    };
    return names[feature] || feature;
};
</script>

<template>
    <AuthenticatedLayout>
        <Head title="Planos e Preços" />

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <!-- Header -->
                <div class="text-center mb-12">
                    <h1 class="text-4xl font-bold mb-4">Escolha o plano ideal para si</h1>
                    <p class="text-lg text-gray-600 dark:text-gray-400">
                        Todos os planos incluem 14 dias de teste grátis
                    </p>
                </div>

                <!-- Current Plan Alert -->
                <div v-if="currentSubscription" class="mb-8">
                    <Card class="border-blue-200 dark:border-blue-800">
                        <CardContent class="pt-6">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">Plano Atual</p>
                                    <p class="text-xl font-bold">{{ currentSubscription.plan.name }}</p>
                                    <p v-if="currentSubscription.status === 'trialing'" class="text-sm text-orange-600">
                                        Teste grátis - Termina em {{ new Date(currentSubscription.trial_ends_at).toLocaleDateString('pt-PT') }}
                                    </p>
                                    <p v-if="currentSubscription.scheduled_plan_id" class="text-sm text-blue-600">
                                        Mudança agendada para {{ currentSubscription.scheduled_plan?.name }} em
                                        {{ new Date(currentSubscription.plan_change_scheduled_for).toLocaleDateString('pt-PT') }}
                                    </p>
                                </div>
                                <Button variant="outline" @click="router.visit(route('subscriptions.index'))">
                                    Ver Detalhes da Subscrição
                                </Button>
                            </div>
                        </CardContent>
                    </Card>
                </div>

                <!-- Plans Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <Card
                        v-for="plan in plans"
                        :key="plan.id"
                        :class="[
                            'relative',
                            plan.is_popular ? 'border-blue-500 dark:border-blue-400 shadow-lg' : '',
                            isCurrentPlan(plan) ? 'ring-2 ring-green-500' : ''
                        ]"
                    >
                        <!-- Popular Badge -->
                        <div v-if="plan.is_popular" class="absolute -top-3 left-1/2 -translate-x-1/2">
                            <Badge class="bg-blue-500 text-white">Mais Popular</Badge>
                        </div>

                        <CardHeader>
                            <CardTitle class="flex items-center justify-between">
                                <span>{{ plan.name }}</span>
                                <Badge v-if="isCurrentPlan(plan)" variant="success">Atual</Badge>
                                <Badge v-else-if="isScheduledPlan(plan)" variant="secondary">Agendado</Badge>
                            </CardTitle>
                            <CardDescription>{{ plan.description }}</CardDescription>
                        </CardHeader>

                        <CardContent class="space-y-6">
                            <!-- Price -->
                            <div>
                                <div class="flex items-baseline">
                                    <span class="text-4xl font-bold">{{ formatPrice(plan.price) }}</span>
                                    <span class="text-gray-600 dark:text-gray-400 ml-2">
                                        / {{ plan.billing_period === 'yearly' ? 'ano' : 'mês' }}
                                    </span>
                                </div>
                                <p v-if="plan.billing_period === 'yearly'" class="text-sm text-green-600 mt-1">
                                    Poupa 20% com faturação anual
                                </p>
                                <p v-if="plan.trial_days > 0" class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                                    {{ plan.trial_days }} dias de teste grátis
                                </p>
                            </div>

                            <!-- Features -->
                            <div class="space-y-3">
                                <p class="font-semibold text-sm">Funcionalidades incluídas:</p>
                                <ul class="space-y-2">
                                    <li v-for="feature in plan.features" :key="feature" class="flex items-start">
                                        <Check class="w-5 h-5 text-green-500 mr-2 flex-shrink-0" />
                                        <span class="text-sm">{{ getFeatureName(feature) }}</span>
                                    </li>
                                </ul>
                            </div>

                            <!-- Limits -->
                            <div v-if="plan.limits" class="space-y-2 pt-4 border-t">
                                <p class="font-semibold text-sm">Limites:</p>
                                <ul class="text-sm space-y-1 text-gray-600 dark:text-gray-400">
                                    <li v-if="plan.limits.users">
                                        {{ plan.limits.users }} {{ plan.limits.users === 1 ? 'utilizador' : 'utilizadores' }}
                                    </li>
                                    <li v-if="plan.limits.users === null">
                                        Utilizadores ilimitados
                                    </li>
                                    <li v-if="plan.limits.companies">
                                        {{ plan.limits.companies }} {{ plan.limits.companies === 1 ? 'empresa' : 'empresas' }}
                                    </li>
                                    <li v-if="plan.limits.companies === null">
                                        Empresas ilimitadas
                                    </li>
                                    <li v-if="plan.limits.storage_gb">
                                        {{ plan.limits.storage_gb }}GB de armazenamento
                                    </li>
                                </ul>
                            </div>
                        </CardContent>

                        <CardFooter>
                            <Button
                                @click="subscribe(plan)"
                                :disabled="!canChangeTo(plan)"
                                :variant="plan.is_popular ? 'default' : 'outline'"
                                class="w-full"
                            >
                                {{ getButtonText(plan) }}
                            </Button>
                        </CardFooter>
                    </Card>
                </div>

                <!-- FAQ or Additional Info -->
                <div class="mt-12 text-center text-sm text-gray-600 dark:text-gray-400">
                    <p>Tem dúvidas sobre os planos? <a href="#" class="text-blue-600 hover:underline">Contacte-nos</a></p>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
