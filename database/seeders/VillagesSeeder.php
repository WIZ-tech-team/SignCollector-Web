<?php

namespace Database\Seeders;

use App\Models\Village;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class VillagesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $villagesData = json_decode(file_get_contents(storage_path('app/public/geo_data/villages.geojson')), true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \Exception('Invalid GeoJSON file');
        }

        if (!isset($villagesData['type']) || $villagesData['type'] !== 'FeatureCollection') {
            throw new \Exception('Not a FeatureCollection GeoJSON');
        }

        DB::beginTransaction();

        try {
            Village::truncate();

            foreach ($villagesData['features'] as $feature) {
                Village::create([
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
