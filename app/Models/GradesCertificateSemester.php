<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class GradesCertificateSemester extends Model
{
    protected $table = 'grades_certificate_semesters';

    protected $fillable = [
        'grades_certificate_level_id',
        'sort_order',
    ];

    protected function casts(): array
    {
        return ['sort_order' => 'integer'];
    }

    public function level(): BelongsTo
    {
        return $this->belongsTo(GradesCertificateLevel::class, 'grades_certificate_level_id');
    }

    public function subjects(): HasMany
    {
        return $this->hasMany(GradesCertificateSubject::class, 'grades_certificate_semester_id')->orderBy('sort_order');
    }
}
