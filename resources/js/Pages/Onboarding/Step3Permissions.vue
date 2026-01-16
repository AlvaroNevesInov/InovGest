<template>
    <Head title="Configurar Permissões - Passo 3" />

    <WizardLayout
        :current-step="3"
        :total-steps="3"
        title="Configure Permissões de Acesso"
        description="Defina as permissões padrão para administradores e membros"
        :show-back-button="true"
        :show-skip-button="true"
        next-button-text="Concluir Configuração"
        @next="submitForm"
        @back="goBack"
        @skip="skipStep"
    >
        <form @submit.prevent="submitForm" class="space-y-8">
            <!-- Admin Permissions -->
            <div>
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900">
                            Permissões de Administrador
                        </h3>
                        <p class="text-sm text-gray-600 mt-1">
                            Defina o que os administradores podem fazer
                        </p>
                    </div>
                    <button
                        type="button"
                        @click="toggleAllAdmin"
                        class="text-sm text-blue-600 hover:text-blue-700 font-medium"
                    >
                        {{ allAdminSelected ? 'Desmarcar Tudo' : 'Marcar Tudo' }}
                    </button>
                </div>

                <div class="space-y-4">
                    <div
                        v-for="(permissions, category) in availablePermissions"
                        :key="`admin-${category}`"
                        class="border border-gray-200 rounded-lg p-4 bg-white"
                    >
                        <h4 class="text-sm font-semibold text-gray-800 mb-3 capitalize">
                            {{ getCategoryLabel(category) }}
                        </h4>
                        <div class="space-y-2">
                            <label
                                v-for="(label, permission) in permissions"
                                :key="`admin-${permission}`"
                                class="flex items-center gap-3 p-2 rounded hover:bg-gray-50 cursor-pointer"
                            >
                                <input
                                    v-model="form.admin_permissions"
                                    type="checkbox"
                                    :value="permission"
                                    class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500"
                                />
                                <span class="text-sm text-gray-700">{{ label }}</span>
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Member Permissions -->
            <div>
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900">
                            Permissões de Membro
                        </h3>
                        <p class="text-sm text-gray-600 mt-1">
                            Defina o que os membros podem fazer
                        </p>
                    </div>
                    <button
                        type="button"
                        @click="toggleAllMember"
                        class="text-sm text-blue-600 hover:text-blue-700 font-medium"
                    >
                        {{ allMemberSelected ? 'Desmarcar Tudo' : 'Marcar Tudo' }}
                    </button>
                </div>

                <div class="space-y-4">
                    <div
                        v-for="(permissions, category) in availablePermissions"
                        :key="`member-${category}`"
                        class="border border-gray-200 rounded-lg p-4 bg-white"
                    >
                        <h4 class="text-sm font-semibold text-gray-800 mb-3 capitalize">
                            {{ getCategoryLabel(category) }}
                        </h4>
                        <div class="space-y-2">
                            <label
                                v-for="(label, permission) in permissions"
                                :key="`member-${permission}`"
                                class="flex items-center gap-3 p-2 rounded hover:bg-gray-50 cursor-pointer"
                            >
                                <input
                                    v-model="form.member_permissions"
                                    type="checkbox"
                                    :value="permission"
                                    class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500"
                                />
                                <span class="text-sm text-gray-700">{{ label }}</span>
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Info Box -->
            <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                <div class="flex gap-3">
                    <svg class="w-5 h-5 text-green-600 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                    </svg>
                    <div class="text-sm text-green-800">
                        <p class="font-medium mb-1">Dica</p>
                        <p class="text-xs">
                            Pode sempre ajustar estas permissões mais tarde nas configurações.
                            Comece com permissões básicas e expanda conforme necessário.
                        </p>
                    </div>
                </div>
            </div>
        </form>
    </WizardLayout>
</template>

<script setup>
import { computed } from 'vue';
import { Head, useForm, router } from '@inertiajs/vue3';
import WizardLayout from '@/Components/Onboarding/WizardLayout.vue';

const props = defineProps({
    tenant: Object,
    currentPermissions: Object,
    availablePermissions: Object,
});

const form = useForm({
    admin_permissions: props.currentPermissions?.default_admin_permissions || [
        'view_entities',
        'create_entities',
        'edit_entities',
        'delete_entities',
        'view_contacts',
        'create_contacts',
        'edit_contacts',
        'delete_contacts',
        'view_calendar',
        'manage_calendar',
        'view_articles',
        'manage_articles',
        'manage_users',
    ],
    member_permissions: props.currentPermissions?.default_member_permissions || [
        'view_entities',
        'create_entities',
        'view_contacts',
        'create_contacts',
        'view_calendar',
    ],
    custom_roles: [],
});

const getAllPermissions = () => {
    const allPerms = [];
    Object.values(props.availablePermissions).forEach((permissions) => {
        Object.keys(permissions).forEach((perm) => {
            allPerms.push(perm);
        });
    });
    return allPerms;
};

const allAdminSelected = computed(() => {
    const allPerms = getAllPermissions();
    return allPerms.every((perm) => form.admin_permissions.includes(perm));
});

const allMemberSelected = computed(() => {
    const allPerms = getAllPermissions();
    return allPerms.every((perm) => form.member_permissions.includes(perm));
});

const toggleAllAdmin = () => {
    if (allAdminSelected.value) {
        form.admin_permissions = [];
    } else {
        form.admin_permissions = getAllPermissions();
    }
};

const toggleAllMember = () => {
    if (allMemberSelected.value) {
        form.member_permissions = [];
    } else {
        form.member_permissions = getAllPermissions();
    }
};

const getCategoryLabel = (category) => {
    const labels = {
        entities: 'Entidades',
        contacts: 'Contactos',
        calendar: 'Calendário',
        articles: 'Artigos',
        financial: 'Financeiro',
        users: 'Utilizadores',
    };
    return labels[category] || category;
};

const submitForm = () => {
    form.post(route('onboarding.step3.save'), {
        preserveState: true,
        preserveScroll: true,
    });
};

const goBack = () => {
    router.visit(route('onboarding.step2'));
};

const skipStep = () => {
    router.visit(route('onboarding.complete'));
};
</script>
