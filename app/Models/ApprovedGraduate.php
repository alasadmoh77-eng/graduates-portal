<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApprovedGraduate extends Model
{
    use HasFactory;

    protected $table = 'approved_graduates';

    protected $fillable = [
        'university_id',
        'name',
        'email',
        'college',
        'major',
        'graduation_year',
    ];

    public function graduate()
    {
        return $this->hasOne(Graduate::class, 'university_id', 'university_id');
    }
}
