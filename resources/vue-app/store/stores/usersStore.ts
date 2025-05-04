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
                    MSwal.fire('رد غير متوقع', getMessageFromObj(res), 'warning');
                }
            })
            .catch((e: AxiosError<BackendResponseData>) => {
                MSwal.fire('خطأ غير متوقع', getMessageFromObj(e), 'error');
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
                    MSwal.fire('رد غير متوقع', getMessageFromObj(res), 'warning');
                }
            })
            .catch((e: AxiosError<BackendResponseData>) => {
                MSwal.fire('خطأ غير متوقع', getMessageFromObj(e), 'error');
            });
    }

    const storeUser = async (data: UpdatableUserData, action: 'create' | 'update') => {
        let message = {
            title: action === 'create' ? 'إنشاء الحساب ؟' : 'تحديث الحساب ؟',
            message: action === 'create' ? "سيتم إنشاء حساب جديد." : "سيتم تحديث معلومات الحساب.",
            icon: action === 'create' ? "warning" : "question"
        }
        await QSwal.fire(message.title, message.message, message.icon as 'warning' | 'question')
            .then(async result => {
                if (result.isConfirmed) {
                    let api: BackendApiRoute = action === 'create' ? `/api/spa/users` : `/api/spa/users/${data.id}/`;
                    await ApiService.post(api, data)
                        .then((res: AxiosResponse<BackendResponseData>) => {
                            if (res.data.status === 'success') {
                                MSwal.fire('نجحت العملية', `تمت العملية بنجاح.`, 'success');
                            } else {
                                MSwal.fire('رد غير متوقع', getMessageFromObj(res), 'warning');
                            }
                        })
                        .catch((e: AxiosError<BackendResponseData>) => {
                            MSwal.fire('خطأ غير متوقع', getMessageFromObj(e), 'error');
                        }).finally(async () => {
                            await fetchUsersPaginated();
                        });
                }
            });
    }

    const removeUser = async (id: number, action: 'delete' | 'archive') => {
        let message = {
            title: action === 'delete' ? 'حذف الحساب ؟' : 'أرشفة الحساب ؟',
            message: action === 'delete' ? "سيتم حذف الحساب." : "سيتم أرشفة الحساب.",
            icon: action === 'delete' ? "warning" : "question"
        }
        await QSwal.fire(message.title, message.message, message.icon as 'warning' | 'question')
            .then(async result => {
                if (result.isConfirmed) {
                    let api: BackendApiRoute = action === 'delete' ? `/api/spa/users/${id}/` : `/api/spa/users/${id}/soft`;
                    await ApiService.delete(api)
                        .then((res: AxiosResponse<BackendResponseData>) => {
                            if (res.data.status === 'success') {
                                MSwal.fire('نجحت العملية', `تمت العملية بنجاخ.`, 'success');
                            } else {
                                MSwal.fire('رد غير متوقع', getMessageFromObj(res), 'warning');
                            }
                        })
                        .catch((e: AxiosError<BackendResponseData>) => {
                            MSwal.fire('خطأ غير متوقع', getMessageFromObj(e), 'error');
                        }).finally(async () => {
                            await fetchUsersPaginated();
                            await fetchArchivedUsersPaginated();
                        });
                }
            });
    }

    const restoreUser = async (id: number) => {
        await QSwal.fire('استرجاع الحساب ؟', "سيتم استرجاع الحساب إلى قائمة المستخدمين.", 'question')
            .then(async result => {
                if (result.isConfirmed) {
                    await ApiService.get(`/api/spa/users/${id}/restore`)
                        .then((res: AxiosResponse<BackendResponseData>) => {
                            if (res.data.status === 'success') {
                                MSwal.fire('نجحت العملية', `تم استرجاع الحساب بنجاح.`, 'success');
                            } else {
                                MSwal.fire('رد غير متوقع', getMessageFromObj(res), 'warning');
                            }
                        })
                        .catch((e: AxiosError<BackendResponseData>) => {
                            MSwal.fire('خطأ غير متوقع', getMessageFromObj(e), 'error');
                        }).finally(async () => {
                            await fetchArchivedUsersPaginated();
                            await fetchUsersPaginated();
                        });
                }
            });
    }

    return { usersPaginated, archivedUsersPaginated, fetchUsersPaginated, storeUser, removeUser, fetchArchivedUsersPaginated, restoreUser };

});