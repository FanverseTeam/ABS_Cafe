<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\CafeSetting;
use App\Models\Menu;
use App\Models\Order;
use App\Models\Promo;
use App\Support\MenuSalesReport;
use App\Support\PeriodFilter;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $period = PeriodFilter::fromRequest($request);
        $margin = (float) CafeSetting::get('profit_margin_percent', '40');
        $dailyTarget = (float) CafeSetting::get('target_daily_sales', '2000000');
        $monthlyTarget = (float) CafeSetting::get('target_monthly_sales', '45000000');

        $summary = $this->summary($period, $margin);

        // Target harian & bulanan
        $salesToday = (float) $this->paidOrders(Carbon::today()->startOfDay(), Carbon::today()->endOfDay())->sum('total');
        $salesMonth = (float) $this->paidOrders(Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth())->sum('total');

        $targets = [
            'daily' => [
                'label' => 'Target Hari Ini',
                'target' => $dailyTarget,
                'actual' => $salesToday,
                'percent' => $dailyTarget > 0 ? min(100, round($salesToday / $dailyTarget * 100, 1)) : 0,
            ],
            'monthly' => [
                'label' => 'Target Bulan Ini',
                'target' => $monthlyTarget,
                'actual' => $salesMonth,
                'percent' => $monthlyTarget > 0 ? min(100, round($salesMonth / $monthlyTarget * 100, 1)) : 0,
            ],
        ];

        // Grafik tren penjualan sesuai periode
        $chart = $this->salesChart($period);

        // Komposisi metode pembayaran & tipe pesanan
        $paid = $this->paidOrders($period->start, $period->end);
        $paymentComposition = $paid
            ->groupBy('payment_method')
            ->map(fn ($rows, $method) => [
                'method' => $method ?: 'lainnya',
                'count' => $rows->count(),
                'total' => (float) $rows->sum('total'),
            ])
            ->values();

        $orderTypeSplit = [
            'dine_in' => (float) $paid->where('order_type', 'dine_in')->sum('total'),
            'take_away' => (float) $paid->where('order_type', 'take_away')->sum('total'),
        ];

        // Top menu & kategori (dari item pesanan, bukan counter statis)
        $topMenus = $this->topMenus($period, 5);
        $topCategories = $this->topCategories($period, 5);

        // Kinerja kasir ringkas
        $cashierPerformance = $this->cashierPerformance($period, 5);

        // Stok menipis
        $lowStockMenus = Menu::with('category')
            ->where(function ($q) {
                $q->where(function ($sub) {
                    $sub->whereNotNull('min_stock')
                        ->whereColumn('stock', '<=', 'min_stock');
                })->orWhere('stock', '<=', 0)
                    ->orWhere('is_available', false);
            })
            ->orderBy('stock', 'asc')
            ->take(6)
            ->get();

        // Promo aktif
        $activePromos = Promo::where('is_active', true)
            ->whereDate('start_date', '<=', now())
            ->whereDate('end_date', '>=', now())
            ->orderBy('end_date')
            ->take(5)
            ->get();

        // Pesanan terbaru dalam periode
        $recentOrders = Order::with('items')
            ->whereBetween('created_at', [$period->start, $period->end])
            ->orderBy('created_at', 'desc')
            ->take(6)
            ->get();

        return view('owner.dashboard', compact(
            'period',
            'summary',
            'targets',
            'chart',
            'paymentComposition',
            'orderTypeSplit',
            'topMenus',
            'topCategories',
            'cashierPerformance',
            'lowStockMenus',
            'activePromos',
            'recentOrders',
        ));
    }

    /**
     * @return array<string, array{value: float, change: ?float}>
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

        $profitCurrent = $salesCurrent * $margin / 100;
        $profitPrev = $salesPrev * $margin / 100;

        return [
            'sales' => ['value' => $salesCurrent, 'change' => PeriodFilter::percentChange($salesCurrent, $salesPrev)],
            'orders' => ['value' => $ordersCurrent, 'change' => PeriodFilter::percentChange($ordersCurrent, $ordersPrev)],
            'avg' => ['value' => $avgCurrent, 'change' => PeriodFilter::percentChange($avgCurrent, $avgPrev)],
            'profit' => ['value' => $profitCurrent, 'change' => PeriodFilter::percentChange($profitCurrent, $profitPrev)],
            'discount' => ['value' => (float) $current->sum('discount'), 'change' => PeriodFilter::percentChange((float) $current->sum('discount'), (float) $previous->sum('discount'))],
            'margin' => $margin,
        ];
    }

    /**
     * @return array{labels: array<int, string>, sales: array<int, float>, orders: array<int, int>, group: string}
     */
    private function salesChart(PeriodFilter $period): array
    {
        $orders = $this->paidOrders($period->start, $period->end);
        $grouping = $period->chartGrouping()['group'];

        $labels = [];
        $sales = [];
        $counts = [];

        if ($grouping === 'hari') {
            $cursor = $period->start->copy();
            while ($cursor->lte($period->end)) {
                $labels[] = $cursor->translatedFormat('d M');
                $sales[] = 0.0;
                $counts[] = 0;
                $cursor->addDay();
            }

            foreach ($orders as $order) {
                $idx = $period->start->diffInDays($order->created_at->copy()->startOfDay());
                if (isset($sales[$idx])) {
                    $sales[$idx] += (float) $order->total;
                    $counts[$idx]++;
                }
            }
        } elseif ($grouping === 'minggu') {
            $cursor = $period->start->copy()->startOfDay();
            $index = 1;
            while ($cursor->lte($period->end)) {
                $labels[] = 'Minggu '.$index;
                $sales[] = 0.0;
                $counts[] = 0;
                $cursor->addWeek();
                $index++;
            }

            foreach ($orders as $order) {
                $idx = intdiv($period->start->diffInDays($order->created_at->copy()->startOfDay()), 7);
                if (isset($sales[$idx])) {
                    $sales[$idx] += (float) $order->total;
                    $counts[$idx]++;
                }
            }
        } else {
            $cursor = $period->start->copy()->startOfMonth();
            while ($cursor->lte($period->end)) {
                $labels[] = $cursor->translatedFormat('M Y');
                $sales[] = 0.0;
                $counts[] = 0;
                $cursor->addMonth();
            }

            foreach ($orders as $order) {
                $idx = $order->created_at->copy()->startOfMonth()->diffInMonths($period->start->copy()->startOfMonth());
                if (isset($sales[$idx])) {
                    $sales[$idx] += (float) $order->total;
                    $counts[$idx]++;
                }
            }
        }

        return ['labels' => $labels, 'sales' => $sales, 'orders' => $counts, 'group' => $grouping];
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
        return DB::table('order_items')
            ->join('orders', 'orders.id', '=', 'order_items.order_id')
            ->leftJoin('menus', 'menus.id', '=', 'order_items.menu_id')
            ->leftJoin('categories', 'categories.id', '=', 'menus.category_id')
            ->where('orders.payment_status', 'lunas')
            ->whereBetween('orders.created_at', [$period->start, $period->end])
            ->select(DB::raw("COALESCE(categories.name, 'Tanpa Kategori') as name"), DB::raw('SUM(order_items.quantity) as quantity'), DB::raw('SUM(order_items.subtotal) as revenue'))
            ->groupBy('name')
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
     * @return Collection<int, array{name: string, orders: int, sales: float}>
     */
    private function cashierPerformance(PeriodFilter $period, int $limit)
    {
        $orders = $this->paidOrders($period->start, $period->end);

        return $orders
            ->groupBy(fn ($order) => $order->cashier_id ?: ('nama:'.($order->cashier_name ?: 'Tanpa Kasir')))
            ->map(function ($rows) {
                $first = $rows->first();

                return [
                    'name' => $first->cashier?->name ?? ($first->cashier_name ?: 'Tanpa Kasir'),
                    'orders' => $rows->count(),
                    'sales' => (float) $rows->sum('total'),
                ];
            })
            ->sortByDesc('sales')
            ->take($limit)
            ->values();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Collection<int, Order>
     */
    private function paidOrders(Carbon $start, Carbon $end)
    {
        return Order::with('cashier')
            ->where('payment_status', 'lunas')
            ->whereBetween('created_at', [$start, $end])
            ->get(['id', 'created_at', 'total', 'discount', 'payment_method', 'order_type', 'cashier_id', 'cashier_name']);
    }
}
