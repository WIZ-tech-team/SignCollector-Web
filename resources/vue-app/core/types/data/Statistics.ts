export type MobileUsersStatistic = {
    total: number;
    month_difference: number;
    status: string;
}

export type ActiveSubscriptionsStatistic = {
    total: number;
    subscribed_users_percentage: number;
}

export type YearSubscriptions = {
    "1"?: number;
    "2"?: number;
    "3"?: number;
    "4"?: number;
    "5"?: number;
    "6"?: number;
    "7"?: number;
    "8"?: number;
    "9"?: number;
    "10"?: number;
    "11"?: number;
    "12"?: number;
}