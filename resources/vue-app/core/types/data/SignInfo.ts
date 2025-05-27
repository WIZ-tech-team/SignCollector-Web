import { Media } from "@/core/types/data/Media";

export type SignInfo = {
    id?: number;
    sign_name: string;
    sign_custom_name?: string;
    sign_code: string;
    sign_code_gcc: string;
    sign_type: string;
    sign_shape: string;
    sign_length: number;
    sign_width: number;
    sign_radius: number;
    sign_color: string;
    sign_content_shape_description: string | null;
    sign_content_arabic_text: string | null;
    sign_content_english_text: string | null;
    sign_condition: string;
    created_at?: string;
    updated_at?: string;
    _disabled?: Record<string, boolean>;
    _hidden?: Record<string, boolean>;
}
