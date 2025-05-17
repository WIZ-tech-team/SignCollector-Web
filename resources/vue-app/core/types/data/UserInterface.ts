import { Media } from "@/core/types/data/Media";

export interface UserInterface {
    id: number;
    name: string;
    email: string;
    email_verified_at: string | null;
    password: string;
    phone: string;
    phone_verified_at: string;
    type: UserType;
    mobile_id: string;
    created_at: string;
    updated_at: string;
    avatar?: Media;
    roles?: Role[];
}

export type UserType = "User" | "Mobile" | "Admin";

export type RoleWithPermissions = {
    name: string;
    permissions: string[];
}

export type Role = {
    "name": string;
    "pivot": {
        "model_type": string;
        "model_id": number;
        "role_id": number;
    }
}