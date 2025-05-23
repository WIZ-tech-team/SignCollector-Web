<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SignInfo extends Model
{
    protected $fillable = [
        'signs_group_id',
        'sign_name',
        'sign_code',
        'sign_code_gcc',
        'sign_type',
        'sign_shape',
        'sign_length',
        'sign_width',
        'sign_radius',
        'sign_color',
        'sign_content_shape_description',
        'sign_content_arabic_text',
        'sign_content_english_text',
        'sign_condition'
    ];

    public function group()
    {
        return $this->belongsTo(SignsGroup::class, 'signs_group_id');
    }
}
