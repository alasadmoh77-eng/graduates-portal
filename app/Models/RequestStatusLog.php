<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RequestStatusLog extends Model
{
    public $timestamps = false; // Only created_at is used

    protected $fillable = [
        'document_request_id',
        'admin_id',
        'from_status',
        'to_status',
        'note',
        'created_at'
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    public function documentRequest(): BelongsTo
    {
        return $this->belongsTo(DocumentRequest::class);
    }

    public function admin(): BelongsTo
    {
        return $this->belongsTo(User::class, 'admin_id');
    }
}
