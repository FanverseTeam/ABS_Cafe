<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\CafeSetting;
use App\Models\CashAdjustment;
use App\Models\CashShift;
use App\Models\Order;
use App\Support\MenuSalesReport;
use App\Support\PeriodFilter;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public const TABS = [
        'penjualan' => 'Laporan Penjualan',
        'keuangan' => 'Laporan Keuangan',
    ];

    public function index(Request $request)
    {
        $tab = array_key_exists($request->query('tab', 'penjualan'), self::TABS) ? $request->query('tab', 'penjualan') : 'penjualan';
        $period = PeriodFilter::fromRequest($request);

        $sales = $this->paidOrders($period);

        $data = [
            'summary' => $this->salesSummary($sales),
            'daily' => $this->dailyBreakdown($period, $sales),
            'perMenu' => $this->perMenu($period),
            'perCategory' => $this->perCategory($period),
            'perCashier' => $this->perCashier($sales),
            'perMethod' => $this->perMethod($sales),
            'perType' => $this->perType($sales),
            'perHour' => $this->perHour($sales),
            'orders' => Order::with('items')
                ->whereBetween('created_at', [$period->start, $period->end])
                ->orderBy('created_at', 'desc')
                ->paginate(15)
                ->withQueryString(),
        ];

        $dailyRecap = $this->buildDailyRecap($period->start, $period->end, $sales);

        $finance = [
            'gross' => (float) $sales->sum('subtotal'),
            'discount' => (float) $sales->sum('discount'),
            'tax' => (float) $sales->sum('tax'),
            'service' => (float) $sales->sum('service_charge'),
            'net' => (float) $sales->sum('total'),
            'byMethod' => $this->perMethod($sales),
            'shifts' => CashShift::with('user')
                ->whereBetween('opened_at', [$period->start, $period->end])
                ->orderBy('opened_at', 'desc')
                ->get(),
            'adjustments' => CashAdjustment::with('user')
                ->whereBetween('created_at', [$period->start, $period->end])
                ->orderBy('created_at', 'desc')
                ->get(),
            'requirePhysical' => CafeSetting::get('require_physical_cash_count', '0') === '1',
            'cancelled' => Order::where('payment_status', 'dibatalkan')
                ->whereBetween('created_at', [$period->start, $period->end])
                ->count(),
            'margin' => (float) CafeSetting::get('profit_margin_percent', '40'),
        ];

        return view('owner.reports.index', compact('tab', 'period', 'sales', 'data', 'dailyRecap', 'finance'));
    }

    public function exportCsv(Request $request)
    {
        if ($request->filled('mulai') || $request->filled('selesai')) {
            $now = Carbon::now();
            $start = Carbon::parse($request->query('mulai', $now->copy()->subDays(6)->toDateString()))->startOfDay();
            $end = Carbon::parse($request->query('selesai', $now->toDateString()))->endOfDay();

            if ($end->lt($start)) {
                [$start, $end] = [$end->copy()->startOfDay(), $start->copy()->endOfDay()];
            }
        } else {
            $period = PeriodFilter::fromRequest($request);
            $start = $period->start;
            $end = $period->end;
        }

        $sales = Order::where('payment_status', 'lunas')
            ->whereBetween('created_at', [$start, $end])
            ->get(['id', 'created_at', 'subtotal', 'discount', 'total']);

        $recap = $this->buildDailyRecap($start, $end, $sales);
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
     * @return array{
     *     rows: array<int, array{date: string, label: string, day_name: string, orders: int, gross: float, discount: float, total: float}>,
     *     totals: array{orders: int, gross: float, discount: float, total: float}
     * }
     */
    private function buildDailyRecap(Carbon $start, Carbon $end, $sales): array
    {
        $grouped = $sales->groupBy(fn ($order) => $order->created_at->toDateString());

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

    public function print(Request $request)
    {
        $tab = array_key_exists($request->query('tab', 'penjualan'), self::TABS) ? $request->query('tab', 'penjualan') : 'penjualan';
        $period = PeriodFilter::fromRequest($request);
        $sales = $this->paidOrders($period);

        $cafe = [
            'name' => CafeSetting::get('cafe_name', 'ABS Cafe'),
            'tagline' => CafeSetting::get('cafe_tagline', 'Accessories & Cafe'),
            'address' => CafeSetting::get('cafe_address', ''),
            'phone' => CafeSetting::get('cafe_phone', ''),
        ];

        $report = [
            'summary' => $this->salesSummary($sales),
            'daily' => $this->dailyBreakdown($period, $sales),
            'perMenu' => $this->perMenu($period),
            'perCategory' => $this->perCategory($period),
            'perCashier' => $this->perCashier($sales),
            'perMethod' => $this->perMethod($sales),
            'finance' => [
                'gross' => (float) $sales->sum('subtotal'),
                'discount' => (float) $sales->sum('discount'),
                'tax' => (float) $sales->sum('tax'),
                'service' => (float) $sales->sum('service_charge'),
                'net' => (float) $sales->sum('total'),
            ],
        ];

        return view('owner.reports.print', compact('tab', 'period', 'report', 'cafe'));
    }

    /**
     * @return array<string, float|int>
     */
    private function salesSummary($sales): array
    {
        $count = $sales->count();

        return [
            'orders' => $count,
            'items' => (int) $sales->sum('items_count'),
            'gross' => (float) $sales->sum('subtotal'),
            'discount' => (float) $sales->sum('discount'),
            'net' => (float) $sales->sum('total'),
            'avg' => $count > 0 ? (float) $sales->sum('total') / $count : 0.0,
        ];
    }

    /**
     * @return Collection<int, array<string, mixed>>
     */
    private function dailyBreakdown(PeriodFilter $period, $sales)
    {
        $byDay = $sales->groupBy(fn ($o) => $o->created_at->toDateString());

        $rows = collect();
        $cursor = $period->start->copy()->startOfDay();
        while ($cursor->lte($period->end)) {
            $key = $cursor->toDateString();
            $dayOrders = $byDay->get($key, collect());
            $rows->push([
                'date' => $cursor->copy(),
                'label' => $cursor->translatedFormat('d M'),
                'orders' => $dayOrders->count(),
                'gross' => (float) $dayOrders->sum('subtotal'),
                'discount' => (float) $dayOrders->sum('discount'),
                'net' => (float) $dayOrders->sum('total'),
                'avg' => $dayOrders->count() > 0 ? (float) $dayOrders->sum('total') / $dayOrders->count() : 0.0,
            ]);
            $cursor->addDay();
        }

        return $rows->reverse()->values();
    }

    private function perMenu(PeriodFilter $period)
    {
        return MenuSalesReport::forPeriod($period);
    }

    private function perCategory(PeriodFilter $period)
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
            ->get()
            ->map(fn ($r) => ['name' => $r->name, 'quantity' => (int) $r->quantity, 'revenue' => (float) $r->revenue]);
    }

    /**
     * Rekap penjualan per nama penjaga kasir (dari kolom cashier_name di pesanan).
     */
    private function perCashier($sales)
    {
        return $sales->groupBy('cashier_name')->map(fn ($rows, $name) => [
            'name' => $name ?: 'Tidak diketahui',
            'count' => $rows->count(),
            'total' => (float) $rows->sum('total'),
        ])->sortByDesc('total')->values();
    }

    private function perMethod($sales)
    {
        return $sales->groupBy('payment_method')->map(fn ($rows, $method) => [
            'method' => $method ?: 'lainnya',
            'count' => $rows->count(),
            'total' => (float) $rows->sum('total'),
        ])->values();
    }

    private function perType($sales): array
    {
        return [
            ['label' => 'Dine-in', 'value' => (float) $sales->where('order_type', 'dine_in')->sum('total')],
            ['label' => 'Take-away', 'value' => (float) $sales->where('order_type', 'take_away')->sum('total')],
        ];
    }

    private function perHour($sales)
    {
        $slots = [];
        for ($h = 7; $h <= 23; $h++) {
            $slots[$h] = ['hour' => $h, 'orders' => 0, 'sales' => 0.0];
        }

        foreach ($sales as $order) {
            $hour = (int) $order->created_at->format('G');
            if (isset($slots[$hour])) {
                $slots[$hour]['orders']++;
                $slots[$hour]['sales'] += (float) $order->total;
            }
        }

        return collect($slots)->values();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Collection<int, Order>
     */
    private function paidOrders(PeriodFilter $period)
    {
        return Order::withCount('items')
            ->where('payment_status', 'lunas')
            ->whereBetween('created_at', [$period->start, $period->end])
            ->get();
    }

    /**
     * @return array<int, array<int, string|int|float>>
     */
    private function salesRows($sales): array
    {
        $rows = [['Kode Order', 'Waktu', 'Customer', 'Tipe', 'Item', 'Subtotal', 'Diskon', 'Total', 'Metode', 'Kasir']];
        foreach ($sales as $o) {
            $rows[] = [
                $o->order_code,
                $o->created_at->format('d/m/Y H:i'),
                $o->customer_name,
                $o->order_type === 'dine_in' ? 'Dine-in' : 'Take-away',
                $o->items_count,
                $o->subtotal,
                $o->discount,
                $o->total,
                strtoupper((string) $o->payment_method),
                $o->cashier_name ?? '-',
            ];
        }

        return $rows;
    }

    /**
     * @return array<int, array<int, string|float>>
     */
    private function financeRows($sales): array
    {
        $rows = [
            ['Ringkasan Keuangan'],
            ['Penjualan Kotor (Subtotal)', (float) $sales->sum('subtotal')],
            ['Total Diskon', (float) $sales->sum('discount')],
            ['Pajak Terkumpul', (float) $sales->sum('tax')],
            ['Service Charge', (float) $sales->sum('service_charge')],
            ['Pendapatan Bersih', (float) $sales->sum('total')],
            [],
            ['Rekap Metode Pembayaran'],
            ['Metode', 'Jumlah Transaksi', 'Total'],
        ];
        foreach ($this->perMethod($sales) as $method) {
            $rows[] = [$method['method'], $method['count'], $method['total']];
        }

        return $rows;
    }
}
