<template>
    <ModalCard title="تصدير اللوائح" btnClasses="flex items-center justify-center gap-2 p-2 rounded-md bg-brand text-light-brand
              hover:bg-light-brand hover:text-brand focus:outline-none">
        <!-- Modal Btn -->
        <template #open_button>
            تصدير
            <SolidHeroIcon name="ArrowUpTrayIcon" classes="w-5 h-5" />
        </template>

        <!-- Modal Content -->
        <button @click.prevent="submitExport"
            class="flex items-center justify-center gap-2 p-2 rounded-md bg-brand text-light-brand hover:bg-light-brand hover:text-brand focus:outline-none">
            تصدير
            <SolidHeroIcon name="ArrowUpTrayIcon" classes="w-5 h-5" />
        </button>

    </ModalCard>
</template>

<script setup lang="ts">
import { getMessageFromObj } from '@/assets/ts/swalMethods';
import { MSwal, QSwal } from '@/core/plugins/SweetAlerts2';
import ApiService from '@/core/services/ApiService';
import { BackendResponseData } from '@/core/types/config/AxiosCustom';
import { useAuthStore } from '@/store/stores/authStore';
import { AxiosError } from 'axios';
import SolidHeroIcon from '@/components/icons/SolidHeroIcon.vue';
import ModalCard from '@/components/cards/ModalCard.vue';

const authStore = useAuthStore();

const submitExport = async () => {
    QSwal.fire('تصدير اللوحات ؟', 'التصدير إلى ملف إكسل.', 'question')
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
                    MSwal.fire('خطأ غير متوقع!', getMessageFromObj(error), 'error');
                });

            }
        })
}
</script>