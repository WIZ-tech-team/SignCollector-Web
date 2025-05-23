<template>
    <form @submit.prevent="onSubmit" class="flex h-[75vh] overflow-auto m-4">
        <div class="overflow-auto h-[90%] w-full">

            <CollabsableCard title="التفاصيل العامة" additional-classes="shadow-none">
                <div class="flex flex-col gap-4">
                    <template v-for="field in detailsFields">
                        <!-- Select -->
                        <div v-if="field.type === 'select'" class="flex flex-col border-r-4 pr-1">
                            <label class="font-medium mb-1">
                                {{ field.label }}
                            </label>
                            <select v-model="detailsModel[field.key as keyof SignsGroup]" :required="field.required"
                                :readonly="field.readonly || !isEdit" :disabled="field.readonly || !isEdit"
                                class="border rounded px-2 py-1 disabled:bg-gray-50 ring-0 outline-none">
                                <option disabled value="">— اختر من القائمة —</option>
                                <option v-for="opt in field.options" :key="opt" :value="opt"
                                    :selected="detailsModel[field.key as keyof SignsGroup] === opt">
                                    {{ opt }}
                                </option>
                            </select>
                        </div>

                        <!-- Textarea -->
                        <div v-else-if="field.type === 'textarea'" class="flex flex-col border-r-4 pr-1">
                            <label class="font-medium mb-1">
                                {{ field.label }}
                            </label>
                            <textarea rows="3" v-model="(detailsModel[field.key as keyof SignsGroup] as string)"
                                :required="field.required" :readonly="field.readonly || !isEdit"
                                class="border rounded px-2 py-1 read-only:bg-gray-50 ring-0 outline-none"></textarea>
                        </div>

                        <!-- Input -->
                        <div v-else class="flex flex-col border-r-4 pr-1">
                            <label class="font-medium mb-1">
                                {{ field.label }}
                            </label>
                            <input v-if="field.type === 'number'" :type="field.type"
                                v-model="detailsModel[field.key as keyof SignsGroup]" :required="field.required"
                                :readonly="field.readonly || !isEdit" step="any" min="0"
                                class="border rounded px-2 py-1 read-only:bg-gray-50 ring-0 outline-none" />
                            <input v-else :type="field.type" v-model="detailsModel[field.key as keyof SignsGroup]"
                                :required="field.required" :readonly="field.readonly || !isEdit"
                                class="border rounded px-2 py-1 read-only:bg-gray-50 ring-0 outline-none" />
                        </div>
                    </template>
                </div>
            </CollabsableCard>

            <template v-for="x in signsCount">
                <CollabsableCard v-if="signsInfoModel[x - 1]" :title="`معلومات الإشارة رقم (${x})`"
                    additional-classes="shadow-none">

                    <div class="flex flex-col gap-4">
                        <template v-for="field in signFields">
                            <!-- Select -->
                            <div v-if="field.type === 'select'" :id="`input_block_for_sign_${x}_${field.key}`"
                                class="flex flex-col border-r-4 pr-1">
                                <label class="font-medium mb-1">
                                    {{ field.label }}
                                </label>
                                <select :id="`input_for_sign_${x}_${field.key}`"
                                    @change="field.key === 'sign_name' ? handleSignNameInputChange(x) : null"
                                    v-model="signsInfoModel[x - 1][field.key as keyof SignInfo]"
                                    :required="field.required" :readonly="field.readonly || !isEdit"
                                    :disabled="field.readonly || !isEdit || signsInfoModel[x - 1]._disabled?.[field.key]"
                                    class="border rounded px-2 py-1 disabled:bg-gray-50 ring-0 outline-none">
                                    <option disabled value="">— اختر من القائمة —</option>
                                    <option v-for="opt in field.options" :key="opt" :value="opt"
                                        :selected="signsInfoModel[x - 1][field.key as keyof SignInfo] === opt">
                                        {{ opt }}
                                    </option>
                                </select>
                            </div>

                            <!-- Textarea -->
                            <div v-else-if="field.type === 'textarea'" :id="`input_block_for_sign_${x}_${field.key}`"
                                class="flex flex-col border-r-4 pr-1">
                                <label class="font-medium mb-1">
                                    {{ field.label }}
                                </label>
                                <textarea :id="`input_for_sign_${x}_${field.key}`" rows="3"
                                    v-model="(signsInfoModel[x - 1][field.key as keyof SignInfo] as string)"
                                    :required="field.required" :readonly="field.readonly || !isEdit"
                                    :disabled="field.readonly || !isEdit || signsInfoModel[x - 1]._disabled?.[field.key]"
                                    class="border rounded px-2 py-1 read-only:bg-gray-50 ring-0 outline-none"></textarea>
                            </div>

                            <!-- Input -->
                            <div v-else :id="`input_block_for_sign_${x}_${field.key}`"
                                class="flex flex-col border-r-4 pr-1">
                                <label class="font-medium mb-1">
                                    {{ field.label }}
                                </label>
                                <input v-if="field.type === 'number'" :id="`input_for_sign_${x}_${field.key}`"
                                    :type="field.type" v-model="signsInfoModel[x - 1][field.key as keyof SignInfo]"
                                    :required="field.required" :readonly="field.readonly || !isEdit"
                                    :disabled="field.readonly || !isEdit || signsInfoModel[x - 1]._disabled?.[field.key]"
                                    step="any" min="0"
                                    class="border rounded px-2 py-1 read-only:bg-gray-50 ring-0 outline-none" />
                                <input v-else :id="`input_for_sign_${x}_${field.key}`" :type="field.type"
                                    v-model="signsInfoModel[x - 1][field.key as keyof SignInfo]"
                                    :required="field.required" :readonly="field.readonly || !isEdit"
                                    :disabled="field.readonly || !isEdit || signsInfoModel[x - 1]._disabled?.[field.key]"
                                    class="border rounded px-2 py-1 read-only:bg-gray-50 ring-0 outline-none" />
                            </div>
                        </template>
                    </div>

                </CollabsableCard>
            </template>

            <button v-if="isEdit" type="submit" class="mt-4 px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                <span v-if="!submitLoading">Save Changes</span>
                <LoadingSpinner v-else />
            </button>
        </div>
    </form>
