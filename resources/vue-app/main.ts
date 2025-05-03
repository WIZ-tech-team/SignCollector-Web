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

// Set API Service COnfig
ApiService.init(app, API_CONFIG.base_url);

app.use(pinia);
app.use(VueApexCharts);

// Check Authentication then Mount the App
(async () => {

    // Constants
    const TOKEN = localStorage.getItem(LOCAL_STORAGE_KEYS.token);

    // Stores
    const authStore = useAuthStore();

    try {
        if (TOKEN) {
            ApiService.setHeader(TOKEN);
            await authStore.fetchAuthUser()
                .finally(async () => {

                    // Authenticate
                    authStore.token = TOKEN;
                    authStore.authenticate();

                });
        } else {
            throw (new Error('Authentication Failed at Begining.'));
        }
    } catch (error: any) {
        console.log('Error: \n', error);
        ApiService.setHeader();
        authStore.removeAuth();
    } finally {

        // Use Router
        app.use(router);
        
        // Mount
        app.mount('#vue_spa');
    }
})();