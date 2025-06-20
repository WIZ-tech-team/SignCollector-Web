<?php

namespace Database\Seeders;

use App\Models\DetailedSign;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DetailedSignsSeeder extends Seeder
{
    public function run()
    {

        // Wipe out the old data
        DetailedSign::truncate();

        $rows = [
            [
                'id'                            => 1,
                'sign_name'                     => 'Left Bend',
                'sign_code'                     => '101',
                'sign_code_gcc'                 => 'W1-1L',
                'sign_type'                     => 'ارشادية',
                'sign_shape'                    => 'مثلث',
                'sign_length'                   => 1,
                'sign_width'                    => 1,
                'sign_radius'                   => 0,
                'sign_color'                    => 'أبيض',
                'road_classification'           => 'وطني',
                'road_name'                     => 'مسقط-الباطنة السريع',
                'road_number'                   => 'N1',
                'road_type'                     => 'أربع حارات',
                'road_direction'                => 'الى مسقط',
                'latitude'                      => 23.568214,
                'longitude'                     => 58.178929,
                'governorate'                   => 'محافظة مسقط',
                'willayat'                      => 'ولاية السيب',
                'village'                       => 'السيب',
                'signs_count'                   => 1,
                'columns_description'           => 'عمود واحد بلوحة واحدة',
                'sign_location_from_road'       => '2.5',
                'sign_base'                     => 'بقاعدة اسمنتية',
                'distance_from_road_edge_meter' => 2.5,
                'sign_column_radius_mm'         => 5,
                'column_height'                 => 2.5,
                'column_colour'                 => 'ابيض و  اسود',
                'column_type'                   => 'حديد',
                'sign_content_shape_description' => 'شكل',
                'sign_content_arabic_text'      => 'نص عربي',
                'sign_content_english_text'     => 'نص انجليزي',
                'sign_condition'                => 'جيدة',
                'comments'                      => null,
                'created_by'                    => 'qais'
            ],
            [
                'id'                            => 2,
                'sign_name'                     => 'Right Bend',
                'sign_code'                     => '102',
                'sign_code_gcc'                 => 'W1-1R',
                'sign_type'                     => 'ارشادية',
                'sign_shape'                    => 'مثلث',
                'sign_length'                   => 1,
                'sign_width'                    => 1,
                'sign_radius'                   => 0,
                'sign_color'                    => 'أبيض',
                'road_classification'           => 'وطني',
                'road_name'                     => 'مسقط-الباطنة السريع',
                'road_number'                   => 'N1',
                'road_type'                     => 'أربع حارات',
                'road_direction'                => 'الى مسقط',
                'latitude'                      => 23.568502,
                'longitude'                     => 58.176712,
                'governorate'                   => 'محافظة مسقط',
                'willayat'                      => 'ولاية بوشر',
                'village'                       => 'السيب',
                'signs_count'                   => 1,
                'columns_description'           => 'عمود واحد بلوحة واحدة',
                'sign_location_from_road'       => '2.5',
                'sign_base'                     => 'بقاعدة اسمنتية',
                'distance_from_road_edge_meter' => 2.5,
                'sign_column_radius_mm'         => 5,
                'column_height'                 => 2.5,
                'column_colour'                 => 'ابيض و  اسود',
                'column_type'                   => 'حديد',
                'sign_content_shape_description' => 'شكل',
                'sign_content_arabic_text'      => 'نص عربي',
                'sign_content_english_text'     => 'نص انجليزي',
                'sign_condition'                => 'جيدة',
                'comments'                      => null,
                'created_by'                    => 'naif'
            ],
            [
                'id'                            => 3,
                'sign_name'                     => 'Series Of Bends First To Left',
                'sign_code'                     => '103',
                'sign_code_gcc'                 => 'W1-3L',
                'sign_type'                     => 'ارشادية',
                'sign_shape'                    => 'مثلث',
                'sign_length'                   => 1,
                'sign_width'                    => 1,
                'sign_radius'                   => 0,
                'sign_color'                    => 'أبيض',
                'road_classification'           => 'شرياني',
                'road_name'                     => 'البريمي-محضة-الروضة',
                'road_number'                   => 'A3',
                'road_type'                     => 'مفرد',
                'road_direction'                => 'الى مسقط',
                'latitude'                      => 23.56875,
                'longitude'                     => 58.174836,
                'governorate'                   => 'محافظة البريمي',
                'willayat'                      => 'ولاية السنينة',
                'village'                       => 'السيب',
                'signs_count'                   => 1,
                'columns_description'           => 'عمود واحد بلوحة واحدة',
                'sign_location_from_road'       => '2.5',
                'sign_base'                     => 'بقاعدة اسمنتية',
                'distance_from_road_edge_meter' => 2.5,
                'sign_column_radius_mm'         => 5,
                'column_height'                 => 2.5,
                'column_colour'                 => 'ابيض و  اسود',
                'column_type'                   => 'حديد',
                'sign_content_shape_description' => 'شكل',
                'sign_content_arabic_text'      => 'نص عربي',
                'sign_content_english_text'     => 'نص انجليزي',
                'sign_condition'                => 'جيدة',
                'comments'                      => null,
                'created_by'                    => 'thuraiya'
            ],
            [
                'id'                            => 4,
                'sign_name'                     => 'Series Of Bends First To Right',
                'sign_code'                     => '104',
                'sign_code_gcc'                 => 'W1-3R',
                'sign_type'                     => 'ارشادية',
                'sign_shape'                    => 'مثلث',
                'sign_length'                   => 1,
                'sign_width'                    => 1,
                'sign_radius'                   => 0,
                'sign_color'                    => 'أبيض',
                'road_classification'           => 'وطني',
                'road_name'                     => 'مسقط-الباطنة السريع',
                'road_number'                   => 'N1',
                'road_type'                     => 'أربع حارات',
                'road_direction'                => 'الى مسقط',
                'latitude'                      => 23.568996,
                'longitude'                     => 58.1733,
                'governorate'                   => 'محافظة مسقط',
                'willayat'                      => 'ولاية بوشر',
                'village'                       => 'السيب',
                'signs_count'                   => 1,
                'columns_description'           => 'عمود واحد بلوحة واحدة',
                'sign_location_from_road'       => '2.5',
                'sign_base'                     => 'بقاعدة اسمنتية',
                'distance_from_road_edge_meter' => 2.5,
                'sign_column_radius_mm'         => 5,
                'column_height'                 => 2.5,
                'column_colour'                 => 'ابيض و  اسود',
                'column_type'                   => 'حديد',
                'sign_content_shape_description' => 'شكل',
                'sign_content_arabic_text'      => 'نص عربي',
                'sign_content_english_text'     => 'نص انجليزي',
                'sign_condition'                => 'جيدة',
                'comments'                      => null,
                'created_by'                    => 'qais'
            ],
            [
                'id'                            => 5,
                'sign_name'                     => 'Steep Descent',
                'sign_code'                     => '105',
                'sign_code_gcc'                 => 'W2-1',
                'sign_type'                     => 'ارشادية',
                'sign_shape'                    => 'مثلث',
                'sign_length'                   => 1,
                'sign_width'                    => 1,
                'sign_radius'                   => 0,
                'sign_color'                    => 'أبيض',
                'road_classification'           => 'شرياني',
                'road_name'                     => 'البريمي-محضة-الروضة',
                'road_number'                   => 'A3',
                'road_type'                     => 'مفرد',
                'road_direction'                => 'الى مسقط',
                'latitude'                      => 23.569133,
                'longitude'                     => 58.172122,
                'governorate'                   => 'محافظة البريمي',
                'willayat'                      => 'ولاية السنينة',
                'village'                       => 'السيب',
                'signs_count'                   => 1,
                'columns_description'           => 'عمود واحد بلوحة واحدة',
                'sign_location_from_road'       => '2.5',
                'sign_base'                     => 'بقاعدة اسمنتية',
                'distance_from_road_edge_meter' => 2.5,
                'sign_column_radius_mm'         => 5,
                'column_height'                 => 2.5,
                'column_colour'                 => 'ابيض و  اسود',
                'column_type'                   => 'حديد',
                'sign_content_shape_description' => 'شكل',
                'sign_content_arabic_text'      => 'نص عربي',
                'sign_content_english_text'     => 'نص انجليزي',
                'sign_condition'                => 'جيدة',
                'comments'                      => null,
                'created_by'                    => 'qais'
            ],
            [
                'id'                            => 6,
                'sign_name'                     => 'Steep Ascent',
                'sign_code'                     => '106',
                'sign_code_gcc'                 => 'W2-2',
                'sign_type'                     => 'ارشادية',
                'sign_shape'                    => 'مثلث',
                'sign_length'                   => 1,
                'sign_width'                    => 1,
                'sign_radius'                   => 0,
                'sign_color'                    => 'أبيض',
                'road_classification'           => 'وطني',
                'road_name'                     => 'مسقط-الباطنة السريع',
                'road_number'                   => 'N1',
                'road_type'                     => 'أربع حارات',
                'road_direction'                => 'الى مسقط',
                'latitude'                      => 23.569224,
                'longitude'                     => 58.171809,
                'governorate'                   => 'محافظة مسقط',
                'willayat'                      => 'ولاية السيب',
                'village'                       => 'السيب',
                'signs_count'                   => 1,
                'columns_description'           => 'عمود واحد بلوحة واحدة',
                'sign_location_from_road'       => '2.5',
                'sign_base'                     => 'بقاعدة اسمنتية',
                'distance_from_road_edge_meter' => 2.5,
                'sign_column_radius_mm'         => 5,
                'column_height'                 => 2.5,
                'column_colour'                 => 'ابيض و  اسود',
                'column_type'                   => 'حديد',
                'sign_content_shape_description' => 'شكل',
                'sign_content_arabic_text'      => 'نص عربي',
                'sign_content_english_text'     => 'نص انجليزي',
                'sign_condition'                => 'جيدة',
                'comments'                      => null,
                'created_by'                    => 'naif'
            ],
            [
                'id'                            => 7,
                'sign_name'                     => 'Carriageway Narrows both Sides',
                'sign_code'                     => '107',
                'sign_code_gcc'                 => 'W3-1',
                'sign_type'                     => 'ارشادية',
                'sign_shape'                    => 'مثلث',
                'sign_length'                   => 1,
                'sign_width'                    => 1,
                'sign_radius'                   => 0,
                'sign_color'                    => 'أبيض',
                'road_classification'           => 'وطني',
                'road_name'                     => 'مسقط-الباطنة السريع',
                'road_number'                   => 'N1',
                'road_type'                     => 'أربع حارات',
                'road_direction'                => 'الى مسقط',
                'latitude'                      => 23.569359,
                'longitude'                     => 58.170539,
                'governorate'                   => 'محافظة مسقط',
                'willayat'                      => 'ولاية السيب',
                'village'                       => 'السيب',
                'signs_count'                   => 1,
                'columns_description'           => 'عمود واحد بلوحة واحدة',
                'sign_location_from_road'       => '2.5',
                'sign_base'                     => 'بقاعدة اسمنتية',
                'distance_from_road_edge_meter' => 2.5,
                'sign_column_radius_mm'         => 5,
                'column_height'                 => 2.5,
                'column_colour'                 => 'ابيض و  اسود',
                'column_type'                   => 'حديد',
                'sign_content_shape_description' => 'شكل',
                'sign_content_arabic_text'      => 'نص عربي',
                'sign_content_english_text'     => 'نص انجليزي',
                'sign_condition'                => 'جيدة',
                'comments'                      => null,
                'created_by'                    => 'maryam'
            ],
            [
                'id'                            => 8,
                'sign_name'                     => 'Carriageway Narrows From Left',
                'sign_code'                     => '108',
                'sign_code_gcc'                 => 'W3-3',
                'sign_type'                     => 'ارشادية',
                'sign_shape'                    => 'مثلث',
                'sign_length'                   => 1,
                'sign_width'                    => 1,
                'sign_radius'                   => 0,
                'sign_color'                    => 'أبيض',
                'road_classification'           => 'وطني',
                'road_name'                     => 'مسقط-الباطنة السريع',
                'road_number'                   => 'N1',
                'road_type'                     => 'أربع حارات',
                'road_direction'                => 'الى مسقط',
                'latitude'                      => 23.569507,
                'longitude'                     => 58.169591,
                'governorate'                   => 'محافظة مسقط',
                'willayat'                      => 'ولاية السيب',
                'village'                       => 'السيب',
                'signs_count'                   => 1,
                'columns_description'           => 'عمود واحد بلوحة واحدة',
                'sign_location_from_road'       => '2.5',
                'sign_base'                     => 'بقاعدة اسمنتية',
                'distance_from_road_edge_meter' => 2.5,
                'sign_column_radius_mm'         => 5,
                'column_height'                 => 2.5,
                'column_colour'                 => 'ابيض و  اسود',
                'column_type'                   => 'حديد',
                'sign_content_shape_description' => 'شكل',
                'sign_content_arabic_text'      => 'نص عربي',
                'sign_content_english_text'     => 'نص انجليزي',
                'sign_condition'                => 'جيدة',
                'comments'                      => null,
                'created_by'                    => 'qais'
            ]
        ];

        foreach ($rows as $data) {
            $sign = DetailedSign::create($data);
            $path = public_path("storage/test_image.png");
            if (file_exists($path)) {
                $sign->addMedia($path)
                    ->preservingOriginal()
                    ->toMediaCollection('detailed_signs');
                $sign->addMedia($path)
                    ->preservingOriginal()
                    ->toMediaCollection('detailed_signs');
            }
        }
    }
}
