<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Faculty extends Model
{
    protected $fillable = ['name_ar', 'name_en', 'description', 'status'];

    public function majors(): HasMany
    {
        return $this->hasMany(Major::class);
    }
}
