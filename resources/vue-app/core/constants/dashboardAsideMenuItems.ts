import { AsideMenuItem, AsideSubmenu } from "@/core/types/config/AsideMenuItem";

export const DASHBOARD_ASIDE_MENU_ITEMS: Array<AsideMenuItem|AsideSubmenu> = [
    {
        title: "الرئيسية",
        heroicon_name: "ComputerDesktopIcon",
        to: '/dashboard',
        allowed_types: ['Admin']
    },
    // {
    //     title: "الخريطة",
    //     heroicon_name: "MapIcon",
    //     to: '/map',
    //     allowed_types: ['Admin']
    // },
    {
        title: "المستخدمين",
        heroicon_name: "UserGroupIcon",
        to: '/users',
        allowed_types: ['Admin']
    }
    // {
    //     title: "Reports",
    //     heroicon_name: "ClipboardDocumentIcon",
    //     is_open: false,
    //     items: [
    //         {
    //             title: "Subscriptions",
    //             heroicon_name: "",
    //             to: '/reports/subscriptions',
    //             allowed_types: ['Admin']
    //         },
    //         // {
    //         //     title: "Usage",
    //         //     heroicon_name: "",
    //         //     to: '/reports/usage',
    //         //     allowed_types: ['Admin']
    //         // },
    //         // {
    //         //     title: "Sales",
    //         //     heroicon_name: "",
    //         //     to: '/reports/sales',
    //         //     allowed_types: ['Admin']
    //         // }
    //     ],
    //     allowed_types: ['Admin']
    // }
];