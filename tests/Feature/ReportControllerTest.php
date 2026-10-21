<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Technician;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReportControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_report_csv_download_contains_status_and_priority_totals(): void
    {
        $admin = User::factory()->create(['role' => 'Admin']);
        $requester = User::factory()->create();
        $category = Category::create(['name' => 'Hardware']);
        Ticket::create([
            'title' => 'Open laptop issue',
            'description' => 'Laptop will not start.',
            'user_id' => $requester->id,
            'category_id' => $category->id,
            'priority' => 'High',
            'status' => 'Open',
        ]);
        Ticket::create([
            'title' => 'Resolved monitor issue',
            'description' => 'Monitor cable was replaced.',
            'user_id' => $requester->id,
            'category_id' => $category->id,
            'priority' => 'Low',
            'status' => 'Resolved',
        ]);

        $response = $this->actingAs($admin)->get(route('reports.export'));

        $response->assertOk();
        $response->assertHeader('content-type', 'text/csv; charset=UTF-8');
        $response->assertStreamedContent("\xEF\xBB\xBFReport,Value,\"Ticket Count\"\n\"Ticket Summary\",\"Total Tickets\",2\n\"Ticket Summary\",\"Open Tickets\",1\n\"Ticket Summary\",\"In Progress Tickets\",0\n\"Ticket Summary\",\"Resolved Tickets\",1\n\"Ticket Summary\",\"Closed Tickets\",0\n\"Ticket Summary\",\"Overdue Tickets\",0\n\"Ticket Summary\",\"High / Critical Priority Tickets\",1\n\"Tickets by Status\",Open,1\n\"Tickets by Status\",Resolved,1\n\"Tickets by Priority\",High,1\n\"Tickets by Priority\",Low,1\n\"Tickets by Category\",Hardware,2\n\"Tickets by Technician\",Unassigned,2\n");
    }

    public function test_report_page_shows_ticket_summary_and_category_and_technician_breakdowns(): void
    {
        $admin = User::factory()->create(['role' => 'Admin']);
        $requester = User::factory()->create();
        $category = Category::create(['name' => 'Hardware']);
        $technician = Technician::create([
            'name' => 'Alex Technician',
            'email' => 'alex.technician@example.com',
        ]);

        Ticket::create([
            'title' => 'Laptop issue',
            'description' => 'Laptop will not start.',
            'user_id' => $requester->id,
            'technician_id' => $technician->id,
            'category_id' => $category->id,
            'priority' => 'Critical',
            'status' => 'In Progress',
            'due_date' => today()->subDay(),
        ]);
        Ticket::create([
            'title' => 'Monitor issue',
            'description' => 'Monitor cable was replaced.',
            'user_id' => $requester->id,
            'category_id' => $category->id,
            'priority' => 'Low',
            'status' => 'Closed',
        ]);

        $response = $this->actingAs($admin)->get(route('reports.index'));

        $response->assertOk()
            ->assertSee('Total Tickets')
            ->assertSee('2', false)
            ->assertSee('In Progress Tickets')
            ->assertSee('Overdue Tickets')
            ->assertSee('High / Critical Priority Tickets')
            ->assertSee('Tickets by Category')
            ->assertSee('Hardware')
            ->assertSee('Tickets by Technician')
            ->assertSee('Alex Technician')
            ->assertSee('Unassigned');
    }

    public function test_report_page_filters_all_ticket_totals_by_creation_date(): void
    {
        $admin = User::factory()->create(['role' => 'Admin']);
        $requester = User::factory()->create();
        $hardware = Category::create(['name' => 'Hardware']);
        $software = Category::create(['name' => 'Software']);
        $technician = Technician::create([
            'name' => 'Alex Technician',
            'email' => 'alex.technician@example.com',
        ]);

        $includedTicket = Ticket::create([
            'title' => 'Included ticket',
            'description' => 'Created within the selected range.',
            'user_id' => $requester->id,
            'technician_id' => $technician->id,
            'category_id' => $hardware->id,
            'priority' => 'Critical',
            'status' => 'Open',
            'due_date' => today()->subDay(),
        ]);
        $includedTicket->created_at = '2026-01-15 12:00:00';
        $includedTicket->save();

        $excludedTicket = Ticket::create([
            'title' => 'Excluded ticket',
            'description' => 'Created outside the selected range.',
            'user_id' => $requester->id,
            'category_id' => $software->id,
            'priority' => 'Low',
            'status' => 'Resolved',
        ]);
        $excludedTicket->created_at = '2026-02-15 12:00:00';
        $excludedTicket->save();

        $response = $this->actingAs($admin)->get(route('reports.index', [
            'from' => '2026-01-15',
            'to' => '2026-01-15',
        ]));

        $response->assertOk()
            ->assertViewHas('filters', [
                'from' => '2026-01-15',
                'to' => '2026-01-15',
            ])
            ->assertSee('value="2026-01-15"', false)
            ->assertSee(route('reports.export', [
                'from' => '2026-01-15',
                'to' => '2026-01-15',
            ]))
            ->assertViewHas('summary', fn (array $summary): bool => $summary['Total Tickets'] === 1
                && $summary['Open Tickets'] === 1
                && $summary['Resolved Tickets'] === 0
                && $summary['Overdue Tickets'] === 1
                && $summary['High / Critical Priority Tickets'] === 1)
            ->assertViewHas('byCategory', fn ($categories): bool => $categories->firstWhere('name', 'Hardware')?->tickets_count === 1
                && $categories->firstWhere('name', 'Software')?->tickets_count === 0)
            ->assertViewHas('byTechnician', fn ($technicians): bool => $technicians->firstWhere('name', 'Alex Technician')?->total === 1
                && $technicians->doesntContain(fn ($technician): bool => $technician->name === 'Unassigned'));

        $this->actingAs($admin)
            ->get(route('reports.index', ['to' => '2026-01-31']))
            ->assertOk()
            ->assertViewHas('summary', fn (array $summary): bool => $summary['Total Tickets'] === 1);
    }

    public function test_report_csv_export_uses_the_selected_creation_date_range(): void
    {
        $admin = User::factory()->create(['role' => 'Admin']);
        $requester = User::factory()->create();
        $category = Category::create(['name' => 'Hardware']);
        $includedTicket = Ticket::create([
            'title' => 'Included ticket',
            'description' => 'Created within the selected range.',
            'user_id' => $requester->id,
            'category_id' => $category->id,
            'priority' => 'High',
            'status' => 'Open',
        ]);
        $includedTicket->created_at = '2026-01-15 12:00:00';
        $includedTicket->save();

        $excludedTicket = Ticket::create([
            'title' => 'Excluded ticket',
            'description' => 'Created outside the selected range.',
            'user_id' => $requester->id,
            'category_id' => $category->id,
            'priority' => 'Low',
            'status' => 'Resolved',
        ]);
        $excludedTicket->created_at = '2026-02-15 12:00:00';
        $excludedTicket->save();

        $response = $this->actingAs($admin)->get(route('reports.export', [
            'from' => '2026-01-15',
            'to' => '2026-01-15',
        ]));

        $response->assertOk()
            ->assertStreamedContent("\xEF\xBB\xBFReport,Value,\"Ticket Count\"\n\"Ticket Summary\",\"Total Tickets\",1\n\"Ticket Summary\",\"Open Tickets\",1\n\"Ticket Summary\",\"In Progress Tickets\",0\n\"Ticket Summary\",\"Resolved Tickets\",0\n\"Ticket Summary\",\"Closed Tickets\",0\n\"Ticket Summary\",\"Overdue Tickets\",0\n\"Ticket Summary\",\"High / Critical Priority Tickets\",1\n\"Tickets by Status\",Open,1\n\"Tickets by Priority\",High,1\n\"Tickets by Category\",Hardware,1\n\"Tickets by Technician\",Unassigned,1\n");
    }

    public function test_report_rejects_end_date_before_start_date(): void
    {
        $admin = User::factory()->create(['role' => 'Admin']);

        $response = $this->actingAs($admin)->get(route('reports.index', [
            'from' => '2026-01-16',
            'to' => '2026-01-15',
        ]));

        $response->assertSessionHasErrors('to');
    }
}
