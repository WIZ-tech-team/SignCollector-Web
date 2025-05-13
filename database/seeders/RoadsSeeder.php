<?php

namespace Database\Seeders;

use App\Models\Road;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoadsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roads = json_decode(file_get_contents(storage_path('app/public/geo_data/roads.geojson')), true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \Exception('Invalid GeoJSON file');
        }

        if (!isset($roads['type']) || $roads['type'] !== 'FeatureCollection') {
            throw new \Exception('Not a FeatureCollection GeoJSON');
        }

        DB::beginTransaction();

        try {
            Road::truncate();

            foreach ($roads['features'] as $feature) {
                Road::create([
                    'name' => $feature['properties']['Road_Name'] ?? null,
                    'number' => $feature['properties']['Road_Numb'] ?? null,
                    'type' => $feature['properties']['Road_Type'] ?? null,
                    'class' => $feature['properties']['Road_Class'] ?? null
                ]);
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
        }
    }
}
