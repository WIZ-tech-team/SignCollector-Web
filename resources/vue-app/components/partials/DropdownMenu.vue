<template>

    <div class="relative">
        <button id="dropdownDefaultButton" data-dropdown-toggle="dropdown" @click.prevent="isDisplayed = !isDisplayed"
            class="font-medium rounded-lg text-sm text-center inline-flex items-center" type="button">
            <SolidHeroIcon name="Bars3Icon" classes="w-6 h-6 text-brand"></SolidHeroIcon>
        </button>

        <!-- Dropdown menu -->
        <div id="dropdown"
            class="z-10 absolute bg-white divide-y divide-gray-100 border rounded-lg shadow-sm w-44 left-0 top-full" :class="{'hidden': !isDisplayed}">
            <ul class="py-2 text-sm text-gray-700" aria-labelledby="dropdownDefaultButton">
                <li v-for="item in items">
                    <slot v-if="item.isSlot" :name="`item_${item.key}_slot`"></slot>
                    <button v-else type="button" @click.prevent="onItemClicked(item.key)"
                        class="block px-4 py-2 hover:bg-gray-100 w-full text-start" :class="item.classes"
                        :disabled="item?.disabled">
                        {{ item.title }}
                    </button>
                </li>
            </ul>
        </div>
    </div>

</template>

<script setup lang="ts">
import SolidHeroIcon from '@/components/icons/SolidHeroIcon.vue';
import { DropdownMenuItem } from '@/core/types/elements/DropDownMenu';
import { PropType, ref } from 'vue';

// Props
const props = defineProps({
    items: {
        type: Object as PropType<DropdownMenuItem[]>,
        required: true
    }
})

// Emits
const emits = defineEmits(['itemClicked']);

const isDisplayed = ref<boolean>(false);

const onItemClicked = (key: string) => {
    emits('itemClicked', key);
    isDisplayed.value = false;
}

</script>