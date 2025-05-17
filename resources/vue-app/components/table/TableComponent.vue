<template>
  <div class="bg-white px-8 py-8 rounded-md flex flex-col items-start justify-start gap-6 h-full">
    <div class="flex items-middle gap-4 justify-between w-full flex-col sm:flex-row">
      <div>
        <span class="block font-bold text-lg">
          {{ title }}
        </span>
        <slot name="header_start_after_title"></slot>
      </div>
      <slot name="header_end"></slot>
    </div>
    <div class="overflow-x-auto bg-white rounded-lg shadow-md w-full">
      <table class="min-w-full divide-y divide-light-brand">
        <!-- Table Header -->
        <thead class="bg-brand">
          <tr>
            <slot name="before_columns"></slot>
            <th v-for="column in columns" scope="col"
              class="px-6 py-3 text-start text-xs font-medium text-light-brand uppercase tracking-wider"
              :class="column?.titleClasses">
              {{ column.title }}
            </th>
            <slot name="before_actions_column"></slot>
            <th v-if="allowActions.allow" scope="col"
              class="px-6 py-3 text-start text-xs font-medium text-light-brand uppercase tracking-wider">
              التحكم
            </th>
            <slot name="after_actions_column"></slot>
          </tr>
        </thead>

        <!-- Table Body -->
        <tbody class="bg-white divide-y divide-gray-200">
          <tr v-for="(datum, index) in dataPaginated?.data">
            <slot :name="`row_${index}_start`"></slot>
            <td v-for="column in columns" class="px-6 py-4 whitespace-nowrap">
              <slot v-if="column.isSlot" :name="`row_${index}_${column.key}_value`"></slot>
              <div v-else class="text-sm text-gray-500" :class="column?.valueClasses">
                {{ datum?.[column.key] ?? '--' }}
              </div>
            </td>
            <slot :name="`before_action_column_values_${index}`"></slot>
            <td v-if="allowActions.allow" class="px-6 py-4 whitespace-nowrap">
              <div class="px-2 py-1 flex flex-wrap gap-3">
                <button v-if="showEdit" type="button"
                  @click.prevent="onEdit(datum)" title="edit"
                  class="p-2 rounded-md bg-warning hover:bg-active-warning cursor-pointer">
                  <SolidHeroIcon name="PencilIcon" classes="w-4 h-4 text-light-warning" />
                </button>
                <button v-if="showArchive" type="button" @click.prevent="onArchive(datum)" title="archive"
                  class="p-2 rounded-md bg-primary hover:bg-active-primary cursor-pointer">
                  <SolidHeroIcon name="ArchiveBoxArrowDownIcon" classes="w-4 h-4 text-light-primary" />
                </button>
                <button v-if="showRestore" type="button" @click.prevent="onRestore(datum)" title="unarchive"
                  class="p-2 rounded-md bg-success hover:bg-active-success cursor-pointer">
                  <SolidHeroIcon name="ArrowUpCircleIcon" classes="w-4 h-4 text-light-success" />
                </button>
                <button v-if="showDelete" type="button" @click.prevent="onDelete(datum)" title="delete"
                  class="p-2 rounded-md bg-danger hover:bg-active-danger cursor-pointer">
                  <SolidHeroIcon name="TrashIcon" classes="w-4 h-4 text-light-danger" />
                </button>
              </div>
            </td>
            <slot :name="`row_${index}_end`"></slot>
          </tr>
        </tbody>

        <!-- Table Footer -->

      </table>
      <div class="px-4 py-2 w-full border-t border-gray-200">
        <PaginationControl v-if="dataPaginated" :last-page="dataPaginated.last_page"
          :current-page="dataPaginated.current_page" @on-previous="emits('onPreviousPage')"
          @on-next="emits('onNextPage')" @on-index-click="(index) => emits('onSpecificPage', index)" />
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { computed, PropType, toRefs } from "vue";
import { PaginatedData } from "@/core/types/data/PaginatedDataInterface";
import { AllowActions, TableColumn } from "@/core/types/elements/Table";
import SolidHeroIcon from "@/components/icons/SolidHeroIcon.vue";
import PaginationControl from "@/components/partials/PaginationControl.vue";
import { useAuthStore } from "@/store/stores/authStore";

// Props
const props = defineProps({
  title: {
    type: String,
    required: true
  },
  dataPaginated: {
    type: Object as PropType<PaginatedData<any>>,
    required: true
  },
  columns: {
    type: Object as PropType<TableColumn[]>,
    required: true
  },
  allowActions: {
    type: Object as PropType<AllowActions>,
    required: false,
    default: { allow: false }
  }
});
const { title, dataPaginated, columns, allowActions } = toRefs(props);

// Emits
const emits = defineEmits([
  'editClicked',
  'deleteClicked',
  'archiveClicked',
  'restoreClicked',
  'onPreviousPage',
  'onNextPage',
  'onSpecificPage'
]);

const authStore = useAuthStore()

const showEdit = computed(() => {
  return allowActions.value?.edit && (!allowActions.value?.editPermission || authStore.canUser(allowActions.value.editPermission));
})
const showDelete = computed(() => {
  return allowActions.value?.delete && (!allowActions.value?.deletePermission || authStore.canUser(allowActions.value.deletePermission));
})
const showArchive = computed(() => {
  return allowActions.value?.archive && (!allowActions.value?.archivePermission || authStore.canUser(allowActions.value.archivePermission));
})
const showRestore = computed(() => {
  return allowActions.value?.restore && (!allowActions.value?.restorePermission || authStore.canUser(allowActions.value.restorePermission));
})

const onEdit = (data?: any) => {
  emits('editClicked', data);
}
const onDelete = (data?: any) => {
  emits('deleteClicked', data);
}
const onArchive = (data?: any) => {
  emits('archiveClicked', data);
}
const onRestore = (data?: any) => {
  emits('restoreClicked', data);
}
</script>