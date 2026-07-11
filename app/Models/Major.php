<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Major extends Model
{
    protected $fillable = ['name_ar', 'name_en', 'faculty_id', 'degree_name_ar', 'degree_name_en'];

    public function faculty(): BelongsTo
    {
        return $this->belongsTo(Faculty::class);
    }
}
