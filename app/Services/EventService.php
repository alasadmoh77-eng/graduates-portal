<?php

namespace App\Services;

use App\Models\Event;
use App\Models\EventRegistration;

class EventService
{
    public function registerGraduate(int $eventId, int $graduateId): ?EventRegistration
    {
        $event = Event::findOrFail($eventId);
        
        // Prevent duplicate registration
        if ($event->registrations()->where('graduate_id', $graduateId)->exists()) {
            return null;
        }

        // Validate seats capacity if set
        if ($event->seats && $event->registrations()->count() >= $event->seats) {
            throw new \Exception('Event is fully booked');
        }

        return EventRegistration::create([
            'event_id' => $eventId,
            'graduate_id' => $graduateId,
            'status' => 'registered'
        ]);
    }
    
    public function markAttendance(int $registrationId): void
    {
        $registration = EventRegistration::findOrFail($registrationId);
        $registration->update(['attended_at' => now()]);
    }
}
