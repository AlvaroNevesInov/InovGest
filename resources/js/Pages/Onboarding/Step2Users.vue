<template>
    <Head title="Convidar Utilizadores - Passo 2" />

    <WizardLayout
        :current-step="2"
        :total-steps="3"
        title="Convide Membros da Equipa"
        description="Adicione utilizadores e defina os seus papéis de acesso"
        :show-back-button="true"
        :show-skip-button="true"
        next-button-text="Continuar"
        @next="submitForm"
        @back="goBack"
        @skip="skipStep"
    >
        <form @submit.prevent="submitForm" class="space-y-6">
            <!-- Existing Users -->
            <div v-if="existingUsers.length > 0">
                <h4 class="text-sm font-medium text-gray-700 mb-3">
                    Utilizadores Atuais ({{ existingUsers.length }})
                </h4>
                <div class="space-y-2 mb-6">
                    <div
                        v-for="user in existingUsers"
                        :key="user.id"
                        class="flex items-center justify-between p-3 bg-gray-50 rounded-lg border border-gray-200"
                    >
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-blue-500 rounded-full flex items-center justify-center text-white font-semibold">
                                {{ user.name.charAt(0).toUpperCase() }}
                            </div>
                            <div>
                                <div class="text-sm font-medium text-gray-900">
                                    {{ user.name }}
                                </div>
                                <div class="text-xs text-gray-500">
                                    {{ user.email }}
                                </div>
                            </div>
                        </div>
                        <span class="px-3 py-1 text-xs font-medium bg-blue-100 text-blue-800 rounded-full">
                            {{ getRoleLabel(user.pivot?.role || 'owner') }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- New Users -->
            <div>
                <div class="flex items-center justify-between mb-3">
                    <h4 class="text-sm font-medium text-gray-700">
                        Convidar Novos Utilizadores
                    </h4>
                    <button
                        type="button"
                        @click="addUser"
                        class="text-sm text-blue-600 hover:text-blue-700 font-medium flex items-center gap-1"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                        Adicionar Utilizador
                    </button>
                </div>

                <div class="space-y-4">
                    <div
                        v-for="(user, index) in form.users"
                        :key="index"
                        class="p-4 border border-gray-200 rounded-lg bg-white"
                    >
                        <div class="flex items-start gap-4">
                            <div class="flex-1 space-y-4">
                                <!-- Name -->
                                <div>
                                    <label :for="`user_name_${index}`" class="block text-xs font-medium text-gray-700 mb-1">
                                        Nome
                                    </label>
                                    <input
                                        :id="`user_name_${index}`"
                                        v-model="user.name"
                                        type="text"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:ring-blue-500 focus:border-blue-500"
                                        placeholder="Nome completo"
                                    />
                                    <p v-if="form.errors[`users.${index}.name`]" class="mt-1 text-xs text-red-600">
                                        {{ form.errors[`users.${index}.name`] }}
                                    </p>
                                </div>

                                <!-- Email -->
                                <div>
                                    <label :for="`user_email_${index}`" class="block text-xs font-medium text-gray-700 mb-1">
                                        Email
                                    </label>
                                    <input
                                        :id="`user_email_${index}`"
                                        v-model="user.email"
                                        type="email"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:ring-blue-500 focus:border-blue-500"
                                        placeholder="email@exemplo.com"
                                    />
                                    <p v-if="form.errors[`users.${index}.email`]" class="mt-1 text-xs text-red-600">
                                        {{ form.errors[`users.${index}.email`] }}
                                    </p>
                                </div>

                                <!-- Role -->
                                <div>
                                    <label :for="`user_role_${index}`" class="block text-xs font-medium text-gray-700 mb-1">
                                        Papel
                                    </label>
                                    <select
                                        :id="`user_role_${index}`"
                                        v-model="user.role"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:ring-blue-500 focus:border-blue-500"
                                    >
                                        <option value="admin">Administrador</option>
                                        <option value="member">Membro</option>
                                    </select>
                                    <p v-if="form.errors[`users.${index}.role`]" class="mt-1 text-xs text-red-600">
                                        {{ form.errors[`users.${index}.role`] }}
                                    </p>
                                </div>
                            </div>

                            <!-- Remove Button -->
                            <button
                                type="button"
                                @click="removeUser(index)"
                                class="mt-6 text-gray-400 hover:text-red-600 transition-colors"
                                title="Remover utilizador"
                            >
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </button>
                        </div>
                    </div>

                    <!-- Empty State -->
                    <div v-if="form.users.length === 0" class="text-center py-8 border-2 border-dashed border-gray-300 rounded-lg">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                        </svg>
                        <p class="mt-2 text-sm text-gray-600">
                            Ainda não adicionou nenhum utilizador
                        </p>
                        <button
                            type="button"
                            @click="addUser"
                            class="mt-4 px-4 py-2 bg-blue-600 text-white rounded-md text-sm font-medium hover:bg-blue-700 transition-colors"
                        >
                            Adicionar Primeiro Utilizador
                        </button>
                    </div>
                </div>
            </div>

            <!-- Info Box -->
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                <div class="flex gap-3">
                    <svg class="w-5 h-5 text-blue-600 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                    </svg>
                    <div class="text-sm text-blue-800">
                        <p class="font-medium mb-1">Como funciona o convite?</p>
                        <ul class="list-disc list-inside space-y-1 text-xs">
                            <li>Os utilizadores receberão um email com instruções para aceder</li>
                            <li>Podem definir a sua própria palavra-passe no primeiro acesso</li>
                            <li>Pode alterar os papéis e permissões a qualquer momento</li>
                        </ul>
                    </div>
                </div>
            </div>
        </form>
    </WizardLayout>
</template>

<script setup>
import { Head, useForm, router } from '@inertiajs/vue3';
import WizardLayout from '@/Components/Onboarding/WizardLayout.vue';

const props = defineProps({
    tenant: Object,
    existingUsers: Array,
});

const form = useForm({
    users: [],
});

const addUser = () => {
    form.users.push({
        name: '',
        email: '',
        role: 'member',
    });
};

const removeUser = (index) => {
    form.users.splice(index, 1);
};

const getRoleLabel = (role) => {
    const labels = {
        owner: 'Proprietário',
        admin: 'Administrador',
        member: 'Membro',
    };
    return labels[role] || role;
};

const submitForm = () => {
    form.post(route('onboarding.step2.save'), {
        preserveState: true,
        preserveScroll: true,
    });
};

const goBack = () => {
    router.visit(route('onboarding.step1'));
};

const skipStep = () => {
    router.visit(route('onboarding.step3'));
};
</script>
