<template>
    <DetailedSignForm :detailed-sign="detailedSign" :is-edit="true"></DetailedSignForm>
</template>

<script setup lang="ts">
import DetailedSignForm from '@/components/form/forms/DetailedSignForm.vue';
import { DetailedSign } from '@/core/types/data/DetailedSign';
import { ref } from 'vue';


const detailedSign = ref<DetailedSign>({
    "id": 32,
    "sign_name": "Left Bend",
    "sign_code": "101",
    "sign_code_gcc": "W1-1L",
    "sign_type": "\u062a\u062d\u0630\u064a\u0631\u064a\u0629",
    "sign_shape": "\u0645\u062b\u0644\u062b",
    "sign_length": 1,
    "sign_width": 1,
    "sign_radius": 0,
    "sign_color": "\u0623\u0632\u0631\u0642",
    "road_classification": "\u0634\u0631\u064a\u0627\u0646\u064a",
    "road_name": "\u0634\u0627\u0631\u0639 \u062a\u062c\u0631\u064a\u0628\u064a \u0627\u0644\u0648\u0627\u062d\u0629",
    "road_number": "KOM5",
    "road_type": "\u0645\u0632\u062f\u0648\u062c",
    "road_direction": "\u0627\u0644\u0631\u0633\u062a\u0627\u0642",
    "latitude": "23.56752290",
    "longitude": "58.17103860",
    "gps_accuracy": "3.80",
    "governorate": "\u0645\u062d\u0627\u0641\u0638\u0629 \u0645\u0633\u0642\u0637",
    "willayat": "\u0648\u0644\u0627\u064a\u0629 \u0627\u0644\u0633\u064a\u0628",
    "village": "\u0627\u0644\u0633\u064a\u0628",
    "signs_count": 1,
    "columns_description": "\u0639\u0645\u0648\u062f \u0648\u0627\u062d\u062f \u0628\u0644\u0648\u062d\u0629 \u0648\u0627\u062d\u062f\u0629",
    "sign_location_from_road": "\u064a\u0633\u0627\u0631",
    "sign_base": "\u0628\u0642\u0627\u0639\u062f\u0629 \u0627\u0633\u0645\u0646\u062a\u064a\u0629",
    "distance_from_road_edge_meter": 5,
    "sign_column_radius_mm": 500,
    "column_height": 5,
    "column_colour": "\u0623\u0628\u064a\u0636 \u0648\u0623\u0633\u0648\u062f",
    "column_type": "\u062d\u062f\u064a\u062f",
    "sign_content_shape_description": null,
    "sign_content_arabic_text": null,
    "sign_content_english_text": null,
    "sign_condition": "\u062c\u064a\u062f\u0629",
    "comments": "no comments now",
    "created_by": "qais",
    "created_at": "2025-05-12 12:58:05",
    "updated_at": "2025-05-12 12:59:46",
    "photo_url": "https:\/\/northsys.cloud\/storage\/57\/compressed_sign_1747054662808.jpg",
    "image_url": "https:\/\/northsys.cloud\/storage\/57\/compressed_sign_1747054662808.jpg",
    "image_log": "58.1710106",
    "image_lar": "23.5670322",
    "image_urls": [
        "https:\/\/northsys.cloud\/storage\/57\/compressed_sign_1747054662808.jpg",
        "https:\/\/northsys.cloud\/storage\/58\/compressed_sign_1747054668006.jpg"
    ],
    "images": [
        {
            "id": 57,
            "model_type": "App\\Models\\DetailedSign",
            "model_id": 32,
            "uuid": "e579e3ea-46f6-4f5a-8041-5c7144247838",
            "collection_name": "detailed_signs",
            "name": "compressed_sign_1747054662808",
            "file_name": "compressed_sign_1747054662808.jpg",
            "mime_type": "image\/jpeg",
            "disk": "public",
            "conversions_disk": "public",
            "size": 2488523,
            "manipulations": [],
            "custom_properties": [],
            "generated_conversions": [],
            "responsive_images": [],
            "order_column": 2,
            "created_at": "2025-05-12T12:58:09.000000Z",
            "updated_at": "2025-05-12T12:58:09.000000Z",
            "original_url": "https:\/\/northsys.cloud\/storage\/57\/compressed_sign_1747054662808.jpg",
            "preview_url": ""
        },
        {
            "id": 58,
            "model_type": "App\\Models\\DetailedSign",
            "model_id": 32,
            "uuid": "8e99e352-dca5-49c1-b8e1-9fe395eee999",
            "collection_name": "detailed_signs",
            "name": "compressed_sign_1747054668006",
            "file_name": "compressed_sign_1747054668006.jpg",
            "mime_type": "image\/jpeg",
            "disk": "public",
            "conversions_disk": "public",
            "size": 2891963,
            "manipulations": [],
            "custom_properties": [],
            "generated_conversions": [],
            "responsive_images": [],
            "order_column": 3,
            "created_at": "2025-05-12T12:58:09.000000Z",
            "updated_at": "2025-05-12T12:58:09.000000Z",
            "original_url": "https:\/\/northsys.cloud\/storage\/58\/compressed_sign_1747054668006.jpg",
            "preview_url": ""
        }
    ]
})
</script>