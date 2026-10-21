<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Ticket;
use App\Models\TicketComment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LanguagePreferenceTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_switch_and_persist_english_and_khmer(): void
    {
        $user = User::factory()->create([
            'locale' => 'en',
            'role' => 'Admin',
        ]);

        $this->actingAs($user)
            ->from(route('dashboard'))
            ->post(route('settings.language'), ['locale' => 'km'])
            ->assertRedirect(route('dashboard'));

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'locale' => 'km',
        ]);

        $this->actingAs($user)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('<html lang="km"', false)
            ->assertSee('ផ្ទាំងគ្រប់គ្រង')
            ->assertSee('សំបុត្រការងារ')
            ->assertSee('English')
            ->assertDontSee('Welcome back');

        foreach ([
            'users.index' => 'អ្នកប្រើប្រាស់',
            'technicians.index' => 'អ្នកបច្ចេកទេស',
            'tickets.index' => 'សំបុត្រការងារ',
            'categories.index' => 'ប្រភេទ',
            'reports.index' => 'របាយការណ៍សំបុត្រការងារ',
            'settings.edit' => 'ការកំណត់ប្រវត្តិរូប',
        ] as $routeName => $translatedHeading) {
            $this->actingAs($user)
                ->get(route($routeName))
                ->assertOk()
                ->assertSee($translatedHeading);
        }

        $this->actingAs($user)
            ->from(route('dashboard'))
            ->post(route('settings.language'), ['locale' => 'en'])
            ->assertRedirect(route('dashboard'));

        $this->actingAs($user)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('<html lang="en"', false)
            ->assertSee('Dashboard')
            ->assertSee('Switch to Khmer');
    }

    public function test_guest_can_select_khmer_for_login_page(): void
    {
        $this->from(route('login'))
            ->post(route('settings.language'), ['locale' => 'km'])
            ->assertRedirect(route('login'));

        $this->get(route('login'))
            ->assertOk()
            ->assertSee('<html lang="km"', false)
            ->assertSee('ចូលប្រើ');
    }

    public function test_guest_language_choice_is_saved_to_the_account_after_login(): void
    {
        $user = User::factory()->create([
            'email' => 'khmer-user@example.com',
            'password' => 'password',
            'locale' => 'en',
        ]);

        $this->from(route('login'))
            ->post(route('settings.language'), ['locale' => 'km'])
            ->assertRedirect(route('login'));

        $this->post(route('login.store'), [
            'email' => 'khmer-user@example.com',
            'password' => 'password',
        ])->assertRedirect(route('dashboard'));

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'locale' => 'km',
        ]);

        $this->get(route('dashboard'))
            ->assertOk()
            ->assertSee('<html lang="km"', false)
            ->assertSee('ផ្ទាំងគ្រប់គ្រង');
    }

    public function test_user_cannot_select_unsupported_locale(): void
    {
        $user = User::factory()->create(['locale' => 'en']);

        $this->actingAs($user)
            ->post(route('settings.language'), ['locale' => 'fr'])
            ->assertSessionHasErrors('locale');

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'locale' => 'en',
        ]);
    }

    public function test_relative_ticket_times_are_localized_for_the_signed_in_user(): void
    {
        $this->travelTo('2026-10-03 12:00:00');
        $user = User::factory()->create([
            'locale' => 'km',
            'role' => 'Admin',
        ]);
        $category = Category::create(['name' => 'Hardware']);
        $ticket = Ticket::create([
            'title' => 'Laptop issue',
            'description' => 'Laptop will not start.',
            'user_id' => $user->id,
            'category_id' => $category->id,
            'priority' => 'Medium',
            'status' => 'Open',
        ]);
        $comment = TicketComment::create([
            'ticket_id' => $ticket->id,
            'user_id' => $user->id,
            'comment' => 'Checked the device.',
        ]);
        $comment->created_at = now()->subDay();
        $comment->save();

        $this->actingAs($user)
            ->get(route('tickets.show', $ticket))
            ->assertOk()
            ->assertSee('1 ថ្ងៃមុន');
    }

    public function test_ticket_notifications_are_saved_in_the_recipient_language(): void
    {
        $admin = User::factory()->create([
            'locale' => 'en',
            'role' => 'Admin',
        ]);
        $requester = User::factory()->create(['locale' => 'km']);
        $category = Category::create(['name' => 'Hardware']);
        $ticket = Ticket::create([
            'title' => 'Laptop issue',
            'description' => 'Laptop will not start.',
            'user_id' => $requester->id,
            'category_id' => $category->id,
            'priority' => 'Medium',
            'status' => 'Open',
        ]);

        $this->actingAs($admin)
            ->post(route('tickets.comments.store', $ticket), ['comment' => 'Investigating the issue.'])
            ->assertRedirect();

        $notification = $requester->fresh()->notifications()->first();

        $this->assertSame('មតិយោបល់ថ្មីលើសំបុត្រការងារ', $notification->data['title']);
        $this->assertSame('បានបន្ថែមមតិយោបល់ទៅសំបុត្រការងារ #'.$ticket->id.'។', $notification->data['message']);
    }
}
