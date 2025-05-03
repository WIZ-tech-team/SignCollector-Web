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
    delete?: boolean;
    archive?: boolean;
    restore?: boolean;
}