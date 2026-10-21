<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

class TicketNotificationController extends Controller
{
    public function open(Request $request, string $notificationId): RedirectResponse
    {
        $notification = $request->user()->notifications()->findOrFail($notificationId);
        $ticket = Ticket::findOrFail(Arr::get($notification->data, 'ticket_id'));

        $notification->markAsRead();

        return redirect()->route('tickets.show', $ticket);
    }

    public function markAllRead(Request $request): RedirectResponse
    {
        $request->user()->unreadNotifications()->update(['read_at' => now()]);

        return back();
    }
}
