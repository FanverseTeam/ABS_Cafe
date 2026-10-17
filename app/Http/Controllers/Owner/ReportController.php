<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\CafeSetting;
use App\Models\CashAdjustment;
use App\Models\CashShift;
use App\Models\Menu;
use App\Models\Order;
use App\Models\Stock;
use App\Models\StockLog;
use App\Models\StockMovement;
use App\Support\PeriodFilter;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public const TABS = [
        'penjualan' => 'Laporan Penjualan',
        'keuangan' => 'Laporan Keuangan',
        'stok' => 'Laporan Stok & Bahan',
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
            'perMethod' => $this->perMethod($sales),
            'perType' => $this->perType($sales),
            'perHour' => $this->perHour($sales),
            'orders' => Order::with('items')
                ->whereBetween('created_at', [$period->start, $period->end])
                ->orderBy('created_at', 'desc')
                ->paginate(15)
                ->withQueryString(),
        ];

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

        $logs = StockLog::with('menu')
            ->whereBetween('created_at', [$period->start, $period->end])
            ->get()
            ->map(fn ($l) => (object) [
                'item_name' => $l->item_name,
                'type' => $l->type,
                'quantity' => (string) $l->quantity,
                'created_at' => $l->created_at,
            ]);

        $rawMovements = StockMovement::with('stock')
            ->whereBetween('created_at', [$period->start, $period->end])
            ->get()
            ->map(fn ($m) => (object) [
                'item_name' => ($m->stock?->name ?? 'Bahan').' (Bahan)',
                'type' => $m->type,
                'quantity' => ((float) $m->quantity).' '.($m->stock?->unit ?? ''),
                'created_at' => $m->created_at,
            ]);

        $movements = $logs->concat($rawMovements)->sortByDesc('created_at')->values();

        $stock = [
            'totalItems' => Menu::count(),
            'stockValue' => (float) Menu::sum(DB::raw('stock * price')),
            'lowStock' => Menu::with('category')->whereNotNull('min_stock')->whereColumn('stock', '<=', 'min_stock')->orderBy('stock')->get(),
            'outOfStock' => Menu::with('category')->where('stock', '<=', 0)->get(),
            'movements' => $movements,
            'usage' => $this->stockUsage($period),
            'ingredientUsage' => $this->ingredientUsage($period),
        ];

        return view('owner.reports.index', array_merge(compact('tab', 'period', 'sales'), [
            'data' => $data,
            'finance' => $finance,
            'stock' => $stock,
        ]));
    }

    public function exportCsv(Request $request)
    {
        $tab = $request->query('tab', 'penjualan');
        $period = PeriodFilter::fromRequest($request);
        $sales = $this->paidOrders($period);

        $fileName = 'Laporan_'.ucfirst($tab).'_ABS_Cafe_'.$period->start->format('Ymd').'-'.$period->end->format('Ymd').'.csv';

        $headers = [
            'Content-type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename={$fileName}",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        $rows = match ($tab) {
            'keuangan' => $this->financeRows($sales),
            'stok' => $this->stockRows($period),
            default => $this->salesRows($sales),
        };

        return response()->stream(function () use ($rows) {
            $file = fopen('php://output', 'w');
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            foreach ($rows as $row) {
                fputcsv($file, $row);
            }
            fclose($file);
        }, 200, $headers);
    }

    public function print(Request $request)
    {
        $tab = $request->query('tab', 'penjualan');
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
            'perMethod' => $this->perMethod($sales),
            'finance' => [
                'gross' => (float) $sales->sum('subtotal'),
                'discount' => (float) $sales->sum('discount'),
                'tax' => (float) $sales->sum('tax'),
                'service' => (float) $sales->sum('service_charge'),
                'net' => (float) $sales->sum('total'),
            ],
            'stock' => [
                'stockValue' => (float) Menu::sum(DB::raw('stock * price')),
                'lowStock' => Menu::with('category')->whereNotNull('min_stock')->whereColumn('stock', '<=', 'min_stock')->orderBy('stock')->get(),
                'outOfStock' => Menu::with('category')->where('stock', '<=', 0)->get(),
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
        return DB::table('order_items')
            ->join('orders', 'orders.id', '=', 'order_items.order_id')
            ->where('orders.payment_status', 'lunas')
            ->whereBetween('orders.created_at', [$period->start, $period->end])
            ->select('order_items.name', DB::raw('SUM(order_items.quantity) as quantity'), DB::raw('SUM(order_items.subtotal) as revenue'))
            ->groupBy('order_items.name')
            ->orderByDesc('revenue')
            ->get()
            ->map(fn ($r) => ['name' => $r->name, 'quantity' => (int) $r->quantity, 'revenue' => (float) $r->revenue]);
    }

    private function perCategory(PeriodFilter $period)
    {
        return DB::table('order_items')
            ->join('orders', 'orders.id', '=', 'order_items.order_id')
            ->leftJoin('menus', 'menus.id', '=', 'order_items.menu_id')
            ->leftJoin('categories', 'categories.id', '=', 'menus.category_id')
            ->where('orders.payment_status', 'lunas')
            ->whereBetween('orders.created_at', [$period->start, $period->end])
            ->select(DB::raw("COALESCE(categories.name, 'Tanpa Kategori') as name"), DB::raw('SUM(order_items.quantity) as quantity'), DB::raw('SUM(order_items.subtotal) as revenue'))
            ->groupBy('name')
            ->orderByDesc('revenue')
            ->get()
            ->map(fn ($r) => ['name' => $r->name, 'quantity' => (int) $r->quantity, 'revenue' => (float) $r->revenue]);
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

    private function stockUsage(PeriodFilter $period)
    {
        return DB::table('order_items')
            ->join('orders', 'orders.id', '=', 'order_items.order_id')
            ->where('orders.payment_status', 'lunas')
            ->whereBetween('orders.created_at', [$period->start, $period->end])
            ->whereNotNull('order_items.menu_id')
            ->select('order_items.menu_id', DB::raw('SUM(order_items.quantity) as sold'))
            ->groupBy('order_items.menu_id')
            ->orderByDesc('sold')
            ->get()
            ->map(function ($row) {
                $menu = Menu::find($row->menu_id);

                return [
                    'name' => $menu?->name ?? 'Menu #'.$row->menu_id,
                    'sold' => (int) $row->sold,
                    'stock' => (int) ($menu?->stock ?? 0),
                    'unit' => $menu?->unit ?? 'pcs',
                ];
            });
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

    private function ingredientUsage(PeriodFilter $period)
    {
        return StockMovement::with('stock')
            ->where('type', 'keluar')
            ->whereBetween('created_at', [$period->start, $period->end])
            ->get()
            ->groupBy('stock_id')
            ->map(function ($rows) {
                $stock = $rows->first()->stock;

                return [
                    'name' => $stock?->name ?? 'Bahan Baku',
                    'used' => (float) $rows->sum('quantity'),
                    'remaining' => (float) ($stock?->quantity ?? 0),
                    'unit' => $stock?->unit ?? 'pcs',
                ];
            })
            ->values();
    }

    /**
     * @return array<int, array<int, string|int|float>>
     */
    private function stockRows(PeriodFilter $period): array
    {
        $rows = [
            ['Laporan Stok & Bahan'],
            ['Nilai Stok (harga jual)', (float) Menu::sum(DB::raw('stock * price'))],
            [],
            ['Menu / Produk', 'Stok', 'Min. Stok', 'Satuan', 'Status'],
        ];
        foreach (Menu::orderBy('stock')->get() as $menu) {
            $rows[] = [
                $menu->name,
                (int) $menu->stock,
                (int) $menu->min_stock,
                $menu->unit ?? 'pcs',
                $menu->stock_status === 'habis' ? 'Habis' : ($menu->stock_status === 'menipis' ? 'Menipis' : 'Aman'),
            ];
        }

        $rows[] = [];
        $rows[] = ['Bahan Baku (Resep)', 'Stok Tersisa', 'Min. Stok', 'Satuan', 'Status'];
        foreach (Stock::where('is_active', true)->orderBy('name')->get() as $stk) {
            $status = $stk->stockStatus();
            $rows[] = [
                $stk->name,
                (float) $stk->quantity,
                (float) $stk->min_quantity,
                $stk->unit,
                $status['label'],
            ];
        }

        return $rows;
    }
}
