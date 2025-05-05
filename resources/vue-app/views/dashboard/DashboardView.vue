<template>
    <div class="flex flex-col align-start gap-8 justify-start">

        <!-- Detailed Signs Table -->
        <!-- Data Table: Detailed Signs -->
        <TableComponent title="بيانات الإشارات" :data-paginated="(signsPaginated as PaginatedData<DetailedSign>)"
            :columns="signsTableColumns">

            <template #header_end>
                <button @click.prevent="submitExport"
                    class="p-2 rounded-md bg-brand text-light-brand hover:bg-light-brand hover:text-brand focus:outline-none">
                    تصدير
                </button>
            </template>

            <template v-for="(sign, index) in signsPaginated?.data" v-slot:[`row_${index}_image_slot_value`]>
                <div v-if="sign?.image" class="h-16 w-16 object-cover rounded-full">
                    <img id='sign_image_preview' class="w-full h-full" :src="sign.image.original_url" alt="photo" />
                </div>
            </template>

            <template v-for="(sign, index) in signsPaginated?.data" v-slot:[`row_${index}_details_modal_slot_value`]>
                <ModalCard title="تفاصيل الإشارة" btn-classes="p-2 text-brand">
                    <template #open_button>
                        التفاصيل
                    </template>

                    <div class="flex flex-col gap-2">
                        <div v-if="sign?.image" class="h-16 w-16 object-cover rounded-full">
                            <img id='sign_image_preview' class="w-full h-full" :src="sign.image.original_url"
                                alt="photo" />
                        </div>
                        <div v-for="key in detailedSignKeysTitles" class="flex flex-col gap-1">
                            <span class="text-sm font-medium text-gray-900">{{ key.title }}</span>
                            <span class="text-sm text-gray-500">{{ sign[key.key] }}</span>
                        </div>
                    </div>
                </ModalCard>
            </template>

        </TableComponent>

    </div>
</template>

<script setup lang="ts">
import { computed, onBeforeMount, ref } from "vue";
import { TableColumn } from "@/core/types/elements/Table";
import { PaginatedData } from "@/core/types/data/PaginatedDataInterface";
import TableComponent from "@/components/table/TableComponent.vue";
import { useDetailedSignsStore } from "@/store/stores/detailedSignsStore";
import { DetailedSign, detailedSignKeysTitles } from "@/core/types/data/DetailedSign";
import ModalCard from "@/components/cards/ModalCard.vue";
import { MSwal, QSwal } from "@/core/plugins/SweetAlerts2";
import { getMessageFromObj } from "@/assets/ts/swalMethods";
import { AxiosError } from "axios";
import { BackendResponseData } from "@/core/types/config/AxiosCustom";
import ApiService from "@/core/services/ApiService";
import { useAuthStore } from "@/store/stores/authStore";

// Lifecycle hooks
onBeforeMount(async () => {
    await detailedSignsStore.fetchDetailedSignsPaginated();
});

// Stores
const detailedSignsStore = useDetailedSignsStore();
const authStore = useAuthStore();

const signsTableColumns = ref<TableColumn[]>([
    {
        title: 'المعرّف',
        key: 'id'
    },
    {
        title: 'الصورة',
        key: 'image_slot',
        isSlot: true
    },
    {
        title: 'الاسم',
        key: 'sign_name',
        valueClasses: "text-sm font-medium text-gray-900"
    },
    {
        title: 'الشكل',
        key: 'sign_shape'
    },
    {
        title: 'الرمز',
        key: 'sign_code'
    },
    {
        title: 'الرمز (GCC)',
        key: 'sign_code_gcc'
    },
    {
        title: 'النوع',
        key: 'sign_type'
    },
    {
        title: 'التفاصيل',
        key: 'details_modal_slot',
        isSlot: true
    }
]);

const signsPaginated = computed(() => {
    return detailedSignsStore.detailedSignsPaginated;
});

const submitExport = async () => {
    QSwal.fire('تصدير الإشارات ؟', 'التصدير إلى ملف إكسل.', 'question')
        .then(async (result) => {
            if (result.isConfirmed) {
                
                ApiService.setHeader(authStore.token as string, 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet')
                await ApiService.post(`/api/spa/signs/detailed/export`, null, {
                    responseType: 'arraybuffer', // Crucial for binary files
                    headers: {
                        'Accept': 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                        'Content-Type': 'multipart/form-data' // Or 'application/json'
                    }
                }).then(response => {
                    // Verify we got binary data
                    if (!(response.data instanceof ArrayBuffer)) {
                        throw new Error('Invalid response format');
                    }

                    // Create blob with explicit type
                    const blob = new Blob([response.data], {
                        type: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
                    });

                    // Get filename
                    let filename = 'Subscriptions_' + new Date().toISOString().split('T')[0] + '.xlsx';
                    const disposition = response.headers['content-disposition'];
                    if (disposition) {
                        const matches = /filename[^;=\n]*=((['"]).*?\2|[^;\n]*)/.exec(disposition);
                        if (matches && matches[1]) {
                            filename = matches[1].replace(/['"]/g, '');
                        }
                    }

                    // Create and trigger download
                    const url = window.URL.createObjectURL(blob);
                    const link = document.createElement('a');
                    link.href = url;
                    link.download = filename;
                    document.body.appendChild(link);
                    link.click();

                    // Cleanup
                    setTimeout(() => {
                        document.body.removeChild(link);
                        window.URL.revokeObjectURL(url);
                    }, 100);
                    
                }).catch((error: AxiosError<BackendResponseData>) => {
                    MSwal.fire('Unexpected Error!', getMessageFromObj(error), 'error');
                });

            }
        })
}

</script>