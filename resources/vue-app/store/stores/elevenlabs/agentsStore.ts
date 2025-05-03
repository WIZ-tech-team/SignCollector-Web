import { ref } from "vue";
import { defineStore } from "pinia";
import { PaginatedData } from "@/core/types/data/PaginatedDataInterface";
import ApiService from "@/core/services/ApiService";
import { AxiosError, AxiosResponse } from "axios";
import { BackendResponseData } from "@/core/types/config/AxiosCustom";
import { MSwal, QSwal } from "@/core/plugins/SweetAlerts2";
import { getMessageFromObj } from "@/assets/ts/swalMethods";
import { BackendApiRoute } from "@/core/types/config/BackendApiRoutes";
import { AIAgent } from "@/core/types/data/elevenlabs/AIAgents";

export const useAgentsStore = defineStore('agentsStore', () => {

    const agentsPaginated = ref<PaginatedData<AIAgent>>();

    const fetchAgentsPaginated = async (query?: string | undefined) => {
        agentsPaginated.value = undefined;
        let api: BackendApiRoute = query ? `/api/spa/admin/elevenlabs/agents${query}` : `/api/spa/admin/elevenlabs/agents`;
        await ApiService.get(api)
            .then((res: AxiosResponse<BackendResponseData>) => {
                if (res.data.status === 'success' && res.data.data) {
                    agentsPaginated.value = res.data.data;
                } else {
                    MSwal.fire('Unexpected Response', getMessageFromObj(res), 'warning');
                }
            })
            .catch((e: AxiosError<BackendResponseData>) => {
                MSwal.fire('Unexpected Error', getMessageFromObj(e), 'error');
            });
    }

    const createAgent = async (data: AIAgent) => {
        await QSwal.fire("create Agent ?", "New agent will be created.", "question")
            .then(async result => {
                if (result.isConfirmed) {
                    await ApiService.post(`/api/spa/admin/elevenlabs/agents`, data)
                        .then((res: AxiosResponse<BackendResponseData>) => {
                            if (res.data.status === 'success') {
                                MSwal.fire('Success', `Agent create process done successfully.`, 'success');
                            } else {
                                MSwal.fire('Unexpected Response', getMessageFromObj(res), 'warning');
                            }
                        })
                        .catch((e: AxiosError<BackendResponseData>) => {
                            MSwal.fire('Unexpected Error', getMessageFromObj(e), 'error');
                        }).finally(async () => {
                            await fetchAgentsPaginated();
                        });
                }
            });
    }

    const updateAgent = async (data: AIAgent) => {
        await QSwal.fire("Update Agent ?", "This agent will be updated.", "question")
            .then(async result => {
                if (result.isConfirmed) {
                    await ApiService.put(`/api/spa/admin/elevenlabs/agents/${data.id}`, data)
                        .then((res: AxiosResponse<BackendResponseData>) => {
                            if (res.data.status === 'success') {
                                MSwal.fire('Success', `Agent update process done successfully.`, 'success');
                            } else {
                                MSwal.fire('Unexpected Response', getMessageFromObj(res), 'warning');
                            }
                        })
                        .catch((e: AxiosError<BackendResponseData>) => {
                            MSwal.fire('Unexpected Error', getMessageFromObj(e), 'error');
                        }).finally(async () => {
                            await fetchAgentsPaginated();
                        });
                }
            });
    }

    const removeAgent = async (id: number, action: 'delete' | 'archive') => {
        await QSwal.fire('delete', 'Delete Agent ?', 'question')
            .then(async result => {
                if (result.isConfirmed) {
                    await ApiService.delete(`/api/spa/admin/elevenlabs/agents/${id}`)
                        .then((res: AxiosResponse<BackendResponseData>) => {
                            if (res.data.status === 'success') {
                                MSwal.fire('Success', `Agent deleted successfully.`, 'success');
                            } else {
                                MSwal.fire('Unexpected Response', getMessageFromObj(res), 'warning');
                            }
                        })
                        .catch((e: AxiosError<BackendResponseData>) => {
                            MSwal.fire('Unexpected Error', getMessageFromObj(e), 'error');
                        }).finally(async () => {
                            await fetchAgentsPaginated();
                        });
                }
            });
    }

    return { agentsPaginated, fetchAgentsPaginated, createAgent, updateAgent, removeAgent };

});