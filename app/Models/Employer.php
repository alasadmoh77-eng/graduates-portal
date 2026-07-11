<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Employer extends Model
{
    protected $primaryKey = 'user_id';
    public $incrementing = false;
    protected $fillable = [
        'user_id', 'company_name', 'company_email', 'phone',
        'address', 'website', 'logo', 'status', 'rejection_reason',
        'industry', 'description',
    ];

    // ── Relationships ────────────────────────────────────────────────────────
    public function user()    { return $this->belongsTo(User::class); }
    public function jobs()    { return $this->hasMany(Job::class, 'employer_id', 'user_id'); }

    // ── Scopes ───────────────────────────────────────────────────────────────
    public function scopeApproved($q)  { return $q->where('status', 'approved'); }
    public function scopePending($q)   { return $q->where('status', 'pending'); }
    public function scopeSuspended($q) { return $q->where('status', 'suspended'); }
    public function scopeRejected($q)  { return $q->where('status', 'rejected'); }

    // ── Helpers ──────────────────────────────────────────────────────────────
    public function isApproved()  { return $this->status === 'approved'; }
    public function isPending()   { return $this->status === 'pending'; }
    public function isSuspended() { return $this->status === 'suspended'; }
    public function isRejected()  { return $this->status === 'rejected'; }

    /** Human-readable status label in Arabic */
    public function statusLabel(): string
    {
        return match($this->status) {
            'approved'  => 'موافق عليه',
            'pending'   => 'قيد المراجعة',
            'rejected'  => 'مرفوض',
            'suspended' => 'موقوف',
            default     => $this->status,
        };
    }

    /** Bootstrap badge class for status */
    public function statusBadge(): string
    {
        return match($this->status) {
            'approved'  => 'success',
            'pending'   => 'warning',
            'rejected'  => 'danger',
            'suspended' => 'secondary',
            default     => 'light',
        };
    }
}
