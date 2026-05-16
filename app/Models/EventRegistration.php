<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class EventRegistration extends Model
{
    protected $fillable = ['event_id', 'graduate_id', 'status', 'attended_at'];
    
    protected function casts(): array { return ['attended_at' => 'datetime']; }

    public function event() { return $this->belongsTo(Event::class); }
    public function graduate() { return $this->belongsTo(User::class, 'graduate_id'); }
}
