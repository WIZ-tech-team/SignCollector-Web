import { Media } from "@/core/types/data/Media";
import { SignInfo } from "@/core/types/data/SignInfo";

export type SignsGroup = {
    id: number;
    road_classification: string;
    road_name: string;
    road_number: string;
    road_type: string;
    road_direction: string;
    latitude: string;
    longitude: string;
    governorate: string;
    willayat: string;
    village: string;
    signs_count: number;
    columns_description: string;
    sign_location_from_road: string;
    sign_base: string;
    distance_from_road_edge_meter: number;
    sign_column_radius_mm: number;
    column_height: number;
    column_colour: string;
    column_type: string;
    comments: string;
    created_by: string;
    created_at: string;
    updated_at: string;
    photo_url?: string;
    image_url?: string;
    image: Media;
    images: Media[];
    image_urls: string[];
    gps_accuracy?: string;
    signs_info: SignInfo
}
