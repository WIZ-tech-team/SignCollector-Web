<template>
    <div class="">
        <!-- API Key Config -->
        <!-- <CollabsableCard title="AI Voice Configurations">
            <VoicesManagementComponent></VoicesManagementComponent>
        </CollabsableCard> -->
        <VoicesManagementComponent></VoicesManagementComponent>

        <!-- Voice Settings Customization Allow Config -->
        <!-- <CollabsableCard title="Voice Settings Customization">
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
        </CollabsableCard> -->

    </div>
</template>

<script setup lang="ts">
// import CollabsableCard from '@/components/cards/CollabsableCard.vue';
// import AgentsConfig from '@/components/elevenlabs/AgentsConfig.vue';
import VoicesManagementComponent from '@/components/elevenlabs/VoicesManagementComponent.vue';
import { MSwal, QSwal } from '@/core/plugins/SweetAlerts2';
// import { XMarkIcon } from '@heroicons/vue/24/solid';
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
