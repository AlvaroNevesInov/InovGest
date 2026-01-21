<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, router } from '@inertiajs/vue3';
import { Card, CardContent, CardDescription, CardFooter, CardHeader, CardTitle } from '@/Components/ui/card';
import { Button } from '@/Components/ui/button';
import Badge from '@/Components/ui/badge.vue';
import Progress from '@/Components/ui/progress.vue';
import Alert from '@/Components/ui/alert.vue';
import AlertDescription from '@/Components/ui/alert-description.vue';
import { CreditCard, Calendar, AlertCircle, CheckCircle, XCircle, Clock } from 'lucide-vue-next';
import { ref } from 'vue';

const props = defineProps({
    tenant: Object,
    subscription: Object,
    usageSummary: Array,
});

const showCancelDialog = ref(false);

const formatPrice = (price) => {
    return new Intl.NumberFormat('pt-PT', {
        style: 'currency',
        currency: 'EUR'
    }).format(price);
};

const formatDate = (date) => {
    return new Date(date).toLocaleDateString('pt-PT', {
        day: '2-digit',
        month: 'long',
        year: 'numeric'
    });
};

const getStatusBadge = (status) => {
    const badges = {
        trialing: { text: 'Em Teste', variant: 'secondary', icon: Clock },
        active: { text: 'Ativo', variant: 'success', icon: CheckCircle },
        past_due: { text: 'Pagamento Pendente', variant: 'warning', icon: AlertCircle },
        canceled: { text: 'Cancelado', variant: 'destructive', icon: XCircle },
        expired: { text: 'Expirado', variant: 'destructive', icon: XCircle },
    };
    return badges[status] || badges.expired;
};

const getFeatureName = (feature) => {
    const names = {
        users: 'Utilizadores',
        companies: 'Empresas',
        storage_gb: 'Armazenamento (GB)',
        invoices_per_month: 'Faturas por Mês',
        proposals_per_month: 'Propostas por Mês',
    };
    return names[feature] || feature;
};

const cancelScheduledChange = () => {
    if (confirm('Tem a certeza que deseja cancelar a alteração de plano agendada?')) {
        router.post(route('subscriptions.cancel-scheduled'));
    }
};

const cancelSubscription = (immediately = false) => {
    const message = immediately
        ? 'Tem a certeza que deseja cancelar a subscrição imediatamente? Perderá acesso a todas as funcionalidades.'
        : 'Tem a certeza que deseja cancelar a subscrição? Continuará com acesso até ao fim do período atual.';

    if (confirm(message)) {
        router.post(route('subscriptions.cancel'), {
            immediately: immediately
        });
    }
};

const resumeSubscription = () => {
    if (confirm('Deseja retomar a sua subscrição?')) {
        router.post(route('subscriptions.resume'));
    }
};
</script>

