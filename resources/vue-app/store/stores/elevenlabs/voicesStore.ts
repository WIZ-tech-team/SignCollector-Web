import { ref } from "vue";
import { defineStore } from "pinia";
import { PaginatedData } from "@/core/types/data/PaginatedDataInterface";
import ApiService from "@/core/services/ApiService";
import { AxiosError, AxiosResponse } from "axios";
import { BackendResponseData } from "@/core/types/config/AxiosCustom";
import { MSwal, QSwal } from "@/core/plugins/SweetAlerts2";
import { getMessageFromObj } from "@/assets/ts/swalMethods";
import { BackendApiRoute } from "@/core/types/config/BackendApiRoutes";
import { AIVoice } from "@/core/types/data/elevenlabs/AIVoices";

export const useVoicesStore = defineStore('voicesStore', () => {

    const voicesPaginated = ref<PaginatedData<AIVoice>>();

    const fetchVoicesPaginated = async (query?: string | undefined) => {
        voicesPaginated.value = undefined;
        let api: BackendApiRoute = query ? `/api/spa/admin/elevenlabs/voices${query}` : `/api/spa/admin/elevenlabs/voices`;
        await ApiService.get(api)
            .then((res: AxiosResponse<BackendResponseData>) => {
                if (res.data.status === 'success' && res.data.data) {
                    voicesPaginated.value = res.data.data;
                } else {
                    MSwal.fire('Unexpected Response', getMessageFromObj(res), 'warning');
                }
            })
            .catch((e: AxiosError<BackendResponseData>) => {
                MSwal.fire('Unexpected Error', getMessageFromObj(e), 'error');
            });
    }

    const storeVoice = async (data: AIVoice, action: 'create' | 'update') => {
        let message = {
            title: action === 'create' ? 'create Voice ?' : 'Update Voice ?',
            message: action === 'create' ? "New voice will be created." : "This voice will be updated.",
            icon: action === 'create' ? "warning" : "question"
        }
        await QSwal.fire(message.title, message.message, message.icon as 'warning' | 'question')
            .then(async result => {
                if (result.isConfirmed) {
                    let api: BackendApiRoute = action === 'create' ? `/api/spa/admin/elevenlabs/voices` : `/api/spa/admin/elevenlabs/voices/${data.id}/`;
                    await ApiService.post(api, data)
                        .then((res: AxiosResponse<BackendResponseData>) => {
                            if (res.data.status === 'success') {
                                MSwal.fire('Success', `Voice ${action} process done successfully.`, 'success');
                            } else {
                                MSwal.fire('Unexpected Response', getMessageFromObj(res), 'warning');
                            }
                        })
                        .catch((e: AxiosError<BackendResponseData>) => {
                            MSwal.fire('Unexpected Error', getMessageFromObj(e), 'error');
                        }).finally(async () => {
                            await fetchVoicesPaginated();
                        });
                }
            });
    }

    const removeVoice = async (id: number, action: 'delete' | 'archive') => {
        await QSwal.fire('delete', 'Delete Voice ?', 'question')
            .then(async result => {
                if (result.isConfirmed) {
                    await ApiService.delete(`/api/spa/admin/elevenlabs/voices/${id}`)
                        .then((res: AxiosResponse<BackendResponseData>) => {
                            if (res.data.status === 'success') {
                                MSwal.fire('Success', `Voice deleted successfully.`, 'success');
                            } else {
                                MSwal.fire('Unexpected Response', getMessageFromObj(res), 'warning');
                            }
                        })
                        .catch((e: AxiosError<BackendResponseData>) => {
                            MSwal.fire('Unexpected Error', getMessageFromObj(e), 'error');
                        }).finally(async () => {
                            await fetchVoicesPaginated();
                        });
                }
            });
    }

    return { voicesPaginated, fetchVoicesPaginated, storeVoice, removeVoice };

});