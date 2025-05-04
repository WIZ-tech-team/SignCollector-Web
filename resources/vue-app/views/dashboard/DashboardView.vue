<template>
    <div class="flex flex-col align-start gap-8 justify-start">

        <!-- Detailed Signs Table -->
        <!-- Data Table: Detailed Signs -->
        <TableComponent title="بيانات الإشارات" :data-paginated="(signsPaginated as PaginatedData<UserInterface>)"
            :columns="signsTableColumns">

            <template v-for="(sign, index) in signsPaginated?.data" v-slot:[`row_${index}_image_slot_value`]>
                <div class="shrink-0">
                    <img id='sign_image_preview' class="h-16 w-16 object-cover rounded-full"
                        :src="sign?.image_url" alt="photo" />
                </div>
            </template>

        </TableComponent>

    </div>
</template>

<script setup lang="ts">
import { computed, onBeforeMount, ref } from "vue";
import resolveConfig from "tailwindcss/resolveConfig";
import tailwindConfig from "@/../../tailwind.config";
import { useUsersStore } from "@/store/stores/usersStore";
import { TableColumn } from "@/core/types/elements/Table";
import { PaginatedData } from "@/core/types/data/PaginatedDataInterface";
import { UserInterface } from "@/core/types/data/UserInterface";
import TableComponent from "@/components/table/TableComponent.vue";
import { useDetailedSignsStore } from "@/store/stores/detailedSignsStore";

// Lifecycle hooks
onBeforeMount(async () => {
    await usersStore.fetchUsersPaginated('?type=Mobile');
    await detailedSignsStore.fetchDetailedSignsPaginated();
});

// Stores
const usersStore = useUsersStore();
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

const tcssConfig = resolveConfig(tailwindConfig);

const series1 = ref<ApexAxisChartSeries>([
    {
        name: 'Sales',
        data: [30, 40, 35, 50, 49, 60],
    },
    {
        name: 'Revenue',
        data: [10, 15, 15, 20, 10, 25],
    }
])

const signsPaginated = computed(() => {
    return detailedSignsStore.detailedSignsPaginated;
});

</script>