<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Category;
use App\Models\Menu;
use App\Models\Promo;
use App\Support\HeicConverter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class PromoController extends Controller
{
    public function index()
    {
        $promos = Promo::with(['targetCategory', 'targetMenu'])->orderBy('created_at', 'desc')->get();
        $categories = Category::where('is_active', true)->get();
        $menus = Menu::orderBy('name')->get();

        return view('admin.promo.index', compact('promos', 'categories', 'menus'));
    }

    public function store(Request $request)
    {
        $bannerRules = $request->hasFile('banner') ? 'nullable|file|max:51200' : 'nullable|string';

        $request->validate([
            'title' => 'required|string|max:255',
            'code' => 'nullable|string|max:50|unique:promo,code',
            'description' => 'required|string',
            'type' => 'required|in:persen,nominal,buy_x_get_y',
            'value' => 'required|numeric|min:0',
            'applies_to' => 'required|in:semua,kategori,menu',
            'target_category_id' => 'nullable|exists:kategori,id',
            'target_menu_id' => 'nullable|exists:menu,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'banner' => $bannerRules,
            'min_spend' => 'nullable|numeric|min:0',
        ], [
            'banner.file' => 'Banner harus berupa file gambar valid.',
            'banner.max' => 'Ukuran banner maksimal 50 MB.',
        ]);

        $bannerUrl = 'https://images.unsplash.com/photo-1501339847302-ac426a4a7cbb?w=800&auto=format&fit=crop&q=80';
        if ($request->hasFile('banner')) {
            $bannerUrl = $this->handleBannerUpload($request->file('banner'));
        } elseif ($request->filled('banner')) {
            $bannerUrl = $request->input('banner');
        }

        $promo = Promo::create([
            'title' => $request->input('title'),
            'code' => $request->filled('code') ? strtoupper($request->input('code')) : null,
            'description' => $request->input('description'),
            'type' => $request->input('type'),
            'value' => $request->input('value'),
            'applies_to' => $request->input('applies_to'),
            'target_category_id' => $request->input('target_category_id'),
            'target_menu_id' => $request->input('target_menu_id'),
            'start_date' => $request->input('start_date'),
            'end_date' => $request->input('end_date'),
            'banner' => $bannerUrl,
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
        $bannerRules = $request->hasFile('banner') ? 'nullable|file|max:51200' : 'nullable|string';

        $request->validate([
            'title' => 'required|string|max:255',
            'code' => 'nullable|string|max:50|unique:promo,code,'.$id,
            'description' => 'required|string',
            'type' => 'required|in:persen,nominal,buy_x_get_y',
            'value' => 'required|numeric|min:0',
            'applies_to' => 'required|in:semua,kategori,menu',
            'target_category_id' => 'nullable|exists:kategori,id',
            'target_menu_id' => 'nullable|exists:menu,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'banner' => $bannerRules,
            'min_spend' => 'nullable|numeric|min:0',
        ], [
            'banner.file' => 'Banner harus berupa file gambar valid.',
            'banner.max' => 'Ukuran banner maksimal 50 MB.',
        ]);

        $promo = Promo::findOrFail($id);

        $bannerUrl = $promo->banner;
        if ($request->hasFile('banner')) {
            $bannerUrl = $this->handleBannerUpload($request->file('banner'));
        } elseif ($request->filled('banner')) {
            $bannerUrl = $request->input('banner');
        }

        $promo->update([
            'title' => $request->input('title'),
            'code' => $request->filled('code') ? strtoupper($request->input('code')) : null,
            'description' => $request->input('description'),
            'type' => $request->input('type'),
            'value' => $request->input('value'),
            'applies_to' => $request->input('applies_to'),
            'target_category_id' => $request->input('target_category_id'),
            'target_menu_id' => $request->input('target_menu_id'),
            'start_date' => $request->input('start_date'),
            'end_date' => $request->input('end_date'),
            'banner' => $bannerUrl,
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

    private function handleBannerUpload($file): string
    {
        $file = HeicConverter::convertUploadedFile($file);
        $ext = strtolower($file->getClientOriginalExtension() ?: 'jpg');
        $filename = 'promo_'.time().'_'.Str::random(8).'.'.$ext;
        $targetDir = public_path('images/promos');
        if (! File::isDirectory($targetDir)) {
            File::makeDirectory($targetDir, 0755, true);
        }
        $file->move($targetDir, $filename);

        return asset('images/promos/'.$filename);
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
