<?php

namespace App\Http\Controllers\Kasir;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Order;
use App\Services\OrderService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class OrderController extends Controller
{
    public function __construct(private readonly OrderService $orders)
    {
    }

    public function index(Request $request)
    {
        $tab = $request->get('tab', 'aktif');

        $query = Order::with(['items', 'timelines' => fn ($q) => $q->latest('logged_at')]);

        if ($request->filled('source')) {
            $query->where('source', $request->get('source'));
        }

        $query = match ($tab) {
            'web' => $query->where('source', 'web')->whereIn('order_status', ['baru', 'diterima', 'diproses', 'siap']),
            'belum_bayar' => $query->unpaid()->where('order_status', '!=', 'selesai'),
            'selesai' => $query->whereDate('created_at', today())->whereIn('order_status', ['selesai', 'dibatalkan']),
            default => $query->active(),
        };

        $orders = $query->latest()->paginate(12)->withQueryString();

        $counts = [
            'aktif' => Order::active()->count(),
            'web' => Order::where('source', 'web')->whereIn('order_status', ['baru', 'diterima', 'diproses', 'siap'])->count(),
            'belum_bayar' => Order::unpaid()->where('order_status', '!=', 'selesai')->count(),
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
        try {
            $this->orders->advanceStatus($order, Auth::user(), 'diterima');
        } catch (ValidationException $e) {
            return back()->with('error', collect($e->errors())->flatten()->first());
        }

        $this->log($request, "Menerima pesanan Web {$order->order_code}");

        return back()->with('success', "Pesanan {$order->order_code} diterima.");
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
