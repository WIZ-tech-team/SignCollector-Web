<template>
    <div class="flex flex-col align-start gap-8 justify-start">

        <!-- Detailed Signs Table -->
        <!-- Data Table: Detailed Signs -->
        <TableComponent title="بيانات الإشارات" :data-paginated="(signsPaginated as PaginatedData<DetailedSign>)"
            :columns="signsTableColumns">

            <template v-for="(sign, index) in signsPaginated?.data" v-slot:[`row_${index}_image_slot_value`]>
                <div v-if="sign?.image" class="h-16 w-16 object-cover rounded-full">
                    <img id='sign_image_preview' class="w-full h-full"
                        :src="sign.image.original_url" alt="photo" />
                </div>
            </template>

        </TableComponent>

    </div>
</template>

<script setup lang="ts">
import { computed, onBeforeMount, ref } from "vue";
import { useUsersStore } from "@/store/stores/usersStore";
import { TableColumn } from "@/core/types/elements/Table";
import { PaginatedData } from "@/core/types/data/PaginatedDataInterface";
import TableComponent from "@/components/table/TableComponent.vue";
import { useDetailedSignsStore } from "@/store/stores/detailedSignsStore";
import { DetailedSign } from "@/core/types/data/DetailedSign";

// Lifecycle hooks
onBeforeMount(async () => {
    await detailedSignsStore.fetchDetailedSignsPaginated();
});

// Stores
const detailedSignsStore = useDetailedSignsStore();


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
    }
]);

const signsPaginated = computed(() => {
    return detailedSignsStore.detailedSignsPaginated;
});

</script>