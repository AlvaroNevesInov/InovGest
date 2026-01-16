<template>
    <Head title="Configurar Branding - Passo 1" />

    <WizardLayout
        :current-step="1"
        :total-steps="3"
        title="Configure o Branding da sua Empresa"
        description="Personalize a aparência do sistema com o logotipo e cores da sua marca"
        :show-back-button="false"
        :show-skip-button="true"
        next-button-text="Continuar"
        @next="submitForm"
        @skip="skipStep"
    >
        <form @submit.prevent="submitForm" class="space-y-6">
            <!-- Company Name -->
            <div>
                <label for="company_name" class="block text-sm font-medium text-gray-700 mb-2">
                    Nome da Empresa
                </label>
                <input
                    id="company_name"
                    v-model="form.company_name"
                    type="text"
                    class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"
                    placeholder="Digite o nome da sua empresa"
                />
                <p v-if="form.errors.company_name" class="mt-1 text-sm text-red-600">
                    {{ form.errors.company_name }}
                </p>
            </div>

            <!-- Logo Upload -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Logotipo da Empresa
                </label>
                <div class="flex items-center gap-4">
                    <!-- Preview -->
                    <div class="flex-shrink-0">
                        <div v-if="logoPreview" class="w-24 h-24 border-2 border-gray-300 rounded-lg overflow-hidden">
                            <img :src="logoPreview" alt="Logo preview" class="w-full h-full object-contain" />
                        </div>
                        <div v-else class="w-24 h-24 border-2 border-dashed border-gray-300 rounded-lg flex items-center justify-center bg-gray-50">
                            <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                        </div>
                    </div>

                    <!-- Upload Button -->
                    <div class="flex-1">
                        <input
                            ref="logoInput"
                            type="file"
                            accept="image/*"
                            class="hidden"
                            @change="handleLogoChange"
                        />
                        <button
                            type="button"
                            @click="$refs.logoInput.click()"
                            class="px-4 py-2 bg-white border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors"
                        >
                            Carregar Logotipo
                        </button>
                        <p class="mt-2 text-xs text-gray-500">
                            PNG, JPG ou SVG até 2MB
                        </p>
                        <p v-if="form.errors.logo" class="mt-1 text-sm text-red-600">
                            {{ form.errors.logo }}
                        </p>
                    </div>
                </div>
            </div>

            <!-- Primary Color -->
            <div>
                <label for="primary_color" class="block text-sm font-medium text-gray-700 mb-2">
                    Cor Primária
                </label>
                <div class="flex items-center gap-4">
                    <input
                        id="primary_color"
                        v-model="form.primary_color"
                        type="color"
                        class="h-12 w-20 border border-gray-300 rounded cursor-pointer"
                    />
                    <input
                        v-model="form.primary_color"
                        type="text"
                        class="flex-1 px-4 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"
                        placeholder="#3b82f6"
                    />
                </div>
                <p class="mt-1 text-xs text-gray-500">
                    Esta cor será usada para botões e elementos principais
                </p>
                <p v-if="form.errors.primary_color" class="mt-1 text-sm text-red-600">
                    {{ form.errors.primary_color }}
                </p>
            </div>

            <!-- Secondary Color -->
            <div>
                <label for="secondary_color" class="block text-sm font-medium text-gray-700 mb-2">
                    Cor Secundária
                </label>
                <div class="flex items-center gap-4">
                    <input
                        id="secondary_color"
                        v-model="form.secondary_color"
                        type="color"
                        class="h-12 w-20 border border-gray-300 rounded cursor-pointer"
                    />
                    <input
                        v-model="form.secondary_color"
                        type="text"
                        class="flex-1 px-4 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"
                        placeholder="#1e40af"
                    />
                </div>
                <p class="mt-1 text-xs text-gray-500">
                    Esta cor será usada para destaques e acentos
                </p>
                <p v-if="form.errors.secondary_color" class="mt-1 text-sm text-red-600">
                    {{ form.errors.secondary_color }}
                </p>
            </div>

            <!-- Preview Section -->
            <div class="border-t pt-6">
                <h4 class="text-sm font-medium text-gray-700 mb-4">Pré-visualização</h4>
                <div class="border border-gray-200 rounded-lg p-4 bg-gray-50">
                    <div class="flex items-center gap-4 mb-4">
                        <div v-if="logoPreview" class="w-12 h-12">
                            <img :src="logoPreview" alt="Logo" class="w-full h-full object-contain" />
                        </div>
                        <span class="text-lg font-semibold text-gray-900">
                            {{ form.company_name || tenant.name }}
                        </span>
                    </div>
                    <div class="flex gap-2">
                        <button
                            type="button"
                            :style="{ backgroundColor: form.primary_color }"
                            class="px-4 py-2 text-white rounded-md text-sm font-medium"
                        >
                            Botão Primário
                        </button>
                        <button
                            type="button"
                            :style="{ backgroundColor: form.secondary_color }"
                            class="px-4 py-2 text-white rounded-md text-sm font-medium"
                        >
                            Botão Secundário
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </WizardLayout>
</template>

<script setup>
import { ref, computed } from 'vue';
import { Head, useForm, router } from '@inertiajs/vue3';
import WizardLayout from '@/Components/Onboarding/WizardLayout.vue';

const props = defineProps({
    tenant: Object,
    currentSettings: Object,
});

const form = useForm({
    company_name: props.currentSettings?.company_name || props.tenant.name,
    logo: null,
    primary_color: props.currentSettings?.primary_color || '#3b82f6',
    secondary_color: props.currentSettings?.secondary_color || '#1e40af',
});

const logoInput = ref(null);
const logoPreview = ref(props.currentSettings?.logo_path ? `/storage/${props.currentSettings.logo_path}` : null);

const handleLogoChange = (event) => {
    const file = event.target.files[0];
    if (file) {
        form.logo = file;
        logoPreview.value = URL.createObjectURL(file);
    }
};

const submitForm = () => {
    form.post(route('onboarding.step1.save'), {
        preserveState: true,
        preserveScroll: true,
    });
};

const skipStep = () => {
    router.visit(route('onboarding.step2'));
};
</script>
