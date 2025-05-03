import { SystemRoute } from "@/core/types/config/SystemRoutes"
import { UserType } from "@/core/types/data/UserInterface";

export type AsideMenuItem = {
    title: string;
    heroicon_name: string;
    to: SystemRoute;
    allowed_types: UserType[];
}

export type AsideSubmenu = {
    title: string;
    heroicon_name: string;
    is_open: boolean;
    items: AsideMenuItem[];
    allowed_types: UserType[];
}