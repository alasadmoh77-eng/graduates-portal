<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class DocumentType extends Model
{
    protected $fillable = ['name_ar', 'name_en', 'code', 'fee_mock', 'eta_days', 'fee_amount', 'currency', 'payment_required'];

    protected function casts(): array
    {
        return [
            'payment_required' => 'boolean',
            'fee_amount' => 'decimal:2',
        ];
    }
}
