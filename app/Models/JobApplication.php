<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class JobApplication extends Model
{
    protected $fillable = [
        'job_id', 'graduate_id', 'cover_letter', 'cv_path', 'status',
        'employer_notes', 'interview_date', 'interview_notes',
    ];

    protected function casts(): array
    {
        return [
            'interview_date' => 'datetime',
        ];
    }

    // ── Relationships ────────────────────────────────────────────────────────
    public function job()      { return $this->belongsTo(Job::class); }
    public function graduate() { return $this->belongsTo(User::class, 'graduate_id'); }

    // ── Scopes ───────────────────────────────────────────────────────────────
    public function scopeNew($q)          { return $q->where('status', 'new'); }
    public function scopeShortlisted($q)  { return $q->where('status', 'shortlisted'); }
    public function scopeInterviewed($q)  { return $q->where('status', 'interviewed'); }
    public function scopeHired($q)        { return $q->where('status', 'hired'); }
    public function scopeRejected($q)     { return $q->where('status', 'rejected'); }

    // ── Helpers ──────────────────────────────────────────────────────────────
    public function statusLabel(): string
    {
        return match($this->status) {
            'new'         => 'جديد',
            'shortlisted' => 'مدرج في القائمة المختصرة',
            'interviewed' => 'تمت المقابلة',
            'hired'       => 'تم التوظيف',
            'rejected'    => 'مرفوض',
            default       => $this->status,
        };
    }

    public function statusBadge(): string
    {
        return match($this->status) {
            'new'         => 'secondary',
            'shortlisted' => 'info',
            'interviewed' => 'primary',
            'hired'       => 'success',
            'rejected'    => 'danger',
            default       => 'light',
        };
    }

    /** Ordered list of valid next statuses from current status (for pipeline UI) */
    public function nextStatuses(): array
    {
        return match($this->status) {
            'new'         => ['shortlisted', 'rejected'],
            'shortlisted' => ['interviewed', 'rejected'],
            'interviewed' => ['hired', 'rejected'],
            default       => [],
        };
    }
}