</template>

<script setup lang="ts">
import { computed, onBeforeMount, onMounted, onUpdated, PropType, ref, toRefs, watch } from 'vue';
import optionsJson from '@/assets/json/detailed_sign_options.json'
import signsJson from '@/assets/json/signs_updated.json'
import CollabsableCard from '@/components/cards/CollabsableCard.vue';
import { SignInfo } from '@/core/types/data/SignInfo';
import { SignsGroup } from '@/core/types/data/SignsGroup';
import LoadingSpinner from '@/components/form/LoadingSpinner.vue';
import { MSwal, QSwal } from '@/core/plugins/SweetAlerts2';
import ApiService from '@/core/services/ApiService';
import { AxiosError, AxiosResponse } from 'axios';
import { BackendResponseData } from '@/core/types/config/AxiosCustom';
import { getMessageFromObj } from '@/assets/ts/swalMethods';
import { useSignsGroupsStore } from '@/store/stores/signsGroupsStore';

const props = defineProps({
    isEdit: {
        type: Boolean,
        required: false,
        default: false
    },
    signsGroup: {
        type: Object as PropType<SignsGroup>,
        required: true
    }
})

const { isEdit, signsGroup } = toRefs(props)

// type DetailedSignGeneralDetails = {
//     id: string;
//     gps_accuracy: string;
//     latitude: string;
//     longitude: string;
//     governorate: string;
//     willayat: string;
//     village: string;
//     road_name: string;
//     road_classification: string;
//     road_number: string;
//     road_type: string;
//     road_direction: string;
//     signs_count: string;
//     columns_description: string;
//     sign_location_from_road: string;
//     sign_base: string;
//     distance_from_road_edge_meter: string;
//     sign_column_radius_mm: string;
//     column_height: string;
//     column_colour: string;
//     column_type: string;
//     comments: string;
//     created_by: string;
//     created_at: string;
//     updated_at: string;
// }

onBeforeMount(() => {
    detailsModel.value = { ...signsGroup.value }
    signsInfoModel.value = detailsModel.value.signs_info.sort((a, b) => {
        if (a?.id && b?.id) return a.id - b.id
    }).map(sign => {
        return {
            ...sign,
            sign_custom_name: sign.sign_name,
            sign_name: signsJson.find(s => s.Sign_Name === sign.sign_name) ? sign.sign_name : 'غير ذلك'
        }
    })
})

