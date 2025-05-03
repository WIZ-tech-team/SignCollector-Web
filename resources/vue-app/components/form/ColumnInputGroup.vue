<template>
    <div class="input-group w-full md:w-1/2" :class="containerClasses">
        <label for="email_input" class="text-lg font-semibold">{{ label }}</label>
        <div class="flex flex-col items-start justify-start gap-1">
            <slot />
            <ErrorMessage v-if="showError" :name="name" class="error-message" />
        </div>
    </div>
</template>

<script setup lang="ts">
import { ErrorMessage } from 'vee-validate';

const props = defineProps({
    label: {
        type: String,
        required: true
    },
    name: {
        type: String,
        required: true
    },
    showError: {
        type: Boolean,
        required: false,
        default: false
    },
    containerClasses: {
        type: String,
        required: false,
        default: ''
    }
})

const { label, name, showError } = props
</script>

<style scoped>
input:focus {
    background-color: blueviolet !important;
}
</style>