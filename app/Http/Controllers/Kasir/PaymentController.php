<?php

namespace App\Http\Controllers\Kasir;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Order;
use App\Models\PaymentMethod;
use App\Services\PaymentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class PaymentController extends Controller
{
    public function __construct(private readonly PaymentService $payments)
    {
    }

    public function index(Request $request)
    {
        $unpaid = Order::with('items')
            ->unpaid()
            ->where('order_status', '!=', 'selesai')
            ->orderBy('created_at')
            ->get();

        $paidToday = Order::with('items')
            ->where('payment_status', 'lunas')
            ->whereDate('paid_at', today())
            ->latest('paid_at')
            ->paginate(10);

        $paidTotal = (float) Order::where('payment_status', 'lunas')
            ->whereDate('paid_at', today())
            ->sum('total');

        return view('kasir.payments.index', compact('unpaid', 'paidToday', 'paidTotal'));
    }

    public function checkout(Order $order)
    {
        if ($order->isPaid()) {
            return redirect()->route('kasir.payments.receipt', $order)
                ->with('success', 'Pesanan ini sudah dibayar.');
        }
        if ($order->isCancelled()) {
            return redirect()->route('kasir.payments.index')->with('error', 'Pesanan sudah dibatalkan.');
        }

        $order->load('items');
        $paymentMethods = PaymentMethod::active()->get();

        return view('kasir.payments.checkout', compact('order', 'paymentMethods'));
    }

    public function pay(Request $request, Order $order)
    {
        $data = $request->validate([
            'method' => 'required|string|max:30',
            'cash_received' => 'nullable|numeric|min:0',
            'reference' => 'nullable|string|max:100',
        ]);

        try {
            $this->payments->record($order, Auth::user(), [
                'method' => $data['method'],
                'cash_received' => $data['cash_received'] ?? 0,
                'reference' => $data['reference'] ?? null,
            ]);
        } catch (ValidationException $e) {
            return back()->withInput()->with('error', collect($e->errors())->flatten()->first());
        }

        ActivityLog::create([
            'user_name' => Auth::user()->name,
            'role' => Auth::user()->primaryRole(),
            'activity' => "Mencetak struk pembayaran {$order->order_code}",
            'activity_type' => 'transaksi',
            'ip_address' => $request->ip(),
        ]);

        return redirect()->route('kasir.payments.receipt', $order)
            ->with('success', "Pembayaran {$order->order_code} berhasil.");
    }

    public function receipt(Order $order)
    {
        $order->load('items', 'promo');
        $setting = [
            'name' => \App\Models\CafeSetting::get('cafe_name', 'ABS Cafe'),
            'address' => \App\Models\CafeSetting::get('address', ''),
            'phone' => \App\Models\CafeSetting::get('phone', ''),
            'footer' => \App\Models\CafeSetting::get('receipt_footer', 'Terima kasih atas kunjungan Anda'),
        ];

        return view('kasir.payments.receipt', compact('order', 'setting'));
    }

    public function receiptJson(Order $order)
    {
        $order->load('items', 'promo');

        return response()->json([
            'code' => $order->order_code,
            'customer' => $order->customer_name,
            'table' => $order->table_number,
            'type' => $order->orderTypeLabel(),
            'payment_method' => $order->paymentMethodLabel(),
            'cashier' => $order->cashier_name,
            'paid_at' => $order->paid_at?->format('d/m/Y H:i'),
            'subtotal' => (float) $order->subtotal,
            'tax' => (float) $order->tax,
            'service' => (float) $order->service_charge,
            'discount' => (float) $order->discount,
            'promo' => $order->promo?->title,
            'total' => (float) $order->total,
            'items' => $order->items->map(fn ($i) => [
                'name' => $i->name,
                'qty' => $i->quantity,
                'price' => (float) $i->price,
                'subtotal' => (float) $i->subtotal,
            ]),
        ]);
    }
}