watch(() => signsGroup.value, () => {
    detailsModel.value = { ...signsGroup.value }
    signsInfoModel.value = detailsModel.value.signs_info.sort((a, b) => {
        if (a?.id && b?.id) return a.id - b.id
    }).map(sign => {
        return {
            ...sign,
            sign_custom_name: sign.sign_name,
            sign_name: signsJson.find(s => s.Sign_Name === sign.sign_name) ? sign.sign_name : 'غير ذلك'
        }
    })
})

// onMounted(() => {
//     signsInfoModel.value.forEach((info, index) => {
//         const selectSignElm = document.getElementById(`input_for_sign_${index + 1}_sign_name`) as HTMLSelectElement
//         if (selectSignElm) {
//             selectSignElm.addEventListener('change', () => handleSignNameInputChange(index + 1, 'sign_name'))
//         }
//     })
// })

// onUpdated(() => {
//     signsInfoModel.value.forEach((info, index) => {
//         const selectSignElm = document.getElementById(`input_for_sign_${index + 1}_sign_name`) as HTMLSelectElement
//         if (selectSignElm) {
//             selectSignElm.addEventListener('change', () => handleSignNameInputChange(index + 1, 'sign_name'))
//         }
//     })
// })

// type SignInfo = {
//     sign_name: string;
//     sign_code: string;
//     sign_code_gcc: string;
//     sign_type: string;
//     sign_shape: string;
//     sign_length: string;
//     sign_width: string;
//     sign_radius: string;
//     sign_color: string;
//     sign_content_shape_description: string;
//     sign_content_arabic_text: string;
//     sign_content_english_text: string;
//     sign_condition: string;
// }

// Stores
const signsGroupsStore = useSignsGroupsStore()

const detailsModel = ref<SignsGroup>({} as SignsGroup)
const signsInfoModel = ref<SignInfo[]>([])
const submitLoading = ref<boolean>(false)

const signsCount = computed<number>(() => {
    return signsInfoModel.value.length
})

