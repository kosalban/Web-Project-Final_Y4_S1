<?php

namespace Tests\Feature;

use Tests\TestCase;

class AboutPageTest extends TestCase
{
    public function test_about_page_is_public_and_explains_the_support_system(): void
    {
        $response = $this->get(route('about'));

        $response->assertOk()
            ->assertSeeText('About KOSAL-IT-Room')
            ->assertSeeText('Organized support tickets')
            ->assertSeeText('Clear assignment and status')
            ->assertSee(route('login'));
    }

    public function test_about_page_uses_the_guest_selected_language(): void
    {
        $response = $this->withSession(['locale' => 'km'])
            ->get(route('about'));

        $response->assertOk()
            ->assertSee('lang="km"', false)
            ->assertSeeText('អំពី KOSAL-IT-Room')
            ->assertSeeText('មុខងាររបស់ប្រព័ន្ធ');
    }

    public function test_login_page_links_to_the_public_about_page(): void
    {
        $response = $this->get(route('login'));

        $response->assertOk()
            ->assertSeeText('About this system')
            ->assertSee(route('about'));
    }
}
