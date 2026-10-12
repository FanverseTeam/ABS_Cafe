<?php

namespace Tests\Feature\Kasir;

use App\Models\Order;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_kasir_dashboard_loads_without_shift(): void
    {
        $this->actingAsKasir()->get('/kasir')->assertOk();
    }

    public function test_kasir_dashboard_loads_with_active_shift(): void
    {
        $this->actingAsKasir();
        $this->openShift();

        $this->get('/kasir')->assertOk();
    }

    public function test_pending_notification_counts_new_web_orders(): void
    {
        $this->actingAsKasir();

        Order::factory()->web()->create();
        Order::factory()->web()->create(['order_status' => 'diproses']);

        $this->getJson('/kasir/notifikasi/pending')
            ->assertOk()
            ->assertJsonPath('web_new', 1);
    }
}