const detailsFields = ref([
    // id
    {
        key: 'id',
        label: 'المعرف',
        type: 'text',
        options: null,
        required: true,
        readonly: true
    },
    // gps_accuracy
    {
        key: 'gps_accuracy',
        label: 'دقة الجي بي اس',
        type: 'text',
        options: null,
        required: false,
        readonly: true
    },
    // latitude
    {
        key: 'latitude',
        label: 'دائرة عرض',
        type: 'number',
        options: null,
        required: true,
        readonly: true
    },
    // longitude
    {
        key: 'longitude',
        label: 'خط طول',
        type: 'number',
        options: null,
        required: true,
        readonly: true
    },
    // governorate
    {
        key: 'governorate',
        label: 'المحافظة',
        type: 'text',
        options: null,
        required: false,
        readonly: true
    },
    // willayat
    {
        key: 'willayat',
        label: 'الولاية',
        type: 'text',
        options: null,
        required: false,
        readonly: true
    },
    // village
    {
        key: 'village',
        label: 'القرية',
        type: 'text',
        options: null,
        required: false,
        readonly: true
    },
    // road_name
    {
        key: 'road_name',
        label: 'اسم الطريق',
        type: 'text',
        options: null,
        required: false,
        readonly: true
    },
    // road_classification
    {
        key: 'road_classification',
        label: 'تصنيف الطريق',
        type: 'text',
        options: null,
        required: false,
        readonly: true
    },
    // road_number
    {
        key: 'road_number',
        label: 'رقم الطريق',
        type: 'text',
        options: null,
        required: false,
        readonly: true
    },
    // road_type
    {
        key: 'road_type',
        label: 'نوع الطريق',
        type: 'text',
        options: null,
        required: false,
        readonly: true
    },
    // road_direction
    {
        key: 'road_direction',
        label: 'الطريق نحو',
        type: 'text',
        options: null,
        required: true,
        readonly: false
    },
    // columns_description
    {
        key: 'columns_description',
        label: 'وصف الأعمدة',
        type: 'select',
        options: optionsJson.columns_description,
        required: true,
        readonly: false
    },
    // sign_location_from_road
    {
        key: 'sign_location_from_road',
        label: 'الموقع من الطريق',
        type: 'select',
        options: optionsJson.sign_location_from_road,
        required: true,
        readonly: false
    },
    // sign_base
    {
        key: 'sign_base',
        label: 'قاعدة الإشارة',
        type: 'select',
        options: optionsJson.sign_base,
        required: true,
        readonly: false
    },
    // distance_from_road_edge_meter
    {
        key: 'distance_from_road_edge_meter',
        label: 'المسافة من حافة الطريق بالمتر',
        type: 'number',
        options: null,
        required: true,
        readonly: false
    },
    // sign_column_radius_mm
    {
        key: 'sign_column_radius_mm',
        label: 'نصف قطر الإشارة بالميلي',
        type: 'number',
        options: null,
        required: true,
        readonly: false
    },
    // column_height
    {
        key: 'column_height',
        label: 'طول العمود',
        type: 'number',
        options: null,
        required: true,
        readonly: false
    },
    // column_colour
    {
        key: 'column_colour',
        label: 'لون العمود',
        type: 'select',
        options: optionsJson.column_colour,
        required: true,
        readonly: false
    },
    // column_type
    {
        key: 'column_type',
        label: 'نوع العمود',
        type: 'select',
        options: optionsJson.column_type,
        required: true,
        readonly: false
    },
    // comments
    {
        key: 'comments',
        label: 'تعليقات',
        type: 'textarea',
        options: null,
        required: false,
        readonly: false
    },
    // created_by
    {
        key: 'created_by',
        label: 'أنشئ بواسطة',
        type: 'text',
        options: null,
        required: false,
        readonly: true
    },
    // created_at
    {
        key: 'created_at',
        label: 'تاريخ الإضافة',
        type: 'text',
        options: null,
        required: false,
        readonly: true
    },
    // updated_at
    {
        key: 'updated_at',
        label: 'وقت التحديث',
        type: 'text',
        options: null,
        required: false,
        readonly: true
    },
    // signs_count
    {
        key: 'signs_count',
        label: 'عدد الاشارات',
        type: 'select',
        options: optionsJson.signs_count,
        required: true,
        readonly: false
    }
])

const signFields = ref([
    // sign_name
    {
        key: 'sign_name',
        label: 'الإشارة',
        type: 'select',
        options: signsJson.map(sign => sign.Sign_Name),
        required: true,
        readonly: false
    },
    // sign_custom_name
    {
        key: 'sign_custom_name',
        label: 'اسم الإشارة',
        type: 'text',
        options: null,
        required: true,
        readonly: false
    },
    // sign_code
    {
        key: 'sign_code',
        label: 'كود الإشارة (2010)',
        type: 'string',
        options: null,
        required: false,
        readonly: false
    },
    // sign_code_gcc
    {
        key: 'sign_code_gcc',
        label: 'كود الإشارة (GCC)',
        type: 'string',
        options: null,
        required: false,
        readonly: false
    },
    // sign_type
    {
        key: 'sign_type',
        label: 'نوع الإشارة',
        type: 'string',
        options: null,
        required: false,
        readonly: false
    },
    // sign_shape
    {
        key: 'sign_shape',
        label: 'شكل الإشارة',
        type: 'string',
        options: null,
        required: false,
        readonly: false
    },
    // sign_length
    {
        key: 'sign_length',
        label: 'طول الإشارة',
        type: 'number',
        options: null,
        required: false,
        readonly: false
    },
    // sign_width
    {
        key: 'sign_width',
        label: 'عرض الإشارة',
        type: 'number',
        options: null,
        required: false,
        readonly: false
    },
    // sign_radius
    {
        key: 'sign_radius',
        label: 'نصف قطر الإشارة',
        type: 'number',
        options: null,
        required: false,
        readonly: false
    },
    // sign_color
    {
        key: 'sign_color',
        label: 'لون الإشارة',
        type: 'select',
        options: optionsJson.sign_color,
        required: true,
        readonly: false
    },
    // sign_content_shape_description
    {
        key: 'sign_content_shape_description',
        label: 'وصف شكل محتوى الإشارة',
        type: 'text',
        options: null,
        required: false,
        readonly: false
    },
    // sign_content_arabic_text
    {
        key: 'sign_content_arabic_text',
        label: 'وصف الأشارة بالعربية',
        type: 'text',
        options: null,
        required: false,
        readonly: false
    },
    // sign_content_english_text
    {
        key: 'sign_content_english_text',
        label: 'وصف الإشارة بالإنجليزية',
        type: 'text',
        options: null,
        required: false,
        readonly: false
    },
    // sign_condition
    {
        key: 'sign_condition',
        label: 'حالة الإشارة',
        type: 'select',
        options: optionsJson.sign_condition,
        required: true,
        readonly: false
    }
])

