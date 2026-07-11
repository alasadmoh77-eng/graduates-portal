<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Job extends Model
{
    protected $table = 'portal_jobs';
    protected $fillable = [
        'employer_id', 'title', 'description', 'requirements',
        'deadline', 'location', 'job_type', 'status',
        'rejection_reason', 'closed_at', 'is_filled', 'filled_at',
    ];

    protected $attributes = [
        'is_filled' => false,
    ];

    protected function casts(): array
    {
        return [
            'deadline'  => 'date',
            'closed_at' => 'datetime',
            'is_filled' => 'boolean',
            'filled_at' => 'datetime',
        ];
    }

    // ── Relationships ────────────────────────────────────────────────────────
    public function employer()     { return $this->belongsTo(User::class, 'employer_id'); }
    public function company()      { return $this->belongsTo(Employer::class, 'employer_id', 'user_id'); }
    public function applications() { return $this->hasMany(JobApplication::class); }

    // ── Scopes ───────────────────────────────────────────────────────────────
    public function scopeActive($q)   { return $q->where('status', 'active'); }
    public function scopePending($q)  { return $q->where('status', 'pending'); }
    public function scopeClosed($q)   { return $q->where('status', 'closed'); }
    public function scopeRejected($q) { return $q->where('status', 'rejected'); }

    // ── Helpers ──────────────────────────────────────────────────────────────
    public function isActive()   { return $this->status === 'active'; }
    public function isPending()  { return $this->status === 'pending'; }
    public function isClosed()   { return $this->status === 'closed'; }
    public function isRejected() { return $this->status === 'rejected'; }

    public function statusLabel(): string
    {
        return match($this->status) {
            'active'   => 'نشط',
            'pending'  => 'قيد المراجعة',
            'closed'   => 'مغلق',
            'rejected' => 'مرفوض',
            default    => $this->status,
        };
    }

    public function statusBadge(): string
    {
        return match($this->status) {
            'active'   => 'success',
            'pending'  => 'warning',
            'closed'   => 'secondary',
            'rejected' => 'danger',
            default    => 'light',
        };
    }
}
