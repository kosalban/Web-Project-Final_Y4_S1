<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Technician;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserRoleAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_view_users_list(): void
    {
        $admin = User::factory()->create([
            'role' => 'Admin',
        ]);

        $response = $this->actingAs($admin)->get(route('users.index'));

        $response->assertStatus(200);
        $response->assertSee('Users');
    }

    public function test_staff_cannot_access_user_management(): void
    {
        $staff = User::factory()->create([
            'role' => 'Staff',
        ]);

        $response = $this->actingAs($staff)->get(route('users.index'));

        $response->assertForbidden();
    }

    public function test_staff_cannot_access_user_create_page(): void
    {
        $staff = User::factory()->create([
            'role' => 'Staff',
        ]);

        $response = $this->actingAs($staff)->get(route('users.create'));

        $response->assertForbidden();
    }

    public function test_staff_cannot_access_user_edit_page(): void
    {
        $staff = User::factory()->create([
            'role' => 'Staff',
        ]);

        $userToEdit = User::factory()->create();

        $response = $this->actingAs($staff)->get(route('users.edit', $userToEdit));

        $response->assertForbidden();
    }

    public function test_staff_does_not_see_users_navigation_link(): void
    {
        $staff = User::factory()->create([
            'role' => 'Staff',
        ]);

        $response = $this->actingAs($staff)->get(route('dashboard'));

        $response->assertStatus(200);
        $response->assertDontSee(route('users.index'));
        $response->assertSee('id="page-loader"', false);
    }

    public function test_staff_can_view_allowed_sections_and_change_password(): void
    {
        $staff = User::factory()->create(['role' => 'Staff']);

        foreach ([
            'dashboard',
            'technicians.index',
            'tickets.index',
            'categories.index',
            'reports.index',
            'settings.edit',
        ] as $routeName) {
            $this->actingAs($staff)->get(route($routeName))->assertOk();
        }

        $response = $this->actingAs($staff)->put(route('settings.update'), [
            'name' => $staff->name,
            'email' => $staff->email,
            'password' => 'new-password',
            'password_confirmation' => 'new-password',
        ]);

        $response->assertRedirect();
        $this->assertTrue(password_verify('new-password', $staff->fresh()->password));
    }

    public function test_staff_cannot_access_technician_or_category_creation_pages(): void
    {
        $staff = User::factory()->create(['role' => 'Staff']);

        foreach (['technicians.create', 'categories.create'] as $routeName) {
            $this->actingAs($staff)->get(route($routeName))->assertForbidden();
        }
    }

    public function test_staff_can_create_tickets(): void
    {
        $staff = User::factory()->create(['role' => 'Staff']);
        $category = Category::create(['name' => 'Hardware']);

        $this->actingAs($staff)
            ->get(route('tickets.create'))
            ->assertOk()
            ->assertSee('Create Ticket')
            ->assertSee('New Ticket');

        $response = $this->actingAs($staff)->post(route('tickets.store'), [
            'title' => 'Laptop will not start',
            'description' => 'The laptop shuts down during startup.',
            'category_id' => $category->id,
            'requester_id' => $staff->id,
            'priority' => 'Medium',
            'status' => 'Open',
        ]);

        $ticket = Ticket::firstOrFail();

        $response->assertRedirect(route('tickets.show', $ticket));
        $this->assertDatabaseHas('tickets', [
            'id' => $ticket->id,
            'title' => 'Laptop will not start',
            'user_id' => $staff->id,
        ]);
    }

    public function test_staff_can_update_and_delete_tickets(): void
    {
        $staff = User::factory()->create(['role' => 'Staff']);
        $category = Category::create(['name' => 'Hardware']);
        $ticket = Ticket::create([
            'title' => 'Laptop issue',
            'description' => 'The laptop will not start.',
            'user_id' => $staff->id,
            'category_id' => $category->id,
            'priority' => 'Medium',
            'status' => 'Open',
        ]);

        $this->actingAs($staff)
            ->get(route('tickets.show', $ticket))
            ->assertOk()
            ->assertSee('Edit')
            ->assertSee('Delete')
            ->assertDontSee('Write a comment...');

        $this->actingAs($staff)
            ->get(route('tickets.edit', $ticket))
            ->assertOk()
            ->assertSee('Edit Ticket');

        $this->actingAs($staff)->put(route('tickets.update', $ticket), [
            'title' => 'Laptop power issue',
            'description' => 'The laptop shuts down during startup.',
            'category_id' => $category->id,
            'requester_id' => $staff->id,
            'priority' => 'High',
            'status' => 'In Progress',
        ])->assertRedirect(route('tickets.show', $ticket));

        $this->assertDatabaseHas('tickets', [
            'id' => $ticket->id,
            'title' => 'Laptop power issue',
            'status' => 'In Progress',
        ]);
        $this->assertDatabaseHas('ticket_activities', [
            'ticket_id' => $ticket->id,
            'user_id' => $staff->id,
            'type' => 'updated',
        ]);

        $this->actingAs($staff)
            ->delete(route('tickets.destroy', $ticket))
            ->assertRedirect(route('tickets.index'));

        $this->assertDatabaseMissing('tickets', ['id' => $ticket->id]);
    }

    public function test_staff_cannot_manage_other_records_or_add_ticket_comments(): void
    {
        $staff = User::factory()->create(['role' => 'Staff']);
        $category = Category::create(['name' => 'Hardware']);
        $technician = Technician::create([
            'name' => 'Alex Technician',
            'email' => 'alex.technician@example.com',
        ]);
        $ticket = Ticket::create([
            'title' => 'Laptop issue',
            'description' => 'The laptop will not start.',
            'user_id' => $staff->id,
            'category_id' => $category->id,
            'priority' => 'Medium',
            'status' => 'Open',
        ]);

        $requests = [
            ['put', route('categories.update', $category)],
            ['delete', route('categories.destroy', $category)],
            ['put', route('technicians.update', $technician)],
            ['delete', route('technicians.destroy', $technician)],
            ['post', route('tickets.comments.store', $ticket)],
        ];

        foreach ($requests as [$method, $url]) {
            $this->actingAs($staff)->{$method}($url)->assertForbidden();
        }

        $this->assertDatabaseHas('categories', ['id' => $category->id]);
        $this->assertDatabaseHas('technicians', ['id' => $technician->id]);
        $this->assertDatabaseHas('tickets', ['id' => $ticket->id]);

        $this->actingAs($staff)
            ->get(route('tickets.show', $ticket))
            ->assertOk()
            ->assertDontSee('Write a comment...');
    }

    public function test_staff_list_pages_hide_management_controls(): void
    {
        $staff = User::factory()->create(['role' => 'Staff']);

        $this->actingAs($staff)
            ->get(route('technicians.index'))
            ->assertDontSee('Add Technician')
            ->assertDontSee('Edit')
            ->assertDontSee('Delete');

        $this->actingAs($staff)
            ->get(route('categories.index'))
            ->assertDontSee('Add Category')
            ->assertDontSee('Edit')
            ->assertDontSee('Delete');

        $this->actingAs($staff)
            ->get(route('tickets.index'))
            ->assertSee('Create Ticket');
    }

    public function test_technicians_index_can_search_and_filter_by_status(): void
    {
        $admin = User::factory()->create(['role' => 'Admin']);

        Technician::create([
            'name' => 'Network Support Specialist',
            'email' => 'network@example.com',
            'phone' => '555-1001',
            'specialization' => 'Network Administration',
            'status' => 'Active',
        ]);

        Technician::create([
            'name' => 'Desktop Support Specialist',
            'email' => 'desktop@example.com',
            'phone' => '555-1002',
            'specialization' => 'Hardware Repair',
            'status' => 'Inactive',
        ]);

        $this->actingAs($admin)
            ->get(route('technicians.index', ['search' => 'network', 'status' => 'Active']))
            ->assertOk()
            ->assertSee('Network Support Specialist')
            ->assertDontSee('Desktop Support Specialist');

        $this->actingAs($admin)
            ->get(route('technicians.index', ['status' => 'Inactive']))
            ->assertOk()
            ->assertSee('Desktop Support Specialist');
    }

    public function test_admin_can_toggle_technician_status(): void
    {
        $admin = User::factory()->create(['role' => 'Admin']);
        $technician = Technician::create([
            'name' => 'Support Engineer',
            'email' => 'support@example.com',
            'status' => 'Inactive',
        ]);

        $response = $this->actingAs($admin)->post(route('technicians.toggle-status', $technician));

        $response->assertRedirect();
        $this->assertSame('Active', $technician->fresh()->status);
    }

    public function test_can_update_user_role_to_staff_without_password(): void
    {
        $admin = User::factory()->create(['role' => 'Admin']);
        $userToUpdate = User::factory()->create(['role' => 'Staff']);

        $response = $this->actingAs($admin)->put(route('users.update', $userToUpdate), [
            'name' => $userToUpdate->name,
            'email' => $userToUpdate->email,
            'role' => 'Staff',
        ]);

        $response->assertSessionHasNoErrors();
        $response->assertRedirect(route('users.index'));
        $this->assertSame('Staff', $userToUpdate->fresh()->role);
    }

    public function test_new_accounts_default_to_staff_role(): void
    {
        $user = User::factory()->create();

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'role' => 'Staff',
        ]);
    }

    public function test_cannot_create_user_with_removed_user_role(): void
    {
        $admin = User::factory()->create(['role' => 'Admin']);

        $response = $this->actingAs($admin)->post(route('users.store'), [
            'name' => 'Legacy Role Account',
            'email' => 'legacy-role@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            'role' => 'User',
        ]);

        $response->assertSessionHasErrors('role');
        $this->assertDatabaseMissing('users', ['email' => 'legacy-role@example.com']);
    }

    public function test_cannot_update_account_to_removed_user_role(): void
    {
        $admin = User::factory()->create(['role' => 'Admin']);
        $userToUpdate = User::factory()->create(['role' => 'Staff']);

        $response = $this->actingAs($admin)->put(route('users.update', $userToUpdate), [
            'name' => $userToUpdate->name,
            'email' => $userToUpdate->email,
            'role' => 'User',
        ]);

        $response->assertSessionHasErrors('role');
        $this->assertSame('Staff', $userToUpdate->fresh()->role);
    }
}
