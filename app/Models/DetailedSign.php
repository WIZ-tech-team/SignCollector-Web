<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class DetailedSign extends Model implements HasMedia
{
    use InteractsWithMedia;

    protected $fillable = [
        'sign_name',
        'sign_code',
        'sign_code_gcc',
        'sign_type',
        'sign_shape',
        'sign_length',
        'sign_width',
        'sign_radius',
        'sign_color',
        'gps_accuracy',
        
        'road_classification',
        'road_name',
        'road_number',
        'road_type',
        'road_direction',
        'latitude',
        'longitude',
        'governorate',
        'willayat',
        'village',
        'signs_count',
        'columns_description',
        'sign_location_from_road',
        'sign_base',
        'distance_from_road_edge_meter',
        'sign_column_radius_mm',
        'column_height',
        'column_colour',
        'column_type',
        'sign_content_shape_description',
        'sign_content_arabic_text',
        'sign_content_english_text',
        'sign_condition',
        'comments',
        'created_by',
        'created_at',
        'image_log',
        'image_lar'
    ];

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('detailed_signs')
            // removed singleFile() so you can append multiple uploads
            ->acceptsMimeTypes([
                'image/jpeg',
                'image/png',
                'image/jpg',
                'image/gif',
                'image/svg',
            ]);
    }

    public function getSignUrlAttribute(): ?string
    {
        return $this->getFirstMediaUrl('detailed_signs') ?: null;
    }

    public function registerMediaConversions(?Media $media = null): void
    {
        $this->addMediaConversion('thumb')
            ->width(300)
            ->height(300)
            ->optimize()
            ->performOnCollections('detailed_signs');
    }

    public function image()
    {
        return $this->morphOne(Media::class, 'model')
            ->where('collection_name', 'detailed_signs')
            ->latest();
    }
}
