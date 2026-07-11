<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class GradesCertificateLevel extends Model
{
    protected $table = 'grades_certificate_levels';

    protected $fillable = [
        'grades_certificate_id',
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
        return $this->belongsTo(GradesCertificate::class, 'grades_certificate_id');
    }

    public function semesters(): HasMany
    {
        return $this->hasMany(GradesCertificateSemester::class, 'grades_certificate_level_id')->orderBy('sort_order');
    }
}
