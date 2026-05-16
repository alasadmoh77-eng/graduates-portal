<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class IssuedDocument extends Model
{
    protected $fillable = [
        'document_request_id',
        'serial_number',
        'qr_token',
        'pdf_path',
        'issued_at',
        'is_valid',
        'revoked_at'
    ];

    protected $casts = [
        'issued_at' => 'datetime',
        'revoked_at' => 'datetime',
        'is_valid' => 'boolean'
    ];

    public function documentRequest(): BelongsTo
    {
        return $this->belongsTo(DocumentRequest::class);
    }
}
