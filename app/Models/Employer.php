<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Employer extends Model
{
    protected $primaryKey = 'user_id';
    public $incrementing = false;
    protected $fillable = ['user_id', 'company_name', 'company_email', 'phone', 'address', 'website', 'logo'];

    public function user() { return $this->belongsTo(User::class); }
    public function jobs() { return $this->hasMany(Job::class); }
}
