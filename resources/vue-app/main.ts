import { createApp } from "vue";
import VueApp from "@/VueApp.vue";
import router from "@/router";
import pinia from "@/store";
import VueApexCharts from "vue3-apexcharts";

// Import tailwind main file
import '../css/app.css';
// Import custom css for Vue app
import '@/assets/css/main.css';
import ApiService from "./core/services/ApiService";
import { API_CONFIG, LOCAL_STORAGE_KEYS } from "./core/constants/appConfigConstants";
import { useAuthStore } from "./store/stores/authStore";

const app = createApp({});
app.component('vue-app', VueApp);

// Initialize ApiService with base URL
ApiService.init(app, API_CONFIG.base_url);

// Install global plugins
app.use(pinia);
app.use(VueApexCharts);

// Bootstrap authentication and mount the app
(async () => {
  const authStore = useAuthStore();
  const token = localStorage.getItem(LOCAL_STORAGE_KEYS.token);

  if (token) {
    // Set token header before fetching user
    ApiService.setHeader(token);
    try {
      await authStore.fetchAuthUser();
      // On success, persist token in store
      authStore.token = token;
      authStore.authenticate();
    } catch (fetchError) {
      // Invalid or expired token
      console.error('Auth fetch failed:', fetchError);
      ApiService.setHeader();
      authStore.removeAuth();
    }
  } else {
    // No token found
    ApiService.setHeader();
    authStore.removeAuth();
  }

  // Now that auth status is known, use the router and mount
  app.use(router);
  app.mount('#vue_spa');
})();