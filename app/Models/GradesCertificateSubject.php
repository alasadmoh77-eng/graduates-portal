<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GradesCertificateSubject extends Model
{
    protected $table = 'grades_certificate_subjects';

    protected $fillable = [
        'grades_certificate_semester_id',
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
        return $this->belongsTo(GradesCertificateSemester::class, 'grades_certificate_semester_id');
    }
}
