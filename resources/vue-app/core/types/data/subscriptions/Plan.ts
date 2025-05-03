export type Plan = {
    id: number;
    paddle_product_id: string;
    name: string;
    description: string;
    status: string;
    custom_data: string;
    meta: string;
    created_at?: string;
    updated_at?: string;
}