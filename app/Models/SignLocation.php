<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SignLocation extends Model
{
    protected $fillable = [
        'latitude',
        'longitude',
        'governorate',
        'willayat',
        'village'
    ];
}
