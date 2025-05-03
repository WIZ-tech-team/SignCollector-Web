export type DropdownMenuItem = {
    title: string;
    key: string; // for identifying the item
    isSlot?: boolean;
    classes?: string; 
    disabled?: boolean; 
}