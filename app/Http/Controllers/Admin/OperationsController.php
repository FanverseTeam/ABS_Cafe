<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\CafeSetting;
use App\Models\PaymentMethod;
use App\Models\Table;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class OperationsController extends Controller
{
    public function index(Request $request)
    {
        $tables = Table::orderBy('number', 'asc')->get();

        $paymentQuery = PaymentMethod::query();

        if ($request->filled('payment_search')) {
            $search = $request->input('payment_search');
            $paymentQuery->where(function ($query) use ($search) {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('code', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($request->filled('payment_status') && $request->input('payment_status') !== 'all') {
            $paymentQuery->where('is_active', $request->input('payment_status') === 'aktif');
        }

        $paymentMethods = $paymentQuery->orderBy('sort_order')->orderBy('name')->get();
        $hostingUrl = $this->normalizeHostUrl(CafeSetting::get('order_base_url'));
        $orderBaseUrl = $hostingUrl.'/menu';

        return view('admin.operations.index', compact('tables', 'paymentMethods', 'orderBaseUrl', 'hostingUrl'));
    }

    /**
     * Normalisasi link hostingan (Cloudflare, Ngrok, domain custom, IP, dll.).
     * Menghapus slash dan rute /menu di belakang secara otomatis.
     */
    private function normalizeHostUrl(?string $input): string
    {
        $url = trim((string) $input);
        if (empty($url)) {
            return rtrim(url('/'), '/');
        }

        // Tambahkan skema jika belum ada
        if (! preg_match('~^https?://~i', $url)) {
            if (preg_match('/^(localhost|127\.0\.0\.1|192\.168\.|10\.)/i', $url)) {
                $url = 'http://'.$url;
            } else {
                $url = 'https://'.$url;
            }
        }

        // Hapus trailing slash
        $url = rtrim($url, '/');

        // Hapus suffix /menu jika user terlanjur memasukkannya
        if (preg_match('~/menu$~i', $url)) {
            $url = preg_replace('~/menu$~i', '', $url);
            $url = rtrim($url, '/');
        }

        return $url;
    }

    /**
     * Update Pengaturan Base URL QR Pemesanan Meja.
     */
    public function updateQrSetting(Request $request)
    {
        $request->validate([
            'order_base_url' => 'nullable|string|max:255',
        ]);

        $hostingUrl = $this->normalizeHostUrl($request->input('order_base_url'));

        CafeSetting::set('order_base_url', $hostingUrl);

        ActivityLog::create([
            'user_name' => Auth::user()->name,
            'role' => Auth::user()->role,
            'activity' => "Memperbarui Link Hostingan QR Pemesanan Meja menjadi {$hostingUrl} (Menu: {$hostingUrl}/menu)",
            'activity_type' => 'meja',
            'ip_address' => $request->ip(),
        ]);

        return back()->with('success', 'Link hostingan QR meja berhasil disimpan! Rute /menu otomatis diterapkan.');
    }

    /**
     * Tampilkan Halaman Cetak Stand / Tent Card QR untuk Semua Meja.
     */
    public function printAllQr(Request $request)
    {
        $tables = Table::orderBy('number', 'asc')->get();
        $hostingUrl = $this->normalizeHostUrl(CafeSetting::get('order_base_url'));
        $orderBaseUrl = $hostingUrl.'/menu';

        return view('admin.operations.print-qr', compact('tables', 'orderBaseUrl'));
    }

    /**
     * Tampilkan Halaman Cetak Stand / Tent Card QR untuk Satu Meja Tertentu.
     */
    public function printSingleQr(Request $request, $id)
    {
        $table = Table::findOrFail($id);
        $tables = collect([$table]);
        $hostingUrl = $this->normalizeHostUrl(CafeSetting::get('order_base_url'));
        $orderBaseUrl = $hostingUrl.'/menu';

        return view('admin.operations.print-qr', compact('tables', 'orderBaseUrl'));
    }

    public function storeTable(Request $request)
    {
        $request->validate([
            'number' => 'required|integer|unique:meja,number',
            'capacity' => 'required|integer|min:1',
            'status' => 'required|in:tersedia,terpakai,nonaktif',
            'location_notes' => 'nullable|string',
        ], [
            'number.unique' => 'Nomor meja ini sudah ada.',
        ]);

        $table = Table::create([
            'number' => $request->input('number'),
            'capacity' => $request->input('capacity'),
            'status' => $request->input('status'),
            'location_notes' => $request->input('location_notes'),
        ]);

        ActivityLog::create([
            'user_name' => Auth::user()->name,
            'role' => Auth::user()->role,
            'activity' => "Menambahkan meja nomor #{$table->number} (Kapasitas: {$table->capacity})",
            'activity_type' => 'meja',
            'ip_address' => $request->ip(),
        ]);

        return back()->with('success', "Meja #{$table->number} berhasil ditambahkan!");
    }

    public function updateTable(Request $request, $id)
    {
        $table = Table::findOrFail($id);

        $request->validate([
            'number' => 'required|integer|unique:meja,number,'.$table->id,
            'capacity' => 'required|integer|min:1',
            'status' => 'required|in:tersedia,terpakai,nonaktif',
            'location_notes' => 'nullable|string',
        ]);

        $table->update([
            'number' => $request->input('number'),
            'capacity' => $request->input('capacity'),
            'status' => $request->input('status'),
            'location_notes' => $request->input('location_notes'),
        ]);

        ActivityLog::create([
            'user_name' => Auth::user()->name,
            'role' => Auth::user()->role,
            'activity' => "Memperbarui meja #{$table->number}",
            'activity_type' => 'meja',
            'ip_address' => $request->ip(),
        ]);

        return back()->with('success', "Meja #{$table->number} berhasil diperbarui.");
    }

    public function updateTableStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:tersedia,terpakai,nonaktif',
        ]);

        $table = Table::findOrFail($id);
        $status = $request->input('status');

        $table->update(['status' => $status]);

        ActivityLog::create([
            'user_name' => Auth::user()->name,
            'role' => Auth::user()->role,
            'activity' => "Mengubah status meja #{$table->number} menjadi {$status}",
            'activity_type' => 'meja',
            'ip_address' => request()->ip(),
        ]);

        return back()->with('success', "Status meja #{$table->number} diubah menjadi {$status}.");
    }

    public function destroyTable($id)
    {
        $table = Table::findOrFail($id);
        $number = $table->number;
        $table->delete();

        ActivityLog::create([
            'user_name' => Auth::user()->name,
            'role' => Auth::user()->role,
            'activity' => "Menghapus meja #{$number}",
            'activity_type' => 'meja',
            'ip_address' => request()->ip(),
        ]);

        return back()->with('success', "Meja #{$number} berhasil dihapus.");
    }

    public function storePaymentMethod(Request $request)
    {
        $request->merge(['code' => Str::slug((string) $request->input('code'), '_')]);

        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:metode_bayar,code',
            'type' => 'required|string|max:30',
            'description' => 'nullable|string',
            'instructions' => 'nullable|string',
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'nullable|boolean',
        ], [
            'code.unique' => 'Kode metode pembayaran ini sudah digunakan.',
        ]);

        $method = PaymentMethod::create([
            'name' => $request->input('name'),
            'code' => $request->input('code'),
            'type' => $request->input('type'),
            'description' => $request->input('description'),
            'instructions' => $request->input('instructions'),
            'sort_order' => $request->input('sort_order', 0),
            'is_active' => $request->boolean('is_active', true),
        ]);

        ActivityLog::create([
            'user_name' => Auth::user()->name,
            'role' => Auth::user()->role,
            'activity' => "Menambahkan metode pembayaran: {$method->name}",
            'activity_type' => 'sistem',
            'ip_address' => $request->ip(),
        ]);

        return back()->with('success', "Metode pembayaran {$method->name} berhasil ditambahkan!");
    }

    public function updatePaymentMethod(Request $request, $id)
    {
        $method = PaymentMethod::findOrFail($id);

        $request->merge(['code' => Str::slug((string) $request->input('code'), '_')]);

        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:metode_bayar,code,'.$method->id,
            'type' => 'required|string|max:30',
            'description' => 'nullable|string',
            'instructions' => 'nullable|string',
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'nullable|boolean',
        ], [
            'code.unique' => 'Kode metode pembayaran ini sudah digunakan.',
        ]);

        $method->update([
            'name' => $request->input('name'),
            'code' => $request->input('code'),
            'type' => $request->input('type'),
            'description' => $request->input('description'),
            'instructions' => $request->input('instructions'),
            'sort_order' => $request->input('sort_order', 0),
            'is_active' => $request->boolean('is_active'),
        ]);

        ActivityLog::create([
            'user_name' => Auth::user()->name,
            'role' => Auth::user()->role,
            'activity' => "Memperbarui metode pembayaran: {$method->name}",
            'activity_type' => 'sistem',
            'ip_address' => $request->ip(),
        ]);

        return back()->with('success', "Metode pembayaran {$method->name} berhasil diperbarui.");
    }

    public function togglePaymentMethod($id)
    {
        $method = PaymentMethod::findOrFail($id);
        $method->is_active = ! $method->is_active;
        $method->save();

        $status = $method->is_active ? 'aktif' : 'nonaktif';

        ActivityLog::create([
            'user_name' => Auth::user()->name,
            'role' => Auth::user()->role,
            'activity' => "Mengubah status metode pembayaran {$method->name} menjadi {$status}",
            'activity_type' => 'sistem',
            'ip_address' => request()->ip(),
        ]);

        return back()->with('success', "Metode pembayaran {$method->name} diubah menjadi {$status}.");
    }

    public function destroyPaymentMethod($id)
    {
        $method = PaymentMethod::findOrFail($id);
        $name = $method->name;
        $method->delete();

        ActivityLog::create([
            'user_name' => Auth::user()->name,
            'role' => Auth::user()->role,
            'activity' => "Menghapus metode pembayaran: {$name}",
            'activity_type' => 'sistem',
            'ip_address' => request()->ip(),
        ]);

        return back()->with('success', "Metode pembayaran {$name} berhasil dihapus.");
    }
}
