import { LOCAL_STORAGE_KEYS } from "@/core/constants/appConfigConstants";
import ApiService from "@/core/services/ApiService";
import { SystemRoute } from "@/core/types/config/SystemRoutes";
import { AxiosError, AxiosResponse } from "axios";
import { defineStore } from "pinia";
import { ref } from "vue";
import { MSwal } from "@/core/plugins/SweetAlerts2";
import { BackendResponseData } from "@/core/types/config/AxiosCustom";
import { UserInterface } from "@/core/types/data/UserInterface";
import { getMessageFromObj } from "@/assets/ts/swalMethods";

export const useAuthStore = defineStore('authStore', () => {

    // Constants
    const user = ref<UserInterface | null>(null);
    const isAuthenticated = ref<boolean>(false);
    const token = ref<string | null>(null);

    // Stores

    async function authenticate() {
        if (token.value && user.value?.id) {
            localStorage.setItem(LOCAL_STORAGE_KEYS.token, token.value);
            isAuthenticated.value = true;
            ApiService.setHeader(token.value);
        } else {
            removeAuth();
        }
    }

    function removeAuth() {
        localStorage.removeItem(LOCAL_STORAGE_KEYS.token);
        isAuthenticated.value = false;
        user.value = null;
        token.value = null;
        ApiService.setHeader();
    }

    async function login(name: string, password: string): Promise<SystemRoute | void> {

        const formdata = new FormData();
        formdata.append('name', name);
        formdata.append('password', password);

        await ApiService.post('/api/spa/admin/auth/login', formdata)
            .then((res: AxiosResponse) => {
                if (res.data?.status === 'success' && res.data?.data) {
                    user.value = res.data?.data?.user ?? null;
                    token.value = res.data?.data?.token ?? "";
                } else {
                    MSwal.fire('Sorry', getMessageFromObj(res), 'error');
                }
            })
            .catch((error: AxiosError<BackendResponseData>) => {
                console.log(error);
                MSwal.fire('Sorry', getMessageFromObj(error), 'error');
            })
            .finally(() => {
                authenticate()
            });

    }

    async function fetchAuthUser(): Promise<SystemRoute | void> {
        await ApiService.get('/api/spa/admin/auth/user')
            .then((res: AxiosResponse) => {
                if (res.data?.status === 'success' && res.data?.data) {
                    user.value = res.data.data;
                }
            })
            .catch((e: Error) => {
                console.log(e);
                throw e;
            });
    }

    async function logout(): Promise<SystemRoute | void> {
        await ApiService.get('/api/spa/admin/auth/logout')
            .finally(() => {
                removeAuth()
            });
    }

    return { user, isAuthenticated, token, login, fetchAuthUser, authenticate, logout, removeAuth }
})