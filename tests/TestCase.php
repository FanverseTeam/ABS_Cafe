<?php

namespace Tests;

use App\Models\CashShift;
use App\Models\User;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use RuntimeException;

abstract class TestCase extends BaseTestCase
{
    /**
     * Guard keselamatan: pastikan test TIDAK PERNAH berjalan pada database
     * pengembangan/produksi. Hanya izinkan sqlite in-memory atau database
     * yang namanya mengandung "test".
     */
    protected function setUp(): void
    {
        parent::setUp();

        $connection = config('database.default');
        $database = (string) config("database.connections.{$connection}.database");

        $isMemory = $connection === 'sqlite' && $database === ':memory:';
        $isTestNamed = str_contains($database, 'test');

        if (! $isMemory && ! $isTestNamed) {
            throw new RuntimeException(
                "Menolak menjalankan test pada database tidak aman: [{$connection}] {$database}. "
                .'Gunakan sqlite :memory: atau database yang namanya mengandung "test".'
            );
        }
    }

    protected function actingAsAdmin(): static
    {
        $this->actingAs(User::factory()->admin()->create());

        return $this;
    }

    protected function adminHomeRoute(): string
    {
        return route('admin.dashboard');
    }

    protected function adminHomePath(): string
    {
        return '/admin';
    }

    protected function actingAsOwner(): static
    {
        $this->actingAs(User::factory()->owner()->create());

        return $this;
    }

    protected function actingAsKasir(?array $attributes = []): static
    {
        $this->actingAs(User::factory()->kasir()->create($attributes));

        return $this;
    }

    protected function openShift(array $attributes = []): CashShift
    {
        return CashShift::factory()->create(array_merge([
            'user_id' => auth()->id(),
            'status' => 'buka',
            'opened_at' => now(),
        ], $attributes));
    }
}
