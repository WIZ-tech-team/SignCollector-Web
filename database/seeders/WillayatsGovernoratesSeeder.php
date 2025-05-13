<?php

namespace Database\Seeders;

use App\Models\Governorate;
use App\Models\Willayat;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class WillayatsGovernoratesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        $willGovData = json_decode(file_get_contents(storage_path('app/public/geo_data/willayats_governorates.geojson')), true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \Exception('Invalid GeoJSON file');
        }

        if (!isset($willGovData['type']) || $willGovData['type'] !== 'FeatureCollection') {
            throw new \Exception('Not a FeatureCollection GeoJSON');
        }

        DB::beginTransaction();

        try {
            
            Governorate::truncate();
            Willayat::truncate();

            foreach ($willGovData['features'] as $feature) {
                $gov = Governorate::where('name_ar', $feature['properties']['Govern'])->first();

                if (!$gov) {
                    $gov = Governorate::create([
                        'name_ar' => $feature['properties']['Govern']
                    ]);
                }

                $gov->willayats()->create([
                    'name_ar' => $feature['properties']['NAMEAR'] ?? null,
                    'shape_leng' => $feature['properties']['SHAPE_Leng'] ?? null,
                    'shape_area' => $feature['properties']['SHAPE_Area'] ?? null
                ]);
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
        }

    }
}
