<script setup>
import { ref } from 'vue';
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';
import NavLink from '@/Components/NavLink.vue';
import ResponsiveNavLink from '@/Components/ResponsiveNavLink.vue';
import { Link } from '@inertiajs/vue3';

const showingNavigationDropdown = ref(false);
</script>

<template>
    <div>
        <div class="min-h-screen bg-gray-100 dark:bg-gray-900">
            <nav
                class="border-b border-gray-100 bg-white dark:border-gray-700 dark:bg-gray-800"
            >
                <!-- Primary Navigation Menu -->
                <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                    <div class="flex h-16 justify-between">
                        <div class="flex">
                            <!-- Logo -->
                            <div class="flex shrink-0 items-center">
                                <Link :href="route('dashboard')">
                                    <ApplicationLogo
                                        class="block h-9 w-auto fill-current text-gray-800 dark:text-gray-200"
                                    />
                                </Link>
                            </div>

                            <!-- Navigation Links -->
                            <div
                                class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex"
                            >
                                <NavLink
                                    :href="route('dashboard')"
                                    :active="route().current('dashboard')"
                                >
                                    Dashboard
                                </NavLink>

                                <!-- Dropdown for Comercial -->
                                <Dropdown align="left" width="48">
                                    <template #trigger>
                                        <button class="inline-flex items-center px-1 border-b-2 border-transparent text-sm font-medium leading-5 text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 hover:border-gray-300 dark:hover:border-gray-700 focus:outline-none focus:text-gray-700 dark:focus:text-gray-300 focus:border-gray-300 dark:focus:border-gray-700 transition duration-150 ease-in-out h-16">
                                            Comercial
                                            <svg class="ml-1 h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                            </svg>
                                        </button>
                                    </template>
                                    <template #content>
                                        <DropdownLink :href="route('entities.index') + '?type=client'">Clientes</DropdownLink>
                                        <DropdownLink :href="route('entities.index') + '?type=supplier'">Fornecedores</DropdownLink>
                                        <DropdownLink :href="route('contacts.index')">Contactos</DropdownLink>
                                        <DropdownLink :href="route('proposals.index')">Propostas</DropdownLink>
                                        <DropdownLink :href="route('orders.index')">Encomendas</DropdownLink>
                                        <DropdownLink :href="route('supplier-orders.index')">Enc. Fornecedores</DropdownLink>
                                        <DropdownLink :href="route('work-orders.index')">Ordens Trabalho</DropdownLink>
                                    </template>
                                </Dropdown>

                                <!-- Dropdown for Financeiro -->
                                <Dropdown align="left" width="48">
                                    <template #trigger>
                                        <button class="inline-flex items-center px-1 border-b-2 border-transparent text-sm font-medium leading-5 text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 hover:border-gray-300 dark:hover:border-gray-700 focus:outline-none focus:text-gray-700 dark:focus:text-gray-300 focus:border-gray-300 dark:focus:border-gray-700 transition duration-150 ease-in-out h-16">
                                            Financeiro
                                            <svg class="ml-1 h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                            </svg>
                                        </button>
                                    </template>
                                    <template #content>
                                        <DropdownLink :href="route('bank-accounts.index')">Contas Bancárias</DropdownLink>
                                        <DropdownLink :href="route('client-accounts.index')">Conta Corrente</DropdownLink>
                                        <DropdownLink :href="route('supplier-invoices.index')">Faturas Fornecedores</DropdownLink>
                                    </template>
                                </Dropdown>

                                <NavLink
                                    :href="route('calendar.index')"
                                    :active="route().current('calendar.*')"
                                >
                                    Calendário
                                </NavLink>

                                <NavLink
                                    :href="route('settings.articles.index')"
                                    :active="route().current('settings.articles.*')"
                                >
                                    Artigos
                                </NavLink>

                                <NavLink
                                    :href="route('documents.index')"
                                    :active="route().current('documents.*')"
                                >
                                    Documentos
                                </NavLink>

                                <!-- Dropdown for Subscrições -->
                                <Dropdown align="left" width="48">
                                    <template #trigger>
                                        <button class="inline-flex items-center px-1 border-b-2 border-transparent text-sm font-medium leading-5 text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 hover:border-gray-300 dark:hover:border-gray-700 focus:outline-none focus:text-gray-700 dark:focus:text-gray-300 focus:border-gray-300 dark:focus:border-gray-700 transition duration-150 ease-in-out h-16">
                                            Subscrições
                                            <svg class="ml-1 h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                            </svg>
                                        </button>
                                    </template>
                                    <template #content>
                                        <DropdownLink :href="route('subscriptions.plans')">Ver Planos</DropdownLink>
                                        <DropdownLink :href="route('subscriptions.index')">Minha Subscrição</DropdownLink>
                                        <DropdownLink :href="route('subscriptions.dashboard')">Dashboard</DropdownLink>
                                        <DropdownLink :href="route('subscriptions.history')">Histórico</DropdownLink>
                                    </template>
                                </Dropdown>

                                <!-- Dropdown for Sistema -->
                                <Dropdown align="right" width="48">
                                    <template #trigger>
                                        <button class="inline-flex items-center px-1 border-b-2 border-transparent text-sm font-medium leading-5 text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 hover:border-gray-300 dark:hover:border-gray-700 focus:outline-none focus:text-gray-700 dark:focus:text-gray-300 focus:border-gray-300 dark:focus:border-gray-700 transition duration-150 ease-in-out h-16">
                                            Sistema
                                            <svg class="ml-1 h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                            </svg>
                                        </button>
                                    </template>
                                    <template #content>
                                        <DropdownLink :href="route('users.index')">Utilizadores</DropdownLink>
                                        <DropdownLink :href="route('roles.index')">Roles</DropdownLink>
                                        <DropdownLink :href="route('activity-log.index')">Logs</DropdownLink>
                                    </template>
                                </Dropdown>

                                <!-- Dropdown for Configurações -->
                                <Dropdown align="right" width="48">
                                    <template #trigger>
                                        <button class="inline-flex items-center px-1 border-b-2 border-transparent text-sm font-medium leading-5 text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 hover:border-gray-300 dark:hover:border-gray-700 focus:outline-none focus:text-gray-700 dark:focus:text-gray-300 focus:border-gray-300 dark:focus:border-gray-700 transition duration-150 ease-in-out h-16">
                                            Configurações
                                            <svg class="ml-1 h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                            </svg>
                                        </button>
                                    </template>
                                    <template #content>
                                        <DropdownLink :href="route('settings.companies.index')">Empresa</DropdownLink>
                                        <DropdownLink :href="route('settings.countries.index')">Países</DropdownLink>
                                        <DropdownLink :href="route('settings.contact-functions.index')">Funções Contacto</DropdownLink>
                                        <DropdownLink :href="route('settings.calendar-types.index')">Tipos Calendário</DropdownLink>
                                        <DropdownLink :href="route('settings.calendar-actions.index')">Ações Calendário</DropdownLink>
                                        <DropdownLink :href="route('settings.vat-rates.index')">Taxas IVA</DropdownLink>
                                    </template>
                                </Dropdown>
                            </div>
                        </div>

                        <div class="hidden sm:ms-6 sm:flex sm:items-center">
                            <!-- Company Selector -->
                            <div class="relative ms-3" v-if="$page.props.tenant?.companies && $page.props.tenant.companies.length > 0">
                                <Dropdown align="right" width="48">
                                    <template #trigger>
                                        <span class="inline-flex rounded-md">
                                            <button
                                                type="button"
                                                class="inline-flex items-center rounded-md border border-gray-300 bg-white px-3 py-2 text-sm font-medium leading-4 text-gray-700 transition duration-150 ease-in-out hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-gray-600"
                                            >
                                                <svg class="-ms-1 me-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 21h19.5m-18-18v18m10.5-18v18m6-13.5V21M6.75 6.75h.75m-.75 3h.75m-.75 3h.75m3-6h.75m-.75 3h.75m-.75 3h.75M6.75 21v-3.375c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21M3 3h12m-.75 4.5H21m-3.75 3.75h.008v.008h-.008v-.008zm0 3h.008v.008h-.008v-.008zm0 3h.008v.008h-.008v-.008z" />
                                                </svg>
                                                {{ $page.props.tenant?.current_company?.name || 'Selecionar Empresa' }}

                                                <svg
                                                    class="-me-0.5 ms-2 h-4 w-4"
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
                                        </span>
                                    </template>

                                    <template #content>
                                        <div class="px-4 py-2 text-xs text-gray-500 dark:text-gray-400 font-semibold uppercase">
                                            Empresas Disponíveis
                                        </div>
                                        <DropdownLink
                                            v-for="company in ($page.props.tenant?.companies || [])"
                                            :key="company.id"
                                            :href="route('companies.switch', company.id)"
                                            method="post"
                                            as="button"
                                            :class="{'bg-gray-100 dark:bg-gray-700': company.id === $page.props.tenant?.current_company?.id}"
                                        >
                                            <div class="flex items-center">
                                                <svg v-if="company.id === $page.props.tenant?.current_company?.id" class="mr-2 h-4 w-4 text-green-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z" clip-rule="evenodd" />
                                                </svg>
                                                <span>{{ company.name }}</span>
                                            </div>
                                        </DropdownLink>
                                        <div class="border-t border-gray-100 dark:border-gray-600"></div>
                                        <DropdownLink :href="route('settings.companies.index')">
                                            <div class="flex items-center">
                                                <svg class="mr-2 h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.343 3.94c.09-.542.56-.94 1.11-.94h1.093c.55 0 1.02.398 1.11.94l.149.894c.07.424.384.764.78.93.398.164.855.142 1.205-.108l.737-.527a1.125 1.125 0 011.45.12l.773.774c.39.389.44 1.002.12 1.45l-.527.737c-.25.35-.272.806-.107 1.204.165.397.505.71.93.78l.893.15c.543.09.94.56.94 1.109v1.094c0 .55-.397 1.02-.94 1.11l-.893.149c-.425.07-.765.383-.93.78-.165.398-.143.854.107 1.204l.527.738c.32.447.269 1.06-.12 1.45l-.774.773a1.125 1.125 0 01-1.449.12l-.738-.527c-.35-.25-.806-.272-1.203-.107-.397.165-.71.505-.781.929l-.149.894c-.09.542-.56.94-1.11.94h-1.094c-.55 0-1.019-.398-1.11-.94l-.148-.894c-.071-.424-.384-.764-.781-.93-.398-.164-.854-.142-1.204.108l-.738.527c-.447.32-1.06.269-1.45-.12l-.773-.774a1.125 1.125 0 01-.12-1.45l.527-.737c.25-.35.273-.806.108-1.204-.165-.397-.505-.71-.93-.78l-.894-.15c-.542-.09-.94-.56-.94-1.109v-1.094c0-.55.398-1.02.94-1.11l.894-.149c.424-.07.765-.383.93-.78.165-.398.143-.854-.107-1.204l-.527-.738a1.125 1.125 0 01.12-1.45l.773-.773a1.125 1.125 0 011.45-.12l.737.527c.35.25.807.272 1.204.107.397-.165.71-.505.78-.929l.15-.894z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                </svg>
                                                Gerir Empresas
                                            </div>
                                        </DropdownLink>
                                    </template>
                                </Dropdown>
                            </div>

                            <!-- Settings Dropdown -->
                            <div class="relative ms-3">
                                <Dropdown align="right" width="48">
                                    <template #trigger>
                                        <span class="inline-flex rounded-md">
                                            <button
                                                type="button"
                                                class="inline-flex items-center rounded-md border border-transparent bg-white px-3 py-2 text-sm font-medium leading-4 text-gray-500 transition duration-150 ease-in-out hover:text-gray-700 focus:outline-none dark:bg-gray-800 dark:text-gray-400 dark:hover:text-gray-300"
                                            >
                                                {{ $page.props.auth?.user?.name || 'Utilizador' }}

                                                <svg
                                                    class="-me-0.5 ms-2 h-4 w-4"
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
                                        </span>
                                    </template>

                                    <template #content>
                                        <DropdownLink
                                            :href="route('profile.edit')"
                                        >
                                            Profile
                                        </DropdownLink>
                                        <DropdownLink
                                            :href="route('logout')"
                                            method="post"
                                            as="button"
                                        >
                                            Log Out
                                        </DropdownLink>
                                    </template>
                                </Dropdown>
                            </div>
                        </div>

                        <!-- Hamburger -->
                        <div class="-me-2 flex items-center sm:hidden">
                            <button
                                @click="
                                    showingNavigationDropdown =
                                        !showingNavigationDropdown
                                "
                                class="inline-flex items-center justify-center rounded-md p-2 text-gray-400 transition duration-150 ease-in-out hover:bg-gray-100 hover:text-gray-500 focus:bg-gray-100 focus:text-gray-500 focus:outline-none dark:text-gray-500 dark:hover:bg-gray-900 dark:hover:text-gray-400 dark:focus:bg-gray-900 dark:focus:text-gray-400"
                            >
                                <svg
                                    class="h-6 w-6"
                                    stroke="currentColor"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        :class="{
                                            hidden: showingNavigationDropdown,
                                            'inline-flex':
                                                !showingNavigationDropdown,
                                        }"
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M4 6h16M4 12h16M4 18h16"
                                    />
                                    <path
                                        :class="{
                                            hidden: !showingNavigationDropdown,
                                            'inline-flex':
                                                showingNavigationDropdown,
                                        }"
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12"
                                    />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Responsive Navigation Menu -->
                <div
                    :class="{
                        block: showingNavigationDropdown,
                        hidden: !showingNavigationDropdown,
                    }"
                    class="sm:hidden"
                >
                    <div class="space-y-1 pb-3 pt-2">
                        <ResponsiveNavLink
                            :href="route('dashboard')"
                            :active="route().current('dashboard')"
                        >
                            Dashboard
                        </ResponsiveNavLink>

                        <!-- Comercial -->
                        <div class="px-4 py-2 text-xs font-semibold text-gray-500 uppercase">Comercial</div>
                        <ResponsiveNavLink
                            :href="route('entities.index') + '?type=client'"
                            :active="route().current('entities.*')"
                        >
                            Clientes
                        </ResponsiveNavLink>
                        <ResponsiveNavLink
                            :href="route('entities.index') + '?type=supplier'"
                            :active="route().current('entities.*')"
                        >
                            Fornecedores
                        </ResponsiveNavLink>
                        <ResponsiveNavLink
                            :href="route('contacts.index')"
                            :active="route().current('contacts.*')"
                        >
                            Contactos
                        </ResponsiveNavLink>
                        <ResponsiveNavLink
                            :href="route('proposals.index')"
                            :active="route().current('proposals.*')"
                        >
                            Propostas
                        </ResponsiveNavLink>
                        <ResponsiveNavLink
                            :href="route('orders.index')"
                            :active="route().current('orders.*')"
                        >
                            Encomendas
                        </ResponsiveNavLink>
                        <ResponsiveNavLink
                            :href="route('supplier-orders.index')"
                            :active="route().current('supplier-orders.*')"
                        >
                            Enc. Fornecedores
                        </ResponsiveNavLink>
                        <ResponsiveNavLink
                            :href="route('work-orders.index')"
                            :active="route().current('work-orders.*')"
                        >
                            Ordens Trabalho
                        </ResponsiveNavLink>

                        <!-- Financeiro -->
                        <div class="px-4 py-2 text-xs font-semibold text-gray-500 uppercase">Financeiro</div>
                        <ResponsiveNavLink
                            :href="route('bank-accounts.index')"
                            :active="route().current('bank-accounts.*')"
                        >
                            Contas Bancárias
                        </ResponsiveNavLink>
                        <ResponsiveNavLink
                            :href="route('client-accounts.index')"
                            :active="route().current('client-accounts.*')"
                        >
                            Conta Corrente
                        </ResponsiveNavLink>
                        <ResponsiveNavLink
                            :href="route('supplier-invoices.index')"
                            :active="route().current('supplier-invoices.*')"
                        >
                            Faturas Fornecedores
                        </ResponsiveNavLink>

                        <!-- Outros -->
                        <div class="px-4 py-2 text-xs font-semibold text-gray-500 uppercase">Outros</div>
                        <ResponsiveNavLink
                            :href="route('calendar.index')"
                            :active="route().current('calendar.*')"
                        >
                            Calendário
                        </ResponsiveNavLink>
                        <ResponsiveNavLink
                            :href="route('settings.articles.index')"
                            :active="route().current('settings.articles.*')"
                        >
                            Artigos
                        </ResponsiveNavLink>
                        <ResponsiveNavLink
                            :href="route('documents.index')"
                            :active="route().current('documents.*')"
                        >
                            Documentos
                        </ResponsiveNavLink>

                        <!-- Subscrições -->
                        <div class="px-4 py-2 text-xs font-semibold text-gray-500 uppercase">Subscrições</div>
                        <ResponsiveNavLink
                            :href="route('subscriptions.plans')"
                            :active="route().current('subscriptions.plans')"
                        >
                            Ver Planos
                        </ResponsiveNavLink>
                        <ResponsiveNavLink
                            :href="route('subscriptions.index')"
                            :active="route().current('subscriptions.index')"
                        >
                            Minha Subscrição
                        </ResponsiveNavLink>
                        <ResponsiveNavLink
                            :href="route('subscriptions.dashboard')"
                            :active="route().current('subscriptions.dashboard')"
                        >
                            Dashboard
                        </ResponsiveNavLink>
                        <ResponsiveNavLink
                            :href="route('subscriptions.history')"
                            :active="route().current('subscriptions.history')"
                        >
                            Histórico
                        </ResponsiveNavLink>

                        <!-- Sistema -->
                        <div class="px-4 py-2 text-xs font-semibold text-gray-500 uppercase">Sistema</div>
                        <ResponsiveNavLink
                            :href="route('users.index')"
                            :active="route().current('users.*')"
                        >
                            Utilizadores
                        </ResponsiveNavLink>
                        <ResponsiveNavLink
                            :href="route('roles.index')"
                            :active="route().current('roles.*')"
                        >
                            Roles
                        </ResponsiveNavLink>
                        <ResponsiveNavLink
                            :href="route('activity-log.index')"
                            :active="route().current('activity-log.*')"
                        >
                            Logs
                        </ResponsiveNavLink>

                        <!-- Configurações -->
                        <div class="px-4 py-2 text-xs font-semibold text-gray-500 uppercase">Configurações</div>
                        <ResponsiveNavLink
                            :href="route('settings.companies.index')"
                            :active="route().current('settings.companies.*')"
                        >
                            Empresa
                        </ResponsiveNavLink>
                        <ResponsiveNavLink
                            :href="route('settings.countries.index')"
                            :active="route().current('settings.countries.*')"
                        >
                            Países
                        </ResponsiveNavLink>
                        <ResponsiveNavLink
                            :href="route('settings.contact-functions.index')"
                            :active="route().current('settings.contact-functions.*')"
                        >
                            Funções Contacto
                        </ResponsiveNavLink>
                        <ResponsiveNavLink
                            :href="route('settings.calendar-types.index')"
                            :active="route().current('settings.calendar-types.*')"
                        >
                            Tipos Calendário
                        </ResponsiveNavLink>
                        <ResponsiveNavLink
                            :href="route('settings.calendar-actions.index')"
                            :active="route().current('settings.calendar-actions.*')"
                        >
                            Ações Calendário
                        </ResponsiveNavLink>
                        <ResponsiveNavLink
                            :href="route('settings.vat-rates.index')"
                            :active="route().current('settings.vat-rates.*')"
                        >
                            Taxas IVA
                        </ResponsiveNavLink>
                    </div>

                    <!-- Responsive Settings Options -->
                    <div
                        class="border-t border-gray-200 pb-1 pt-4 dark:border-gray-600"
                    >
                        <div class="px-4">
                            <div
                                class="text-base font-medium text-gray-800 dark:text-gray-200"
                            >
                                {{ $page.props.auth?.user?.name || 'Utilizador' }}
                            </div>
                            <div class="text-sm font-medium text-gray-500">
                                {{ $page.props.auth?.user?.email || '' }}
                            </div>
                        </div>

                        <div class="mt-3 space-y-1">
                            <ResponsiveNavLink :href="route('profile.edit')">
                                Profile
                            </ResponsiveNavLink>
                            <ResponsiveNavLink
                                :href="route('logout')"
                                method="post"
                                as="button"
                            >
                                Log Out
                            </ResponsiveNavLink>
                        </div>
                    </div>
                </div>
            </nav>

            <!-- Page Heading -->
            <header
                class="bg-white shadow dark:bg-gray-800"
                v-if="$slots.header"
            >
                <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
                    <slot name="header" />
                </div>
            </header>

            <!-- Page Content -->
            <main>
                <slot />
            </main>
        </div>
    </div>
</template>
