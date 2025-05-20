<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SignsGroupResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        // grab every media item in the 'signs_groups' collection
        $allMedia = $this->getMedia('signs_groups');

        // first image URL (or null)
        $firstUrl = optional($allMedia->first())->getUrl();

        return [
            // ← include every model field explicitly
            'id'                              => $this->id,

            'road_classification'             => $this->road_classification,
            'road_name'                       => $this->road_name,
            'road_number'                     => $this->road_number,
            'road_type'                       => $this->road_type,
            'road_direction'                  => $this->road_direction,

            'latitude'                        => $this->latitude,
            'longitude'                       => $this->longitude,
            'gps_accuracy'                    => $this->gps_accuracy,
            'image_location'                  => $this->image_location,

            'governorate'                     => $this->governorate,
            'willayat'                        => $this->willayat,
            'village'                         => $this->village,

            'signs_count'                     => $this->signs_count,
            'columns_description'             => $this->columns_description,
            'sign_location_from_road'         => $this->sign_location_from_road,
            'sign_base'                       => $this->sign_base,
            'distance_from_road_edge_meter'   => $this->distance_from_road_edge_meter,
            'sign_column_radius_mm'           => $this->sign_column_radius_mm,
            'column_height'                   => $this->column_height,
            'column_colour'                   => $this->column_colour,
            'column_type'                     => $this->column_type,

            'comments'                        => $this->comments,
            'created_by'                      => $this->created_by,
            'created_at'                      => $this->created_at,
            'updated_at'                      => $this->updated_at,

            // first image shortcuts
            'photo_url'                       => $firstUrl,
            'image_url'                       => $firstUrl,
            
            'image_log'                       => $this->image_log,
            'image_lar'                       => $this->image_lar,
            // array of ALL uploaded images
            'image_urls'                      => $allMedia->map(fn($media) => $media->getUrl())->toArray(),
            'images'                          => $allMedia->toArray(),
            'signs_info'                      => $this->signsInfo
        ];
    }
}
