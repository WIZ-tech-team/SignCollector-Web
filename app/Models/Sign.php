<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Sign extends Model implements HasMedia
{

    use InteractsWithMedia;

    protected $fillable = [
        'name',
        'code_2010',
        'code_gcc',
        'type',
        'shape',
        'length',
        'width',
        'background_radius',
        'background_color'
    ];

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('signs')
            ->singleFile()
            ->acceptsMimeTypes(['image/jpeg', 'image/png', 'image/jpg', 'image/gif', 'image/svg']);
    }

    public function getSignUrlAttribute(): string
    {
        return $this->getFirstMediaUrl('signs') ?? null;
    }

    public function image()
    {
        return $this->morphOne(Media::class, 'model')
            ->where('collection_name', 'signs')
            ->latest();
    }

    public function location()
    {
        return $this->hasOne(SignLocation::class);
    }

    public function roadData()
    {
        return $this->hasOne(SignRoadData::class);
    }
    
    public function chassisDetail()
    {
        return $this->hasOne(SignChassisDetail::class);
    }

    public function content()
    {
        return $this->hasOne(SignContent::class);
    }

    public function otherDetail()
    {
        return $this->hasOne(SignOtherDetail::class);
    }
}
