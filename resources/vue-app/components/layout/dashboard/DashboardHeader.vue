<template>
    <header id="ttk_dashboard_header" class="shadow-md">
        <div class="flex items-center justify-start gap-8 w-full bg-white p-0">
            <div class="flex items-center justify-start gap-4 w-[260px] px-4 py-4 bg-brand h-[72px]">
                <button type="button" @click.prevent="emits('toggleSidebar')"
                    class="px-1 py-0 rounded-md text-white bg-white hover:bg-white">
                    <Bars3Icon class="w-6 h-8 text-brand text-bold bg-white rounded-md"></Bars3Icon>
                </button>
                <span class="text-white text-lg font-bold">
                    <!-- <img src="@/assets/images/" alt="" class="w-100"> -->
                    <span class="font-bold text-2xl">
                        حصر لوائح الطرق
                    </span>
                </span>
            </div>
            <div class="flex items-center justify-between py-4 px-8 user-section">
                <span v-if="authStore.user" class="font-semibold text-brand capitalize">
                    {{ `لوحة التحكم` }}
                </span>
                <button type="button" @click.prevent="logout" :disabled="logoutDisabled"
                    class="p-2 bg-white hover:bg-light-brand cursor-pointer rounded-md disabled:cursor-default">
                    <div class="flex gap-1 items-center justify-center">
                        <span class="text-brand">
                            تسجيل الخروج
                        </span>
                        <ArrowLeftStartOnRectangleIcon class="w-6 h-6 text-brand"></ArrowLeftStartOnRectangleIcon>
                    </div>
                </button>
            </div>
        </div>

    </header>
</template>

<script setup lang="ts">
import { ref } from "vue";
import { QSwal } from '@/core/plugins/SweetAlerts2';
import { useAuthStore } from '@/store/stores/authStore';
import { Bars3Icon, ArrowLeftStartOnRectangleIcon } from '@heroicons/vue/24/solid'; // Solid icons
import { useRouter } from 'vue-router';

// Emits
const emits = defineEmits(['toggleSidebar']);

// Routing
const router = useRouter();

// Stores
const authStore = useAuthStore();

// Custom constants
const logoutDisabled = ref<boolean>(false);

// Functions
function logout() {
    logoutDisabled.value = true;
    QSwal.fire('هل أنت متأكد؟', 'تسجيل الخروج الان', 'question').then(result => {
        if (result.isConfirmed) {
            authStore.logout()
                .finally(async () => {
                    await router.push('/sign-in');
                });
        }
    }).finally(() => {
        logoutDisabled.value = false;
    });
}

</script>

<style scoped>
#ttk_dashboard_layout.hide-ttk-dashboard-sidebar #ttk_dashboard_header .on-side-hidden {
    background-color: white !important;
}

#ttk_dashboard_layout.hide-ttk-dashboard-sidebar #ttk_dashboard_header .on-side-hidden .logo {
    @apply text-brand;
}

#ttk_dashboard_header .user-section {
    width: calc(100% - 290px);
}
</style>