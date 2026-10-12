<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Promo;
use App\Models\Category;
use App\Models\ActivityLog;

class PromoController extends Controller
{
    public function index()
    {
        $promos = Promo::with('targetCategory')->orderBy('created_at', 'desc')->get();
        $categories = Category::where('is_active', true)->get();
        return view('admin.promo.index', compact('promos', 'categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'code' => 'nullable|string|max:50|unique:promos,code',
            'description' => 'required|string',
            'type' => 'required|in:persen,nominal,buy_x_get_y',
            'value' => 'required|numeric|min:0',
            'applies_to' => 'required|in:semua,kategori,menu',
            'target_category_id' => 'nullable|exists:categories,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'banner' => 'nullable|string',
            'min_spend' => 'nullable|numeric|min:0',
        ]);

        $promo = Promo::create([
            'title' => $request->input('title'),
            'code' => $request->filled('code') ? strtoupper($request->input('code')) : null,
            'description' => $request->input('description'),
            'type' => $request->input('type'),
            'value' => $request->input('value'),
            'applies_to' => $request->input('applies_to'),
            'target_category_id' => $request->input('target_category_id'),
            'start_date' => $request->input('start_date'),
            'end_date' => $request->input('end_date'),
            'banner' => $request->input('banner') ?: 'https://images.unsplash.com/photo-1501339847302-ac426a4a7cbb?w=800&auto=format&fit=crop&q=80',
            'is_active' => $request->boolean('is_active', true),
            'min_spend' => $request->input('min_spend', 0),
        ]);

        ActivityLog::create([
            'user_name' => Auth::user()->name,
            'role' => Auth::user()->role,
            'activity' => "Membuat promo baru: {$promo->title}",
            'activity_type' => 'promo',
            'ip_address' => $request->ip(),
        ]);

        return back()->with('success', "Promo {$promo->title} berhasil dibuat!");
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'code' => 'nullable|string|max:50|unique:promos,code,'.$id,
            'description' => 'required|string',
            'type' => 'required|in:persen,nominal,buy_x_get_y',
            'value' => 'required|numeric|min:0',
            'applies_to' => 'required|in:semua,kategori,menu',
            'target_category_id' => 'nullable|exists:categories,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'banner' => 'nullable|string',
            'min_spend' => 'nullable|numeric|min:0',
        ]);

        $promo = Promo::findOrFail($id);
        $promo->update([
            'title' => $request->input('title'),
            'code' => $request->filled('code') ? strtoupper($request->input('code')) : null,
            'description' => $request->input('description'),
            'type' => $request->input('type'),
            'value' => $request->input('value'),
            'applies_to' => $request->input('applies_to'),
            'target_category_id' => $request->input('target_category_id'),
            'start_date' => $request->input('start_date'),
            'end_date' => $request->input('end_date'),
            'banner' => $request->input('banner') ?: $promo->banner,
            'is_active' => $request->boolean('is_active'),
            'min_spend' => $request->input('min_spend', 0),
        ]);

        ActivityLog::create([
            'user_name' => Auth::user()->name,
            'role' => Auth::user()->role,
            'activity' => "Memperbarui promo: {$promo->title}",
            'activity_type' => 'promo',
            'ip_address' => $request->ip(),
        ]);

        return back()->with('success', "Promo {$promo->title} berhasil diperbarui.");
    }

    public function toggleActive($id)
    {
        $promo = Promo::findOrFail($id);
        $promo->is_active = !$promo->is_active;
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
