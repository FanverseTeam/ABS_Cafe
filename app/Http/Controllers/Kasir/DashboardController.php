<?php

namespace App\Http\Controllers\Kasir;

use App\Http\Controllers\Controller;
use App\Models\CafeSetting;
use App\Models\Order;
use App\Models\PenjagaKasir;
use App\Models\Reservation;
use App\Models\Table;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // Data Penjaga Kasir yang terdaftar aktif oleh Admin (khusus staf bagian kasir)
        $penjagaList = Schema::hasTable('penjaga_kasir') ? PenjagaKasir::activeKasir()->get() : collect();
        $validNames = $penjagaList->pluck('name')->all();

        // Ambil nama penjaga yang sedang aktif (prioritaskan session, lalu CafeSetting)
        $activePenjaga = session('active_penjaga_kasir');
        if (! in_array($activePenjaga, $validNames, true)) {
            $activePenjaga = CafeSetting::get('active_penjaga_kasir');
        }

        // Jika nama yang tersimpan tidak valid atau tidak aktif lagi di daftar admin, gunakan yang pertama
        if (! in_array($activePenjaga, $validNames, true)) {
            $activePenjaga = $penjagaList->first()?->name;
        }

        session(['active_penjaga_kasir' => $activePenjaga]);
        if ($activePenjaga) {
            CafeSetting::set('active_penjaga_kasir', $activePenjaga);
        }

        // Metrik penjualan harian (berbasis hari, tidak tergantung shift)
        $todayPaidQuery = Order::where('payment_status', 'lunas')->whereDate('paid_at', today());
        $todaySales = (float) $todayPaidQuery->sum('total');
        $todayOrdersCount = (int) $todayPaidQuery->count();
        $unpaidCount = (int) Order::forKasir()->today()->unpaid()->where('order_status', '!=', 'selesai')->count();
        $activeCount = (int) Order::forKasir()->today()->whereIn('order_status', ['diproses', 'siap'])->count();

        // Rekap penjaga yang sedang bertugas hari ini
        $attendantSales = 0.0;
        $attendantOrdersCount = 0;
        if ($activePenjaga) {
            $attendantOrders = Order::where('payment_status', 'lunas')
                ->whereDate('paid_at', today())
                ->where('cashier_name', $activePenjaga);
            $attendantSales = (float) $attendantOrders->sum('total');
            $attendantOrdersCount = (int) $attendantOrders->count();
        }

        // Pesanan masuk web hari ini saja (jika ganti hari, kasir mulai kosong kembali)
        $newWebOrders = Order::with('items')
            ->where('source', 'web')
            ->where('order_status', 'baru')
            ->today()
            ->orderBy('created_at')
            ->get();

        // Pesanan kasir yang belum bayar / menunggu hari ini saja
        $waitingOrders = Order::with('items')
            ->forKasir()
            ->today()
            ->unpaid()
            ->where('order_status', '!=', 'selesai')
            ->orderBy('created_at')
            ->take(8)
            ->get();

        // Meja yang secara realtime sedang terisi (oleh pesanan aktif atau reservasi dalam jendela aktif)
        $allTables = Table::with(['reservations' => fn ($q) => $q->whereIn('status', ['menunggu', 'dikonfirmasi', 'datang'])])
            ->orderBy('number')
            ->get();
        $occupiedTables = $allTables->filter(fn (Table $t) => $t->realtimeStatus() === 'terpakai')->values();

        // Reservasi yang mencakup hari ini (termasuk reservasi multi-hari)
        $todayStr = today()->toDateString();
        $todayReservations = Reservation::with('table')
            ->where(function ($q) use ($todayStr) {
                $q->whereDate('reservation_date', '<=', $todayStr)
                    ->where(function ($endQ) use ($todayStr) {
                        $endQ->whereDate('reservation_end_date', '>=', $todayStr)
                            ->orWhere(function ($nullQ) use ($todayStr) {
                                $nullQ->whereNull('reservation_end_date')
                                    ->whereDate('reservation_date', $todayStr);
                            });
                    });
            })
            ->orderBy('reservation_date')
            ->orderBy('reservation_time')
            ->get();

        // Daftar nama kasir aktif untuk dropdown ganti kasir
        $staffCashiers = User::where('status', 'aktif')
            ->whereIn('role', ['kasir', 'staf', 'admin'])
            ->orderBy('name')
            ->pluck('name');

        $user = Auth::user();
        $currentCashierName = session('active_cashier_name', $user?->name ?? 'Kasir');

        return view('kasir.dashboard', compact(
            'penjagaList',
            'activePenjaga',
            'todaySales',
            'todayOrdersCount',
            'unpaidCount',
            'activeCount',
            'attendantSales',
            'attendantOrdersCount',
            'newWebOrders',
            'waitingOrders',
            'occupiedTables',
            'todayReservations',
            'staffCashiers',
            'currentCashierName',
        ));
    }

    /**
     * Set atau ubah nama penjaga kasir yang sedang bertugas.
     */
    public function setPenjaga(Request $request)
    {
        $request->validate([
            'penjaga_kasir_name' => 'required|string|max:100',
        ]);

        $name = trim((string) $request->input('penjaga_kasir_name'));

        // Pastikan nama yang dipilih terdaftar dan aktif di data yang diatur admin (khusus staf bagian kasir)
        if (Schema::hasTable('penjaga_kasir')) {
            $exists = PenjagaKasir::activeKasir()->where('name', $name)->exists();
            if (! $exists) {
                return back()->with('error', 'Penjaga kasir tidak valid, bukan bagian kasir, atau sedang dinonaktifkan.');
            }
        }

        session([
            'active_penjaga_kasir' => $name,
            'active_cashier_name' => $name,
        ]);
        CafeSetting::set('active_penjaga_kasir', $name);

        if ($request->wantsJson()) {
            return response()->json([
                'ok' => true,
                'message' => "Penjaga kasir aktif diubah ke: {$name}",
                'penjaga' => $name,
            ]);
        }

        return back()->with('success', "Penjaga kasir yang bertugas diatur ke: {$name}");
    }

    /**
     * Ganti nama kasir bertugas di session — tanpa logout.
     */
    public function switchCashier(Request $request)
    {
        $request->validate(['cashier_name' => 'required|string|max:100']);
        $name = trim((string) $request->input('cashier_name'));

        if (Schema::hasTable('penjaga_kasir')) {
            $exists = PenjagaKasir::activeKasir()->where('name', $name)->exists();
            if (! $exists && ! User::where('status', 'aktif')->where('role', 'kasir')->where('name', $name)->exists()) {
                return back()->with('error', 'Nama kasir tidak valid atau bukan staf bagian kasir.');
            }
        }

        session([
            'active_cashier_name' => $name,
            'active_penjaga_kasir' => $name,
        ]);
        CafeSetting::set('active_penjaga_kasir', $name);

        return back()->with('success', 'Kasir bertugas: '.$name);
    }

    /**
     * Polling ringan untuk notifikasi pesanan Web baru hari ini.
     */
    public function pendingCount(Request $request)
    {
        $count = Order::where('source', 'web')
            ->where('order_status', 'baru')
            ->today()
            ->count();
        $unpaid = $request->user()
            ? Order::forKasir()->today()->unpaid()->where('order_status', '!=', 'selesai')->count()
            : 0;

        return response()->json([
            'web_new' => $count,
            'unpaid' => $unpaid,
            'time' => now()->format('H:i:s'),
        ]);
    }
}
