<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SignChassisDetail extends Model
{
    protected $fillable = [
        'count',
        'columns_description',
        'location_from_road',
        'base',
        'disatance_from_road_edge',
        'column_radius',
        'column_height',
        'column_color',
        'column_type'
    ];
}
