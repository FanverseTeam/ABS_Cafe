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

    public function test_report_can_be_exported_as_csv_with_date_range(): void
    {
        $start = now()->subDays(2)->toDateString();
        $end = now()->toDateString();

        Order::factory()->paid()->create([
            'subtotal' => 120000,
            'discount' => 20000,
            'total' => 100000,
            'created_at' => now()->subDays(2),
        ]);

        Order::factory()->paid()->create([
            'subtotal' => 50000,
            'discount' => 0,
            'total' => 50000,
            'created_at' => now(),
        ]);

        $response = $this->actingAsOwner()->get("/owner/laporan/export?mulai={$start}&selesai={$end}");
        $response->assertOk();
        $this->assertStringContainsString('text/csv', (string) $response->headers->get('content-type'));

        $content = $response->streamedContent();
        $this->assertStringContainsString('Tanggal,Hari,', $content);
        $this->assertStringContainsString($start, $content);
        $this->assertStringContainsString($end, $content);
        $this->assertStringContainsString('TOTAL,,2,170000,20000,150000', $content);
    }

    public function test_report_print_view_loads(): void
    {
        Menu::factory()->unavailable()->create();

        $this->actingAsOwner()->get('/owner/laporan/cetak')->assertOk();
    }
}
