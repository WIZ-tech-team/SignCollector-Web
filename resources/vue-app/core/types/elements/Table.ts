export type TableColumn = {
    title: string;
    key: string;
    titleClasses?: string;
    valueClasses?: string;
    isSlot?: boolean;
}

export type AllowActions = {
    allow: boolean;
    edit?: boolean;
    editPermission?: string;
    delete?: boolean;
    deletePermission?: string;
    archive?: boolean;
    archivePermission?: string;
    restore?: boolean;
    restorePermission?: string;
}