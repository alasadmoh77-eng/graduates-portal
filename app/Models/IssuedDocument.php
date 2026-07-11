<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class IssuedDocument extends Model
{
    protected $fillable = [
        'document_request_id',
        'serial_number',
        'qr_token',
        'pdf_path',
        'issued_at',
        'is_valid',
        'revoked_at',
        'all_signed_at'
    ];

    protected $casts = [
        'issued_at' => 'datetime',
        'revoked_at' => 'datetime',
        'all_signed_at' => 'datetime',
        'is_valid' => 'boolean'
    ];

    public function documentRequest(): BelongsTo
    {
        return $this->belongsTo(DocumentRequest::class);
    }

    public function signatures(): HasMany
    {
        return $this->hasMany(DocumentSignature::class);
    }

    public function isFullySigned(): bool
    {
        return $this->all_signed_at !== null;
    }

    public function getRequiredSigners(): array
    {
        $docCode = strtolower($this->documentRequest->documentType->code ?? '');
        $isGradesCert = in_array($docCode, ['grades_certificate', 'grade_certificate', 'grades', 'certificate_grades']);

        if ($isGradesCert) {
            return [
                'مسجل الكلية',
                'عميد الكلية',
                'المسجل العام',
                'نائب رئيس الجامعة لشؤون الطلاب',
            ];
        }

        return [
            'المختص الأكاديمي',
            'مدير إدارة شؤون الخريجين',
            'مسجل الكلية',
            'عميد الكلية',
        ];
    }

    public function remainingSigners(): array
    {
        $signed = $this->signatures->pluck('role_title')->toArray();
        return array_diff($this->getRequiredSigners(), $signed);
    }

    public function getCurrentSigner(): ?string
    {
        $signed = $this->signatures->pluck('role_title')->toArray();
        foreach ($this->getRequiredSigners() as $roleTitle) {
            if (!in_array($roleTitle, $signed)) {
                return $roleTitle;
            }
        }
        return null;
    }
}
