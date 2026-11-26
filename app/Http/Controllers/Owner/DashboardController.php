<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\CafeSetting;
use App\Models\Order;
use App\Models\Promo;
use App\Support\MenuSalesReport;
use App\Support\PeriodFilter;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $period = PeriodFilter::fromRequest($request);
        $chartRange = in_array($request->query('chart_range'), ['hari', 'bulan', 'tahun'], true)
            ? (string) $request->query('chart_range')
            : 'hari';
        $margin = (float) CafeSetting::get('profit_margin_percent', '40');

        $summary = $this->summary($period, $margin);

        // Total penjualan hari ini
        $todayOrders = $this->paidOrders(Carbon::today()->startOfDay(), Carbon::today()->endOfDay());
        $todaySales = [
            'total' => (float) $todayOrders->sum('total'),
            'orders' => (int) $todayOrders->count(),
        ];

        // Grafik tren persentase penjualan (pilihan jangka waktu: hari / bulan / tahun)
        $chart = $this->salesPercentageChart($period, $chartRange);

        // Komposisi metode pembayaran
        $paid = $this->paidOrders($period->start, $period->end);
        $paymentComposition = $paid
            ->groupBy('payment_method')
            ->map(fn ($rows, $method) => [
                'method' => $method ?: 'lainnya',
                'count' => $rows->count(),
                'total' => (float) $rows->sum('total'),
            ])
            ->values();

        // Top menu (dari item pesanan, bukan counter statis)
        $topMenus = $this->topMenus($period, 5);

        // Promo aktif
        $activePromos = Promo::where('is_active', true)
            ->whereDate('start_date', '<=', now())
            ->whereDate('end_date', '>=', now())
            ->orderBy('end_date')
            ->take(5)
            ->get();

        return view('owner.dashboard', compact(
            'period',
            'chartRange',
            'summary',
            'todaySales',
            'chart',
            'paymentComposition',
            'topMenus',
            'activePromos',
        ));
    }

    /**
     * Ekspor rekap penjualan harian berdasarkan rentang tanggal pilihan (bisa banyak hari sekaligus).
     */
    public function exportDailyRecap(Request $request): StreamedResponse
    {
        $now = Carbon::now();
        $start = Carbon::parse($request->query('mulai', $now->copy()->subDays(6)->toDateString()))->startOfDay();
        $end = Carbon::parse($request->query('selesai', $now->toDateString()))->endOfDay();

        if ($end->lt($start)) {
            [$start, $end] = [$end->copy()->startOfDay(), $start->copy()->endOfDay()];
        }

        $recap = $this->buildDailyRecap($start, $end);
        $fileName = 'Rekap_Penjualan_Harian_ABS_Cafe_'.$start->format('Ymd').'-'.$end->format('Ymd').'.csv';

        $headers = [
            'Content-type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename={$fileName}",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        return response()->stream(function () use ($recap): void {
            $file = fopen('php://output', 'w');
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            fputcsv($file, ['Tanggal', 'Hari', 'Jumlah Pesanan', 'Penjualan Kotor (Rp)', 'Diskon (Rp)', 'Total Penjualan (Rp)']);

            foreach ($recap['rows'] as $row) {
                fputcsv($file, [
                    $row['date'],
                    $row['day_name'],
                    $row['orders'],
                    (int) round($row['gross']),
                    (int) round($row['discount']),
                    (int) round($row['total']),
                ]);
            }

            fputcsv($file, [
                'TOTAL',
                '',
                $recap['totals']['orders'],
                (int) round($recap['totals']['gross']),
                (int) round($recap['totals']['discount']),
                (int) round($recap['totals']['total']),
            ]);

            fclose($file);
        }, 200, $headers);
    }

    /**
     * @return array<string, array{value: float, change: ?float}|float>
     */
    private function summary(PeriodFilter $period, float $margin): array
    {
        $current = $this->paidOrders($period->start, $period->end);
        $previous = $this->paidOrders($period->prevStart, $period->prevEnd);

        $salesCurrent = (float) $current->sum('total');
        $salesPrev = (float) $previous->sum('total');

        $ordersCurrent = (int) $current->count();
        $ordersPrev = (int) $previous->count();

        $avgCurrent = $ordersCurrent > 0 ? $salesCurrent / $ordersCurrent : 0;
        $avgPrev = $ordersPrev > 0 ? $salesPrev / $ordersPrev : 0;

        $discountCurrent = (float) $current->sum('discount');
        $discountPrev = (float) $previous->sum('discount');

        $netCurrent = $salesCurrent - $discountCurrent;
        $netPrev = $salesPrev - $discountPrev;

        return [
            'sales' => ['value' => $salesCurrent, 'change' => PeriodFilter::percentChange($salesCurrent, $salesPrev)],
            'orders' => ['value' => $ordersCurrent, 'change' => PeriodFilter::percentChange($ordersCurrent, $ordersPrev)],
            'avg' => ['value' => $avgCurrent, 'change' => PeriodFilter::percentChange($avgCurrent, $avgPrev)],
            'profit' => ['value' => $netCurrent, 'change' => PeriodFilter::percentChange($netCurrent, $netPrev)],
            'net' => ['value' => $netCurrent, 'change' => PeriodFilter::percentChange($netCurrent, $netPrev)],
            'discount' => ['value' => $discountCurrent, 'change' => PeriodFilter::percentChange($discountCurrent, $discountPrev)],
            'margin' => $margin,
        ];
    }

    /**
     * Membangun rekap penjualan per hari dalam rentang tanggal.
     *
     * @return array{
     *     rows: array<int, array{date: string, label: string, day_name: string, orders: int, gross: float, discount: float, total: float}>,
     *     totals: array{orders: int, gross: float, discount: float, total: float}
     * }
     */
    private function buildDailyRecap(Carbon $start, Carbon $end): array
    {
        $orders = Order::where('payment_status', 'lunas')
            ->whereBetween('created_at', [$start->copy()->startOfDay(), $end->copy()->endOfDay()])
            ->get(['id', 'created_at', 'subtotal', 'discount', 'total']);

        $grouped = $orders->groupBy(fn (Order $order) => $order->created_at->toDateString());

        $rows = [];
        $totalOrders = 0;
        $totalGross = 0.0;
        $totalDiscount = 0.0;
        $totalSales = 0.0;

        $cursor = $start->copy()->startOfDay();
        $endDay = $end->copy()->startOfDay();

        while ($cursor->lte($endDay)) {
            $dateKey = $cursor->toDateString();
            $dayOrders = $grouped->get($dateKey, collect());

            $count = $dayOrders->count();
            $gross = (float) $dayOrders->sum('subtotal');
            $discount = (float) $dayOrders->sum('discount');
            $total = (float) $dayOrders->sum('total');

            $rows[] = [
                'date' => $dateKey,
                'label' => $cursor->translatedFormat('d M Y'),
                'day_name' => $cursor->translatedFormat('l'),
                'orders' => $count,
                'gross' => $gross,
                'discount' => $discount,
                'total' => $total,
            ];

            $totalOrders += $count;
            $totalGross += $gross;
            $totalDiscount += $discount;
            $totalSales += $total;

            $cursor->addDay();
        }

        // Urutkan terbaru di atas untuk tampilan tabel beranda
        $rows = array_reverse($rows);

        return [
            'rows' => $rows,
            'totals' => [
                'orders' => $totalOrders,
                'gross' => $totalGross,
                'discount' => $totalDiscount,
                'total' => $totalSales,
            ],
        ];
    }

    /**
     * Grafik tren persentase penjualan berdasarkan jangka waktu (hari, bulan, tahun).
     *
     * @return array{labels: array<int, string>, percentages: array<int, float>, sales: array<int, float>, range: string, total_sales: float}
     */
    private function salesPercentageChart(PeriodFilter $period, string $range): array
    {
        $labels = [];
        $sales = [];

        if ($range === 'bulan') {
            $yearStart = $period->end->copy()->startOfYear();
            $yearEnd = $period->end->copy()->endOfYear();
            $orders = $this->paidOrders($yearStart, $yearEnd);

            for ($m = 1; $m <= 12; $m++) {
                $labels[] = Carbon::create($period->end->year, $m, 1)->translatedFormat('M Y');
                $sales[] = 0.0;
            }

            foreach ($orders as $order) {
                $idx = $order->created_at->month - 1;
                if (isset($sales[$idx])) {
                    $sales[$idx] += (float) $order->total;
                }
            }
        } elseif ($range === 'tahun') {
            $endYear = $period->end->year;
            $startYear = $endYear - 4;
            $rangeStart = Carbon::create($startYear, 1, 1)->startOfDay();
            $rangeEnd = Carbon::create($endYear, 12, 31)->endOfDay();
            $orders = $this->paidOrders($rangeStart, $rangeEnd);

            for ($y = $startYear; $y <= $endYear; $y++) {
                $labels[] = (string) $y;
                $sales[] = 0.0;
            }

            foreach ($orders as $order) {
                $idx = $order->created_at->year - $startYear;
                if (isset($sales[$idx])) {
                    $sales[$idx] += (float) $order->total;
                }
            }
        } else {
            // Default: harian — jika periode hanya 1 hari (Hari Ini), tampilkan 7 hari terakhir agar tren harian terlihat
            $chartStart = $period->days() > 1
                ? $period->start->copy()->startOfDay()
                : $period->end->copy()->subDays(6)->startOfDay();
            $chartEnd = $period->end->copy()->endOfDay();

            $orders = $this->paidOrders($chartStart, $chartEnd);

            $cursor = $chartStart->copy();
            while ($cursor->lte($chartEnd)) {
                $labels[] = $cursor->translatedFormat('d M');
                $sales[] = 0.0;
                $cursor->addDay();
            }

            foreach ($orders as $order) {
                $idx = $chartStart->diffInDays($order->created_at->copy()->startOfDay());
                if (isset($sales[$idx])) {
                    $sales[$idx] += (float) $order->total;
                }
            }
        }

        $totalSales = array_sum($sales);
        $percentages = array_map(
            fn (float $val): float => $totalSales > 0 ? round(($val / $totalSales) * 100, 1) : 0.0,
            $sales
        );

        return [
            'labels' => $labels,
            'percentages' => $percentages,
            'sales' => $sales,
            'range' => $range,
            'total_sales' => (float) $totalSales,
        ];
    }

    /**
     * @return Collection<int, array{name: string, quantity: int, revenue: float, variants: array<int, array<string, mixed>>}>
     */
    private function topMenus(PeriodFilter $period, int $limit)
    {
        return MenuSalesReport::forPeriod($period)
            ->sortByDesc('quantity')
            ->take($limit)
            ->values();
    }

    /**
     * @return Collection<int, array{name: string, quantity: int, revenue: float}>
     */
    private function topCategories(PeriodFilter $period, int $limit)
    {
        return DB::table('pesanan_item')
            ->join('pesanan', 'pesanan.id', '=', 'pesanan_item.order_id')
            ->leftJoin('menu', 'menu.id', '=', 'pesanan_item.menu_id')
            ->leftJoin('kategori', 'kategori.id', '=', 'menu.category_id')
            ->where('pesanan.payment_status', 'lunas')
            ->whereBetween('pesanan.created_at', [$period->start, $period->end])
            ->select(DB::raw("COALESCE(kategori.name, 'Tanpa Kategori') as name"), DB::raw('SUM(pesanan_item.quantity) as quantity'), DB::raw('SUM(pesanan_item.subtotal) as revenue'))
            ->groupBy(DB::raw("COALESCE(kategori.name, 'Tanpa Kategori')"))
            ->orderByDesc('revenue')
            ->limit($limit)
            ->get()
            ->map(fn ($row) => [
                'name' => $row->name,
                'quantity' => (int) $row->quantity,
                'revenue' => (float) $row->revenue,
            ]);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Collection<int, Order>
     */
    private function paidOrders(Carbon $start, Carbon $end)
    {
        return Order::where('payment_status', 'lunas')
            ->whereBetween('created_at', [$start, $end])
            ->get(['id', 'created_at', 'total', 'discount', 'payment_method', 'order_type']);
    }
}
