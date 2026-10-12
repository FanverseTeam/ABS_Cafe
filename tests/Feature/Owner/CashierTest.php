<?php

namespace Tests\Feature\Owner;

use App\Models\CashShift;
use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CashierTest extends TestCase
{
    use RefreshDatabase;

    public function test_cashier_performance_index_loads(): void
    {
        $kasir = User::factory()->kasir()->create(['name' => 'Kasir Andalan']);
        Order::factory()->paid()->create(['cashier_id' => $kasir->id, 'cashier_name' => $kasir->name, 'created_at' => now()]);

        $this->actingAsOwner()->get('/owner/kinerja-kasir')
            ->assertOk()
            ->assertSee('Kasir Andalan');
    }

    public function test_cashier_detail_loads(): void
    {
        $kasir = User::factory()->kasir()->create();
        CashShift::factory()->for($kasir)->create(['opened_at' => now()]);
        Order::factory()->paid()->create(['cashier_id' => $kasir->id, 'created_at' => now()]);

        $this->actingAsOwner()->get("/owner/kinerja-kasir/{$kasir->id}")->assertOk();
    }

    public function test_cashier_detail_rejects_non_kasir_user(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAsOwner()->get("/owner/kinerja-kasir/{$admin->id}")->assertNotFound();
    }
}
