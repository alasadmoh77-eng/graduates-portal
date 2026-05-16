<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    protected $fillable = ['graduate_id', 'subject', 'category', 'status'];

    public function graduate() { return $this->belongsTo(User::class, 'graduate_id'); }
    public function messages() { return $this->hasMany(TicketMessage::class); }
}
