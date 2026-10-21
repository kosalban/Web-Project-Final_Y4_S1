<?php

namespace App\Notifications;

use App\Models\User;
use Illuminate\Notifications\Notification;

class TicketActivityNotification extends Notification
{
    public function __construct(
        public int $ticketId,
        public string $title,
        public string $message,
    ) {}

    public function via(User $notifiable): array
    {
        return ['database'];
    }

    /** @return array<string, int|string> */
    public function toDatabase(User $notifiable): array
    {
        return [
            'ticket_id' => $this->ticketId,
            'title' => $this->title,
            'message' => $this->message,
        ];
    }
}
