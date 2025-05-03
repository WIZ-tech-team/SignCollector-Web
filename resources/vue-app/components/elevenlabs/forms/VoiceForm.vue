<template>
    <Form ref="voiceManagementForm" @submit="onSubmit" :validation-schema="validationSchema"
        class="flex flex-col items-top justify-start gap-8 bg-white shadow-md rounded-md px-12 py-8">
        <div class="flex w-full items-middle justify-between">
            <div class="text-2xl font-bold text-gray-700">
                Manage AI Voices Data:
                <span v-if="isEditForm" class="font-normal text-lg">Update Voice</span>
                <span v-else class="font-normal text-lg">Create Voice</span>
            </div>
            <button type="button" @click.prevent="onReturnArrowClick" title="back"
                class="group cursor-pointer p-1 rounded-md hover:bg-light-primary">
                <ArrowLeftIcon class="w-6 h-6 text-primary"></ArrowLeftIcon>
            </button>
        </div>

        <div class="flex flex-col items-start justify-start gap-4">
            <ImageInput label="Image" preview-id="voice_image_preview_img" :image-url="voiceModel.image_url"
                :is-note-displayed="true" @onImageChange="loadFile">
                <span v-if="!voiceModel.image_file" class="error-message">
                    Note: image field required
                </span>
                <Field name="image_input" type="file" v-model="voiceModel.image_file" class="hidden"></Field>
            </ImageInput>

            <ColumnInputGroup name="name" :show-error="true" label="Name" container-classes="md:w-full">
                <Field id="name" name="name" type="text" v-model="voiceModel.name" class="dashboard-input w-full">
                </Field>
            </ColumnInputGroup>

            <ColumnInputGroup name="elevenlabs_key" :show-error="true" label="ElevenLabs Key" container-classes="md:w-full">
                <Field id="elevenlabs_key" name="elevenlabs_key" type="text" v-model="voiceModel.elevenlabs_key"
                    class="dashboard-input w-full"></Field>
            </ColumnInputGroup>

            <ColumnInputGroup name="is_available" label="Availability" :show_error="true" container-classes="md:w-full">
                <Field name="is_available" type="text" v-model="voiceModel.is_available" v-slot="{ field }">
                    <select v-bind="field" class="dashboard-input w-full">
                        <option :value="0" label="Not Available"></option>
                        <option :value="1" label="Available"></option>
                    </select>
                </Field>
            </ColumnInputGroup>
        </div>

        <div class="flex items-center justify-between flex-row-reverse gap-4 self-end">

            <!-- Submit button -->
            <LoadingButton type="submit" :is-loading="submitLoading"
                class="px-4 py-2 bg-primary hover:bg-active-primary text-light-primary rounded-md">
                <span v-if="isEditForm">Update Voice</span>
                <span v-else="isEditForm">Submit Create</span>
            </LoadingButton>

            <!-- Reset button -->
            <button v-if="isEditForm" type="button" @click.prevent="resetEditModel"
                class="px-4 py-2 text-primary hover:text-light-primary hover:bg-primary bg-light-primary rounded-md">
                <span>reset</span>
            </button>

            <!-- Reset button -->
            <button v-else type="reset"
                class="px-4 py-2 text-primary hover:text-light-primary hover:bg-primary bg-light-primary rounded-md">
                <span>reset</span>
            </button>

        </div>
    </Form>
</template>

<script setup lang="ts">
import ColumnInputGroup from "@/components/form/ColumnInputGroup.vue";
import ImageInput from "@/components/form/ImageInput.vue";
import LoadingButton from "@/components/form/LoadingButton.vue";
import { AIVoice } from "@/core/types/data/elevenlabs/AIVoices";
import { ImageOptions } from "@/core/types/elements/ImageInput";
import { useVoicesStore } from "@/store/stores/elevenlabs/voicesStore";
import { ArrowLeftIcon } from "@heroicons/vue/24/solid";
import { Form, Field, useForm } from "vee-validate";
import { computed, onBeforeMount, PropType, ref, toRefs, watch } from "vue";
import { number, object, string } from "yup";

// Props
const props = defineProps({
    voiceToEdit: {
        type: Object as PropType<AIVoice | null>,
        required: false
    }
});

// Emits
const emits = defineEmits(['hideForm']);

// Lifecycle hooks
onBeforeMount(() => {
    if (voiceToEdit?.value) {
        resetEditModel();
    }
});

// Types


// Html refs
const voiceManagementForm = ref(null);

// Stores
const voicesStore = useVoicesStore();

// Destructions
const { voiceToEdit } = toRefs(props);

// Custom constants
const voiceModel = ref<AIVoice & ImageOptions>({
    id: 0,
    name: "",
    is_available: false,
    elevenlabs_key: ""
});

const isEditForm = computed(() => {
    if (voiceToEdit?.value) {
        return true;
    } else {
        return false;
    }
});

const validationSchema = computed(() => {
    return object().shape({
        name: string().required(),
        is_available: number()
            .required()
            .oneOf([0,1], 'Invalid input.'),
        elevenlabs_key: string().required(),
    })
});

const submitLoading = ref<boolean>(false);

// For Image Handling
const { setFieldValue } = useForm({ validationSchema: validationSchema.value });

watch([() => voiceToEdit?.value], () => {
    if (voiceToEdit?.value) {
        resetEditModel()
    }
});

const onSubmit = () => {
    submitLoading.value = true;
    (async () => {
        if (isEditForm.value && voiceToEdit?.value) {
            await voicesStore.storeVoice(voiceModel.value, 'update');
        } else {
            await voicesStore.storeVoice(voiceModel.value, 'create');
        }
    })().finally(async () => {
        onReturnArrowClick();
        submitLoading.value = false;
    });
}

const resetEditModel = () => {
    if (isEditForm.value && voiceToEdit?.value) {
        voiceModel.value = {
            id: voiceToEdit.value.id,
            name: voiceToEdit.value.name,
            elevenlabs_key: voiceToEdit.value.elevenlabs_key,
            is_available: voiceToEdit.value.is_available,
            image_url: voiceToEdit.value?.image ? voiceToEdit.value.image.original_url : ""
        };
    }
}

const onReturnArrowClick = () => {
    if (voiceToEdit?.value) {
        voiceToEdit.value = null;
    }
    emits('hideForm');
}

const loadFile = function (event: Event) {

    const target = event.target as HTMLInputElement;
    if (target.files) {
        const file = target.files[0];
        voiceModel.value.image_file = file;

        const output = document.getElementById('voice_image_preview_img') as HTMLImageElement;
        if (output) {
            if (target.files[0]) {
                output.src = URL.createObjectURL(target.files[0]);
                setFieldValue('image_input', output.src);
                output.onload = function () {
                    URL.revokeObjectURL(output.src) // free memory
                }
            } else {
                setFieldValue('image_input', null);
                voiceModel.value.image_file = undefined;
                output.src = "";
            }
        }
    }

};
</script>