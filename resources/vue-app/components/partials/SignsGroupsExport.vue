<template>
    <ModalCard title="تصدير اللوائح" btnClasses="flex items-center justify-center gap-2 p-2 rounded-md bg-brand text-light-brand
              hover:bg-light-brand hover:text-brand focus:outline-none">
        <!-- Modal Btn -->
        <template #open_button>
            تصدير
            <SolidHeroIcon name="ArrowUpTrayIcon" classes="w-5 h-5" />
        </template>

        <div>
            <Form @submit="submitExport" :validation-schema="validationSchema"
                class="flex flex-col items-top justify-start gap-8">

                <div class="flex flex-col items-start justify-start gap-4">

                    <ColumnInputGroup name="file_type" :show-error="true" label="نوع الملف" container-classes="">
                        <div class="flex gap-4">
                            <div class="flex gap-2 items-center px-2 py-1 border rounded-md">
                                <Field id="file_type" name="file_type" type="radio" value="excel"
                                    v-model="exportModel.type" class="">
                                </Field>
                                <label for="file_type" class="text-lg font-semibold text-nowrap">Excel (xlsx)</label>
                            </div>
                            <div class="flex gap-2 items-center px-2 py-1 border rounded-md">
                                <Field id="file_type" name="file_type" type="radio" value="shapefile"
                                    v-model="exportModel.type" class="">
                                </Field>
                                <label for="file_type" class="text-lg font-semibold text-nowrap">Shape File</label>
                            </div>
                            <div class="flex gap-2 items-center px-2 py-1 border rounded-md">
                                <Field id="file_type" name="file_type" type="radio" value="kml"
                                    v-model="exportModel.type" class="">
                                </Field>
                                <label for="file_type" class="text-lg font-semibold text-nowrap">KML</label>
                            </div>
                        </div>
                    </ColumnInputGroup>

                    <ColumnInputGroup v-if="exportModel.type === 'excel'" name="excel_data_type_filter" :show-error="true"
                        label="نوع البيانات لملف الإكسل" container-classes="">
                        <div class="flex gap-4">
                            <div class="flex gap-2 items-center px-2 py-1 border rounded-md">
                                <Field id="excel_data_type_filter" name="excel_data_type_filter" type="radio"
                                    value="signs" v-model="exportModel.excel_data_type" class="">
                                </Field>
                                <label for="excel_data_type_filter" class="text-lg font-semibold text-nowrap">
                                    بيانات اللوائح
                                </label>
                            </div>
                            <div class="flex gap-2 items-center px-2 py-1 border rounded-md">
                                <Field id="excel_data_type_filter" name="excel_data_type_filter" type="radio"
                                    value="groups" v-model="exportModel.excel_data_type" class="">
                                </Field>
                                <label for="excel_data_type_filter" class="text-lg font-semibold text-nowrap">
                                    بيانات مجموعات اللوائح
                                </label>
                            </div>
                        </div>
                    </ColumnInputGroup>

                    <ColumnInputGroup name="options_filter" :show-error="true" label="الفلترة" container-classes="">
                        <div class="flex gap-4">
                            <div class="flex gap-2 items-center px-2 py-1 border rounded-md">
                                <Field id="options_filter" name="options_filter" type="radio" :value="false"
                                    v-model="chooseRoad" class="">
                                </Field>
                                <label for="options_filter" class="text-lg font-semibold text-nowrap">تحديد المحافظة و
                                    الولاية</label>
                            </div>
                            <div class="flex gap-2 items-center px-2 py-1 border rounded-md">
                                <Field id="options_filter" name="options_filter" type="radio" :value="true"
                                    v-model="chooseRoad" class="">
                                </Field>
                                <label for="options_filter" class="text-lg font-semibold text-nowrap">تحديد
                                    الطريق</label>
                            </div>
                        </div>
                    </ColumnInputGroup>

                    <ColumnInputGroup v-if="!chooseRoad" name="governorate" :show-error="true" label="المحافظة"
                        container-classes="md:w-full">
                        <Field id="governorate" name="governorate" type="text" v-model="exportModel.governorate"
                            v-slot="{ field }">
                            <select v-bind="field" class="dashboard-input w-full">
                                <option value="all">الكل</option>
                                <option v-for="option in governorates" :value="option.name_ar">
                                    {{ option.name_ar }}
                                </option>
                            </select>
                        </Field>
                    </ColumnInputGroup>

                    <ColumnInputGroup v-if="!chooseRoad" name="willayat" :show-error="true" label="الولاية"
                        container-classes="md:w-full">
                        <Field id="willayat" name="willayat" type="text" v-model="exportModel.willayat"
                            v-slot="{ field }">
                            <select v-bind="field" class="dashboard-input w-full">
                                <option value="all">الكل</option>
                                <option v-for="option in willayats" :value="option.name_ar">
                                    {{ option.name_ar }}
                                </option>
                            </select>
                        </Field>
                    </ColumnInputGroup>

                    <ColumnInputGroup v-if="chooseRoad" name="road" :show-error="true" label="الطريق"
                        container-classes="md:w-full">
                        <Field id="road" name="road" type="text" v-model="exportModel.road" v-slot="{ field }">
                            <select v-bind="field" class="dashboard-input w-full">
                                <option value="all">الكل</option>
                                <option v-for="option in roads" :value="option.name">
                                    {{ option.name }}
                                </option>
                            </select>
                        </Field>
                    </ColumnInputGroup>
                </div>

                <div class="flex items-center justify-between flex-row-reverse gap-2 self-end">

                    <!-- Submit button -->
                    <LoadingButton type="submit" :is-loading="submitLoading" class="flex items-center justify-center gap-2 p-2 rounded-md bg-brand text-light-brand
                            hover:bg-light-brand hover:text-brand focus:outline-none text-nowrap">
                        <div class="flex items-center justify-center gap-2">
                            تصدير
                            <SolidHeroIcon name="ArrowUpTrayIcon" classes="w-5 h-5" />
                        </div>
                    </LoadingButton>

                    <!-- Reset button -->
                    <button type="reset"
                        class="px-4 py-2 text-active-danger hover:text-light-danger hover:bg-danger bg-light-danger rounded-md">
                        <span>إعادة تعيين</span>
                    </button>

                </div>
            </Form>
        </div>

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
import { Field, Form } from 'vee-validate';
import ColumnInputGroup from '../form/ColumnInputGroup.vue';
import LoadingButton from '../form/LoadingButton.vue';
import { computed, onBeforeMount, ref } from 'vue';
import { object } from 'yup';
import { DetailedSign } from '@/core/types/data/DetailedSign';

