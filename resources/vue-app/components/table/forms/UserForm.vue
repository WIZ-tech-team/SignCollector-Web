<template>
    <Form ref="userManagementForm" @submit="onSubmit" :validation-schema="validationSchema"
        class="flex flex-col items-top justify-start gap-8 bg-white shadow-md rounded-md px-12 py-8">
        <div class="flex w-full items-middle justify-between">
            <div class="text-2xl font-bold text-gray-700">
                Manage Users Data:
                <span v-if="isEditForm" class="font-normal text-lg">Update User</span>
                <span v-else class="font-normal text-lg">Create User</span>
            </div>
            <button type="button" @click.prevent="onReturnArrowClick" title="back"
                class="group cursor-pointer p-1 rounded-md hover:bg-light-primary">
                <ArrowLeftIcon class="w-6 h-6 text-primary"></ArrowLeftIcon>
            </button>
        </div>

        <div class="flex flex-col w-full gap-4">
            <div class="flex flex-col md:flex-row w-full gap-4">
                <ColumnInputGroup name="name_input" label="Name" :show-error="true">
                    <Field id="name_input" name="name_input" type="text" v-model="userModel.name"
                        class="dashboard-input w-full"></Field>
                </ColumnInputGroup>
                <ColumnInputGroup name="email_input" :show-error="true" label="Email">
                    <Field id="email_input" name="email_input" type="email" v-model="userModel.email"
                        class="dashboard-input w-full"></Field>
                </ColumnInputGroup>
            </div>

            <div class="flex flex-col md:flex-row w-full gap-4">
                <ColumnInputGroup name="phone_input" :show-error="true" label="Phone">
                    <Field id="phone_input" name="phone_input" type="text" v-model="userModel.phone"
                        class="dashboard-input w-full"></Field>
                </ColumnInputGroup>
                <!-- <ColumnInputGroup name="type_input" :show-error="true" label="Type">
                    <Field id="type_input" name="type_input" type="text" v-model="userModel.type"
                        class="dashboard-input w-full"></Field>
                </ColumnInputGroup> -->
                <ColumnInputGroup name="type_input" label="Type" :show_error="true">
                    <Field name="type_input" type="text" v-model="userModel.type" v-slot="{ field }">
                        <select v-bind="field" class="dashboard-input w-full" :class="{ 'placeholder': !userModel.type }">
                            <option disabled selected hidden value="" class="" label="select masjid">
                                select type
                            </option>
                            <option v-for="type in userTypes" :value="type" :label="type"></option>
                        </select>
                    </Field>
                </ColumnInputGroup>
            </div>

            <ImageInput label="Avatar" preview-id="avatar_preview_img" :image-url="userModel.avatar_url"
                :is-note-displayed="true" @onImageChange="loadFile">
                <span v-if="!userModel.avatar" class="error-message">
                    Note: avatar field required
                </span>
                <Field name="avatar_input" type="file" v-model="userModel.avatar" class="hidden"></Field>
            </ImageInput>

            <!-- <div class="flex flex-col gap-2">
                <div class="">Avatar</div>
                
                <div class="flex items-center space-x-6">
                    <div class="shrink-0">
                        <img id='avatar_preview_img' class="h-16 w-16 object-cover rounded-full"
                            :src="userModel.avatar_url" alt="avatar" />
                    </div>
                    <label class="block">
                        <input type="file" :onchange="loadFile" class="block w-full text-sm text-slate-500
                            file:mr-4 file:py-2 file:px-4
                            file:rounded-full file:border-0
                            file:text-sm file:font-semibold
                            file:bg-violet-50 file:text-violet-700
                            hover:file:bg-violet-100" :accept="ALLOWED_IMAGE_TYPES.toString()" />
                    </label>
                    <span v-if="!userModel.avatar" name="avatar_input" class="error-message">
                        
                    </span>
                </div>
            </div> -->
        </div>

        <div v-if="isEditForm" class="flex items-center justify-between w-full">
            <span class="text-lg font-semibold">
                Account Password
            </span>
            <button v-if="!editPassword" type="button" @click.prevent="editPassword = true"
                class="px-6 py-2 text-brand hover:text-light-brand hover:bg-brand bg-light-brand rounded-md">
                <span>Edit Password</span>
            </button>
            <button v-else type="button" @click.prevent="editPassword = false"
                class="group p-1 text-danger hover:text-light-danger hover:bg-danger bg-light-danger rounded-md">
                <SolidHeroIcon name="XMarkIcon" classes="w-5 h-5 text-danger group-hover:text-light-danger" />
            </button>
        </div>

        <div class="flex flex-col md:flex-row w-full gap-4">
            <template v-if="isEditForm && editPassword">
                <ColumnInputGroup name="old_password_input" label="Old Password" :show-error="true"
                    class="input-group w-full md:w-1/3">
                    <Field id="old_password_input" name="old_password_input" type="password"
                        v-model="userModel.old_password" class="dashboard-input w-full"></Field>
                </ColumnInputGroup>
            </template>
            <template v-if="!isEditForm || (isEditForm && editPassword)">
                <ColumnInputGroup name="password_input" label="Password" :show-error="true"
                    class="input-group w-full md:w-1/2" :class="{ 'md:w-1/3': (isEditForm && editPassword) }">
                    <Field id="password_input" name="password_input" type="password" v-model="userModel.password"
                        class="dashboard-input w-full"></Field>
                </ColumnInputGroup>
            </template>
            <template v-if="!isEditForm || (isEditForm && editPassword)">
                <ColumnInputGroup name="password_confirm_input" label="Old Password" :show-error="true"
                    class="input-group w-full md:w-1/2" :class="{ 'md:w-1/3': (isEditForm && editPassword) }">
                    <Field id="password_confirm_input" name="password_confirm_input" type="password"
                        v-model="userModel.password_confirmation" class="dashboard-input w-full"></Field>
                </ColumnInputGroup>
            </template>
        </div>

        <div class="flex items-center justify-between flex-row-reverse gap-4 self-end">

            <!-- Submit button -->
            <LoadingButton type="submit" :is-loading="submitLoading"
                class="px-4 py-2 bg-primary hover:bg-active-primary text-light-primary rounded-md">
                <span v-if="isEditForm">Update User</span>
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
import SolidHeroIcon from "@/components/icons/SolidHeroIcon.vue";
import { ALLOWED_IMAGE_TYPES } from "@/core/constants/allowedImageProperties";
import { UserInterface } from "@/core/types/data/UserInterface";
import { UpdatableUserData, useUsersStore } from "@/store/stores/usersStore";
import { ArrowLeftIcon } from "@heroicons/vue/24/solid";
import { Form, Field, ErrorMessage, useForm, useField } from "vee-validate";
import { computed, onBeforeMount, PropType, ref, toRefs, watch } from "vue";
import { object, string, ref as yupRef } from "yup";

