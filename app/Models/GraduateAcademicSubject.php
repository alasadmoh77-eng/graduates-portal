<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GraduateAcademicSubject extends Model
{
    protected $fillable = [
        'graduate_academic_semester_id',
        'sort_order',
        'catalog_key',
        'name',
        'credit_hours',
        'score',
        'rating',
    ];

    protected function casts(): array
    {
        return ['sort_order' => 'integer'];
    }

    public function semester(): BelongsTo
    {
        return $this->belongsTo(GraduateAcademicSemester::class, 'graduate_academic_semester_id');
    }
}
