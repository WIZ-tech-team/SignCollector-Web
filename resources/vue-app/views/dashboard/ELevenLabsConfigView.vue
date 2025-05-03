<template>
    <div class="flex flex-col items-start justify-start gap-8 w-full">
        <!-- API Key Config -->
        <CollabsableCard title="API Key Management">
            <div class="flex flex-col gap-4 w-full">
                <div class="input-group w-full">
                    <label for="elevellabs_api_key_input" class="label">
                        API Key
                    </label>
                    <input type="text" v-model="ApiKeyModel" :class="['dashboard-input w-full',
                        { 'text-gray-500': !updatable }]" :disabled="!updatable" />
                    <span :class="[
                        { 'hidden': ApiKeyModel.length },
                        { 'error-message': !ApiKeyModel.length },]">
                        This is a required field.
                    </span>
                </div>
                <button type="button" v-if="!updatable" @click.prevent="updatable = !updatable" class="text-warning bg-light-warning px-4 py-2 rounded-md hover:bg-active-warning 
                    hover:text-light-warning w-[10rem] self-end">
                    Edit
                </button>
                <button type="button" v-else @click.prevent="submitChangeKey" class="bg-primary text-light-primary px-4 py-2 rounded-md hover:bg-active-primary 
                    w-[10rem] self-end disabled:bg-light-primary disabled:text-primary disabled:hover:bg-light-primary"
                    :disabled="ApiKeyModel.length == 0">
                    Update Key
                </button>
            </div>
        </CollabsableCard>

        <!-- Voice Settings Customization Allow Config -->
        <CollabsableCard title="Voice Settings Customization">
            <div class="flex flex-col sm:flex-row items-start justify-start gap-4 sm:gap-8 sm:justify-between">
                <div class="flex flex-col items-start justify-start gap-2 w-full">
                    <div class="font-semibold text-md text-gray-600">
                        Available Choices
                    </div>

                    <div class="flex flex-wrap items-center justify-start gap-4 w-full">
                        <button v-for="choice in voiceSettingsChoices.filter(c => !c.allowed)"
                            @click.prevent="choice.allowed = true"
                            class="px-2 py-1 text-light-brand bg-brand hover:bg-active-brand">
                            {{ choice.name }}
                        </button>
                    </div>
                </div>
                <div class="flex flex-col items-start justify-start gap-2 w-full">
                    <div class="font-semibold text-md text-gray-600">
                        Allowed Settings for User Customization
                    </div>

                    <div class="flex flex-wrap items-center justify-start gap-4 w-full">
                        <span v-for="choice in voiceSettingsChoices.filter(c => c.allowed)"
                            class="px-2 py-1 text-brand bg-light-brand flex items-center gap-2">
                            <span>{{ choice.name }}</span>
                            <XMarkIcon @click.prevent="choice.allowed = false"
                                class="w-5 h-5 text-brand cursor-pointer"></XMarkIcon>
                        </span>
                    </div>
                </div>
            </div>
        </CollabsableCard>

        <CollabsableCard title="App Available Voices">
            <div class="flex flex-col items-start justify-start gap-2 w-full">
                <div class="font-semibold text-md text-gray-600">
                    Choose Which Voices is Available for Mobile App Users
                </div>

                <div class="flex flex-col items-start justify-start gap-4 w-full">
                    <div class="flex flex-wrap items-center justify-start gap-8 w-full">
                        <div v-for="voice in voicesList"
                            class="flex gap-2 items-center justify-start border border-1 rounded-sm px-2 py-1">
                            <input :id="`${voice.name}_checkbox_input`" type="checkbox" :value="voice.name"
                                class="dashboard-input w-4 h-4 active:text-primary" :checked="voice.available" />
                            <label :for="`${voice.name}_checkbox_input`" class="text-md">
                                {{ voice.name }}
                            </label>
                        </div>
                    </div>
                    <button type="button" @click.prevent="submitVoicesList" class="px-4 py-2 bg-primary hover:bg-active-primary text-light-primary rounded-md self-end">
                        Submit Voices List
                    </button>
                </div>
            </div>
        </CollabsableCard>

        <!-- Payment Threshold Settings -->
        <CollabsableCard title="Payment Settings">
            <div class="flex flex-col gap-4 w-full">
                <div class="input-group w-full">
                    <label for="elevellabs_api_key_input" class="label">
                        Payment Threshold ($)
                    </label>
                    <input type="number" v-model="pthModel" :class="['dashboard-input w-full',
                        { 'text-gray-500': !pthUpdatable }]" :disabled="!pthUpdatable" />
                    <span :class="[
                        { 'hidden': pthModel },
                        { 'error-message': !pthModel },]">
                        This is a required field.
                    </span>
                </div>
                <button type="button" v-if="!pthUpdatable" @click.prevent="pthUpdatable = !pthUpdatable" class="text-warning bg-light-warning px-4 py-2 rounded-md hover:bg-active-warning 
                    hover:text-light-warning w-[10rem] self-end">
                    Edit
                </button>
                <button type="button" v-else @click.prevent="submitChangePth" class="bg-primary text-light-primary px-4 py-2 rounded-md hover:bg-active-primary 
                    w-[10rem] self-end disabled:bg-light-primary disabled:text-primary disabled:hover:bg-light-primary"
                    :disabled="!pthModel">
                    Update Threshold
                </button>
            </div>
        </CollabsableCard>
    </div>