// Props
const props = defineProps({
    userToEdit: {
        type: Object as PropType<UserInterface | null>,
        required: false
    }
});

// Emits
const emits = defineEmits(['hideForm']);

// Lifecycle hooks
onBeforeMount(() => {
    if (userToEdit?.value) {
        resetEditModel();
    }
});

// Types


// Html refs
const userManagementForm = ref(null);

// Stores
const usersStore = useUsersStore();

// Destructions
const { userToEdit } = toRefs(props);

// Custom constants
const userTypes = ref(['Admin', 'Mobile', 'User']);
const userModel = ref<UpdatableUserData>({
    id: 0,
    name: "",
    email: "",
    phone: "",
    type: "User"
});

const isEditForm = computed(() => {
    if (userToEdit?.value) {
        return true;
    } else {
        return false;
    }
});

const editPassword = ref<boolean>(false);

const generalYupObjectShape = ref({
    name_input: string().required(),
    email_input: string().email().required(),
    phone_input: string().required(),
    type_input: string().oneOf(userTypes.value).required()
});
const old_password_input = string()
    .required('Password confirmation is required');
const password_input = string()
    .required('Password is required')
    .min(8, 'Password must be at least 8 characters')
    .matches(/[A-Z]/, 'Password must contain at least one uppercase letter')
    .matches(/[a-z]/, 'Password must contain at least one lowercase letter')
    .matches(/[0-9]/, 'Password must contain at least one number')
    .matches(/[@$!%*?&#]/, 'Password must contain at least one special character');
const password_confirm_input = string()
    .required('Password confirmation is required')
    .oneOf([yupRef('password_input')], 'Passwords must match');

const validationSchema = computed(() => {
    if (isEditForm.value) {
        if (editPassword.value) {
            return object().shape({
                ...generalYupObjectShape.value,
                old_password_input: old_password_input,
                password_input: password_input,
                password_confirm_input: password_confirm_input
            })
        } else {
            return object().shape({
                ...generalYupObjectShape.value,
            })
        }

    } else {
        return object().shape({
            ...generalYupObjectShape.value,
            password_input: password_input,
            password_confirm_input: password_confirm_input
        });
    }
});

const submitLoading = ref<boolean>(false);

// For Image Handling
const { setFieldValue } = useForm({ validationSchema: validationSchema.value });
const { errorMessage: avatarErrorMsg } = useField('avatar_input');
// const avatarFile = ref<File | undefined>(undefined);


watch([() => userToEdit?.value], () => {
    if (userToEdit?.value) {
        resetEditModel()
    }
});

const onSubmit = () => {
    submitLoading.value = true;
    (async () => {
        if (isEditForm.value && userToEdit?.value) {
            await usersStore.storeUser(userModel.value, 'update');
        } else {
            // set new user id
            await usersStore.storeUser(userModel.value, 'create');
        }
    })().finally(async () => {
        onReturnArrowClick();
        submitLoading.value = false;
    });
}

const resetEditModel = () => {
    if (isEditForm.value && userToEdit?.value) {
        userModel.value = {
            id: userToEdit.value.id,
            name: userToEdit.value.name,
            email: userToEdit.value.email,
            phone: userToEdit.value.phone,
            type: userToEdit.value.type,
            avatar_url: userToEdit.value?.avatar ? userToEdit.value.avatar.original_url : ""
        };
    }
}

const onReturnArrowClick = () => {
    if (userToEdit?.value) {
        userToEdit.value = null;
    }
    emits('hideForm');
}

const loadFile = function (event: Event) {

    const target = event.target as HTMLInputElement;
    if (target.files) {
        const file = target.files[0];
        userModel.value.avatar = file;

        const output = document.getElementById('avatar_preview_img') as HTMLImageElement;
        if (output) {
            if (target.files[0]) {
                output.src = URL.createObjectURL(target.files[0]);
                setFieldValue('avatar_input', output.src);
                output.onload = function () {
                    URL.revokeObjectURL(output.src) // free memory
                }
            } else {
                setFieldValue('avatar_input', null);
                userModel.value.avatar = undefined;
                output.src = "";
            }
        }
    }

};
</script>