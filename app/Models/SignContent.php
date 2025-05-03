<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SignContent extends Model
{
    protected $fillable = [
        'shape_description',
        'written_content'
    ];

    protected function getWrittenContentAttribute($value)
    {
        return json_decode($value, true);
    }

    protected function setWrittenContentAttribute($value)
    {
        $this->attributes['written_content'] = json_encode($value);
    }
    
}
