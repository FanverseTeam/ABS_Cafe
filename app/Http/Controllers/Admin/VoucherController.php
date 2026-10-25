<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Category;
use App\Models\Promo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VoucherController extends Controller
{
    public function index()
    {
        $promos = Promo::with('targetCategory')->orderBy('created_at', 'desc')->get();
        $categories = Category::where('is_active', true)->get();

        return view('admin.vouchers.index', compact('promos', 'categories'));
    }

    public function create()
    {
        $promo = new Promo;
        $categories = Category::where('is_active', true)->orderBy('name')->get();

        return view('promos.form', [
            'promo' => $promo,
            'categories' => $categories,
            'isEdit' => false,
            'action' => route('admin.vouchers.store'),
            'backUrl' => route('admin.vouchers.index'),
            'layout' => 'admin.layouts.app',
            'role' => 'admin',
        ]);
    }

    public function edit($promo)
    {
        $promo = $promo instanceof Promo ? $promo : Promo::findOrFail($promo);
        $categories = Category::where('is_active', true)->orderBy('name')->get();

        return view('promos.form', [
            'promo' => $promo,
            'categories' => $categories,
            'isEdit' => true,
            'action' => route('admin.vouchers.update', $promo->id),
            'backUrl' => route('admin.vouchers.index'),
            'layout' => 'admin.layouts.app',
            'role' => 'admin',
        ]);
    }

    public function store(Request $request)
    {
        $validated = $this->validateVoucher($request);

        $promo = Promo::create([
            'title' => $validated['title'],
            'code' => $validated['code'],
            'description' => $validated['description'],
            'type' => $validated['type'],
            'value' => $validated['value'],
            'applies_to' => $validated['applies_to'],
            'target_category_id' => $validated['target_category_id'] ?? null,
            'start_date' => $validated['start_date'],
            'end_date' => $validated['end_date'],
            'banner' => ($validated['banner'] ?? null) ?: 'https://images.unsplash.com/photo-1501339847302-ac426a4a7cbb?w=800&auto=format&fit=crop&q=80',
            'is_active' => $request->boolean('is_active', true),
            'min_spend' => $validated['min_spend'] ?? 0,
            'quota' => $validated['quota'] ?? null,
        ]);

        ActivityLog::create([
            'user_name' => Auth::user()->name,
            'role' => Auth::user()->role,
            'activity' => "Membuat promo baru: {$promo->title}",
            'activity_type' => 'promo',
            'ip_address' => $request->ip(),
        ]);

        return redirect()->route('admin.vouchers.index')->with('success', "Promo {$promo->title} berhasil dibuat!");
    }

    public function update(Request $request, $id)
    {
        $promo = Promo::findOrFail($id);
        $validated = $this->validateVoucher($request, $promo->id);

        $promo->update([
            'title' => $validated['title'],
            'code' => $validated['code'],
            'description' => $validated['description'],
            'type' => $validated['type'],
            'value' => $validated['value'],
            'applies_to' => $validated['applies_to'],
            'target_category_id' => $validated['target_category_id'] ?? null,
            'start_date' => $validated['start_date'],
            'end_date' => $validated['end_date'],
            'banner' => ($validated['banner'] ?? null) ?: $promo->banner,
            'is_active' => $request->boolean('is_active'),
            'min_spend' => $validated['min_spend'] ?? 0,
            'quota' => $validated['quota'] ?? null,
        ]);

        ActivityLog::create([
            'user_name' => Auth::user()->name,
            'role' => Auth::user()->role,
            'activity' => "Memperbarui promo: {$promo->title}",
            'activity_type' => 'promo',
            'ip_address' => $request->ip(),
        ]);

        return redirect()->route('admin.vouchers.index')->with('success', "Promo {$promo->title} berhasil diperbarui.");
    }

    /**
     * @return array<string, mixed>
     */
    private function validateVoucher(Request $request, ?int $ignoreId = null): array
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'code' => 'nullable|string|max:50|unique:promo,code'.($ignoreId ? ','.$ignoreId : ''),
            'description' => 'required|string',
            'type' => 'required|in:persen,nominal,buy_x_get_y',
            'value' => 'required|numeric|min:0',
            'applies_to' => 'required|in:semua,kategori,menu',
            'target_category_id' => 'nullable|required_if:applies_to,kategori|exists:kategori,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'banner' => 'nullable|string',
            'min_spend' => 'nullable|numeric|min:0',
            'quota' => 'nullable|integer|min:0',
        ], [
            'title.required' => 'Judul promo wajib diisi.',
            'title.max' => 'Judul promo maksimal 255 karakter.',
            'code.unique' => 'Kode promo sudah digunakan.',
            'description.required' => 'Deskripsi promo wajib diisi.',
            'type.required' => 'Jenis promo wajib dipilih.',
            'type.in' => 'Jenis promo tidak valid.',
            'value.required' => 'Nilai diskon wajib diisi.',
            'value.numeric' => 'Nilai diskon harus berupa angka.',
            'value.min' => 'Nilai diskon minimal 0.',
            'applies_to.required' => 'Ketentuan berlaku untuk wajib dipilih.',
            'target_category_id.required_if' => 'Kategori target wajib dipilih jika promo berlaku untuk kategori.',
            'target_category_id.exists' => 'Kategori target yang dipilih tidak valid.',
            'start_date.required' => 'Tanggal mulai wajib diisi.',
            'start_date.date' => 'Format tanggal mulai tidak valid.',
            'end_date.required' => 'Tanggal selesai wajib diisi.',
            'end_date.date' => 'Format tanggal selesai tidak valid.',
            'end_date.after_or_equal' => 'Tanggal selesai harus sama atau setelah tanggal mulai.',
        ]);

        $validated['code'] = $request->filled('code') ? strtoupper((string) $request->input('code')) : null;

        return $validated;
    }

    public function toggleActive($id)
    {
        $promo = Promo::findOrFail($id);
        $promo->is_active = ! $promo->is_active;
        $promo->save();

        $statusStr = $promo->is_active ? 'Diaktifkan' : 'Dinonaktifkan';

        ActivityLog::create([
            'user_name' => Auth::user()->name,
            'role' => Auth::user()->role,
            'activity' => "Promo {$promo->title} telah {$statusStr}",
            'activity_type' => 'promo',
            'ip_address' => request()->ip(),
        ]);

        return back()->with('success', "Status promo {$promo->title} berhasil diubah.");
    }

    public function destroy($id)
    {
        $promo = Promo::findOrFail($id);
        $title = $promo->title;
        $promo->delete();

        ActivityLog::create([
            'user_name' => Auth::user()->name,
            'role' => Auth::user()->role,
            'activity' => "Menghapus promo: {$title}",
            'activity_type' => 'promo',
            'ip_address' => request()->ip(),
        ]);

        return back()->with('success', "Promo {$title} telah dihapus.");
    }
}
