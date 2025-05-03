<template>
    <Form @submit="onSubmit" :validation-schema="validationSchema"
        class="flex flex-col items-top justify-start gap-8">

        <div class="flex flex-col items-start justify-start gap-4">
            <ColumnInputGroup name="export_from" :show-error="true" label="From" container-classes="md:w-full">
                <Field id="export_from" name="export_from" type="date" :max="TODAY.toISOString().split('T')[0]" v-model="exportModel.from" class="dashboard-input w-full">
                </Field>
            </ColumnInputGroup>

            <ColumnInputGroup name="export_to" :show-error="true" label="To" container-classes="md:w-full">
                <Field id="export_to" name="export_to" type="date" :max="TODAY.toISOString().split('T')[0]" v-model="exportModel.to" class="dashboard-input w-full">
                </Field>
            </ColumnInputGroup>
        </div>

        <div class="flex items-center justify-between flex-row-reverse gap-4 self-end">

            <!-- Submit button -->
            <LoadingButton type="submit" :is-loading="submitLoading"
                class="px-4 py-2 bg-primary hover:bg-active-primary text-light-primary rounded-md">
                Export
            </LoadingButton>

            <!-- Reset button -->
            <button type="reset"
                class="px-4 py-2 text-primary hover:text-light-primary hover:bg-primary bg-light-primary rounded-md">
                <span>reset</span>
            </button>

        </div>
    </Form>
</template>

<script setup lang="ts">
import ColumnInputGroup from "@/components/form/ColumnInputGroup.vue";
import LoadingButton from "@/components/form/LoadingButton.vue";
import { Form, Field } from "vee-validate";
import { computed, onBeforeMount, ref, toRefs } from "vue";
import { date, object, ref as yupRef } from "yup";

// Props
const props = defineProps({
    submitLoading: {
        type: Boolean,
        required: false,
        default: false
    }
});
const { submitLoading } = toRefs(props);

// Emits
const emits = defineEmits(['submit']);

// Lifecycle hooks
onBeforeMount(() => {
});

// Types
export type ExportDates = {
    from:string;
    to:string;
}

// Custom constants
const exportModel = ref<ExportDates>({
    from: "",
    to: ""
});

const TODAY = ref(new Date());

const validationSchema = computed(() => {
    return object().shape({
        export_from: date().max(TODAY.value).required().label("Export 'from' date"),
        export_to: date().min(yupRef('export_from')).max(TODAY.value).required().label("Export 'to' date")
    })
});

// For Image Handling

const onSubmit = () => {
    emits("submit", exportModel.value);
}

</script>