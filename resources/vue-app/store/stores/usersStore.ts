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

export type UpdatableUserData = {
    id: number;
    name: string;
    email: string;
    email_verified_at?: string | null;
    old_password?: string;
    password?: string;
    password_confirmation?: string;
    phone: string;
    phone_verified_at?: string;
    type: UserType;
    avatar_url?: string;
    avatar?: File | null;
}

export const useUsersStore = defineStore('usersStore', () => {

    const usersPaginated = ref<PaginatedData<UserInterface>>();
    const archivedUsersPaginated = ref<PaginatedData<UserInterface>>();

    const fetchUsersPaginated = async (query?: string | undefined) => {
        usersPaginated.value = undefined;
        let api: BackendApiRoute = query ? `/api/spa/users${query}` : `/api/spa/users`;
        await ApiService.get(api)
            .then((res: AxiosResponse<BackendResponseData>) => {
                if (res.data.status === 'success' && res.data.data) {
                    usersPaginated.value = res.data.data;
                } else {
                    MSwal.fire('Unexpected Response', getMessageFromObj(res), 'warning');
                }
            })
            .catch((e: AxiosError<BackendResponseData>) => {
                MSwal.fire('Unexpected Error', getMessageFromObj(e), 'error');
            });
    }

    const fetchArchivedUsersPaginated = async (query?: string | undefined) => {
        archivedUsersPaginated.value = undefined;
        let api: BackendApiRoute = query ? `/api/spa/users/trash${query}` : `/api/spa/users/trash`;
        await ApiService.get(api)
            .then((res: AxiosResponse<BackendResponseData>) => {
                if (res.data.status === 'success' && res.data.data) {
                    archivedUsersPaginated.value = res.data.data;
                } else {
                    MSwal.fire('Unexpected Response', getMessageFromObj(res), 'warning');
                }
            })
            .catch((e: AxiosError<BackendResponseData>) => {
                MSwal.fire('Unexpected Error', getMessageFromObj(e), 'error');
            });
    }

    const storeUser = async (data: UpdatableUserData, action: 'create' | 'update') => {
        let message = {
            title: action === 'create' ? 'create User ?' : 'Update User ?',
            message: action === 'create' ? "New user account will be created." : "This user account will be updated.",
            icon: action === 'create' ? "warning" : "question"
        }
        await QSwal.fire(message.title, message.message, message.icon as 'warning' | 'question')
            .then(async result => {
                if (result.isConfirmed) {
                    let api: BackendApiRoute = action === 'create' ? `/api/spa/users` : `/api/spa/users/${data.id}/`;
                    await ApiService.post(api, data)
                        .then((res: AxiosResponse<BackendResponseData>) => {
                            if (res.data.status === 'success') {
                                MSwal.fire('Success', `User ${action} process done successfully.`, 'success');
                            } else {
                                MSwal.fire('Unexpected Response', getMessageFromObj(res), 'warning');
                            }
                        })
                        .catch((e: AxiosError<BackendResponseData>) => {
                            MSwal.fire('Unexpected Error', getMessageFromObj(e), 'error');
                        }).finally(async () => {
                            await fetchUsersPaginated();
                        });
                }
            });
    }

    const removeUser = async (id: number, action: 'delete' | 'archive') => {
        let message = {
            title: action === 'delete' ? 'Delete User ?' : 'Archive User ?',
            message: action === 'delete' ? "Deleted account data can't be restored." : "Archived user data will be hidden from app data.",
            icon: action === 'delete' ? "warning" : "question"
        }
        await QSwal.fire(message.title, message.message, message.icon as 'warning' | 'question')
            .then(async result => {
                if (result.isConfirmed) {
                    let api: BackendApiRoute = action === 'delete' ? `/api/spa/users/${id}/` : `/api/spa/users/${id}/soft`;
                    await ApiService.delete(api)
                        .then((res: AxiosResponse<BackendResponseData>) => {
                            if (res.data.status === 'success') {
                                MSwal.fire('Success', `User account ${action} done successfully.`, 'success');
                            } else {
                                MSwal.fire('Unexpected Response', getMessageFromObj(res), 'warning');
                            }
                        })
                        .catch((e: AxiosError<BackendResponseData>) => {
                            MSwal.fire('Unexpected Error', getMessageFromObj(e), 'error');
                        }).finally(async () => {
                            await fetchUsersPaginated();
                            await fetchArchivedUsersPaginated();
                        });
                }
            });
    }

    const restoreUser = async (id: number) => {
        await QSwal.fire('Restore User ?', "Restored user data will be shown with users list.", 'question')
            .then(async result => {
                if (result.isConfirmed) {
                    await ApiService.get(`/api/spa/users/${id}/restore`)
                        .then((res: AxiosResponse<BackendResponseData>) => {
                            if (res.data.status === 'success') {
                                MSwal.fire('Success', `User account restored successfully.`, 'success');
                            } else {
                                MSwal.fire('Unexpected Response', getMessageFromObj(res), 'warning');
                            }
                        })
                        .catch((e: AxiosError<BackendResponseData>) => {
                            MSwal.fire('Unexpected Error', getMessageFromObj(e), 'error');
                        }).finally(async () => {
                            await fetchArchivedUsersPaginated();
                            await fetchUsersPaginated();
                        });
                }
            });
    }

    return { usersPaginated, archivedUsersPaginated, fetchUsersPaginated, storeUser, removeUser, fetchArchivedUsersPaginated, restoreUser };

});