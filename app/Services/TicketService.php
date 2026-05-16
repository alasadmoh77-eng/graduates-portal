<?php

namespace App\Services;

use App\Models\Ticket;
use App\Models\TicketMessage;
use App\Enums\TicketStatus;

class TicketService
{
    public function createTicket(int $graduateId, string $subject, string $category, string $initialMessage): Ticket
    {
        $ticket = Ticket::create([
            'graduate_id' => $graduateId,
            'subject' => $subject,
            'category' => $category,
            'status' => TicketStatus::OPEN->value,
        ]);

        $this->addReply($ticket->id, $graduateId, $initialMessage);

        return $ticket;
    }

    public function addReply(int $ticketId, int $senderId, string $message): TicketMessage
    {
        $ticket = Ticket::findOrFail($ticketId);
        
        // Auto update status to IN_PROGRESS if admin replies to an OPEN ticket
        if ($ticket->status === TicketStatus::OPEN->value && \App\Models\User::find($senderId)->role === 'admin') {
            $ticket->update(['status' => TicketStatus::IN_PROGRESS->value]);
        }

        return TicketMessage::create([
            'ticket_id' => $ticketId,
            'sender_user_id' => $senderId,
            'message' => $message,
        ]);
    }
}