watch(() => detailsModel.value.signs_count, () => {
    if (signsInfoModel.value.length > detailsModel.value.signs_count) {
        signsInfoModel.value = signsInfoModel.value.slice(0, detailsModel.value.signs_count)
    } else {
        for (let i = 0; i < detailsModel.value.signs_count; i++) {
            if (signsInfoModel.value.length === i) {
                signsInfoModel.value.push({
                    sign_name: "",
                    sign_code: "",
                    sign_code_gcc: "",
                    sign_type: "",
                    sign_shape: "",
                    sign_length: 0,
                    sign_width: 0,
                    sign_radius: 0,
                    sign_color: "",
                    sign_content_shape_description: "",
                    sign_content_arabic_text: "",
                    sign_content_english_text: "",
                    sign_condition: ""
                })
            }
        }
    }
})

// const handleSignNameInputChange = (order: number, key: string) => {
//     console.log('changed');

//     if (key === 'sign_name') {

//         const signCodeInput = document.getElementById(`input_for_sign_${order}_sign_code`) as HTMLInputElement
//         const signCodeGCCInput = document.getElementById(`input_for_sign_${order}_sign_code_gcc`) as HTMLInputElement
//         const signTypeInput = document.getElementById(`input_for_sign_${order}_sign_type`) as HTMLInputElement
//         const signShapeInput = document.getElementById(`input_for_sign_${order}_sign_shape`) as HTMLInputElement
//         const signCustomNameInput = document.getElementById(`input_for_sign_${order}_sign_custom_name`) as HTMLInputElement

//         if (signsInfoModel.value[order - 1].sign_name !== "غير ذلك") {
//             if (signCodeInput) {
//                 signCodeInput.value = signsJson.find(s => s.Sign_Name == signsInfoModel.value[order - 1].sign_name)?.Sign_Code_2010 ?? ''
//                 signCodeInput.disabled = true
//                 // signCodeInput.required = true
//             }

//             if (signCodeGCCInput) {
//                 signCodeGCCInput.value = signsJson.find(s => s.Sign_Name == signsInfoModel.value[order - 1].sign_name)?.Sign_Code_GCC ?? ''
//                 signCodeGCCInput.disabled = true
//                 // signCodeGCCInput.required = true
//             }

//             if (signTypeInput) {
//                 signTypeInput.value = signsJson.find(s => s.Sign_Name == signsInfoModel.value[order - 1].sign_name)?.Sign_Type ?? ''
//                 signTypeInput.disabled = true
//                 // signTypeInput.required = true
//             }

//             if (signShapeInput) {
//                 signShapeInput.value = signsJson.find(s => s.Sign_Name == signsInfoModel.value[order - 1].sign_name)?.Sign_Shape ?? ''
//                 signShapeInput.disabled = true
//                 // signShapeInput.required = true
//             }

//             if (signCustomNameInput) {
//                 signCustomNameInput.value = signsJson.find(s => s.Sign_Name == signsInfoModel.value[order - 1].sign_name)?.Sign_Name ?? ''
//                 signCustomNameInput.disabled = true
//                 // signCustomNameInput.required = true
//             }
//         } else {
//             if (signCodeInput) {
//                 signCodeInput.value = ''
//                 signCodeInput.disabled = false
//                 // signCodeInput.required = true
//             }

//             if (signCodeGCCInput) {
//                 signCodeGCCInput.value = ''
//                 signCodeGCCInput.disabled = false
//                 // signCodeGCCInput.required = true
//             }

//             if (signTypeInput) {
//                 signTypeInput.value = ''
//                 signTypeInput.disabled = false
//                 // signTypeInput.required = true
//             }

