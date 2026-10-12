<?php

namespace Tests\Unit;

use App\Support\PeriodFilter;
use Illuminate\Http\Request;
use Tests\TestCase;

class PeriodFilterTest extends TestCase
{
    private function filter(array $query): PeriodFilter
    {
        return PeriodFilter::fromRequest(Request::create('/owner', 'GET', $query));
    }

    public function test_default_period_is_today(): void
    {
        $period = $this->filter([]);

        $this->assertSame('hari_ini', $period->key);
        $this->assertTrue($period->start->isSameDay(now()));
        $this->assertTrue($period->end->isSameDay(now()));
    }

    public function test_seven_day_period_spans_seven_days(): void
    {
        $period = $this->filter(['periode' => '7_hari']);

        $this->assertSame('7_hari', $period->key);
        $this->assertSame(7, $period->days());
    }

    public function test_thirty_day_period_spans_thirty_days(): void
    {
        $period = $this->filter(['periode' => '30_hari']);

        $this->assertSame(30, $period->days());
    }

    public function test_month_period_covers_current_month(): void
    {
        $period = $this->filter(['periode' => 'bulan_ini']);

        $this->assertSame('bulan_ini', $period->key);
        $this->assertSame(now()->startOfMonth()->toDateString(), $period->start->toDateString());
    }

    public function test_custom_period_swaps_reversed_dates(): void
    {
        $period = $this->filter([
            'periode' => 'kustom',
            'mulai' => now()->toDateString(),
            'selesai' => now()->subDays(4)->toDateString(),
        ]);

        $this->assertTrue($period->start->lte($period->end));
        $this->assertSame('kustom', $period->key);
    }

    public function test_previous_period_ends_right_before_current(): void
    {
        $period = $this->filter(['periode' => '7_hari']);

        $this->assertTrue($period->prevEnd->lt($period->start));
        $this->assertTrue($period->prevStart->lt($period->prevEnd));
        $this->assertTrue($period->prevStart->diffInDays($period->prevEnd) >= 6);
    }

    public function test_percent_change_calculations(): void
    {
        $this->assertSame(100.0, PeriodFilter::percentChange(100, 0));
        $this->assertNull(PeriodFilter::percentChange(0, 0));
        $this->assertSame(50.0, PeriodFilter::percentChange(150, 100));
        $this->assertSame(-50.0, PeriodFilter::percentChange(50, 100));
    }

    public function test_chart_grouping_by_range(): void
    {
        $this->assertSame('hari', $this->filter(['periode' => '7_hari'])->chartGrouping()['group']);
        $this->assertSame('minggu', PeriodFilter::fromRequest(
            Request::create('/owner', 'GET', ['periode' => 'kustom', 'mulai' => now()->subDays(90)->toDateString(), 'selesai' => now()->toDateString()])
        )->chartGrouping()['group']);
        $this->assertSame('bulan', PeriodFilter::fromRequest(
            Request::create('/owner', 'GET', ['periode' => 'kustom', 'mulai' => now()->subDays(300)->toDateString(), 'selesai' => now()->toDateString()])
        )->chartGrouping()['group']);
    }
}
