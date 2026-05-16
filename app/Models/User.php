<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = ['name', 'email', 'password', 'role', 'is_active'];
    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array {
        return ['email_verified_at' => 'datetime', 'password' => 'hashed', 'is_active' => 'boolean'];
    }

    public function graduate() { return $this->hasOne(Graduate::class); }
    public function academicRecord() { return $this->hasOne(GraduateAcademicRecord::class); }
    public function employer() { return $this->hasOne(Employer::class); }
    public function documentRequests() { return $this->hasMany(DocumentRequest::class); }
}
