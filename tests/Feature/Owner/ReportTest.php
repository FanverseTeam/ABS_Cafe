<?php

namespace Tests\Feature\Owner;

use App\Models\Menu;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReportTest extends TestCase
{
    use RefreshDatabase;

    public function test_report_index_loads_for_each_tab(): void
    {
        $this->actingAsOwner()->get('/owner/laporan')->assertOk();
        $this->actingAsOwner()->get('/owner/laporan?tab=keuangan')->assertOk();
        $this->actingAsOwner()->get('/owner/laporan?tab=stok')->assertOk();
        $this->actingAsOwner()->get('/owner/laporan?tab=ngawur')->assertOk();
    }

    public function test_report_reflects_paid_orders(): void
    {
        $order = Order::factory()->paid()->create(['total' => 75000, 'subtotal' => 68182, 'created_at' => now()]);
        OrderItem::factory()->for($order)->create(['name' => 'Kopi Laporan', 'quantity' => 2, 'subtotal' => 68000]);
        Order::factory()->create(['payment_status' => 'menunggu_pembayaran', 'created_at' => now()]);

        $response = $this->actingAsOwner()->get('/owner/laporan?periode=hari_ini');
        $response->assertOk();
        $response->assertViewHas('data', function ($data) {
            return (float) $data['summary']['net'] === 75000.0
                && $data['summary']['orders'] === 1;
        });
    }

    public function test_report_supports_custom_period(): void
    {
        $this->actingAsOwner()->get('/owner/laporan?periode=kustom&mulai='.now()->subDays(5)->toDateString().'&selesai='.now()->toDateString())
            ->assertOk();
    }

    public function test_report_can_be_exported_as_csv_for_each_tab(): void
    {
        foreach (['penjualan', 'keuangan', 'stok'] as $tab) {
            $response = $this->actingAsOwner()->get("/owner/laporan/export?tab={$tab}");
            $response->assertOk();
            $this->assertStringContainsString('text/csv', $response->headers->get('content-type'));
        }
    }

    public function test_report_print_view_loads(): void
    {
        Menu::factory()->lowStock()->create();

        $this->actingAsOwner()->get('/owner/laporan/cetak')->assertOk();
    }
}
