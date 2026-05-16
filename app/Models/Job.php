<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Job extends Model
{
    protected $table = 'portal_jobs';
    protected $fillable = ['employer_id', 'title', 'description', 'requirements', 'deadline', 'location', 'job_type', 'status'];
    
    protected function casts(): array { return ['deadline' => 'date']; }

    public function employer() { return $this->belongsTo(User::class, 'employer_id'); }
    public function applications() { return $this->hasMany(JobApplication::class); }
}
