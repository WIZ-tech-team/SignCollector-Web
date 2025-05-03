<template>
    <div class="">

        <!-- Data Table: Voices -->
        <TableComponent v-if="!showVoiceForm" title="AI Voices Management"
            :data-paginated="(voicePaginated as PaginatedData<AIVoice>)" :columns="voicesTableColumns"
            :allow-actions="voicesTableAllowedActions"
            @on-next-page="voicesStore.fetchVoicesPaginated(`?page=${(voicePaginated?.current_page as number) + 1}`)"
            @on-previous-page="voicesStore.fetchVoicesPaginated(`?page=${(voicePaginated?.current_page as number) - 1}`)"
            @on-specific-page="(index) => voicesStore.fetchVoicesPaginated(`?page=${index}`)" @edit-clicked="onEditVoice"
            @delete-clicked="deleteVoice">

            <template #header_end>
                <div class="flex flex-col md:flex-row gap-4 items-start">
                    <button @click.prevent="createVoice = true" class="px-4 py-2 bg-brand hover:bg-active-brand
                    flex flex-row gap-2 items-center justify-center rounded-md">
                        <SolidHeroIcon name="PlusIcon" classes="text-light-brand w-5 h-5" />
                        <span class="font-semibold text-md text-light-brand">Create</span>
                    </button>
                </div>
            </template>

            <!-- Custom Cell: image -->
            <template v-for="(voice, index) in voicePaginated?.data" v-slot:[`row_${index}_image_slot_value`]>
                <div class="shrink-0">
                    <img id='voice_image_preview' class="h-16 w-16 object-cover rounded-full"
                        :src="voice?.image?.original_url" alt="image" />
                </div>
            </template>

            <!-- Custom Cell: availability -->
            <template v-for="(voice, index) in voicePaginated?.data" v-slot:[`row_${index}_availability_slot_value`]>
                <span class="px-2 py-1 text-xs font-semibold rounded-full" :class="{
                    'bg-light-success text-success': voice.is_available,
                    'bg-danger text-light-danger': !voice.is_available
                }">
                    {{ voice.is_available ? 'available' : 'not-available' }}
                </span>
            </template>

        </TableComponent>

        <!-- Form: Voice -->
        <VoiceForm v-if="showVoiceForm" :voiceToEdit="voiceToEdit" @hideForm="onHideVoiceEditForm"></VoiceForm>

    </div>
</template>

<script setup lang="ts">
import { computed, onBeforeMount, ref } from "vue";
import TableComponent from "@/components/table/TableComponent.vue";
import { PaginatedData } from "@/core/types/data/PaginatedDataInterface";
import { TableColumn } from "@/core/types/elements/Table";
import SolidHeroIcon from "@/components/icons/SolidHeroIcon.vue";
import VoiceForm from "./forms/VoiceForm.vue";
import { AIVoice } from "@/core/types/data/elevenlabs/AIVoices";
import { useVoicesStore } from "@/store/stores/elevenlabs/voicesStore";

// Lifecycle hooks
onBeforeMount(async () => {
    await voicesStore.fetchVoicesPaginated();
});

// Stores
const voicesStore = useVoicesStore();

const voiceToEdit = ref<AIVoice | null>(null);
const createVoice = ref<boolean>(false);

const voicesTableColumns = ref<TableColumn[]>([
    {
        title: 'ID',
        key: 'id'
    },
    {
        title: 'Image',
        key: 'image_slot',
        isSlot: true
    },
    {
        title: 'Name',
        key: 'name',
        valueClasses: "text-sm font-medium text-gray-900"
    },
    {
        title: 'ElevenLabs Key',
        key: 'elevenlabs_key'
    },
    {
        title: 'Availability',
        key: 'availability_slot',
        isSlot: true
    }
]);
const voicesTableAllowedActions = computed(() => {
    return {
        allow: true,
        delete: true,
        edit: true
    }
});

const voicePaginated = computed(() => {
    return voicesStore.voicesPaginated;
});

const showVoiceForm = computed(() => {
    if (voiceToEdit.value || createVoice.value) {
        return true;
    }
});

const onEditVoice = (voice: AIVoice) => {
    voiceToEdit.value = { ...voice };
}

const onHideVoiceEditForm = () => {
    voiceToEdit.value = null;
    createVoice.value = false;
}

const deleteVoice = (voice: AIVoice) => {
    voicesStore.removeVoice(voice.id, 'delete');
}

</script>