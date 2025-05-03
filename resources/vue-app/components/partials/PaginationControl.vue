<template>
    <div class="flex flex-wrap items-center gap-2 justify-between w-full p-1">
        <span>
            {{ `Page ${currentPage} of ${lastPage}` }}
        </span>
        <nav aria-label="Page navigation example">
            <ul class="inline-flex -space-x-px text-sm">
                <li>
                    <button type="button" @click.prevent="emits('onPrevious')" class="nav-item rounded-s-lg"
                        :disabled="currentPage <= 1">
                        Previous
                    </button>
                </li>
                <li v-for="index in lastPage" class="nav-item" :class="{ 'active': currentPage === index }">
                    <button type="button" @click.prevent="emits('onIndexClick', index)" class="w-full">
                        <span>{{ index }}</span>
                    </button>
                </li>
                <li>
                    <button type="button" @click.prevent="emits('onNext')" class="nav-item rounded-e-lg"
                        :disabled="currentPage >= lastPage">
                        Next
                    </button>
                </li>
            </ul>
        </nav>
    </div>
</template>

<script setup lang="ts">
import { toRefs } from 'vue';

const props = defineProps({
    lastPage: {
        type: Number,
        required: true
    },
    currentPage: {
        type: Number,
        required: true
    }
});
const { lastPage, currentPage } = toRefs(props);

const emits = defineEmits(['onNext', 'onPrevious', 'onIndexClick']);

</script>

<style scoped>
.nav-item {
    @apply flex items-center justify-center px-3 h-8 leading-tight text-gray-500 bg-white border border-gray-300 hover:bg-gray-100 hover:text-gray-700 disabled:bg-gray-100 disabled:text-gray-300;
}

.nav-item.active {
    @apply text-light-brand border bg-brand;
}
</style>