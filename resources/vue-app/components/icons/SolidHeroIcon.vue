<template>
    <component :is="iconComponent" :class="classes" />
</template>

<script setup lang="ts">
import * as solidIcons from '@heroicons/vue/24/solid';
import { computed, toRefs } from 'vue';

const props = defineProps({
  name: {
    type: String,
    required: true
  },
  classes: {
    type: String,
    required: false,
    default: 'h-6 w-6'
  }
})

const { name, classes } = toRefs(props);

const iconComponent = computed(() => {
  return solidIcons[name.value as keyof typeof solidIcons];
});

</script>