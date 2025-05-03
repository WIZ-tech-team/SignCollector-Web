<template>
    <div class="flex items-middle justify-center bg-transparent h-screen w-full py-8">
        <Form @submit="onSubmit" :validation-schema="schema"
            class="flex flex-col items-center justify-start gap-8 bg-white shadow-md rounded-md px-12 py-8 h-[450px]">
            <!-- Logo -->
            <div class="flex flex-col gap-2 items-center justify-center">
                <!-- <img class="h-12 w-auto" src="@/assets/images/" alt="Logo" /> -->
                 <span class="font-bold text-4xl text-active-brand">
                    حصر لوائح الطرق
                 </span>
                 <span class="font-bold text-2xl text-brand">
                    لوحة التحكم
                 </span>
            </div>
            <div class="text-2xl font-bold text-gray-700">
                مرحبا بك !
            </div>
            <div class="flex flex-col gap-4">
                <div class="input-group w-full">
                    <label for="email_input" class="text-lg font-semibold">الإيميل</label>
                    <div class="flex flex-col items-start justify-start gap-1">
                        <Field id="email_input" name="email_input" type="email" v-model="form.email"
                            class="input w-full"></Field>
                        <ErrorMessage name="email_input" class="error-message" />
                    </div>
                </div>
                <div class="input-group w-full">
                    <label for="password_input" class="text-lg font-semibold">كلمة السر</label>
                    <div class="flex flex-col items-start justify-start gap-1">
                        <Field id="password_input" name="password_input" type="password" v-model="form.password"
                            class="input w-full"></Field>
                        <ErrorMessage name="password_input" class="error-message" />
                    </div>
                </div>
            </div>

            <!-- Submit button -->
            <LoadingButton type="submit" :is-loading="submitLoading"
                classes="px-4 py-2 w-full bg-primary hover:bg-active-primary text-light-primary rounded-md disabled:bg-indigo-400">
                <span>دخول</span>
            </LoadingButton>
        </Form>
    </div>
</template>

<script setup lang="ts">
import { ref } from 'vue';
import { Field, ErrorMessage, useForm, Form } from 'vee-validate';
import * as yup from 'yup';
import { useAuthStore } from '@/store/stores/authStore';
import { useRouter } from 'vue-router';
import LoadingButton from '@/components/form/LoadingButton.vue';

// Routing
const router = useRouter();

// Stores
const authStore = useAuthStore();

// Custom constants
const submitLoading = ref<boolean>(false);

// Define validation schema with Yup
const schema = yup.object().shape({
    email_input: yup.string().email('Invalid email').required('Email is required'),
    password_input: yup
        .string()
        .min(8, 'Password must be at least 8 characters')
        .required('Password is required'),
});

// Initialize form with VeeValidate
const { handleSubmit } = useForm({
    validationSchema: schema,
});

// Form data
const form = ref({
    email: '',
    password: '',
});

// Submit handler
const onSubmit = async (): Promise<void> => {
    submitLoading.value = true;
    await authStore.login(form.value.email, form.value.password)
        .finally(async () => {
            if (authStore.isAuthenticated) {
                if (authStore.user?.type === 'Admin') {
                    await router.push("/dashboardLayout");
                } else {
                    await router.push("/auth/401");
                }
            } else {
                await router.push("/");
            }
            submitLoading.value = false;
        });
}
</script>

<style scoped>
/* Add custom styles if needed */
/* .input {

} */
</style>