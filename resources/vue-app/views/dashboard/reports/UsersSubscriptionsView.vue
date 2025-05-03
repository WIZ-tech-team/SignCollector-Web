<template>
    <div class="flex flex-col align-start gap-8 justify-start">
        <!-- Cards -->
        <!-- <div class="flex items-center gap-8 justify-start bg-white shadow-md rounded-md p-4 overflow-x-auto">
            <div class="p-0 w-[50%] h-[300px]">
                <apexchart type="pie" :options="chart2Options" :series="series2" height="100%"></apexchart>
            </div>
            <StatisticCard :isIncreased="true" title="Active Subscriptions" value="523" indicator="75%"
                description="93% of the app users."
                additionalClasses="bg-transparent shadow-none justify-between w-full" />
        </div> -->



        <!-- Data Table: Agents -->
        <TableComponent title="Users Subscriptions" :data-paginated="(tableDataTemp as PaginatedData<Subscription>)"
            :columns="subscriptionsTableColumns">

            <template #header_end>
                <div class="flex flex-col md:flex-row gap-4 items-start">
                    <ModalCard title="Export Subscriptions Data" btn-classes="px-4 py-2 bg-brand hover:bg-active-brand
                    flex flex-row gap-2 items-center justify-center rounded-md">
                        <template #open_button>
                            <SolidHeroIcon name="ArrowUpOnSquareIcon" classes="text-light-brand w-5 h-5" />
                            <span class="font-semibold text-md text-light-brand">Export</span>
                        </template>
                        <ExportForm :submit-loading="exportLoading" @submit="submitExport"></ExportForm>
                    </ModalCard>
                </div>
            </template>

            <!-- Custom Cell: user -->
            <template v-for="(datum, index) in tableDataTemp?.data" v-slot:[`row_${index}_user_slot_value`]>
                <span class="font-bold text-sm">
                    {{tableDataMapped.find(e => e.id === datum.id)?.user}}
                </span>
            </template>

            <!-- Custom Cell: items -->
            <template v-for="(datum, index) in tableDataTemp?.data" v-slot:[`row_${index}_plans_slot_value`]>
                <span class="text-sm">
                    {{tableDataMapped.find(e => e.id === datum.id)?.plans_text}}
                </span>
            </template>

            <!-- Custom Cell: transactions -->
            <template v-for="(datum, index) in tableDataTemp?.data" v-slot:[`row_${index}_transactions_slot_value`]>
                <span class="text-sm">
                    {{tableDataMapped.find(e => e.id === datum.id)?.transactions_text}}
                </span>
            </template>

        </TableComponent>

        <!-- Charts -->
    </div>
</template>

<script setup lang="ts">
import { computed, onBeforeMount, ref } from "vue";
import { useReportsStore } from "@/store/stores/reportsStore";
import { Subscription } from "@/core/types/data/subscriptions/Subscription";
import { PaginatedData } from "@/core/types/data/PaginatedDataInterface";
import SolidHeroIcon from "@/components/icons/SolidHeroIcon.vue";
import TableComponent from "@/components/table/TableComponent.vue";
import { TableColumn } from "@/core/types/elements/Table";
import ExportForm, { ExportDates } from "@/components/form/forms/ExportForm.vue";
import { MSwal, QSwal } from "@/core/plugins/SweetAlerts2";
import ApiService from "@/core/services/ApiService";
import { useAuthStore } from "@/store/stores/authStore";
import ModalCard from "@/components/cards/ModalCard.vue";
import { getMessageFromObj } from "@/assets/ts/swalMethods";
import { AxiosError } from "axios";
import { BackendResponseData } from "@/core/types/config/AxiosCustom";

onBeforeMount(async () => {
    await reportsStore.fetchReportData(tableDataTemp, "subscriptions");
})

// Stores
const reportsStore = useReportsStore();
const authStore = useAuthStore();

const exportLoading = ref(false)

const tableDataTemp = ref<PaginatedData<Subscription>>();

const subscriptionsTableColumns = ref<TableColumn[]>([
    {
        title: "ID",
        key: "id"
    },
    {
        title: "User",
        key: "user_slot",
        isSlot: true
    },
    {
        title: "Type",
        key: "type"
    },
    {
        title: "Status",
        key: "status"
    },
    {
        title: "Trial End",
        key: "trial_ends_at"
    },
    {
        title: "Paused Date",
        key: "paused_at"
    },
    {
        title: "Ends At",
        key: "ends_at"
    },

    {
        title: "Plans",
        key: "plans_slot",
        isSlot: true
    },
    {
        title: "Transactions",
        key: "transactions_slot",
        isSlot: true
    },
    {
        title: "Created At",
        key: "created_at"
    }
]);

const tableDataMapped = computed(() => {
    if (tableDataTemp.value) {
        let data = tableDataTemp.value.data as Subscription[];

        let mappedData = data.map(subscription => {
            let plansTxt = "";
            if (subscription.items) {
                let plans = subscription.items.map(item => {
                    return item.plan.name;
                });
                plansTxt = plans.join(',\n');
            }

            let transTxt = "";
            if (subscription.transactions) {
                let transactions = subscription.transactions.map(trans => {
                    return `${(trans.total / 100)} ${trans.currency} - ${trans.status}`;
                });
                transTxt = transactions.join(',\n');
            }

            return {
                id: subscription.id,
                user: subscription.billable?.name,
                type: subscription.type,
                status: subscription.status,
                trial_ends_at: subscription.trial_ends_at,
                paused_at: subscription.paused_at,
                ends_at: subscription.ends_at,
                created_at: subscription.created_at,
                plans_text: plansTxt,
                transactions_text: transTxt
            }

        })

        return mappedData;
    } else {
        return [];
    }
});

const exportFileHeaders = ref([
    'id',
    'user',
    'type',
    'status',
    'trial_ends_at',
    'paused_at',
    'ends_at',
    'created_at',
    'plans_text',
    'transactions_text'
]);

const exportKeys = computed(() => {
    return subscriptionsTableColumns.value.map(c => c.title);
});

const submitExport = async (dates: ExportDates) => {
    QSwal.fire('Export Subscriptions ?', 'The subscriptions in the selected date range will be exported as .xlsx Excel file.', 'question')
        .then(async (result) => {
            if (result.isConfirmed) {
                const formData = new FormData();
                formData.append('from', dates.from);
                formData.append('to', dates.to);
                for (let i = 0; i < exportFileHeaders.value.length; i++) {
                    formData.append(`headers[${i}]`, exportFileHeaders.value[i]);
                }

                ApiService.setHeader(authStore.token as string, 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet')
                await ApiService.post(`/api/spa/admin/reports/subscriptions/export`, formData, {
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