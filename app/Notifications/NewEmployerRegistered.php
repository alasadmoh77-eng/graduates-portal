<?php
namespace App\Notifications;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class NewEmployerRegistered extends Notification
{
    use Queueable;

    public function __construct(public User $employer) {}

    public function via($notifiable): array { return ['database']; }

    public function toArray($notifiable): array
    {
        return [
            'type' => 'new_employer_registered',
            'message' => 'New employer registration request awaiting review.',
            'company_name' => $this->employer->employer?->company_name ?? $this->employer->name,
            'registration_date' => $this->employer->created_at->format('Y-m-d H:i'),
            'link'    => route('admin.employers.show', $this->employer->id),
        ];
    }
}
