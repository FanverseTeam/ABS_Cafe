<?php

namespace App\Http\Controllers\Kasir;

use App\Http\Controllers\Controller;
use App\Models\CashShift;
use App\Models\Order;
use App\Models\Reservation;
use App\Models\Table;
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

        return view('kasir.dashboard', compact(
            'shift',
            'summary',
            'newWebOrders',
            'waitingOrders',
            'occupiedTables',
            'todayReservations',
        ));
    }

    /**
     * Polling ringan untuk notifikasi pesanan Web baru.
     */
    public function pendingCount(Request $request)
    {
        $count = Order::where('source', 'web')->where('order_status', 'baru')->count();
        $unpaid = $request->user() && CashShift::where('user_id', $request->user()->id)->where('status', 'buka')->exists()
            ? Order::unpaid()->where('order_status', '!=', 'selesai')->count()
            : 0;

        return response()->json([
            'web_new' => $count,
            'unpaid' => $unpaid,
            'time' => now()->format('H:i:s'),
        ]);
    }
}
