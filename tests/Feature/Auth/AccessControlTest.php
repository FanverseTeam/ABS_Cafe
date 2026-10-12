<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AccessControlTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_are_redirected_from_admin_area(): void
    {
        $this->get('/admin')->assertRedirect(route('admin.login'));
    }

    public function test_guests_are_redirected_from_owner_area(): void
    {
        $this->get('/owner')->assertRedirect(route('login'));
    }

    public function test_guests_are_redirected_from_kasir_area(): void
    {
        $this->get('/kasir')->assertRedirect(route('login'));
    }

    public function test_admin_can_access_admin_area(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)->get('/admin')->assertOk();
    }

    public function test_kasir_cannot_access_admin_area(): void
    {
        $kasir = User::factory()->kasir()->create();

        $this->actingAs($kasir)->get('/admin')->assertRedirect(route('admin.login'));
        $this->assertGuest();
    }

    public function test_owner_cannot_access_admin_area(): void
    {
        $owner = User::factory()->owner()->create();

        $this->actingAs($owner)->get('/admin')->assertRedirect(route('admin.login'));
        $this->assertGuest();
    }

    public function test_owner_can_access_owner_area(): void
    {
        $owner = User::factory()->owner()->create();

        $this->actingAs($owner)->get('/owner')->assertOk();
    }

    public function test_admin_cannot_access_owner_area(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)->get('/owner')->assertForbidden();
    }

    public function test_kasir_can_access_kasir_area(): void
    {
        $kasir = User::factory()->kasir()->create();

        $this->actingAs($kasir)->get('/kasir')->assertOk();
    }

    public function test_owner_cannot_access_kasir_area(): void
    {
        $owner = User::factory()->owner()->create();

        $this->actingAs($owner)->get('/kasir')->assertForbidden();
    }

    public function test_inactive_user_is_logged_out_when_accessing_protected_area(): void
    {
        $kasir = User::factory()->kasir()->create(['status' => 'nonaktif']);

        $this->actingAs($kasir)->get('/kasir')->assertRedirect(route('login'));
        $this->assertGuest();
    }

    public function test_kasir_transaction_routes_require_active_shift(): void
    {
        $kasir = User::factory()->kasir()->create();

        $this->actingAs($kasir)->get('/kasir/pos')->assertRedirect(route('kasir.shift.open'));
    }

    public function test_kasir_can_open_shift_page_without_active_shift(): void
    {
        $kasir = User::factory()->kasir()->create();

        $this->actingAs($kasir)->get('/kasir/shift/buka')->assertOk();
    }
}
