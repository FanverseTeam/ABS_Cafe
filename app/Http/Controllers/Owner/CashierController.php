<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\PenjagaKasir;
use App\Support\PeriodFilter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class CashierController extends Controller
{
    /**
     * Kinerja per nama penjaga kasir (berdasarkan cashier_name di pesanan).
     */
    public function index(Request $request)
    {
        $period = PeriodFilter::fromRequest($request);

        // Ambil semua nama penjaga kasir yang terdaftar + nama yang ada di pesanan
        $penjagaNames = Schema::hasTable('penjaga_kasir')
            ? PenjagaKasir::orderBy('name')->pluck('name')->unique()
            : collect();
        $orderNames = Order::where('payment_status', 'lunas')
            ->whereBetween('created_at', [$period->start, $period->end])
            ->whereNotNull('cashier_name')
            ->distinct()
            ->pluck('cashier_name');

        $allNames = $penjagaNames->merge($orderNames)->unique()->sort()->values();

        $cashiers = $allNames->map(fn (string $name) => $this->metricsForName($name, $period))
            ->sortByDesc('sales')
            ->values();

        $chart = [
            'labels' => $cashiers->pluck('name')->all(),
            'sales' => $cashiers->pluck('sales')->all(),
            'orders' => $cashiers->pluck('orders')->all(),
        ];

        return view('owner.cashiers.index', compact('period', 'cashiers', 'chart'));
    }

    public function show(Request $request, int $id)
    {
        $period = PeriodFilter::fromRequest($request);
        $penjaga = PenjagaKasir::findOrFail($id);

        $metrics = $this->metricsForName($penjaga->name, $period);

        $orders = Order::with('items')
            ->where('cashier_name', $penjaga->name)
            ->whereBetween('created_at', [$period->start, $period->end])
            ->orderBy('created_at', 'desc')
            ->paginate(15)
            ->withQueryString();

        // Rekap harian untuk grafik
        $daily = $this->paidOrdersByName($penjaga->name, $period)
            ->groupBy(fn ($o) => $o->created_at->toDateString())
            ->map(fn ($rows, $date) => [
                'date' => $date,
                'orders' => $rows->count(),
                'sales' => (float) $rows->sum('total'),
            ])
            ->sortKeys()
            ->values();

        // Variabel $cashier dan $shifts untuk kompatibilitas view lama
        $cashier = $penjaga;
        $shifts = collect();

        return view('owner.cashiers.show', compact('period', 'cashier', 'metrics', 'shifts', 'orders', 'daily'));
    }

    /**
     * Metrik kinerja berdasarkan nama penjaga kasir.
     *
     * @return array<string, mixed>
     */
    private function metricsForName(string $name, PeriodFilter $period): array
    {
        $orders = $this->paidOrdersByName($name, $period);
        $count = $orders->count();
        $sales = (float) $orders->sum('total');

        $penjaga = PenjagaKasir::where('name', $name)->first();
        $words = preg_split('/\s+/', trim($name)) ?: [];
        $first = isset($words[0]) && $words[0] !== '' ? mb_substr($words[0], 0, 1) : 'K';
        $second = isset($words[1]) && $words[1] !== '' ? mb_substr($words[1], 0, 1) : '';
        $initials = mb_strtoupper($first.$second);

        // Rata-rata kecepatan layanan
        $prepMinutes = $this->averagePrepMinutes($name, $period);

        return [
            'id' => $penjaga?->id ?? 0,
            'name' => $name,
            'username' => $penjaga ? 'penjaga' : '-',
            'initials' => $initials,
            'orders' => $count,
            'sales' => $sales,
            'avg' => $count > 0 ? $sales / $count : 0.0,
            'shifts' => 0,
            'cash_difference' => 0.0,
            'prep_minutes' => $prepMinutes,
            'last_login_at' => null,
        ];
    }

    private function averagePrepMinutes(string $name, PeriodFilter $period): ?float
    {
        $orders = Order::with('timelines')
            ->where('cashier_name', $name)
            ->whereBetween('created_at', [$period->start, $period->end])
            ->get();

        $durations = $orders->map(function (Order $order) {
            $ready = $order->timelines->firstWhere('status', 'SIAP');

            return $ready?->logged_at ? $order->created_at->diffInMinutes($ready->logged_at) : null;
        })->filter(fn ($value) => $value !== null);

        return $durations->isNotEmpty() ? round($durations->avg(), 1) : null;
    }

    /**
     * @return Collection<int, Order>
     */
    private function paidOrdersByName(string $name, PeriodFilter $period)
    {
        return Order::where('cashier_name', $name)
            ->where('payment_status', 'lunas')
            ->whereBetween('created_at', [$period->start, $period->end])
            ->get(['id', 'created_at', 'total']);
    }
}
