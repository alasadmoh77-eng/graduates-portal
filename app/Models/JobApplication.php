<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class JobApplication extends Model
{
    protected $fillable = ['job_id', 'graduate_id', 'cover_letter', 'cv_path', 'status'];

    public function job() { return $this->belongsTo(Job::class); }
    public function graduate() { return $this->belongsTo(User::class, 'graduate_id'); }
}
