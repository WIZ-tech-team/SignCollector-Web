import { getMessageFromObj } from "@/assets/ts/swalMethods";
import { MSwal } from "@/core/plugins/SweetAlerts2";
import ApiService from "@/core/services/ApiService";
import { BackendResponseData } from "@/core/types/config/AxiosCustom";
import { BackendApiRoute } from "@/core/types/config/BackendApiRoutes";
import { AxiosError, AxiosResponse } from "axios";
import { defineStore } from "pinia";
import { Ref } from "vue";

type ApiMap = {
    users: BackendApiRoute;
    subscriptions: BackendApiRoute;
    year_subscriptions: BackendApiRoute;
}

export const useStatisticsStore = defineStore('statisticsStore', () => {

    const API_MAP: ApiMap = {
        users: '/api/spa/admin/statistics/users',
        subscriptions: '/api/spa/admin/statistics/subscriptions',
        year_subscriptions: '/api/spa/admin/statistics/subscriptions/year'
    };

    const fetchStatistics = async (temp: Ref, type: keyof ApiMap) => {
        await ApiService.get(API_MAP[type])
            .then((res: AxiosResponse<BackendResponseData>) => {
                if (res.data.status === 'success' && res.data.data) {
                    temp.value = res.data.data;
                } else {
                    MSwal.fire('Unexpected Response', getMessageFromObj(res), 'warning');
                }
            })
            .catch((e: AxiosError<BackendResponseData>) => {
                MSwal.fire('Unexpected Error', getMessageFromObj(e), 'error');
            });
    }

    return { fetchStatistics };

})