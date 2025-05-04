import { ref } from "vue";
import { defineStore } from "pinia";
import { UserInterface, UserType } from "@/core/types/data/UserInterface";
import { PaginatedData } from "@/core/types/data/PaginatedDataInterface";
import ApiService from "@/core/services/ApiService";
import { AxiosError, AxiosResponse } from "axios";
import { BackendResponseData } from "@/core/types/config/AxiosCustom";
import { MSwal, QSwal } from "@/core/plugins/SweetAlerts2";
import { getMessageFromObj } from "@/assets/ts/swalMethods";
import { BackendApiRoute } from "@/core/types/config/BackendApiRoutes";
import { DetailedSign } from "@/core/types/data/DetailedSign";

export const useDetailedSignsStore = defineStore('detailedSignsPaginatedStore', () => {

    const detailedSignsPaginated = ref<PaginatedData<DetailedSign>>();
    const allSigns = ref<DetailedSign[]>([]);

    const fetchDetailedSignsPaginated = async (query?: string | undefined) => {
        detailedSignsPaginated.value = undefined;
        let api: BackendApiRoute = query ? `/api/spa/signs/detailed${query}` : `/api/spa/signs/detailed`;
        await ApiService.get(api)
            .then((res: AxiosResponse<BackendResponseData>) => {
                if (res.data.status === 'success' && res.data.data) {
                    detailedSignsPaginated.value = res.data.data;
                } else {
                    MSwal.fire('Unexpected Response', getMessageFromObj(res), 'warning');
                }
            })
            .catch((e: AxiosError<BackendResponseData>) => {
                MSwal.fire('Unexpected Error', getMessageFromObj(e), 'error');
            });
    }

    const fetchAllDetailedSigns = async () => {
        allSigns.value = [];
        await ApiService.get(`/api/mobile/signs/detailed`)
            .then((res: AxiosResponse<BackendResponseData>) => {
                if (res.data.status === 'success' && res.data.data) {
                    allSigns.value = res.data.data;
                } else {
                    MSwal.fire('Unexpected Response', getMessageFromObj(res), 'warning');
                }
            })
            .catch((e: AxiosError<BackendResponseData>) => {
                MSwal.fire('Unexpected Error', getMessageFromObj(e), 'error');
            });
    }

    return { detailedSignsPaginated, allSigns, fetchDetailedSignsPaginated, fetchAllDetailedSigns };

});