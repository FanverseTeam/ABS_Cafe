<?php

namespace App\Http\Controllers\Kasir;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\CafeSetting;
use App\Models\Category;
use App\Models\Order;
use App\Models\PaymentMethod;
use App\Models\Promo;
use App\Models\Table;
use App\Services\OrderService;
use App\Services\PaymentService;
use App\Services\ShiftService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class PosController extends Controller
{
    public function __construct(
        private readonly OrderService $orders,
        private readonly PaymentService $payments,
        private readonly ShiftService $shifts,
    ) {}

    public function index(Request $request)
    {
        $categories = Category::where('is_active', true)
            ->orderBy('order')
            ->with(['menus' => fn ($q) => $q->orderByDesc('is_best_seller')->orderBy('name')->with('variants')])
            ->get();

        $tables = Table::orderBy('number')->get();
        $paymentMethods = PaymentMethod::active()->get();
        $promos = Promo::running()->get();

        // Pesanan yang ditahan: belum dibayar & belum selesai, dibuat shift berjalan
        $shift = $this->shifts->activeFor(Auth::user());
        $heldOrders = $shift
            ? Order::with('items')->where('cash_shift_id', $shift->id)->unpaid()
                ->where('order_status', '!=', 'selesai')->latest()->get()
            : collect();

        $resume = null;
        if ($request->filled('resume')) {
            $resume = Order::with('items')->find($request->integer('resume'));
        }

        return view('kasir.pos.index', compact(
            'categories',
            'tables',
            'paymentMethods',
            'promos',
            'heldOrders',
            'resume',
        ));
    }

    public function totals(Request $request)
    {
        $cart = json_decode($request->input('cart', '[]'), true);
        $cart = is_array($cart) ? $cart : [];

        try {
            $built = $this->orders->buildLines($cart);
            $promo = $this->orders->findPromo(trim($request->input('voucher', '')) ?: null);
            ['discount' => $discount] = $this->orders->calculateDiscount($promo, $built['items'], $built['subtotal']);

            $tax = round(($built['subtotal'] - $discount) * (float) CafeSetting::get('tax_percent', 10) / 100, 2);
            $serviceActive = (bool) CafeSetting::get('service_charge_active', 0);
            $service = $serviceActive ? round(($built['subtotal'] - $discount) * (float) CafeSetting::get('service_charge_percent', 5) / 100, 2) : 0;
            $total = round($built['subtotal'] + $tax + $service - $discount, 2);

            return response()->json([
                'ok' => true,
                'subtotal' => $built['subtotal'],
                'discount' => $discount,
                'tax' => $tax,
                'service' => $service,
                'total' => $total,
                'voucher' => $promo?->title,
            ]);
        } catch (ValidationException $e) {
            return response()->json(['ok' => false, 'message' => collect($e->errors())->flatten()->first()], 422);
        }
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'order_type' => 'required|in:dine_in,take_away',
            'table_number' => 'nullable|integer|exists:meja,number',
            'customer_name' => 'required|string|max:100',
            'customer_phone' => 'nullable|string|max:30',
            'customer_email' => 'nullable|email|max:100',
            'cart' => 'required|string',
            'voucher' => 'nullable|string|max:50',
            'action' => 'required|in:save,pay',
            'payment_method' => 'nullable|string|max:30',
            'cash_received' => 'nullable|numeric|min:0',
            'reference' => 'nullable|string|max:100',
        ], [
            'customer_name.required' => 'Nama pelanggan wajib diisi.',
            'table_number.exists' => 'Nomor meja yang dipilih tidak valid.',
        ]);

        $cart = json_decode($data['cart'], true);
        if (! is_array($cart) || empty($cart)) {
            return back()->withInput()->with('error', 'Keranjang masih kosong.');
        }

        $shift = $this->shifts->activeFor(Auth::user());
        if (! $shift) {
            return redirect()->route('kasir.shift.open')->with('error', 'Buka shift terlebih dahulu.');
        }

        if ($data['order_type'] === 'dine_in' && empty($data['table_number'])) {
            return back()->withInput()->with('error', 'Pilih nomor meja untuk pesanan Dine-in.');
        }

        try {
            $order = $this->orders->createOrder([
                'items' => $cart,
                'order_type' => $data['order_type'],
                'table_number' => $data['table_number'] ?? null,
                'customer_name' => $data['customer_name'],
                'customer_phone' => $data['customer_phone'] ?? null,
                'customer_email' => $data['customer_email'] ?? null,
                'voucher' => $data['voucher'] ?? null,
                'source' => 'offline',
                'order_status' => 'baru',
                'payment_method' => $data['payment_method'] ?? 'tunai',
            ], Auth::user(), $shift);

            if ($data['order_type'] === 'dine_in' && ! empty($data['table_number'])) {
                Table::where('number', $data['table_number'])->update(['status' => 'terpakai']);
            }
        } catch (ValidationException $e) {
            return back()->withInput()->with('error', collect($e->errors())->flatten()->first());
        }

        if ($data['action'] === 'pay') {
            try {
                $this->payments->record($order, Auth::user(), [
                    'method' => $data['payment_method'] ?? 'tunai',
                    'cash_received' => $data['cash_received'] ?? 0,
                    'reference' => $data['reference'] ?? null,
                ]);
            } catch (ValidationException $e) {
                return redirect()->route('kasir.payments.checkout', $order)
                    ->with('error', collect($e->errors())->flatten()->first());
            }

            return redirect()->route('kasir.payments.receipt', $order)
                ->with('success', "Pesanan {$order->order_code} berhasil dibayar.");
        }

        $this->log($request, "Menyimpan pesanan ditahan {$order->order_code}");

        return redirect()->route('kasir.pos.index')
            ->with('success', "Pesanan {$order->order_code} disimpan. Bisa dilanjutkan/dibayar nanti.");
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
