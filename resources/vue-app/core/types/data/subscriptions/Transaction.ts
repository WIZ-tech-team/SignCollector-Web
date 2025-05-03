export type Transaction = {
    id: number;
    billable_type: string;
    billable_id: number;
    paddle_id: string;
    paddle_subscription_id: string;
    invoice_number: string;
    status: string;
    total: number;
    tax: string;
    currency: string;
    billed_at: string;
    created_at: string;
    updated_at: string;
}