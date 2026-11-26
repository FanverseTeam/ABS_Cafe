<?php

namespace Tests\Feature\Owner;

use App\Models\Order;
use App\Models\Promo;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_owner_dashboard_loads(): void
    {
        $this->actingAsOwner()->get('/owner')->assertOk();
    }

    public function test_summary_only_counts_paid_orders_within_period(): void
    {
        Order::factory()->paid()->create(['total' => 200000, 'subtotal' => 180000, 'created_at' => now()]);
        Order::factory()->create(['total' => 999999, 'payment_status' => 'menunggu_pembayaran', 'created_at' => now()]);

        $response = $this->actingAsOwner()->get('/owner?periode=hari_ini');
        $response->assertOk();
        $response->assertViewHas('summary', fn ($summary) => (float) $summary['sales']['value'] === 200000.0);
    }

    public function test_removed_sections_are_not_displayed_on_owner_dashboard(): void
    {
        $response = $this->actingAsOwner()->get('/owner');

        $response->assertOk()
            ->assertViewMissing('targets')
            ->assertViewMissing('cashierPerformance')
            ->assertViewMissing('unavailableMenus')
            ->assertViewMissing('recentOrders')
            ->assertViewMissing('dailyRecap')
            ->assertViewMissing('topCategories')
            ->assertDontSee('Pencapaian Target')
            ->assertDontSee('Kinerja Kasir')
            ->assertDontSee('Pesanan Terbaru')
            ->assertDontSee('Rata-rata Order')
            ->assertDontSee('Laba (')
            ->assertDontSee('Kategori Teratas')
            ->assertDontSee('Rekap Penjualan Harian')
            ->assertDontSee('Semua menu aktif dan dapat dipesan.');
    }

    public function test_today_sales_is_displayed_on_owner_dashboard(): void
    {
        Order::factory()->paid()->create([
            'subtotal' => 150000,
            'discount' => 10000,
            'total' => 140000,
            'created_at' => now(),
        ]);

        $response = $this->actingAsOwner()->get('/owner?periode=7_hari');

        $response->assertOk()
            ->assertSee('Total Penjualan Hari Ini')
            ->assertViewHas('todaySales', fn ($today) => (float) $today['total'] === 140000.0 && $today['orders'] === 1);
    }

    public function test_owner_can_export_multi_day_daily_recap_csv(): void
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

        $response = $this->actingAsOwner()->get(route('owner.daily_recap.export', [
            'mulai' => $start,
            'selesai' => $end,
        ]));

        $response->assertOk();
        $this->assertStringContainsString('text/csv', (string) $response->headers->get('Content-Type'));

        $content = $response->streamedContent();
        $this->assertStringContainsString('Tanggal,Hari,', $content);
        $this->assertStringContainsString($start, $content);
        $this->assertStringContainsString($end, $content);
        $this->assertStringContainsString('TOTAL,,2,170000,20000,150000', $content);
    }

    public function test_sales_percentage_chart_supports_hari_bulan_and_tahun_ranges(): void
    {
        Order::factory()->paid()->create(['total' => 300000, 'created_at' => now()]);
        Order::factory()->paid()->create(['total' => 100000, 'created_at' => now()->subDay()]);

        $dailyResponse = $this->actingAsOwner()->get('/owner?chart_range=hari');
        $dailyResponse->assertOk()->assertViewHas('chart', function ($chart) {
            return $chart['range'] === 'hari'
                && count($chart['percentages']) === 7
                && abs(array_sum($chart['percentages']) - 100.0) < 0.2;
        });

        $monthlyResponse = $this->actingAsOwner()->get('/owner?chart_range=bulan');
        $monthlyResponse->assertOk()->assertViewHas('chart', function ($chart) {
            return $chart['range'] === 'bulan'
                && count($chart['labels']) === 12
                && count($chart['percentages']) === 12;
        });

        $yearlyResponse = $this->actingAsOwner()->get('/owner?chart_range=tahun');
        $yearlyResponse->assertOk()->assertViewHas('chart', function ($chart) {
            return $chart['range'] === 'tahun'
                && count($chart['labels']) === 5
                && count($chart['percentages']) === 5;
        });
    }

    public function test_active_promos_are_listed(): void
    {
        Promo::factory()->create(['title' => 'Promo Aktif Sekarang', 'is_active' => true]);
        Promo::factory()->inactive()->create(['title' => 'Promo Mati']);

        $this->actingAsOwner()->get('/owner')
            ->assertOk()
            ->assertViewHas('activePromos', fn ($promos) => $promos->pluck('title')->contains('Promo Aktif Sekarang')
                && ! $promos->pluck('title')->contains('Promo Mati'));
    }

    public function test_period_change_is_reflected(): void
    {
        $old = Order::factory()->paid()->create(['total' => 50000, 'created_at' => now()->subDays(20)]);

        $response = $this->actingAsOwner()->get('/owner?periode=7_hari');
        $response->assertOk();
        $response->assertViewHas('summary', fn ($summary) => (float) $summary['sales']['value'] === 0.0);

        $this->assertNotNull($old);
    }
}
