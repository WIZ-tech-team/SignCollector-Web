<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Willayat extends Model
{
    protected $fillable = ['governorate_id', 'name_ar', 'shape_leng', 'shape_area'];

    public function governorate()
    {
        return $this->belongsTo(Governorate::class);
    }
}
