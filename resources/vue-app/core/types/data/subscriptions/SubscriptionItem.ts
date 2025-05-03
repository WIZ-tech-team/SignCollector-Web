import { Plan } from "@/core/types/data/subscriptions/Plan";

export type SubscriptionItem = {
    id: number;
    subscription_id: number;
    product_id: string;
    price_id: string;
    status: string;
    quantity: number;
    created_at?: string;
    updated_at?: string;
    plan: Plan;
}