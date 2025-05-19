import { Media } from "@/core/types/data/Media";

export type DetailedSign = {
    id: number;
    sign_name: string;
    sign_code: string;
    sign_code_gcc: string;
    sign_type: string;
    sign_shape: string;
    sign_length: number;
    sign_width: number;
    sign_radius: number;
    sign_color: string;
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
    sign_content_shape_description: string|null;
    sign_content_arabic_text: string|null;
    sign_content_english_text: string|null;
    sign_condition: string;
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
}

export const detailedSignKeysTitles = [
    { title: 'المعرف', key: 'id' },
    { title: 'اسم الإشارة', key: 'sign_name' },
    { title: 'كود الإشارة', key: 'sign_code' },
    { title: 'كود الإشارة CGG', key: 'sign_code_gcc' },
    { title: 'نوع الإشارة', key: 'sign_type' },
    { title: 'شكل الإشارة', key: 'sign_shape' },
    { title: 'طول الإشارة', key: 'sign_length' },
    { title: 'عرض الإشارة', key: 'sign_width' },
    { title: 'نصف قطر الإشارة', key: 'sign_radius' },
    { title: 'لون الإشارة', key: 'sign_color' },
    { title: 'تصنيف الطريق', key: 'road_classification' },
    { title: 'اسم الطريق', key: 'road_name' },
    { title: 'رقم الطريق', key: 'road_number' },
    { title: 'نوع الطريق', key: 'road_type' },
    { title: 'اتجاه الطريق', key: 'road_direction' },
    { title: 'دائرة عرض', key: 'latitude' },
    { title: 'خط طول', key: 'longitude' },
    { title: 'المحافظة', key: 'governorate' },
    { title: 'الولاية', key: 'willayat' },
    { title: 'القرية', key: 'village' },
    { title: 'عدد الاشارات', key: 'signs_count' },
    { title: 'وصف الأعمدة', key: 'columns_description' },
    { title: 'الموقع من الطريق', key: 'sign_location_from_road' },
    { title: 'قاعدة الإشارة', key: 'sign_base' },
    { title: 'المسافة من حافة الطريق', key: 'distance_from_road_edge_meter' },
    { title: 'نصف قطر الإشارة بالميلي', key: 'sign_column_radius_mm' },
    { title: 'طول العمود', key: 'column_height' },
    { title: 'لون العمود', key: 'column_colour' },
    { title: 'نوع العمود', key: 'column_type' },
    { title: 'وصف شكل محتوى الإشارة', key: 'sign_content_shape_description' },
    { title: 'وصف الأشارة بالعربية', key: 'sign_content_arabic_text' },
    { title: 'وصف الإشارة بالإنجليزية', key: 'sign_content_english_text' },
    { title: 'حالة الإشارة', key: 'sign_condition' },
    { title: 'تعليقات', key: 'comments' },
    // { title: '', key: 'created_by' },
    { title: 'تاريخ الإضافة', key: 'created_at' }
    // { title: '', key: 'updated_at' },
    // { title: '', key: 'photo_url' },
    // { title: '', key: 'image_url' },
    // { title: '', key: 'image' }

]
// {
//         title: "المعرف",
//         key: "id",
//     },
//     {
//         title: "اسم اللوحة",
//         key: "sign_name"
//     },
//     {
//         title: "كود اللوحة",
//         key: "sign_code"
//     },