<?php

namespace Tests\Feature\Kasir;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class ProfileTest extends TestCase
{
    use RefreshDatabase;

    public function test_kasir_profile_index_loads(): void
    {
        $this->actingAsKasir()->get('/kasir/profil')->assertOk();
    }

    public function test_kasir_can_update_profile(): void
    {
        $this->actingAsKasir();
        $kasir = auth()->user();

        $this->post('/kasir/profil', [
            'name' => 'Kasir Baru',
            'username' => $kasir->username,
            'email' => $kasir->email,
            'phone' => '0812000',
        ])->assertRedirect();

        $this->assertSame('Kasir Baru', $kasir->fresh()->name);
    }

    public function test_kasir_can_change_password(): void
    {
        $this->actingAsKasir();
        $kasir = auth()->user();
        $kasir->update(['password' => Hash::make('passwordlama')]);

        $this->post('/kasir/profil/password', [
            'current_password' => 'passwordlama',
            'password' => 'passwordbaru',
            'password_confirmation' => 'passwordbaru',
        ])->assertRedirect();

        $this->assertTrue(Hash::check('passwordbaru', $kasir->fresh()->password));
    }

    public function test_change_password_fails_with_wrong_current(): void
    {
        $this->actingAsKasir();
        $kasir = auth()->user();
        $kasir->update(['password' => Hash::make('passwordlama')]);

        $this->post('/kasir/profil/password', [
            'current_password' => 'salah',
            'password' => 'passwordbaru',
            'password_confirmation' => 'passwordbaru',
        ])->assertSessionHasErrors('current_password');
    }
}