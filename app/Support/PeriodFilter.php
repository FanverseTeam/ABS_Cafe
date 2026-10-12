<?php

namespace App\Support;

use Carbon\Carbon;
use Illuminate\Http\Request;

/**
 * Helper filter periode untuk halaman Owner.
 *
 * Periode tersedia: Hari Ini, 7 Hari, 30 Hari, Bulan Ini, dan Kustom.
 * Menyertakan perhitungan periode pembanding (sebelumnya) untuk menampilkan
 * kenaikan/penurunan pada kartu ringkasan.
 */
class PeriodFilter
{
    public function __construct(
        public string $key,
        public Carbon $start,
        public Carbon $end,
        public Carbon $prevStart,
        public Carbon $prevEnd,
        public string $label,
    ) {}

    public static function fromRequest(Request $request): self
    {
        $key = (string) $request->query('periode', 'hari_ini');
        $now = Carbon::now();

        switch ($key) {
            case '7_hari':
                $start = $now->copy()->subDays(6)->startOfDay();
                $end = $now->copy()->endOfDay();
                $label = '7 Hari Terakhir';
                break;

            case '30_hari':
                $start = $now->copy()->subDays(29)->startOfDay();
                $end = $now->copy()->endOfDay();
                $label = '30 Hari Terakhir';
                break;

            case 'bulan_ini':
                $start = $now->copy()->startOfMonth();
                $end = $now->copy()->endOfMonth();
                $label = 'Bulan '.$now->copy()->translatedFormat('F Y');
                break;

            case 'kustom':
                $start = Carbon::parse($request->query('mulai', $now->copy()->subDays(6)->toDateString()))->startOfDay();
                $end = Carbon::parse($request->query('selesai', $now->toDateString()))->endOfDay();
                if ($end->lt($start)) {
                    [$start, $end] = [$end->copy()->startOfDay(), $start->copy()->endOfDay()];
                }
                $key = 'kustom';
                $label = 'Kustom: '.$start->translatedFormat('d M Y').' - '.$end->translatedFormat('d M Y');
                break;

            case 'hari_ini':
            default:
                $key = 'hari_ini';
                $start = $now->copy()->startOfDay();
                $end = $now->copy()->endOfDay();
                $label = 'Hari Ini, '.$now->copy()->translatedFormat('d M Y');
                break;
        }

        // Periode pembanding: durasi yang sama tepat sebelum periode terpilih.
        $days = $start->diffInDays($end) + 1;
        $prevEnd = $start->copy()->subDay()->endOfDay();
        $prevStart = $prevEnd->copy()->subDays($days - 1)->startOfDay();

        return new self($key, $start, $end, $prevStart, $prevEnd, $label);
    }

    /**
     * @return array<string, string>
     */
    public function queryParams(): array
    {
        $params = ['periode' => $this->key];

        if ($this->key === 'kustom') {
            $params['mulai'] = $this->start->toDateString();
            $params['selesai'] = $this->end->toDateString();
        }

        return $params;
    }

    public function days(): int
    {
        return $this->start->diffInDays($this->end) + 1;
    }

    /**
     * Label grafik yang dikelompokkan otomatis (per hari / minggu / bulan).
     *
     * @return array{group: string, labels: array<int, string>}
     */
    public function chartGrouping(): array
    {
        $days = $this->days();

        if ($days <= 31) {
            return ['group' => 'hari', 'labels' => []];
        }

        if ($days <= 120) {
            return ['group' => 'minggu', 'labels' => []];
        }

        return ['group' => 'bulan', 'labels' => []];
    }

    public static function percentChange(float $current, float $previous): ?float
    {
        if ($previous == 0.0) {
            return $current > 0 ? 100.0 : null;
        }

        return round((($current - $previous) / $previous) * 100, 1);
    }
}