type Willayat = {
    name_ar: string;
    shape_leng: string;
    shape_area: string;
    governorate_id: number;
}

type Governorate = {
    name_ar: string;
    willayats: Willayat[];
}

type Road = {
    name: string;
    type: string;
    number: string;
    class: string;
}

onBeforeMount(async () => {
    await ApiService.get('/api/governorates').then(res => {
        if (res.data.status === 'success') {
            governorates.value = res.data.data
        }
    })

    await ApiService.get('/api/roads').then(res => {
        if (res.data.status === 'success') {
            roads.value = res.data.data
        }
    })
})

const authStore = useAuthStore();

type ExportOptions = {
    type: 'shapefile' | 'excel' | 'kml' | '';
    governorate: string;
    willayat: string;
    road: string;
    excel_data_type: 'signs' | 'groups' | '';
}

const exportModel = ref<ExportOptions>({
    type: '',
    governorate: 'all',
    willayat: 'all',
    road: 'all',
    excel_data_type: ''
})

const chooseRoad = ref<boolean>(false)

const roads = ref<Road[]>([])
const governorates = ref<Governorate[]>([])
const willayats = computed<Willayat[]>(() => {
    if (exportModel.value.governorate != 'all') {
        return governorates.value.find(g => g.name_ar === exportModel.value.governorate)?.willayats ?? []
    } else {
        return []
    }
})

const submitLoading = ref<boolean>(false)
const validationSchema = computed(() => {
    return object().shape({})
});

const submitExport = async () => {
    submitLoading.value = true
    QSwal.fire('تصدير اللوحات ؟', `سيتم التصدير إلى ملف (${exportModel.value.type})`, 'question')
        .then(async (result) => {
            if (result.isConfirmed) {
                switch (exportModel.value.type) {
                    case 'excel':
                        await exportToExcel()
                        break
                    case 'kml':
                        await exportToKml()
                        break
                    case 'shapefile':
                        await exportToShapefile()
                        break
                    default:
                        break
                }
            }
        }).finally(() => {
            submitLoading.value = false
            exportModel.value.governorate = 'all'
            exportModel.value.willayat = 'all'
            exportModel.value.road = 'all'
        })
}

