<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SignRoadData extends Model
{
    protected $fillable = [
        'classification',
        'name',
        'number',
        'type',
        'direction'
    ];
}
