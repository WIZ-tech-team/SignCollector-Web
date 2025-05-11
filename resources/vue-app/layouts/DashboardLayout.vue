<template>
  <div id="ttk_dashboard_layout" class="hide-ttk-dashboard-sidebar">
    <!-- overlay still in markup but never shown/clickable -->
    <div
      v-if="false"
      id="ttk_sm_screen_show_aside_layer"
    ></div>

    <!-- header stays, but we removed its toggleSidebar listener -->
    <DashboardHeader></DashboardHeader>

    <!-- aside stays in markup but never rendered -->
    <DashboardAside v-if="false"></DashboardAside>

    <div id="ttk_dashboard_main_footer_container">
      <DashboardMain>
        <RouterView />
      </DashboardMain>
      <footer id="ttk_dashboard_footer">
        حقوق النشر محفوظة &copy; 2025
      </footer>
    </div>
  </div>
</template>

<script setup lang="ts">
import DashboardAside   from '@/components/layout/dashboard/DashboardAside.vue';
import DashboardHeader  from '@/components/layout/dashboard/DashboardHeader.vue';
import DashboardMain    from '@/components/layout/dashboard/DashboardMain.vue';
// we no longer need useDashboardLayoutControl here, since nothing toggles
</script>

<style scoped>
/* no changes */
</style>
