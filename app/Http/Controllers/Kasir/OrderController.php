<?php

namespace App\Http\Controllers\Kasir;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\CafeSetting;
use App\Models\Order;
use App\Services\OrderService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class OrderController extends Controller
{
    public function __construct(private readonly OrderService $orders) {}

    public function index(Request $request)
    {
        $tab = $request->get('tab', 'aktif');

        // Pesanan masuk di kasir dibuat per hari ini saja. Jika beda hari, kasir mulai kosong kembali.
        $query = Order::with(['items', 'timelines' => fn ($q) => $q->latest('logged_at')])
            ->forKasir()
            ->today();

        if ($request->filled('source')) {
            $query->where('source', $request->get('source'));
        }

        $query = match ($tab) {
            'web' => $query->where('source', 'web')->whereIn('order_status', ['diproses', 'siap']),
            'belum_bayar' => $query->unpaid()->where('order_status', '!=', 'selesai'),
            'selesai' => $query->whereIn('order_status', ['selesai', 'dibatalkan']),
            default => $query->active(),
        };

        $orders = $query->latest()->paginate(12)->withQueryString();

        $counts = [
            'aktif' => Order::forKasir()->today()->active()->count(),
            'web' => Order::forKasir()->today()->where('source', 'web')->whereIn('order_status', ['diproses', 'siap'])->count(),
            'belum_bayar' => Order::forKasir()->today()->unpaid()->where('order_status', '!=', 'selesai')->count(),
        ];

        return view('kasir.orders.index', compact('orders', 'tab', 'counts'));
    }

    public function show(Order $order)
    {
        $order->load('items', 'timelines', 'promo');

        return response()->json([
            'order' => [
                'id' => $order->id,
                'code' => $order->order_code,
                'customer' => $order->customer_name,
                'table' => $order->table_number,
                'type' => $order->orderTypeLabel(),
                'source' => $order->sourceLabel(),
                'status' => $order->orderStatusBadge(),
                'payment' => $order->paymentStatusBadge(),
                'subtotal' => (float) $order->subtotal,
                'tax' => (float) $order->tax,
                'service' => (float) $order->service_charge,
                'discount' => (float) $order->discount,
                'total' => (float) $order->total,
                'created' => $order->created_at?->format('d/m/Y H:i'),
                'cashier' => $order->cashier_name,
                'note' => $order->cancel_reason,
            ],
            'items' => $order->items->map(fn ($i) => [
                'name' => $i->name,
                'qty' => $i->quantity,
                'price' => (float) $i->price,
                'subtotal' => (float) $i->subtotal,
                'notes' => $i->notes,
            ]),
            'timelines' => $order->timelines->map(fn ($t) => [
                'status' => $t->status,
                'actor' => $t->actor,
                'description' => $t->description,
                'time' => $t->logged_at?->format('d/m/Y H:i'),
            ]),
        ]);
    }

    public function accept(Request $request, Order $order)
    {
        $penjagaName = session('active_penjaga_kasir') ?: CafeSetting::get('active_penjaga_kasir');
        if (! $penjagaName) {
            return back()->with('error', 'Wajib memilih nama penjaga kasir yang bertugas di Dashboard sebelum menerima pesanan!');
        }

        try {
            $this->orders->advanceStatus($order, Auth::user(), 'diterima');
            if (empty($order->cashier_name)) {
                $order->update(['cashier_name' => $penjagaName]);
            }
        } catch (ValidationException $e) {
            return back()->with('error', collect($e->errors())->flatten()->first());
        }

        $this->log($request, "Menerima pesanan Web {$order->order_code} oleh penjaga {$penjagaName}");

        return back()->with('success', "Pesanan {$order->order_code} diterima oleh {$penjagaName}.");
    }

    public function status(Request $request, Order $order)
    {
        $data = $request->validate([
            'status' => 'nullable|string|max:20',
        ]);

        try {
            $this->orders->advanceStatus($order, Auth::user(), $data['status'] ?? null);
        } catch (ValidationException $e) {
            return back()->with('error', collect($e->errors())->flatten()->first());
        }

        $this->log($request, "Memperbarui status {$order->order_code} menjadi {$order->fresh()->order_status}");

        return back()->with('success', "Status pesanan {$order->order_code} diperbarui.");
    }

    public function cancel(Request $request, Order $order)
    {
        $data = $request->validate([
            'cancel_reason' => 'required|string|max:200',
        ]);

        try {
            $this->orders->cancel($order, Auth::user(), $data['cancel_reason']);
        } catch (ValidationException $e) {
            return back()->with('error', collect($e->errors())->flatten()->first());
        }

        $this->log($request, "Membatalkan pesanan {$order->order_code}");

        return back()->with('success', "Pesanan {$order->order_code} dibatalkan.");
    }

    /**
     * Memproses scan QR code pesanan dari pelanggan.
     * Mengubah status pesanan dari 'baru'/'menunggu' menjadi 'diproses'.
     */
    public function scan(Request $request, string $code)
    {
        $raw = trim($code);
        if (preg_match('~(?:scan|sukses)/([^/?#]+)~i', $raw, $matches)) {
            $raw = $matches[1];
        }
        $cleanCode = urldecode($raw);

        $order = Order::where('order_code', $cleanCode)
            ->orWhere('order_code', '#'.$cleanCode)
            ->orWhere('order_code', ltrim($cleanCode, '#'))
            ->first();

        if (! $order) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => "Pesanan dengan kode {$cleanCode} tidak ditemukan.",
                ], 404);
            }

            return redirect()->route('kasir.orders.index')
                ->with('error', "Pesanan dengan kode {$cleanCode} tidak ditemukan.");
        }

        if ($order->isCancelled()) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => "Pesanan {$order->order_code} sudah dibatalkan.",
                ], 422);
            }

            return redirect()->route('kasir.orders.index')
                ->with('error', "Pesanan {$order->order_code} sudah dibatalkan.");
        }

        $user = Auth::user();
        $isNewlyProcessed = false;

        // Jika pesanan belum diproses (masih baru / diterima / menunggu), ubah ke diproses
        if (in_array($order->order_status, ['baru', 'diterima', 'menunggu'], true)) {
<<<<<<< HEAD
            $penjagaName = session('active_penjaga_kasir') ?: session('active_cashier_name') ?: CafeSetting::get('active_penjaga_kasir') ?: $user->name;
=======
            $penjagaName = session('active_penjaga_kasir')
                ?: session('active_cashier_name')
                ?: CafeSetting::get('active_penjaga_kasir')
                ?: $user->name;
>>>>>>> 6ebee46b65a42102454bfc65150cba0c5d1831b5
            $order->update([
                'order_status' => 'diproses',
                'cashier_id' => $order->cashier_id ?: $user->id,
                'cashier_name' => $order->cashier_name ?: $penjagaName,
            ]);

            $this->orders->addTimeline(
                $order,
                'DIPROSES',
                $user->name,
                'Pesanan diverifikasi melalui Scan QR Kasir dan mulai diproses bar/dapur'
            );

            $this->log($request, "Scan QR pesanan {$order->order_code} - status diubah menjadi Sedang Diproses");
            $isNewlyProcessed = true;
        }

        $checkoutUrl = route('kasir.payments.checkout', $order);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => "Pesanan {$order->order_code} berhasil di-scan! Status kini: Sedang Diproses.",
                'order_code' => $order->order_code,
                'order_status' => $order->order_status,
                'payment_status' => $order->payment_status,
                'checkout_url' => $checkoutUrl,
                'is_newly_processed' => $isNewlyProcessed,
            ]);
        }

        // Jika pesanan belum lunas, langsung arahkan kasir ke halaman pembayaran
        if (! $order->isPaid()) {
            return redirect()->route('kasir.payments.checkout', $order)
                ->with('success', "Pesanan {$order->order_code} berhasil di-scan! Status kini: Sedang Diproses. Silakan proses pembayaran.");
        }

        return redirect()->route('kasir.orders.index')
            ->with('success', "Pesanan {$order->order_code} berhasil di-scan! Status: Sedang Diproses.");
    }

    /**
     * Endpoint form submit untuk Scan QR / input manual dari modal kasir.
     */
    public function scanSubmit(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:150',
        ]);

        return $this->scan($request, $validated['code']);
    }

    private function log(Request $request, string $activity): void
    {
        ActivityLog::create([
            'user_name' => Auth::user()->name,
            'role' => Auth::user()->primaryRole(),
            'activity' => $activity,
            'activity_type' => 'pesanan',
            'ip_address' => $request->ip(),
        ]);
    }
}
