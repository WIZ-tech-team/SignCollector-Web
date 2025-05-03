import { Media } from "@/core/types/data/Media";

export type AIVoice = {
    id: number;
    name: string;
    is_available: boolean;
    elevenlabs_key: string;
    created_at?: string;
    updated_at?: string;
    image?: Media;
}