const exportToExcel = async () => {

    const formData = new FormData()
    formData.append('governorate', exportModel.value.governorate)
    formData.append('willayat', exportModel.value.willayat)
    formData.append('road', exportModel.value.road)
    formData.append('data_type', exportModel.value.excel_data_type)

    ApiService.setHeader(authStore.token as string, 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet')
    await ApiService.post(`/api/spa/signs/groups/export/excel`, formData, {
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

const exportToKml = async () => {

    const formData = new FormData();
    formData.append('governorate', exportModel.value.governorate);
    formData.append('willayat', exportModel.value.willayat);
    formData.append('road', exportModel.value.road);

    ApiService.setHeader(authStore.token as string, 'application/vnd.google-earth.kml+xml');
    const response = await ApiService.post(`/api/spa/signs/groups/export/kml`, formData, {
        responseType: 'arraybuffer', // Still needed for binary download
        headers: {
            'Accept': 'application/vnd.google-earth.kml+xml',
            'Content-Type': 'multipart/form-data'
        }
    }).then(response => {
        // Verify we got binary data
        if (!(response.data instanceof ArrayBuffer)) {
            throw new Error('Invalid response format');
        }

        // Create blob with KML MIME type
        const blob = new Blob([response.data], {
            type: 'application/vnd.google-earth.kml+xml'
        });

        // Get filename (same logic as Excel)
        let filename = 'RoadSigns_' + new Date().toISOString().split('T')[0] + '.kml';
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
    })
}

// const escapeArabic = (str: string) => {
//     return str.split('').map(c =>
//         c.charCodeAt(0) > 127 ? `\\u${c.charCodeAt(0).toString(16).padStart(4, '0')}` : c
//     ).join('');
// };

const exportToShapefile = async () => {

    const formData = new FormData();
    formData.append('governorate', exportModel.value.governorate);
    formData.append('willayat', exportModel.value.willayat);
    formData.append('road', exportModel.value.road);

    ApiService.setHeader(authStore.token as string, 'application/zip');
    const response = await ApiService.post(`/api/spa/signs/groups/export/shapefile`, formData, {
        responseType: 'arraybuffer', // Needed for binary files
        headers: {
            'Accept': 'application/zip',
            'Content-Type': 'multipart/form-data'
        }
    }).then(response => {
        // Verify we got binary data
        if (!(response.data instanceof ArrayBuffer)) {
            throw new Error('Invalid response format');
        }

        // Create blob with ZIP MIME type
        const blob = new Blob([response.data], { type: 'application/zip' });

        // Determine filename dynamically
        let filename = 'RoadSigns_' + new Date().toISOString().split('T')[0] + '.zip';
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
    })
        .catch((error: AxiosError<BackendResponseData>) => {
            MSwal.fire('خطأ غير متوقع!', getMessageFromObj(error), 'error');
        })
}

// const exportToShapefile = async () => {

//     const formData = new FormData();
//     formData.append('governorate', exportModel.value.governorate);
//     formData.append('willayat', exportModel.value.willayat);
//     formData.append('road', exportModel.value.road);

//     await ApiService.post('/api/spa/signs/detailed/export/shapefile', formData)
//         .then(async res => {
//             if (res.data?.status === 'success' && res.data?.data as DetailedSign[]) {
//                 const signs: DetailedSign[] = res.data.data
//                 const geojson: GeoJSON.FeatureCollection = {
//                     type: "FeatureCollection",
//                     features: signs.map((sign, i) => {
//                         // let signData = sign
//                         // for (let key in signData) {
//                         //     let val = typeof sign[key as keyof DetailedSign] === 'string'
//                         //         ? escapeArabic(sign[key as keyof DetailedSign] as string)
//                         //         : sign[key as keyof DetailedSign]

//                         //     signData[key as keyof DetailedSign] = val;
//                         // }
//                         return {
//                             type: "Feature",
//                             geometry: {
//                                 type: "Point",
//                                 coordinates: [Number(sign.longitude), Number(sign.latitude)]
//                             } as GeoJSON.Point,
//                             properties: {
//                                 id: sign.id,
//                                 name: sign.sign_name,
//                                 column: sign.sign_type,
//                                 color: sign.sign_color
//                             }
//                         }
//                     })
//                 }

//                 let nowDate = new Date()
//                 let folderName = `Signs_${nowDate.getDay()}-${nowDate.getMonth() + 1}-${nowDate.getFullYear()}`

//                 // const options = {
//                 //     types: { point: 'Signs' },
//                 //     dbf: {
//                 //         fields: [
//                 //             { name: 'id', type: 'N', size: 10 },
//                 //             { name: 'name', type: 'C', size: 254 }, // Larger field for Arabic
//                 //             { name: 'column', type: 'C', size: 254 },
//                 //             { name: 'color', type: 'C', size: 254 }
//                 //             // Add all other fields that may contain Arabic
//                 //         ],
//                 //         stringEncoder: (str: string) => {
//                 //             const buffer = new ArrayBuffer(str.length * 2);
//                 //             const view = new Uint8Array(buffer);
//                 //             for (let i = 0; i < str.length; i++) {
//                 //                 view[i] = str.charCodeAt(i) > 255 ? 63 : str.charCodeAt(i); // Fallback to '?'
//                 //             }
//                 //             return buffer;
//                 //         },
//                 //         encoding: 'UTF-8' // Critical for Arabic support
//                 //     }
//                 // };

//                 // window.shpwrite.download(geojson, options)

//                 window.shpwrite.download(geojson, {
//                     folder: folderName,
//                     types: {
//                         point: 'Signs'
//                     }
//                 })

//                 // Create and download a .cpg file to specify encoding
//                 createCPGFile("coordinates.cpg", "UTF-8");

//             }
//         })
//         .catch((error: AxiosError<BackendResponseData>) => {
//             MSwal.fire('خطأ غير متوقع!', getMessageFromObj(error), 'error');
//         })
// }

// function createCPGFile(fileName: string, encoding: string) {
//     const blob = new Blob([encoding], { type: "text/plain" });
//     const link = document.createElement("a");
//     link.href = URL.createObjectURL(blob);
//     link.download = fileName;
//     document.body.appendChild(link);
//     link.click();
//     document.body.removeChild(link);
// }

</script>

<style scoped>
.dashboard-input {
    padding: .25rem .25rem !important;
}
</style>