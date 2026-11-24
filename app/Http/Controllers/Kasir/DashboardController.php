<?php

namespace App\Http\Controllers\Kasir;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Reservation;
use App\Models\Table;
use App\Models\User;
use App\Services\ShiftService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function __construct(private readonly ShiftService $shifts) {}

    public function index()
    {
        $user = Auth::user();
        $shift = $this->shifts->activeFor($user);

        $summary = $shift ? $this->shifts->summary($shift) : null;

        $newWebOrders = Order::with('items')
            ->where('source', 'web')
            ->where('order_status', 'baru')
            ->orderBy('created_at')
            ->get();

        $waitingOrders = Order::with('items')
            ->forKasir()
            ->unpaid()
            ->where('order_status', '!=', 'selesai')
            ->orderBy('created_at')
            ->take(8)
            ->get();

        $occupiedTables = Table::where('status', 'terpakai')
            ->orderBy('number')
            ->get();

        $todayReservations = Reservation::with('table')
            ->whereDate('reservation_date', today())
            ->orderBy('reservation_time')
            ->get();

        // Daftar nama kasir aktif untuk dropdown ganti kasir
        $staffCashiers = User::where('status', 'aktif')
            ->whereIn('role', ['kasir', 'staf', 'admin'])
            ->orderBy('name')
            ->pluck('name');

        $currentCashierName = session('active_cashier_name', $user->name);

        return view('kasir.dashboard', compact(
            'shift',
            'summary',
            'newWebOrders',
            'waitingOrders',
            'occupiedTables',
            'todayReservations',
            'staffCashiers',
            'currentCashierName',
        ));
    }

    /**
     * Ganti nama kasir bertugas di session — tanpa logout.
     */
    public function switchCashier(Request $request)
    {
        $request->validate(['cashier_name' => 'required|string|max:100']);

        session(['active_cashier_name' => $request->input('cashier_name')]);

        return back()->with('success', 'Kasir bertugas: '.$request->input('cashier_name'));
    }

    /**
     * Polling ringan untuk notifikasi pesanan Web baru.
     */
    public function pendingCount(Request $request)
    {
        $count = Order::where('source', 'web')->where('order_status', 'baru')->count();
        $unpaid = Order::unpaid()->where('order_status', '!=', 'selesai')->count();

        return response()->json([
            'web_new' => $count,
            'unpaid' => $unpaid,
            'time' => now()->format('H:i:s'),
        ]);
    }
}
