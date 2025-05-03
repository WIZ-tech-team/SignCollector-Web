<template>
    <div class="flex flex-col gap-2">
        <span class="block">
            {{ label }}
        </span>
        <div class="flex items-center space-x-6">
            <div class="shrink-0">
                <img :id='previewId' class="h-16 w-16 object-cover rounded-full" :src="imageUrl" alt="avatar" />
            </div>
            <label class="block">
                <input type="file" :onchange="onChange" class="block w-full text-sm text-slate-500
                    file:mr-4 file:py-2 file:px-4
                    file:rounded-full file:border-0
                    file:text-sm file:font-semibold
                    file:bg-violet-50 file:text-violet-700
                    hover:file:bg-violet-100" :accept="ALLOWED_IMAGE_TYPES.toString()" />
            </label>
            <slot />
        </div>
    </div>
</template>

<script setup lang="ts">
import { ALLOWED_IMAGE_TYPES } from "@/core/constants/allowedImageProperties";
import { PropType } from "vue";

// Props
const props = defineProps({
    label: {
        type: String,
        required: true
    },
    previewId: {
        type: String,
        required: true
    },
    imageUrl: {
        type: Object as PropType<string | undefined>,
        required: true
    }
});

// Emits
const emits = defineEmits(['onImageChange']);

const onChange = (event: Event) => {
    emits('onImageChange', event);
}

</script>