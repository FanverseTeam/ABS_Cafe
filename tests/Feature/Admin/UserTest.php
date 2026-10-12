<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    private function payload(array $overrides = []): array
    {
        return array_merge([
            'name' => 'Staf Baru',
            'username' => 'stafbaru',
            'email' => 'stafbaru@abscafe.com',
            'password' => 'rahasia123',
            'role' => 'kasir',
            'status' => 'aktif',
            'phone' => '081200000000',
        ], $overrides);
    }

    public function test_user_index_loads(): void
    {
        User::factory()->create(['name' => 'Orang Penting']);

        $this->actingAsAdmin()->get('/admin/pengguna')->assertOk()->assertSee('Orang Penting');
    }

    public function test_admin_can_create_user_with_hashed_password(): void
    {
        $this->actingAsAdmin()->post('/admin/pengguna/store', $this->payload())->assertRedirect();

        $user = User::where('username', 'stafbaru')->first();
        $this->assertNotNull($user);
        $this->assertTrue(Hash::check('rahasia123', $user->password));
        $this->assertDatabaseHas('activity_logs', ['activity_type' => 'pengguna']);
    }

    public function test_create_user_rejects_duplicate_username_and_email(): void
    {
        User::factory()->create(['username' => 'stafbaru', 'email' => 'stafbaru@abscafe.com']);

        $this->actingAsAdmin()->post('/admin/pengguna/store', $this->payload())
            ->assertSessionHasErrors(['username', 'email']);
    }

    public function test_create_user_validates_role_and_password_length(): void
    {
        $this->actingAsAdmin()->post('/admin/pengguna/store', $this->payload([
            'role' => 'superhero',
            'password' => '123',
        ]))->assertSessionHasErrors(['role', 'password']);
    }

    public function test_admin_can_update_user(): void
    {
        $user = User::factory()->create(['name' => 'Budi']);

        $this->actingAsAdmin()->post("/admin/pengguna/{$user->id}/update", [
            'name' => 'Budi Santoso',
            'username' => $user->username,
            'email' => $user->email,
            'role' => 'kasir',
            'status' => 'aktif',
        ])->assertRedirect();

        $this->assertSame('Budi Santoso', $user->fresh()->name);
    }

    public function test_admin_cannot_deactivate_self(): void
    {
        $this->actingAsAdmin();
        $self = auth()->user();

        $this->post("/admin/pengguna/{$self->id}/toggle-status")
            ->assertRedirect()
            ->assertSessionHas('error');

        $this->assertSame('aktif', $self->fresh()->status);
    }

    public function test_admin_can_toggle_another_user_status(): void
    {
        $this->actingAsAdmin();
        $other = User::factory()->kasir()->create(['status' => 'aktif']);

        $this->post("/admin/pengguna/{$other->id}/toggle-status")->assertRedirect();

        $this->assertSame('nonaktif', $other->fresh()->status);
    }

    public function test_admin_can_reset_password_with_custom_value(): void
    {
        $user = User::factory()->create();
        $this->actingAsAdmin();

        $this->post("/admin/pengguna/{$user->id}/reset-password", ['new_password' => 'passwordbaru'])
            ->assertRedirect()
            ->assertSessionHas('success');

        $this->assertTrue(Hash::check('passwordbaru', $user->fresh()->password));
    }

    public function test_reset_password_rejects_short_value(): void
    {
        $user = User::factory()->create();
        $this->actingAsAdmin();

        $this->post("/admin/pengguna/{$user->id}/reset-password", ['new_password' => '123'])
            ->assertSessionHasErrors('new_password');
    }

    public function test_reset_password_without_value_uses_default(): void
    {
        $user = User::factory()->create();
        $this->actingAsAdmin();

        $this->post("/admin/pengguna/{$user->id}/reset-password")->assertRedirect();

        $this->assertTrue(Hash::check('password123', $user->fresh()->password));
    }

    public function test_admin_cannot_delete_self(): void
    {
        $this->actingAsAdmin();
        $self = auth()->user();

        $this->delete("/admin/pengguna/{$self->id}")
            ->assertRedirect()
            ->assertSessionHas('error');

        $this->assertDatabaseHas('users', ['id' => $self->id]);
    }

    public function test_admin_can_delete_another_user(): void
    {
        $this->actingAsAdmin();
        $other = User::factory()->kasir()->create();

        $this->delete("/admin/pengguna/{$other->id}")->assertRedirect();

        $this->assertDatabaseMissing('users', ['id' => $other->id]);
    }
}
