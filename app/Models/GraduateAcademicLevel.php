<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class GraduateAcademicLevel extends Model
{
    protected $fillable = [
        'graduate_academic_record_id',
        'sort_order',
        'name',
        'academic_year',
        'level_avg',
        'final_result',
    ];

    protected function casts(): array
    {
        return ['sort_order' => 'integer'];
    }

    public function record(): BelongsTo
    {
        return $this->belongsTo(GraduateAcademicRecord::class, 'graduate_academic_record_id');
    }

    public function semesters(): HasMany
    {
        return $this->hasMany(GraduateAcademicSemester::class)->orderBy('sort_order');
    }
}
