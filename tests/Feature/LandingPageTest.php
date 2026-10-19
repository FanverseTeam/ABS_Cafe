<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LandingPageTest extends TestCase
{
    use RefreshDatabase;

    public function test_staf_landing_page_is_accessible_for_guests(): void
    {
        $this->get('/staf')
            ->assertOk()
            ->assertSee('ABS Cafe')
            ->assertSee('Good Coffee')
            ->assertSee('Good Mood')
            ->assertSee('Portal Staf ABS Cafe')
            ->assertSee(route('login'));
    }

    public function test_staf_landing_page_has_correct_meta_tags(): void
    {
        $this->get('/staf')
            ->assertOk()
            ->assertSee('ABS Cafe – Good Coffee, Good Mood', false) // title
            ->assertSee('Portal Staf ABS Cafe', false) // description
            ->assertSee('og:title', false)
            ->assertSee('og:description', false)
            ->assertSee('og:image', false);
    }

    public function test_guest_sees_login_button_linking_to_login_route(): void
    {
        $this->get('/staf')
            ->assertOk()
            ->assertSee('Login')
            ->assertSee(route('login'));
    }

    public function test_admin_sees_dashboard_button_linking_to_admin_home(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)->get('/staf')
            ->assertOk()
            ->assertSee('Buka Dashboard')
            ->assertSee($this->adminHomeRoute());
    }

    public function test_owner_sees_dashboard_button_linking_to_owner_dashboard(): void
    {
        $owner = User::factory()->owner()->create();

        $this->actingAs($owner)->get('/staf')
            ->assertOk()
            ->assertSee('Buka Dashboard')
            ->assertSee(route('owner.dashboard'));
    }

    public function test_kasir_sees_dashboard_button_linking_to_kasir_dashboard(): void
    {
        $kasir = User::factory()->kasir()->create();

        $this->actingAs($kasir)->get('/staf')
            ->assertOk()
            ->assertSee('Buka Dashboard')
            ->assertSee(route('kasir.dashboard'));
    }

    public function test_landing_page_displays_role_chips(): void
    {
        $this->get('/staf')
            ->assertOk()
            ->assertSee('Admin')
            ->assertSee('Owner')
            ->assertSee('Kasir');
    }

    public function test_landing_page_has_footer_with_location(): void
    {
        $this->get('/staf')
            ->assertOk()
            ->assertSee('ABS Cafe')
            ->assertSee('Tulungagung');
    }

    public function test_landing_page_uses_indonesian_language(): void
    {
        $this->get('/staf')
            ->assertOk()
            ->assertSee('Kelola operasional dan pantau bisnis ABS Cafe dalam satu tempat');
    }

    public function test_root_redirects_to_staf_landing(): void
    {
        $this->get('/')->assertRedirect(route('staf.landing'));
    }

    public function test_logout_redirects_to_staf_landing(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)->post('/admin/logout')
            ->assertRedirect(route('staf.landing'))
            ->assertSessionHas('success');
    }
}
