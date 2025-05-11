<template>
    <aside id="ttk_dashboard_aside" class="shadow-md">
        <div class="flex flex-col items-start justify-start gap-4 w-full h-full px-4 py-6 bg-brand">

            <template v-for="item in menuItems">

                <router-link v-if="!('items' in item)" :to="item.to"
                    class="group text-md px-4 py-2 rounded-md text-white hover:text-active-brand hover:bg-light-brand w-full aside-menu-item">
                    <div class="flex w-full gap-2">
                        <SolidHeroIcon v-if="item.heroicon_name" :name="item.heroicon_name"
                            classes="text-white w-6 h-6 group-hover:text-active-brand group-[.router-link-active:hover]:text-white" />
                        <span>
                            {{ item.title }}
                        </span>
                    </div>
                </router-link>

                <div v-else class="w-full">
                    <button type="button" @click.prevent="toggleSubmenu(item)" class="group text-md px-4 py-2 rounded-md 
                    text-white hover:text-light-brand hover:bg-active-brand w-full
                    flex items-center justify-between aside-menu-item">
                        <div class="flex w-full gap-2">
                            <SolidHeroIcon v-if="item.heroicon_name" :name="item.heroicon_name"
                                classes="text-white w-6 h-6 group-hover:text-light-brand group-[.router-link-active:hover]:text-white" />
                            <span>
                                {{ item.title }}
                            </span>
                        </div>
                        <ChevronDownIcon class="w-5 h-5 transition-transform"
                            :class="{ 'rotate-180': item.is_open }" />
                    </button>
                    <!-- Submenu Items -->
                    <div v-if="item.is_open" class="flex flex-col gap-2 ml-3 mt-2">
                        <router-link v-for="subMenuItem in item.items" :to="subMenuItem.to"
                            class="group text-md px-4 py-2 rounded-md text-white hover:text-active-brand hover:bg-light-brand w-full aside-menu-item">
                            <div class="flex w-full gap-2">
                                <SolidHeroIcon v-if="subMenuItem.heroicon_name" :name="subMenuItem.heroicon_name"
                                    classes="text-white w-6 h-6 group-hover:text-active-brand group-[.router-link-active:hover]:text-white" />
                                <span>
                                    {{ subMenuItem.title }}
                                </span>
                            </div>
                        </router-link>
                    </div>
                </div>
            </template>
        </div>
    </aside>
</template>

<script setup lang="ts">
import SolidHeroIcon from '@/components/icons/SolidHeroIcon.vue';
import { DASHBOARD_ASIDE_MENU_ITEMS } from '@/core/constants/dashboardAsideMenuItems';
import { AsideSubmenu } from '@/core/types/config/AsideMenuItem';
import { ChevronDownIcon } from "@heroicons/vue/24/solid";
import { ref } from 'vue';

const menuItems = ref(DASHBOARD_ASIDE_MENU_ITEMS);

function toggleSubmenu(submenu: AsideSubmenu) {
    submenu.is_open = !submenu.is_open;
}

</script>

<style scoped>
.aside-menu-item.router-link-active {
    @apply bg-active-brand text-white;
}
</style>