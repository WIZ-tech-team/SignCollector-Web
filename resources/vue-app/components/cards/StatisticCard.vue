<template>
    <div class="bg-white p-6 rounded-lg shadow-md" :class="additionalClasses">
      <!-- Card Header -->
      <div class="flex items-center justify-between">
        <h3 class="text-lg font-medium text-gray-500">{{ title }}</h3>
        <!-- Badge for Increase/Decrease -->
        <div v-if="indicator"
          class="flex items-center space-x-1 px-3 py-1 rounded-full text-sm"
          :class="{
            'bg-light-primary text-primary': isIncreased,
            'bg-danger text-light-danger': !isIncreased,
          }"
        >
          <ArrowUpIcon v-if="isIncreased" class="h-4 w-4" />
          <ArrowDownIcon v-else class="h-4 w-4" />
          <span>{{ indicator }}</span>
        </div>
      </div>
  
      <!-- Card Value -->
      <p class="mt-4 text-3xl font-bold text-primary">{{ value }}</p>
  
      <!-- Card Footer -->
      <p class="mt-2 text-sm text-gray-500">{{ description }}</p>
    </div>
</template>

<script setup lang="ts">
import { ArrowUpIcon, ArrowDownIcon } from "@heroicons/vue/24/solid";

const props = defineProps({
    isIncreased: {
        type: Boolean,
        required: true
    },
    title: {
        type: String,
        required: true
    },
    value: {
        type: String,
        required: true
    },
    description: {
        type: String,
        required: false,
        default: ''
    },
    indicator: {
        type: String,
        required: false,
        default: ''
    },
    additionalClasses: {
        type: String,
        required: false,
        default: ''
    }
})

</script>