<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ThemePreferenceTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_save_and_use_dark_or_light_theme(): void
    {
        $user = User::factory()->create(['theme' => 'light']);

        $this->from(route('dashboard'))
            ->actingAs($user)
            ->post(route('settings.theme'), ['theme' => 'dark'])
            ->assertRedirect(route('dashboard'));

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'theme' => 'dark',
        ]);

        $this->actingAs($user)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('data-bs-theme="dark"', false)
            ->assertSee('Switch to light theme');

        $this->from(route('dashboard'))
            ->actingAs($user)
            ->post(route('settings.theme'), ['theme' => 'light'])
            ->assertRedirect(route('dashboard'));

        $this->actingAs($user)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('data-bs-theme="light"', false)
            ->assertSee('Switch to dark theme');
    }

    public function test_user_cannot_save_an_unsupported_theme(): void
    {
        $user = User::factory()->create(['theme' => 'light']);

        $this->actingAs($user)
            ->post(route('settings.theme'), ['theme' => 'midnight'])
            ->assertSessionHasErrors('theme');

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'theme' => 'light',
        ]);
    }

    public function test_guest_cannot_change_a_theme_preference(): void
    {
        $this->post(route('settings.theme'), ['theme' => 'dark'])
            ->assertRedirect(route('login'));
    }
}