</template>

<script setup lang="ts">
import CollabsableCard from '@/components/cards/CollabsableCard.vue';
import { MSwal, QSwal } from '@/core/plugins/SweetAlerts2';
import { XMarkIcon } from '@heroicons/vue/24/solid';
import { onBeforeMount, ref } from 'vue';

// Lifecycle hooks
onBeforeMount(() => {
    ApiKeyModel.value = ApiKey.value;
});

// Start :: API Key Config
const ApiKey = ref('https://elevenlabs.com/api_key/example/endpoint');
const ApiKeyModel = ref('');
const updatable = ref(false);

const submitChangeKey = () => {
    if (ApiKeyModel.value.length) {
        QSwal.fire('Warning', 'The mobile app API key will be changed !', 'question')
            .then(result => {
                if (result.isConfirmed) {
                    ApiKey.value = ApiKeyModel.value;
                    MSwal.fire('Success', 'API Key changed successfully.', 'success');
                    updatable.value = false;
                }
            })
    } else {
        MSwal.fire('Sorry', 'API Key must have a value.', 'error');
    }
}
// End :: API Key Config

// Start :: Voice Settings Config
const voiceSettingsChoices = ref([
    {
        name: 'Stability',
        allowed: false
    },
    {
        name: 'Speed',
        allowed: true
    },
    {
        name: 'Style',
        allowed: false
    },
    {
        name: 'Similarity Boost',
        allowed: false
    }
]);
// End :: Voice Settings Config

// Start :: Mobile Available Voices
const voicesList = ref([
    {
        name: 'Rachel',
        available: true
    },
    {
        name: 'Bella',
        available: false
    },
    {
        name: 'Liam',
        available: true
    },
    {
        name: 'Sophia',
        available: true
    },
    {
        name: 'Ethan',
        available: false
    }
]);

const submitVoicesList = () => {
    QSwal.fire('Warning', 'The available voices list for mobile app users will be changed !', 'question')
        .then(result => {
            if (result.isConfirmed) {
                MSwal.fire('Success', 'The mobile app avilable voices list updated successfullt.', 'success');
            }
        })
}
// End :: Mobile Available Voices

// Start :: Payment Threshold Config
const paymentThreshold = ref(10);
const pthModel = ref(10);
const pthUpdatable = ref(false);

const submitChangePth = () => {
    if (pthModel.value) {
        QSwal.fire('Warning', 'The payment threshold will be changed !', 'question')
            .then(result => {
                if (result.isConfirmed) {
                    paymentThreshold.value = pthModel.value;
                    MSwal.fire('Success', 'Payment Threshold changed successfully.', 'success');
                    pthUpdatable.value = false;
                }
            })
    } else {
        MSwal.fire('Sorry', 'Payment Threshold must have a value.', 'error');
    }
}
// End :: Payment Threshold Config

</script>
