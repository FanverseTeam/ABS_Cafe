<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Order;
use App\Models\OrderTimeline;
use App\Models\ActivityLog;
use App\Models\CafeSetting;
use App\Services\OrderService;

class OrderController extends Controller
{
    public function __construct(private readonly OrderService $orders)
    {
    }

    public function index(Request $request)
    {
        $query = Order::with(['items', 'timelines'])->orderBy('created_at', 'desc');

        // Search
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('order_code', 'like', "%{$search}%")
                  ->orWhere('customer_name', 'like', "%{$search}%");
            });
        }

        // Filter Payment Status
        if ($request->filled('payment_status') && $request->input('payment_status') !== 'all') {
            $query->where('payment_status', $request->input('payment_status'));
        }

        // Filter Order Status
        if ($request->filled('order_status') && $request->input('order_status') !== 'all') {
            $query->where('order_status', $request->input('order_status'));
        }

        // Filter Order Type
        if ($request->filled('order_type') && $request->input('order_type') !== 'all') {
            $query->where('order_type', $request->input('order_type'));
        }

        $orders = $query->paginate(10)->withQueryString();

        $cafeSettings = [
            'name' => CafeSetting::get('cafe_name', 'ABS Cafe'),
            'tagline' => CafeSetting::get('cafe_tagline', 'Accessories & Cafe'),
            'header' => CafeSetting::get('receipt_header', ''),
            'footer' => CafeSetting::get('receipt_footer', ''),
        ];

        return view('admin.orders.index', compact('orders', 'cafeSettings'));
    }

    public function show($id)
    {
        $order = Order::with(['items', 'timelines'])->findOrFail($id);
        return response()->json($order);
    }

    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'order_status' => 'required|in:diproses,siap,selesai',
        ]);

        $order = Order::findOrFail($id);
        $oldStatus = $order->order_status;
        $newStatus = $request->input('order_status');

        $order->update(['order_status' => $newStatus]);

        $keterangan = match ($newStatus) {
            'diproses' => 'Pesanan mulai diproses oleh tim bar/dapur',
            'siap' => 'Pesanan telah siap untuk disajikan ke meja',
            'selesai' => 'Pesanan telah selesai dinikmati pelanggan',
            default => 'Status pesanan diubah',
        };

        OrderTimeline::create([
            'order_id' => $order->id,
            'status' => strtoupper($newStatus),
            'actor' => Auth::user()->name,
            'description' => $keterangan,
            'logged_at' => now(),
        ]);

        ActivityLog::create([
            'user_name' => Auth::user()->name,
            'role' => Auth::user()->role,
            'activity' => "Mengubah status pesanan {$order->order_code} dari {$oldStatus} menjadi {$newStatus}",
            'activity_type' => 'pesanan',
            'ip_address' => $request->ip(),
        ]);

        return back()->with('success', "Status pesanan {$order->order_code} berhasil diperbarui menjadi {$newStatus}.");
    }

    public function cancel(Request $request, $id)
    {
        $request->validate([
            'cancel_reason' => 'required|string|min:5',
        ], [
            'cancel_reason.required' => 'Alasan pembatalan wajib diisi.',
            'cancel_reason.min' => 'Alasan pembatalan minimal 5 karakter.',
        ]);

        $order = Order::findOrFail($id);
        $reason = $request->input('cancel_reason');

        $this->orders->cancel($order, Auth::user(), $reason);

        ActivityLog::create([
            'user_name' => Auth::user()->name,
            'role' => Auth::user()->role,
            'activity' => "Membatalkan pesanan {$order->order_code} dengan alasan: \"{$reason}\"",
            'activity_type' => 'pesanan',
            'ip_address' => $request->ip(),
        ]);

        return back()->with('success', "Pesanan {$order->order_code} telah dibatalkan.");
    }

    public function exportCsv(Request $request)
    {
        $fileName = 'Pesanan_ABS_Cafe_' . date('Ymd_His') . '.csv';
        $orders = Order::orderBy('created_at', 'desc')->get();

        $headers = [
            "Content-type" => "text/csv; charset=UTF-8",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        ];

        $columns = ['Kode Order', 'Waktu', 'Customer', 'Tipe', 'Meja', 'Total (Rp)', 'Metode Bayar', 'Status Bayar', 'Status Pesanan', 'Kasir'];

        $callback = function () use ($orders, $columns) {
            $file = fopen('php://output', 'w');
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF)); // BOM for UTF-8
            fputcsv($file, $columns);

            foreach ($orders as $o) {
                fputcsv($file, [
                    $o->order_code,
                    $o->created_at->format('d/m/Y H:i'),
                    $o->customer_name,
                    $o->order_type === 'dine_in' ? 'Dine-in' : 'Take-away',
                    $o->table_number ?? '-',
                    $o->total,
                    strtoupper($o->payment_method),
                    strtoupper(str_replace('_', ' ', $o->payment_status)),
                    strtoupper($o->order_status),
                    $o->cashier_name ?? '-',
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
