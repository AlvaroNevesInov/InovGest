<script setup>

import { ref, computed } from 'vue';

import { router, useForm, usePage } from '@inertiajs/vue3';

import PrimaryButton from '@/Components/PrimaryButton.vue';

import SecondaryButton from '@/Components/SecondaryButton.vue';

import DangerButton from '@/Components/DangerButton.vue';

import InputError from '@/Components/InputError.vue';

import InputLabel from '@/Components/InputLabel.vue';

import TextInput from '@/Components/TextInput.vue';

import Modal from '@/Components/Modal.vue';



const page = usePage();

const user = computed(() => page.props.auth.user);



const enabling = ref(false);

const confirming = ref(false);

const disabling = ref(false);



const qrCode = ref(null);

const setupKey = ref(null);

const recoveryCodes = ref([]);



const confirmationForm = useForm({

    code: '',

});



const twoFactorEnabled = computed(() => {

    return !enabling.value && user.value?.two_factor_enabled;

});



const enableTwoFactorAuthentication = () => {

    enabling.value = true;



    router.post('/user/two-factor-authentication', {}, {

        preserveScroll: true,

        onSuccess: () => Promise.all([

            showQrCode(),

            showSetupKey(),

            showRecoveryCodes(),

        ]),

        onFinish: () => {

            enabling.value = false;

            confirming.value = true;

        },

    });

};



const showQrCode = () => {

    return axios.get('/user/two-factor-qr-code').then(response => {

        qrCode.value = response.data.svg;

    });

};



const showSetupKey = () => {

    return axios.get('/user/two-factor-secret-key').then(response => {

        setupKey.value = response.data.secretKey;

    });

};



const showRecoveryCodes = () => {

    return axios.get('/user/two-factor-recovery-codes').then(response => {

        recoveryCodes.value = response.data;

    });

};



const confirmTwoFactorAuthentication = () => {

    confirmationForm.post('/user/confirmed-two-factor-authentication', {

        errorBag: 'confirmTwoFactorAuthentication',

        preserveScroll: true,

        preserveState: true,

        onSuccess: () => {

            confirming.value = false;

            qrCode.value = null;

            setupKey.value = null;

        },

    });

};



const regenerateRecoveryCodes = () => {

    axios.post('/user/two-factor-recovery-codes').then(() => showRecoveryCodes());

};



const disableTwoFactorAuthentication = () => {

    disabling.value = true;



    router.delete('/user/two-factor-authentication', {

        preserveScroll: true,

        onSuccess: () => {

            disabling.value = false;

            confirming.value = false;

        },

    });

};

</script>



<template>

    <section>

        <header>

            <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">

                Autenticação de Dois Fatores

            </h2>



            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">

                Adicione segurança adicional à sua conta usando autenticação de dois fatores.

            </p>

        </header>



        <div class="mt-6 space-y-6">

            <div v-if="twoFactorEnabled && !confirming">

                <div class="flex items-center">

                    <svg class="w-5 h-5 text-green-500 mr-2" fill="currentColor" viewBox="0 0 20 20">

                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>

                    </svg>

                    <p class="text-sm text-gray-700 dark:text-gray-300">

                        Autenticação de dois fatores está habilitada.

                    </p>

                </div>



                <div class="mt-4">

                    <div class="max-w-xl text-sm text-gray-600 dark:text-gray-400">

                        <p>

                            Guarde estes códigos de recuperação num local seguro. Eles podem ser usados para recuperar acesso à sua conta se o dispositivo de autenticação de dois fatores for perdido.

                        </p>

                    </div>



                    <div v-if="recoveryCodes.length > 0" class="mt-4 grid gap-1 max-w-xl font-mono text-sm bg-gray-100 dark:bg-gray-800 px-4 py-4 rounded-lg">

                        <div v-for="code in recoveryCodes" :key="code" class="text-gray-900 dark:text-gray-100">

                            {{ code }}

                        </div>

                    </div>



                    <div class="mt-4 flex gap-3">

                        <SecondaryButton @click="regenerateRecoveryCodes">

                            Regenerar Códigos de Recuperação

                        </SecondaryButton>



                        <SecondaryButton @click="showRecoveryCodes" v-if="recoveryCodes.length === 0">

                            Mostrar Códigos de Recuperação

                        </SecondaryButton>

                    </div>

                </div>



                <div class="mt-6">

                    <DangerButton @click="disableTwoFactorAuthentication" :class="{ 'opacity-25': disabling }" :disabled="disabling">

                        Desabilitar 2FA

                    </DangerButton>

                </div>

            </div>



            <div v-else>

                <div v-if="!twoFactorEnabled && !confirming">

                    <div class="max-w-xl text-sm text-gray-600 dark:text-gray-400">

                        <p>

                            Quando a autenticação de dois fatores está habilitada, você será solicitado a fornecer um token seguro e aleatório durante a autenticação. Você pode recuperar este token do aplicativo Google Authenticator do seu telefone.

                        </p>

                    </div>



                    <div class="mt-6">

                        <PrimaryButton @click="enableTwoFactorAuthentication" :class="{ 'opacity-25': enabling }" :disabled="enabling">

                            Habilitar 2FA

                        </PrimaryButton>

                    </div>

                </div>



                <div v-if="confirming">

                    <div class="max-w-xl text-sm text-gray-600 dark:text-gray-400 mb-4">

                        <p>

                            Para finalizar a habilitação da autenticação de dois fatores, escaneie o seguinte código QR usando o aplicativo autenticador do seu telefone ou digite a chave de configuração e forneça o código OTP gerado.

                        </p>

                    </div>



                    <div v-if="qrCode" class="mt-4 p-4 inline-block bg-white dark:bg-gray-700 rounded">

                        <div v-html="qrCode"></div>

                    </div>



                    <div v-if="setupKey" class="mt-4">

                        <div class="max-w-xl text-sm text-gray-600 dark:text-gray-400">

                            <p class="font-semibold">

                                Chave de Configuração: <span class="font-mono">{{ setupKey }}</span>

                            </p>

                        </div>

                    </div>



                    <div class="mt-6">

                        <InputLabel for="code" value="Código de Verificação" />

                        <TextInput

                            id="code"

                            v-model="confirmationForm.code"

                            type="text"

                            class="mt-1 block w-full"

                            inputmode="numeric"

                            autofocus

                            autocomplete="one-time-code"

                        />

                        <InputError class="mt-2" :message="confirmationForm.errors.code" />

                    </div>



                    <div class="mt-6 flex gap-3">

                        <PrimaryButton

                            @click="confirmTwoFactorAuthentication"

                            :class="{ 'opacity-25': confirmationForm.processing }"

                            :disabled="confirmationForm.processing"

                        >

                            Confirmar

                        </PrimaryButton>



                        <SecondaryButton

                            @click="disableTwoFactorAuthentication"

                            :class="{ 'opacity-25': disabling }"

                            :disabled="disabling"

                        >

                            Cancelar

                        </SecondaryButton>

                    </div>

                </div>

            </div>

        </div>

    </section>

</template>