//             if (signShapeInput) {
//                 signShapeInput.value = ''
//                 signShapeInput.disabled = false
//                 // signShapeInput.required = true
//             }

//             if (signCustomNameInput) {
//                 signCustomNameInput.value = ''
//                 signCustomNameInput.disabled = false
//                 // signCustomNameInput.required = true
//             }
//         }
//     }
// }

const handleSignNameInputChange = (order: number) => {
    const currentSign = signsInfoModel.value[order - 1];
    const selectedSign = signsJson.find(s => s.Sign_Name === currentSign.sign_name);

    if (currentSign.sign_name !== "غير ذلك" && selectedSign) {
        signsInfoModel.value[order - 1] = {
            ...currentSign,
            sign_code: selectedSign.Sign_Code_2010,
            sign_code_gcc: selectedSign.Sign_Code_GCC,
            sign_type: selectedSign.Sign_Type,
            sign_shape: selectedSign.Sign_Shape,
            sign_custom_name: selectedSign.Sign_Name,
            // These fields should be disabled when sign_name is selected
            _disabled: {
                sign_code: true,
                sign_code_gcc: true,
                sign_type: true,
                sign_shape: true,
                sign_custom_name: true
            }
        };
    } else {
        signsInfoModel.value[order - 1] = {
            ...currentSign,
            sign_code: "",
            sign_code_gcc: "",
            sign_type: "",
            sign_shape: "",
            sign_custom_name: "",
            _disabled: {
                sign_code: false,
                sign_code_gcc: false,
                sign_type: false,
                sign_shape: false,
                sign_custom_name: false
            }
        };
    }
};

const onSubmit = () => {
    console.log("Details:\n", detailsModel.value)
    console.log("Signs:\n", signsInfoModel.value)

    submitLoading.value = true

    signsInfoModel.value.forEach(sign => {
        if (sign.sign_name === 'غير ذلك')
            sign.sign_name = sign?.sign_custom_name ?? sign.sign_name
    })

    // To get details excluding som eproperties
    const { photo_url, image_url, image_log, image_lar, image_urls, images, ...otherDetails } = detailsModel.value

    const data = {
        ...otherDetails,
        signs_info: [...signsInfoModel.value.map(sign => {
            let { created_at, updated_at, _disabled, ...other } = sign
            return other
        })]
    }

    console.log("data:\n", data)

    QSwal.fire('تحديث البيانات ؟', `سيتم تحديث البيانات المتعلقة بمجموعة اللوائح رقم (${data.id})`, 'question')
        .then(async result => {
            if (result.isConfirmed) {
                await ApiService.post(`/api/spa/signs/groups/${data.id}`, data)
                    .then((res: AxiosResponse<BackendResponseData>) => {
                        if (res.data.status === 'success')
                            MSwal.fire('تم', 'تم تحديث المجموعة بنجاح.', 'success')
                        else
                            MSwal.fire('رد غير متوقع !', getMessageFromObj(res), 'warning')
                    })
                    .catch((err: AxiosError<BackendResponseData>) => {
                        MSwal.fire('خطأ غير متوقع !', getMessageFromObj(err), 'error')
                    })
                    .finally(async () => {
                        await signsGroupsStore.fetchSignsGroupsPaginated(1)
                        signsInfoModel.value = signsInfoModel.value.map(sign => {
                            return {
                                ...sign,
                                sign_name: signsJson.find(s => s.Sign_Name === sign.sign_name) ? sign.sign_name : 'غير ذلك'
                            }
                        })
                        submitLoading.value = false
                    })
            }
        })

}

/**
 // photo_url
    {
        key: 'photo_url',
        label: '',
        type: 'text',
        options: [],
        required: false,
        readonly: true
    },
    // image_url
    {
        key: 'image_url',
        label: '',
        type: 'text',
        options: [],
        required: false,
        readonly: true
    },
    // image
    {
        key: 'image',
        label: '',
        type: 'text',
        options: [],
        required: false,
        readonly: true
    },
    // images
    {
        key: 'images',
        label: '',
        type: 'text',
        options: [],
        required: false,
        readonly: true
    },
    // image_urls
    {
        key: 'image_urls',
        label: '',
        type: 'text',
        options: [],
        required: false,
        readonly: true
    },
 */
</script>