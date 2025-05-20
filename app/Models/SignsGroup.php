<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class SignsGroup extends Model implements HasMedia
{
    use InteractsWithMedia;

    protected $fillable = [
        'latitude',
        'longitude',
        'gps_accuracy',
        'governorate',
        'willayat',
        'village',
        'road_classification',
        'road_name',
        'road_number',
        'road_type',
        'road_direction',
        'signs_count',
        'columns_description',
        'sign_location_from_road',
        'sign_base',
        'distance_from_road_edge_meter',
        'sign_column_radius_mm',
        'column_height',
        'column_colour',
        'column_type',
        'comments',
        'image_log',
        'image_lar',
        'image_location',
        'created_by',
        'created_at',
        'updated_at'
    ];

    public function signsInfo()
    {
        return $this->hasMany(SignInfo::class);
    }

    public function setCreatedAtAttribute($value)
    {
        $this->attributes['created_at'] = $value;
    }

    public function getCreatedAtAttribute($value) {
        return Carbon::parse($value)->format('Y-m-d H:i:s');
    }

    public function getUpdatedAtAttribute($value) {
        return Carbon::parse($value)->format('Y-m-d H:i:s');
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('signs_groups')
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
        return $this->getFirstMediaUrl('signs_groups') ?: null;
    }

    public function registerMediaConversions(?Media $media = null): void
    {
        $this->addMediaConversion('thumb')
            ->width(300)
            ->height(300)
            ->optimize()
            ->performOnCollections('signs_groups');
    }

    public function images()
    {
        return $this->morphMany(Media::class, 'model')
            ->where('collection_name', 'signs_groups');
    }
}
