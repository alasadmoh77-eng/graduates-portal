<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = ['name', 'email', 'password', 'role', 'is_active', 'signature_image', 'signer_role'];
    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
            'is_signature_approved' => 'boolean',
            'signature_approved_at' => 'datetime',
        ];
    }

    public function graduate() { return $this->hasOne(Graduate::class); }
    public function academicRecord() { return $this->hasOne(GraduateAcademicRecord::class); }
    public function gradesCertificate() { return $this->hasOne(GradesCertificate::class); }
    public function employer() { return $this->hasOne(Employer::class); }
    public function jobs() { return $this->hasMany(Job::class, 'employer_id'); }
    public function documentRequests() { return $this->hasMany(DocumentRequest::class); }
    public function documentSignatures() { return $this->hasMany(DocumentSignature::class); }

    public function signatureUrl(): ?string
    {
        if (!$this->signature_image) {
            return null;
        }
        return \Illuminate\Support\Facades\Storage::disk('public')->url($this->signature_image);
    }

    public function signaturePath(): ?string
    {
        if (!$this->signature_image) {
            return null;
        }
        $path = \Illuminate\Support\Facades\Storage::disk('public')->path($this->signature_image);
        return file_exists($path) ? $path : null;
    }

    public function hasExistingSignatureFile(): bool
    {
        return filled($this->signature_image)
            && \Illuminate\Support\Facades\Storage::disk('public')->exists($this->signature_image);
    }

    public function signatureApprover()
    {
        return $this->belongsTo(User::class, 'signature_approved_by');
    }

    public function canManageDocumentRecovery(): bool
    {
        $authorized = ['المختص الأكاديمي', 'مسجل الكلية', 'مدير إدارة شؤون الخريجين'];
        return in_array($this->signer_role, $authorized);
    }
}
