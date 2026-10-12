<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\CashShift;
use App\Models\Order;
use App\Models\User;
use App\Support\PeriodFilter;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class CashierController extends Controller
{
    public function index(Request $request)
    {
        $period = PeriodFilter::fromRequest($request);

        $cashiers = User::where('role', 'kasir')
            ->orderBy('name')
            ->get()
            ->map(fn (User $user) => $this->metricsFor($user, $period))
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
        $cashier = User::where('role', 'kasir')->findOrFail($id);

        $metrics = $this->metricsFor($cashier, $period);

        $shifts = CashShift::with('user')
            ->where('user_id', $cashier->id)
            ->whereBetween('opened_at', [$period->start, $period->end])
            ->orderBy('opened_at', 'desc')
            ->get();

        $orders = Order::with('items')
            ->where('cashier_id', $cashier->id)
            ->whereBetween('created_at', [$period->start, $period->end])
            ->orderBy('created_at', 'desc')
            ->paginate(15)
            ->withQueryString();

        // Rekap harian untuk grafik
        $daily = $this->paidOrders($cashier, $period)
            ->groupBy(fn ($o) => $o->created_at->toDateString())
            ->map(fn ($rows, $date) => [
                'date' => $date,
                'orders' => $rows->count(),
                'sales' => (float) $rows->sum('total'),
            ])
            ->sortKeys()
            ->values();

        return view('owner.cashiers.show', compact('period', 'cashier', 'metrics', 'shifts', 'orders', 'daily'));
    }

    /**
     * @return array<string, mixed>
     */
    private function metricsFor(User $user, PeriodFilter $period): array
    {
        $orders = $this->paidOrders($user, $period);
        $count = $orders->count();
        $sales = (float) $orders->sum('total');

        $shifts = CashShift::where('user_id', $user->id)
            ->whereBetween('opened_at', [$period->start, $period->end])
            ->get();

        // Kasir tercepat: rata-rata menit dari order dibuat sampai status SIAP
        $prepMinutes = $this->averagePrepMinutes($user, $period);

        return [
            'id' => $user->id,
            'name' => $user->name,
            'username' => $user->username,
            'initials' => $user->initials(),
            'orders' => $count,
            'sales' => $sales,
            'avg' => $count > 0 ? $sales / $count : 0.0,
            'shifts' => $shifts->count(),
            'cash_difference' => (float) $shifts->sum('cash_difference'),
            'prep_minutes' => $prepMinutes,
            'last_login_at' => $user->last_login_at,
        ];
    }

    private function averagePrepMinutes(User $user, PeriodFilter $period): ?float
    {
        $orders = Order::with('timelines')
            ->where('cashier_id', $user->id)
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
    private function paidOrders(User $user, PeriodFilter $period)
    {
        return Order::where('cashier_id', $user->id)
            ->where('payment_status', 'lunas')
            ->whereBetween('created_at', [$period->start, $period->end])
            ->get(['id', 'created_at', 'total']);
    }
}
