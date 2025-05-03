import { UserInterface } from "../UserInterface";
import { SubscriptionItem } from "./SubscriptionItem";
import { Transaction } from "./Transaction";

export type Subscription = {
    id: number;
    billable_type: string;
    billable_id: number;
    type: string;
    paddle_id: string;
    status: string;
    trial_ends_at: string;
    paused_at: string;
    ends_at: string;
    created_at?: string;
    updated_at?: string;
    items?: SubscriptionItem[];
    transactions?: Transaction[];
    billable?: UserInterface;
}