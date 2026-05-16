<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class DocumentType extends Model
{
    protected $fillable = ['name_ar', 'name_en', 'code', 'fee_mock', 'eta_days'];
}
