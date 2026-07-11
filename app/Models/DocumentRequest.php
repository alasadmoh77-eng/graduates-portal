<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DocumentRequest extends Model
{
    protected $fillable = [
        'user_id',
        'document_type_id',
        'tracking_code',
        'language',
        'purpose',
        'delivery_type',
        'status',
        'admin_note',
        'payment_status',
        'payment_proof_path',
        'payment_reviewed_by',
        'payment_reviewed_at',
        'payment_rejection_reason',
        'fee_amount',
        'currency',
    ];

    protected function casts(): array
    {
        return [
            'payment_reviewed_at' => 'datetime',
            'fee_amount' => 'decimal:2',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function documentType(): BelongsTo
    {
        return $this->belongsTo(DocumentType::class);
    }

    public function issuedDocument(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(IssuedDocument::class);
    }

    public function logs(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(RequestStatusLog::class)->orderBy('created_at', 'desc');
    }

    public function paymentReviewedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'payment_reviewed_by');
    }
}
