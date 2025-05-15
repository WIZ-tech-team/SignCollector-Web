<template>
    <div class="flex flex-col align-start gap-8 justify-start page-container">

        <!-- Cards -->
        <!-- <div class="flex align-middle gap-8 justify-between">
            <StatisticCard :isIncreased="true" title="App Users" value="574" indicator="20%"
                description="Increase compared to the last month by 144 user." additionalClasses="w-[100%]" />
        </div> -->

        <!-- Data Table: Users -->
        <TableComponent v-if="!showUserForm" :title="tableTitle"
            :data-paginated="(usersPaginated as PaginatedData<UserInterface>)" :columns="usersTableColumns"
            :allow-actions="usersTableAllowedActions"
            @on-next-page="usersStore.fetchUsersPaginated(`?page=${(usersPaginated?.current_page as number) + 1}`)"
            @on-previous-page="usersStore.fetchUsersPaginated(`?page=${(usersPaginated?.current_page as number) - 1}`)"
            @on-specific-page="(index) => usersStore.fetchUsersPaginated(`?page=${index}`)" @edit-clicked="onEditUser"
            @delete-clicked="deleteUser" @archive-clicked="archiveUser" @restore-clicked="restoreUser">

            <template #header_end>
                <div class="flex flex-col md:flex-row gap-4 items-start">
                    <button @click.prevent="createUser = true" class="px-4 py-2 bg-brand hover:bg-active-brand
                    flex flex-row gap-2 items-center justify-center rounded-md">
                        <SolidHeroIcon name="PlusIcon" classes="text-light-brand w-5 h-5" />
                        <span class="font-semibold text-md text-light-brand">حساب جديد</span>
                    </button>
                    <DropdownMenu :items="dropdownMenuItems" @item-clicked="onDropdownItemClicked"></DropdownMenu>
                </div>
            </template>

            <!-- Custom Cell: avatar -->
            <template v-for="(user, index) in usersPaginated?.data" v-slot:[`row_${index}_avatar_slot_value`]>
                <div class="shrink-0">
                    <img id='avatar_preview_img' class="h-16 w-16 object-cover rounded-full"
                        :src="user?.avatar?.original_url" alt="avatar" />
                </div>
            </template>

            <!-- Custom Cell: type -->
            <template v-for="(user, index) in usersPaginated?.data" v-slot:[`row_${index}_type_slot_value`]>
                <span class="px-2 py-1 text-xs font-semibold rounded-full" :class="{
                    'bg-light-success text-success': user.type === 'User',
                    'bg-light-primary text-primary': user.type === 'Mobile',
                    'bg-danger text-light-danger': user.type === 'Admin',
                }">
                    {{ user.type }}
                </span>
            </template>

        </TableComponent>

        <!-- Form: User -->
        <UserForm v-if="showUserForm" :userToEdit="userToEdit" @hideForm="onHideUserForm"></UserForm>

    </div>
</template>

<script setup lang="ts">
import { computed, onBeforeMount, ref } from "vue";
import UserForm from "@/components/table/forms/UserForm.vue";
import { useUsersStore } from "@/store/stores/usersStore";
import { UserInterface } from "@/core/types/data/UserInterface";
import TableComponent from "@/components/table/TableComponent.vue";
import { PaginatedData } from "@/core/types/data/PaginatedDataInterface";
import { AllowActions, TableColumn } from "@/core/types/elements/Table";
import SolidHeroIcon from "@/components/icons/SolidHeroIcon.vue";
import DropdownMenu from "@/components/partials/DropdownMenu.vue";
import { DropdownMenuItem } from "@/core/types/elements/DropDownMenu";

// Lifecycle hooks
onBeforeMount(async () => {
    await usersStore.fetchUsersPaginated();
});

// Stores
const usersStore = useUsersStore();

const userToEdit = ref<UserInterface | null>(null);
const createUser = ref<boolean>(false);
const displayedUsers = ref<'archived' | 'not-archived'>('not-archived');

const usersTableColumns = ref<TableColumn[]>([
    {
        title: 'المعرّف',
        key: 'id'
    },
    {
        title: 'الصورة الشخصية',
        key: 'avatar_slot',
        isSlot: true
    },
    {
        title: 'الاسم',
        key: 'name',
        valueClasses: "text-sm font-medium text-gray-900"
    },
    {
        title: 'الإيميل',
        key: 'email'
    },
    {
        title: 'رقم الهاتف',
        key: 'phone'
    },
    {
        title: 'النوع',
        key: 'type_slot',
        isSlot: true
    }
]);
const usersTableAllowedActions = computed(() => {
    return {
        allow: true,
        delete: displayedUsers.value === 'archived',
        edit: displayedUsers.value === 'not-archived',
        archive: displayedUsers.value === 'not-archived',
        restore: displayedUsers.value === 'archived'
    }
});

const dropdownMenuItems = ref<DropdownMenuItem[]>([
    {
        title: 'المستخدمين',
        key: 'all'
    },
    {
        title: 'المسؤولين',
        key: 'admin'
    },
    {
        title: 'مستخدمين التطبيق',
        key: 'mobile'
    },
    {
        title: 'الحسابات المؤرشفة',
        key: 'archived',
        classes: 'border-t disabled:bg-gray-50 disabled:text-gray-500'
    }
])

const usersPaginated = computed(() => {
    return displayedUsers.value === 'not-archived' ? usersStore.usersPaginated : usersStore.archivedUsersPaginated;
});

const tableTitle = computed(() => {
    return displayedUsers.value === 'not-archived' ? 'المستخدمين' : 'حسابات المستخدمين المؤرشفة';
})

const showUserForm = computed(() => {
    if (userToEdit.value || createUser.value) {
        return true;
    }
});

const onEditUser = (user: UserInterface) => {
    userToEdit.value = { ...user };
}

const onHideUserForm = () => {
    userToEdit.value = null;
    createUser.value = false;
}

const deleteUser = (user: UserInterface) => {
    usersStore.removeUser(user.id, 'delete');
}

const archiveUser = (user: UserInterface) => {
    usersStore.removeUser(user.id, 'archive');
}

const restoreUser = (user: UserInterface) => {
    usersStore.restoreUser(user.id);
    // .finally(() => {
    //     displayedUsers.value = 'not-archived';
    //     usersStore.fetchUsersPaginated();
    // });
}

const onDropdownItemClicked = (itemKey: string) => {
    switch (itemKey) {
        case 'all':
            displayedUsers.value = 'not-archived';
            usersStore.fetchUsersPaginated();
            break;
        case 'admin':
            displayedUsers.value === 'not-archived' ?
                usersStore.fetchUsersPaginated(`?type=Admin`) :
                usersStore.fetchArchivedUsersPaginated(`?type=Admin`);
            break;
        case 'mobile':
            displayedUsers.value === 'not-archived' ?
                usersStore.fetchUsersPaginated(`?type=Mobile`) :
                usersStore.fetchArchivedUsersPaginated(`?type=Mobile`);
            break;
        case 'archived':
            displayedUsers.value = 'archived';
            usersStore.fetchArchivedUsersPaginated();
            break;
        default:
            usersStore.fetchUsersPaginated();
    }
}

</script>