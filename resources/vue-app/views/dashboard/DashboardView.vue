<template>
    <div class="flex flex-col align-start gap-8 justify-start">
        <!-- Cards -->
        <!-- <div class="flex align-middle gap-8 justify-between">
            <StatisticCard v-if="usersStatistics" :isIncreased="usersStatistics.status !== 'decreased'"
                title="App Users" :value="usersStatistics.total + ''"
                :indicator="((usersStatistics.month_difference / usersStatistics.total) * 100).toFixed(2) + '%'"
                :description="`${usersStatistics.status} compared to the last month by ${usersStatistics.month_difference} user.`"
                additionalClasses="w-[30%]" />
            <StatisticCard :isIncreased="true" title="Active Subscriptions" value="523" indicator="75%"
                description="93% of the app users." additionalClasses="w-[30%]" />
            <StatisticCard :isIncreased="true" title="Successfull Interactions" value="15821" indicator="95%"
                description="Number of successful interactions between users and app AI." additionalClasses="w-[30%]" />
        </div> -->

        <!-- Users Table -->
        <!-- Data Table: Users -->
        <TableComponent title="Mobile App Users" :data-paginated="(usersPaginated as PaginatedData<UserInterface>)"
            :columns="usersTableColumns"
            @on-next-page="usersStore.fetchUsersPaginated(`?page=${(usersPaginated?.current_page as number) + 1}&type=Mobile`)"
            @on-previous-page="usersStore.fetchUsersPaginated(`?page=${(usersPaginated?.current_page as number) - 1}&type=Mobile`)"
            @on-specific-page="(index) => usersStore.fetchUsersPaginated(`?page=${index}&type=Mobile`)">

            <template v-for="(user, index) in usersPaginated?.data" v-slot:[`row_${index}_avatar_slot_value`]>
                <div class="shrink-0">
                    <img id='avatar_preview_img' class="h-16 w-16 object-cover rounded-full"
                        :src="user?.avatar?.original_url" alt="avatar" />
                </div>
            </template>

        </TableComponent>

    </div>
</template>

<script setup lang="ts">
import { computed, onBeforeMount, ref } from "vue";
import { ApexOptions } from "apexcharts";
import colors from "tailwindcss/colors";
import resolveConfig from "tailwindcss/resolveConfig";
import tailwindConfig from "@/../../tailwind.config";
import { useUsersStore } from "@/store/stores/usersStore";
import { TableColumn } from "@/core/types/elements/Table";
import { PaginatedData } from "@/core/types/data/PaginatedDataInterface";
import { UserInterface } from "@/core/types/data/UserInterface";
import TableComponent from "@/components/table/TableComponent.vue";
import { ActiveSubscriptionsStatistic, MobileUsersStatistic, YearSubscriptions } from "@/core/types/data/Statistics";
import { useStatisticsStore } from "@/store/stores/statisticsStore";
import { MONTHS_MAP, MonthsMapKeys } from "@/core/constants/date";

// Lifecycle hooks
onBeforeMount(async () => {
    await usersStore.fetchUsersPaginated('?type=Mobile');
});

// Stores
const usersStore = useUsersStore();


const usersTableColumns = ref<TableColumn[]>([
    {
        title: 'ID',
        key: 'id'
    },
    {
        title: 'Avatar',
        key: 'avatar_slot',
        isSlot: true
    },
    {
        title: 'Name',
        key: 'name',
        valueClasses: "text-sm font-medium text-gray-900"
    },
    {
        title: 'Email',
        key: 'email'
    },
    {
        title: 'Phone',
        key: 'phone'
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

const usersPaginated = computed(() => {
    return usersStore.usersPaginated;
});

</script>