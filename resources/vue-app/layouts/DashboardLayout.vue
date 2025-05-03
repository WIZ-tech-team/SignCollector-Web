<template>
    <div id="ttk_dashboard_layout" class="hide-ttk-dashboard-sidebar">
        <div @click.prevent="onSmScreenLayerClick()" id="ttk_sm_screen_show_aside_layer"></div>
        <DashboardHeader @toggleSidebar="() => {checkAndToggleShowSidebarClass(!controlState.hideSidebar)}"></DashboardHeader>
        <DashboardAside></DashboardAside>
        <div id="ttk_dashboard_main_footer_container">
            <DashboardMain>
                <RouterView></RouterView>
            </DashboardMain>
            <footer id="ttk_dashboard_footer">
                حقوق النشر محفوظة &copy; 2025
            </footer>
        </div>
    </div>
</template>

<script setup lang="ts">
import DashboardAside from '@/components/layout/dashboard/DashboardAside.vue';
import DashboardHeader from '@/components/layout/dashboard/DashboardHeader.vue';
import DashboardMain from '@/components/layout/dashboard/DashboardMain.vue';
import { useDashboardLayoutControl } from '@/composables/useDashboardLayoutControl';
// import { useViewportSize } from '@/composables/useViewportSize';
import { onMounted, ref, watch } from 'vue';

// Lifecucle hooks
onMounted(() => {
    // dashboardHeader.value = document.getElementById(TTK_DASHBOARD_HEADER_ID);
    // if (dashboardHeader.value) {
    //     headerResizeObserver.observe(dashboardHeader.value);
    // }
});

// // Html elements
// const dashboardHeader = ref<HTMLElement | null>(null);
// const dashboardMainContainer = ref<HTMLElement | null>(null);
// const dashboardAside = ref<HTMLElement | null>(null);

// // Header Resize Observer
// const headerResizeObserver = new ResizeObserver(() => {
//     dashboardHeader.value = document.getElementById(TTK_DASHBOARD_HEADER_ID);
//     dashboardMainContainer.value = document.getElementById(TTK_DASHBOARD_MAIN_FOOTER_CONTAINER_ID);
//     dashboardAside.value = document.getElementById(TTK_DASHBOARD_ASIDE_ID);
//     // updateLayoutComponentsSizes()
// });

// Composables
const { controlState, checkAndToggleShowSidebarClass } = useDashboardLayoutControl();

watch([() => controlState.hideSidebar], () => {
    // if (dashboardHeader.value) {
    //     console.log(controlState.hideSidebar);
    //     // updateLayoutComponentsSizes()
    // }
})

const onSmScreenLayerClick = () => {
    controlState.hideSidebar = true;
}

</script>