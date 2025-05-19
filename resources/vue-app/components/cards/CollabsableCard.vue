<template>
    <div
        :class="classes + ' ' + additionalClasses">
        <div class="flex items-center gap-8 justify-between w-full">
            <span class="font-bold text-xl text-gray-900">
                {{ title }}
            </span>
            <button type="button" @click="isCollapsed = !isCollapsed" :class="[
                'group rounded-md p-1', {
                    'bg-light-brand text-brand hover:bg-active-brand hover:text-light-brand': !isCollapsed,
                    'bg-brand text-light-brand hover:bg-active-brand': isCollapsed
                }]">
                <ChevronDoubleDownIcon v-if="isCollapsed" :class="[
                    'w-6 h-6', {
                        'text-brand group-hover:text-light-brand': !isCollapsed,
                        isCollapsed: 'text-light-brand'
                    }]">
                </ChevronDoubleDownIcon>
                <ChevronUpIcon v-else class="w-6 h-6 text-brand group-hover:text-light-brand"></ChevronUpIcon>
            </button>
        </div>
        <div :class="['w-full', { 'hidden': isCollapsed }]">
            <slot>aaa</slot>
        </div>
    </div>
</template>

<script setup lang="ts">
import { ChevronDoubleDownIcon, ChevronUpIcon } from "@heroicons/vue/24/solid";
import { ref } from "vue";

// Props
const props = defineProps({
    title: {
        type: String,
        required: true
    },
    classes: {
        type: String,
        required: false,
        default: "flex flex-col items-start justify-start gap-4 p-8 shadow-md bg-white rounded-md transition-all duration-75 w-full"
    },
    additionalClasses: {
        type: String,
        required: false,
        default: ""
    }
})

// Core constant
const isCollapsed = ref(true)

</script>