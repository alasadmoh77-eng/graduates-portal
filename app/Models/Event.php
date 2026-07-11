<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    protected $fillable = ['title_ar', 'title_en', 'description_ar', 'description_en', 'start_at', 'location', 'seats', 'status'];
    
    protected function casts(): array { return ['start_at' => 'datetime']; }

    public function registrations() { return $this->hasMany(EventRegistration::class); }
}
