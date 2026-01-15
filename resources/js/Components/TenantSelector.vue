<script setup>
import { computed } from 'vue';
import { router, usePage } from '@inertiajs/vue3';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';

const page = usePage();

const currentTenant = computed(() => page.props.tenant?.current);
const tenants = computed(() => page.props.tenant?.tenants || []);

const switchTenant = (tenant) => {
    if (tenant.id === currentTenant.value?.id) {
        return;
    }

    router.post(route('tenants.switch', tenant.id), {}, {
        preserveState: false,
        preserveScroll: true,
    });
};

const createTenant = () => {
    router.visit(route('tenants.create'));
};
</script>

<template>
    <Dropdown v-if="tenants.length > 0" align="right" width="48">
        <template #trigger>
            <button
                type="button"
                class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 dark:text-gray-400 bg-white dark:bg-gray-800 hover:text-gray-700 dark:hover:text-gray-300 focus:outline-none transition ease-in-out duration-150"
            >
                <svg class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                </svg>
                <span>{{ currentTenant?.name || 'Selecionar Tenant' }}</span>
                <svg
                    class="ms-2 -me-0.5 h-4 w-4"
                    xmlns="http://www.w3.org/2000/svg"
                    viewBox="0 0 20 20"
                    fill="currentColor"
                >
                    <path
                        fill-rule="evenodd"
                        d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                        clip-rule="evenodd"
                    />
                </svg>
            </button>
        </template>

        <template #content>
            <div class="px-4 py-2 border-b border-gray-200 dark:border-gray-600">
                <div class="text-xs text-gray-500 dark:text-gray-400">Tenants</div>
            </div>

            <button
                v-for="tenant in tenants"
                :key="tenant.id"
                @click="switchTenant(tenant)"
                class="w-full text-left block px-4 py-2 text-sm leading-5 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800 focus:outline-none focus:bg-gray-100 dark:focus:bg-gray-800 transition duration-150 ease-in-out"
                :class="{
                    'bg-gray-50 dark:bg-gray-900': tenant.id === currentTenant?.id,
                    'font-semibold': tenant.id === currentTenant?.id,
                }"
            >
                <div class="flex items-center justify-between">
                    <span>{{ tenant.name }}</span>
                    <svg
                        v-if="tenant.id === currentTenant?.id"
                        class="h-5 w-5 text-green-500"
                        fill="currentColor"
                        viewBox="0 0 20 20"
                    >
                        <path
                            fill-rule="evenodd"
                            d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                            clip-rule="evenodd"
                        />
                    </svg>
                </div>
                <div v-if="tenant.pivot?.role" class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                    {{ tenant.pivot.role }}
                </div>
            </button>

            <div class="border-t border-gray-200 dark:border-gray-600">
                <DropdownLink :href="route('tenants.create')" as="button">
                    <svg class="h-4 w-4 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Criar Novo Tenant
                </DropdownLink>
            </div>
        </template>
    </Dropdown>
</template>
