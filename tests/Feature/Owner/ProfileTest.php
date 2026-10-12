<?php

namespace Tests\Feature\Owner;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class ProfileTest extends TestCase
{
    use RefreshDatabase;

    public function test_owner_profile_index_loads(): void
    {
        $this->actingAsOwner()->get('/owner/profil')->assertOk();
    }

    public function test_owner_can_update_profile(): void
    {
        $this->actingAsOwner();
        $owner = auth()->user();

        $this->post('/owner/profil', [
            'name' => 'Owner Baru',
            'username' => $owner->username,
            'email' => $owner->email,
            'phone' => '08123456789',
        ])->assertRedirect();

        $this->assertSame('Owner Baru', $owner->fresh()->name);
    }

    public function test_owner_profile_username_must_be_unique(): void
    {
        $this->actingAsOwner();
        $other = User::factory()->create();
        $owner = auth()->user();

        $this->post('/owner/profil', [
            'name' => $owner->name,
            'username' => $other->username,
            'email' => $owner->email,
        ])->assertSessionHasErrors('username');
    }

    public function test_owner_can_change_password_with_correct_current(): void
    {
        $this->actingAsOwner();
        $owner = auth()->user();
        $owner->update(['password' => Hash::make('passwordlama')]);

        $this->post('/owner/profil/password', [
            'current_password' => 'passwordlama',
            'password' => 'passwordbaru',
            'password_confirmation' => 'passwordbaru',
        ])->assertRedirect();

        $this->assertTrue(Hash::check('passwordbaru', $owner->fresh()->password));
    }

    public function test_change_password_fails_with_wrong_current(): void
    {
        $this->actingAsOwner();
        $owner = auth()->user();
        $owner->update(['password' => Hash::make('passwordlama')]);

        $this->post('/owner/profil/password', [
            'current_password' => 'salah',
            'password' => 'passwordbaru',
            'password_confirmation' => 'passwordbaru',
        ])->assertSessionHasErrors('current_password');

        $this->assertTrue(Hash::check('passwordlama', $owner->fresh()->password));
    }

    public function test_change_password_requires_confirmation(): void
    {
        $this->actingAsOwner();
        $owner = auth()->user();

        $this->post('/owner/profil/password', [
            'current_password' => 'passwordlama',
            'password' => 'passwordbaru',
            'password_confirmation' => 'beda',
        ])->assertSessionHasErrors('password');
    }
}
