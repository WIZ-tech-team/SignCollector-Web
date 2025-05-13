<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Governorate extends Model
{
    protected $fillable = ['name_ar'];

    public function willayats()
    {
        return $this->hasMany(Willayat::class);
    }
}
