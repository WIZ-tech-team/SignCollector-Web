import { getMessageFromObj } from "@/assets/ts/swalMethods";
import { MSwal } from "@/core/plugins/SweetAlerts2";
import ApiService from "@/core/services/ApiService";
import { BackendResponseData } from "@/core/types/config/AxiosCustom";
import { BackendApiRoute } from "@/core/types/config/BackendApiRoutes";
import { AxiosError, AxiosResponse } from "axios";
import { defineStore } from "pinia";
import { Ref } from "vue";

type ApiMap = {
    subscriptions: BackendApiRoute;
}

export const useReportsStore = defineStore('reportsStore', () => {

    const API_MAP: ApiMap = {
        subscriptions: '/api/spa/admin/reports/subscriptions/all'
    };

    const fetchReportData = async (temp: Ref, type: keyof ApiMap) => {
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

    return { fetchReportData };

})