<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class GraduateAcademicSemester extends Model
{
    protected $fillable = [
        'graduate_academic_level_id',
        'sort_order',
    ];

    protected function casts(): array
    {
        return ['sort_order' => 'integer'];
    }

    public function level(): BelongsTo
    {
        return $this->belongsTo(GraduateAcademicLevel::class, 'graduate_academic_level_id');
    }

    public function subjects(): HasMany
    {
        return $this->hasMany(GraduateAcademicSubject::class)->orderBy('sort_order');
    }
}
