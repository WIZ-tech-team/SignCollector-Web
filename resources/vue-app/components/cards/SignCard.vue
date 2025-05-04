<template>
    <div class="flex flex-col md:flex-row items-center bg-white rounded-lg shadow-md p-4 gap-4">
        <div class="h-48 w-48 object-cover rounded-full">
            <img id='sign_image_preview' class="w-full h-full" :src="sign?.image_url"
                alt="photo" />
        </div>
        <div class="flex flex-wrap">
            <div class="flex flex-col gap-2">
                <div class="text-sm font-medium text-gray-900">الاسم: {{ sign.sign_name }}</div>
                <div class="text-sm font-medium text-gray-900">الشكل: {{ sign.sign_shape }}</div>
                <div class="text-sm font-medium text-gray-900">النوع: {{ sign.sign_type }}</div>
                <div class="text-sm font-medium text-gray-900">{{ `Lat: ${sign.latitude}` }}</div>
                <div class="text-sm font-medium text-gray-900">{{ `Long: ${sign.longitude}` }}</div>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import { DetailedSign } from '@/core/types/data/DetailedSign';
import { PropType, toRefs } from 'vue';


const props = defineProps({
    sign: {
        type: Object as PropType<DetailedSign>,
        required: true
    }
})

const { sign } = toRefs(props)

</script>