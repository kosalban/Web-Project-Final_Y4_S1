<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Technician;
use App\Models\Ticket;
use App\Models\TicketActivity;
use App\Models\User;
use App\Notifications\TicketActivityNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class TicketControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_ticket_form_displays_requester_and_extended_fields(): void
    {
        $admin = User::factory()->create(['role' => 'Admin']);
        User::factory()->create(['name' => 'Alex Requester']);

        $response = $this->actingAs($admin)->get(route('tickets.create'));

        $response->assertSee('Requester / User');
        $response->assertSee('Alex Requester');
        $response->assertSee('value="'.$admin->id.'" selected', false);
        $response->assertSee('Department');
        $response->assertSee('Device / Asset');
        $response->assertSee('Due Date');
        $response->assertSee('Resolution');
        $response->assertSee('Root Cause');
        $response->assertSee('Attachment');
    }

    public function test_admin_can_create_ticket_with_requester_details_and_private_attachment(): void
    {
        $admin = User::factory()->create(['role' => 'Admin']);
        $requester = User::factory()->create(['name' => 'Alex Requester']);
        $category = Category::create(['name' => 'Hardware']);
        Storage::fake('local');

        $response = $this->actingAs($admin)->post(route('tickets.store'), [
            'title' => 'Laptop will not start',
            'description' => 'The laptop shuts down during startup.',
            'category_id' => $category->id,
            'requester_id' => $requester->id,
            'department' => 'Finance',
            'asset' => 'LT-204 / SN-8872',
            'due_date' => '2026-10-03',
            'resolution' => 'Replaced the power adapter.',
            'root_cause' => 'Adapter cable was damaged.',
            'priority' => 'High',
            'status' => 'Resolved',
            'attachment' => UploadedFile::fake()->create('diagnostic.pdf', 100, 'application/pdf'),
        ]);

        $ticket = Ticket::firstOrFail();

        $response->assertRedirect(route('tickets.show', $ticket));
        $this->assertDatabaseHas('tickets', [
            'id' => $ticket->id,
            'user_id' => $requester->id,
            'department' => 'Finance',
            'asset' => 'LT-204 / SN-8872',
            'due_date' => '2026-10-03 00:00:00',
            'resolution' => 'Replaced the power adapter.',
            'root_cause' => 'Adapter cable was damaged.',
            'attachment_name' => 'diagnostic.pdf',
        ]);
        Storage::disk('local')->assertExists($ticket->attachment_path);

        $this->actingAs($admin)
            ->get(route('tickets.attachment', $ticket))
            ->assertDownload('diagnostic.pdf');
    }

    public function test_ticket_print_page_shows_ticket_number_and_history(): void
    {
        $user = User::factory()->create();
        $category = Category::create(['name' => 'Hardware']);
        $ticket = Ticket::create([
            'title' => 'Printer offline',
            'description' => 'The printer is not responding.',
            'user_id' => $user->id,
            'category_id' => $category->id,
            'priority' => 'Medium',
            'status' => 'Open',
        ]);
        $ticket->comments()->create(['user_id' => $user->id, 'comment' => 'Restarted the printer.']);

        $response = $this->actingAs($user)->get(route('tickets.print', $ticket));

        $response->assertSee('Ticket #'.$ticket->id);
        $response->assertSee('Restarted the printer.');
        $response->assertSee('Save as PDF');
    }

    public function test_dashboard_counts_overdue_open_tickets(): void
    {
        $user = User::factory()->create();
        $category = Category::create(['name' => 'Hardware']);
        Ticket::create([
            'title' => 'Overdue ticket',
            'description' => 'Needs attention.',
            'user_id' => $user->id,
            'category_id' => $category->id,
            'priority' => 'High',
            'status' => 'Open',
            'due_date' => today()->subDay(),
        ]);

        $response = $this->actingAs($user)->get(route('dashboard'));

        $response->assertSee('Overdue');
        $response->assertSee('1');
    }

    public function test_ticket_filters_narrow_results_by_relationships_and_date_range(): void
    {
        $admin = User::factory()->create(['role' => 'Admin']);
        $requester = User::factory()->create(['name' => 'Morgan Requester']);
        $otherRequester = User::factory()->create(['name' => 'Jamie Requester']);
        $technician = Technician::create(['name' => 'Taylor Tech', 'email' => 'taylor@example.com']);
        $otherTechnician = Technician::create(['name' => 'Riley Tech', 'email' => 'riley@example.com']);
        $category = Category::create(['name' => 'Network']);
        $otherCategory = Category::create(['name' => 'Hardware']);

        $matchingTicket = Ticket::create([
            'title' => 'Matching network ticket',
            'description' => 'Matches every filter.',
            'user_id' => $requester->id,
            'technician_id' => $technician->id,
            'category_id' => $category->id,
            'priority' => 'High',
            'status' => 'Open',
        ]);
        $wrongTechnicianTicket = Ticket::create([
            'title' => 'Wrong technician ticket',
            'description' => 'Does not match technician.',
            'user_id' => $requester->id,
            'technician_id' => $otherTechnician->id,
            'category_id' => $category->id,
            'priority' => 'High',
            'status' => 'Open',
        ]);
        $oldTicket = Ticket::create([
            'title' => 'Older network ticket',
            'description' => 'Does not match date range.',
            'user_id' => $requester->id,
            'technician_id' => $technician->id,
            'category_id' => $category->id,
            'priority' => 'High',
            'status' => 'Open',
        ]);
        $wrongRequesterTicket = Ticket::create([
            'title' => 'Wrong requester ticket',
            'description' => 'Does not match requester or category.',
            'user_id' => $otherRequester->id,
            'technician_id' => $technician->id,
            'category_id' => $otherCategory->id,
            'priority' => 'High',
            'status' => 'Open',
        ]);
        $oldTicket->created_at = today()->subDay();
        $oldTicket->save();

        $this->actingAs($admin)
            ->get(route('tickets.index', [
                'technician_id' => $technician->id,
                'category_id' => $category->id,
                'requester_id' => $requester->id,
                'date_from' => today()->toDateString(),
                'date_to' => today()->toDateString(),
            ]))
            ->assertSee($matchingTicket->title)
            ->assertDontSee($wrongTechnicianTicket->title)
            ->assertDontSee($oldTicket->title)
            ->assertDontSee($wrongRequesterTicket->title);
    }

    public function test_ticket_updates_and_comments_create_activity_and_notify_participants(): void
    {
        $admin = User::factory()->create(['role' => 'Admin']);
        $anotherAdmin = User::factory()->create(['role' => 'Admin']);
        $requester = User::factory()->create(['role' => 'Staff']);
        $technician = Technician::create(['name' => 'Taylor Tech', 'email' => 'taylor@example.com']);
        $category = Category::create(['name' => 'Network']);
        $ticket = Ticket::create([
            'title' => 'Wi-Fi outage',
            'description' => 'The office network is unavailable.',
            'user_id' => $requester->id,
            'category_id' => $category->id,
            'priority' => 'High',
            'status' => 'Open',
        ]);
        Notification::fake();

        $this->actingAs($admin)->put(route('tickets.update', $ticket), [
            'title' => $ticket->title,
            'description' => $ticket->description,
            'category_id' => $category->id,
            'technician_id' => $technician->id,
            'priority' => $ticket->priority,
            'status' => 'In Progress',
            'requester_id' => $requester->id,
        ])->assertRedirect(route('tickets.show', $ticket));

        $this->assertDatabaseHas('ticket_activities', [
            'ticket_id' => $ticket->id,
            'user_id' => $admin->id,
            'type' => 'assigned',
        ]);
        $this->assertDatabaseHas('ticket_activities', [
            'ticket_id' => $ticket->id,
            'user_id' => $admin->id,
            'type' => 'status_changed',
        ]);
        $notifications = Notification::sent($requester, TicketActivityNotification::class);
        $this->assertCount(1, $notifications);
        $this->assertStringContainsString('assigned to Taylor Tech', $notifications[0]->message);
        $this->assertStringContainsString('Open to In Progress', $notifications[0]->message);
        Notification::assertSentTo($anotherAdmin, TicketActivityNotification::class);

        $this->actingAs($admin)->post(route('tickets.comments.store', $ticket), [
            'comment' => 'The network switch has been restarted.',
        ])->assertRedirect();

        $this->assertDatabaseHas('ticket_activities', [
            'ticket_id' => $ticket->id,
            'user_id' => $admin->id,
            'type' => 'commented',
        ]);
        Notification::assertSentTo($requester, TicketActivityNotification::class, fn (TicketActivityNotification $notification): bool => $notification->title === 'New ticket comment');

        $activities = TicketActivity::where('ticket_id', $ticket->id)->with('user')->get();
        $this->assertSame(3, $activities->count());

        $this->actingAs($requester)
            ->get(route('tickets.show', $ticket))
            ->assertSee('changed status from Open to In Progress')
            ->assertSee('changed assignment from Unassigned to Taylor Tech')
            ->assertSee('added a comment');
    }

    public function test_notification_opens_its_ticket_and_marks_it_read(): void
    {
        $requester = User::factory()->create(['role' => 'Staff']);
        $category = Category::create(['name' => 'Network']);
        $ticket = Ticket::create([
            'title' => 'Router offline',
            'description' => 'The router is offline.',
            'user_id' => $requester->id,
            'category_id' => $category->id,
            'priority' => 'Medium',
            'status' => 'Open',
        ]);
        $requester->notify(new TicketActivityNotification($ticket->id, 'Ticket assigned', 'Ticket assigned to Taylor Tech.'));
        $notification = $requester->notifications()->firstOrFail();

        $this->actingAs($requester)
            ->get(route('dashboard'))
            ->assertSee('Ticket assigned')
            ->assertSee('Ticket assigned to Taylor Tech.');

        $this->actingAs($requester)
            ->post(route('notifications.open', $notification->id))
            ->assertRedirect(route('tickets.show', $ticket));

        $this->assertNotNull($notification->fresh()->read_at);

        $requester->notify(new TicketActivityNotification($ticket->id, 'Ticket updated', 'Ticket status changed.'));

        $this->actingAs($requester)
            ->post(route('notifications.read-all'))
            ->assertRedirect();

        $this->assertSame(0, $requester->unreadNotifications()->count());
    }
}