<template>
    <AuthenticatedLayout>
        <Head title="Minha Subscrição" />

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
                <!-- Header -->
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-3xl font-bold">Minha Subscrição</h1>
                        <p class="text-gray-600 dark:text-gray-400">Gerir o seu plano e faturação</p>
                    </div>
                    <Button variant="outline" @click="router.visit(route('subscriptions.plans'))">
                        Ver Todos os Planos
                    </Button>
                </div>

                <!-- No Subscription -->
                <div v-if="!subscription">
                    <Alert>
                        <AlertCircle class="h-4 w-4" />
                        <AlertDescription>
                            Não tem uma subscrição ativa. Por favor, escolha um plano para continuar.
                        </AlertDescription>
                    </Alert>
                    <div class="mt-4">
                        <Button @click="router.visit(route('subscriptions.plans'))">
                            Escolher um Plano
                        </Button>
                    </div>
                </div>

                <!-- Subscription Details -->
                <template v-else>
                    <!-- Status Alerts -->
                    <div v-if="subscription.status === 'trialing'" class="space-y-4">
                        <Alert class="border-blue-200 dark:border-blue-800">
                            <Clock class="h-4 w-4" />
                            <AlertDescription>
                                Está em período de teste. O seu teste termina em
                                <strong>{{ formatDate(subscription.trial_ends_at) }}</strong>.
                                Após o período de teste, será cobrado {{ formatPrice(subscription.plan.price) }}
                                por {{ subscription.plan.billing_period === 'yearly' ? 'ano' : 'mês' }}.
                            </AlertDescription>
                        </Alert>
                    </div>

                    <div v-if="subscription.status === 'canceled'" class="space-y-4">
                        <Alert variant="destructive">
                            <XCircle class="h-4 w-4" />
                            <AlertDescription>
                                A sua subscrição foi cancelada e terminará em
                                <strong>{{ formatDate(subscription.ends_at) }}</strong>.
                                Pode retomar a qualquer momento antes desta data.
                            </AlertDescription>
                        </Alert>
                        <Button @click="resumeSubscription" variant="default">
                            Retomar Subscrição
                        </Button>
                    </div>

                    <div v-if="subscription.scheduled_plan_id" class="space-y-4">
                        <Alert class="border-orange-200 dark:border-orange-800">
                            <Calendar class="h-4 w-4" />
                            <AlertDescription class="flex items-center justify-between">
                                <span>
                                    Tem uma alteração de plano agendada para
                                    <strong>{{ subscription.scheduled_plan?.name }}</strong>
                                    em {{ formatDate(subscription.plan_change_scheduled_for) }}.
                                </span>
                                <Button
                                    @click="cancelScheduledChange"
                                    variant="outline"
                                    size="sm"
                                >
                                    Cancelar Alteração
                                </Button>
                            </AlertDescription>
                        </Alert>
                    </div>

                    <!-- Main Subscription Card -->
                    <Card>
                        <CardHeader>
                            <div class="flex items-center justify-between">
                                <div>
                                    <CardTitle class="flex items-center gap-2">
                                        <CreditCard class="w-5 h-5" />
                                        {{ subscription.plan.name }}
                                    </CardTitle>
                                    <CardDescription>{{ subscription.plan.description }}</CardDescription>
                                </div>
                                <Badge :variant="getStatusBadge(subscription.status).variant">
                                    <component :is="getStatusBadge(subscription.status).icon" class="w-4 h-4 mr-1" />
                                    {{ getStatusBadge(subscription.status).text }}
                                </Badge>
                            </div>
                        </CardHeader>

                        <CardContent class="space-y-6">
                            <!-- Billing Info -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">Valor</p>
                                    <p class="text-2xl font-bold">
                                        {{ formatPrice(subscription.plan.price) }}
                                        <span class="text-sm font-normal text-gray-600">
                                            / {{ subscription.plan.billing_period === 'yearly' ? 'ano' : 'mês' }}
                                        </span>
                                    </p>
                                </div>

                                <div v-if="subscription.next_billing_date">
                                    <p class="text-sm text-gray-600 dark:text-gray-400">Próxima Faturação</p>
                                    <p class="text-lg font-semibold">
                                        {{ formatDate(subscription.next_billing_date) }}
                                    </p>
                                </div>

                                <div v-if="subscription.current_period_start">
                                    <p class="text-sm text-gray-600 dark:text-gray-400">Período Atual</p>
                                    <p class="text-sm">
                                        {{ formatDate(subscription.current_period_start) }} -
                                        {{ formatDate(subscription.current_period_end) }}
                                    </p>
                                </div>

                                <div v-if="subscription.prorated_amount">
                                    <p class="text-sm text-gray-600 dark:text-gray-400">Valor Pró-rata (último upgrade)</p>
                                    <p class="text-lg font-semibold text-blue-600">
                                        {{ formatPrice(subscription.prorated_amount) }}
                                    </p>
                                </div>
                            </div>
                        </CardContent>

                        <CardFooter class="flex gap-2">
                            <Button
                                @click="router.visit(route('subscriptions.plans'))"
                                variant="default"
                            >
                                Alterar Plano
                            </Button>
                            <Button
                                v-if="subscription.status !== 'canceled'"
                                @click="cancelSubscription(false)"
                                variant="outline"
                            >
                                Cancelar Subscrição
                            </Button>
                        </CardFooter>
                    </Card>

                    <!-- Usage Summary -->
                    <Card v-if="usageSummary && usageSummary.length > 0">
                        <CardHeader>
                            <CardTitle>Utilização</CardTitle>
                            <CardDescription>Acompanhe o uso dos recursos do seu plano</CardDescription>
                        </CardHeader>
                        <CardContent class="space-y-6">
                            <div
                                v-for="usage in usageSummary"
                                :key="usage.feature"
                                class="space-y-2"
                            >
                                <div class="flex items-center justify-between">
                                    <span class="text-sm font-medium">{{ getFeatureName(usage.feature) }}</span>
                                    <span class="text-sm text-gray-600 dark:text-gray-400">
                                        {{ usage.used }} / {{ usage.limit === null ? '∞' : usage.limit }}
                                    </span>
                                </div>
                                <Progress
                                    :value="usage.percentage || 0"
                                    :class="[
                                        usage.has_reached_limit ? 'bg-red-200' : 'bg-gray-200',
                                        usage.percentage > 80 ? '[&>div]:bg-orange-500' : '[&>div]:bg-blue-500'
                                    ]"
                                />
                                <p
                                    v-if="usage.has_reached_limit"
                                    class="text-xs text-red-600"
                                >
                                    Limite atingido! Considere fazer upgrade.
                                </p>
                                <p
                                    v-else-if="usage.percentage > 80"
                                    class="text-xs text-orange-600"
                                >
                                    Perto do limite ({{ usage.percentage }}%)
                                </p>
                            </div>
                        </CardContent>
                    </Card>
                </template>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
