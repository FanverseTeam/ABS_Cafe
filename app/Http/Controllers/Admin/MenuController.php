<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Category;
use App\Models\Menu;
use App\Services\MenuMergeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class MenuController extends Controller
{
    public function index(Request $request)
    {
        $categories = Category::where('is_active', true)->orderBy('order', 'asc')->get();

        $query = Menu::with('category');

        // Search
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Category Filter
        if ($request->filled('category_id') && $request->input('category_id') !== 'all') {
            $query->where('category_id', $request->input('category_id'));
        }

        // Sorting
        $sort = $request->input('sort', 'terlaris');
        match ($sort) {
            'nama' => $query->orderBy('name', 'asc'),
            'harga_asc' => $query->orderBy('price', 'asc'),
            'harga_desc' => $query->orderBy('price', 'desc'),
            default => $query->orderBy('sold_count', 'desc'),
        };

        $menus = $query->get();
        $viewMode = $request->input('view', 'grid');

        return view('admin.menu.index', compact('menus', 'categories', 'viewMode'));
    }

    public function store(Request $request)
    {
        $this->validateMenu($request);

        $variantGroup = $request->filled('variant_group') ? trim($request->input('variant_group')) : null;
        $variantLabel = $request->filled('variant_label') ? trim($request->input('variant_label')) : null;

        if (! $variantGroup && preg_match('/^(.+?)\s*\(([^)]+)\)\s*$/', $request->input('name'), $m)) {
            $variantGroup = trim($m[1]);
            $variantLabel = trim($m[2]);
        }

        $menu = Menu::create([
            'name' => $request->input('name'),
            'variant_group' => $variantGroup,
            'variant_label' => $variantLabel,
            'category_id' => $request->input('category_id'),
            'price' => $request->input('price'),
            'description' => $request->input('description'),
            'image' => null,
            'is_available' => $request->boolean('is_available', true),
            'is_best_seller' => $request->boolean('is_best_seller', false),
            'sold_count' => 0,
        ]);

        if ($request->hasFile('image_file')) {
            $menu->updateImageFile($request->file('image_file'));
            $menu->save();
        }

        ActivityLog::create([
            'user_name' => Auth::user()->name,
            'role' => Auth::user()->role,
            'activity' => "Menambahkan menu baru: {$menu->name} (Rp ".number_format($menu->price, 0, ',', '.').')',
            'activity_type' => 'menu',
            'ip_address' => $request->ip(),
        ]);

        return back()->with('success', "Menu {$menu->name} berhasil ditambahkan!");
    }

    public function update(Request $request, $id)
    {
        $this->validateMenu($request);

        $menu = Menu::findOrFail($id);

        $variantGroup = $request->filled('variant_group') ? trim($request->input('variant_group')) : null;
        $variantLabel = $request->filled('variant_label') ? trim($request->input('variant_label')) : null;

        if (! $variantGroup && preg_match('/^(.+?)\s*\(([^)]+)\)\s*$/', $request->input('name'), $m)) {
            $variantGroup = trim($m[1]);
            $variantLabel = trim($m[2]);
        }

        if ($request->hasFile('image_file')) {
            $menu->updateImageFile($request->file('image_file'));
        }

        $menu->update([
            'name' => $request->input('name'),
            'variant_group' => $variantGroup,
            'variant_label' => $variantLabel,
            'category_id' => $request->input('category_id'),
            'price' => $request->input('price'),
            'description' => $request->input('description'),
            'image' => $menu->image,
            'is_available' => $request->boolean('is_available'),
            'is_best_seller' => $request->boolean('is_best_seller'),
        ]);

        ActivityLog::create([
            'user_name' => Auth::user()->name,
            'role' => Auth::user()->role,
            'activity' => "Memperbarui informasi menu: {$menu->name}",
            'activity_type' => 'menu',
            'ip_address' => $request->ip(),
        ]);

        return back()->with('success', "Perubahan menu {$menu->name} berhasil disimpan.");
    }

    public function duplicate($id)
    {
        $original = Menu::findOrFail($id);
        $duplicate = $original->replicate();
        $duplicate->name = "{$original->name} (Salinan)";
        $duplicate->sold_count = 0;
        $duplicate->save();

        ActivityLog::create([
            'user_name' => Auth::user()->name,
            'role' => Auth::user()->role,
            'activity' => "Menduplikasi menu {$original->name}",
            'activity_type' => 'menu',
            'ip_address' => request()->ip(),
        ]);

        return back()->with('success', "Menu {$original->name} berhasil diduplikasi.");
    }

    public function toggleAvailable($id)
    {
        $menu = Menu::findOrFail($id);
        $menu->is_available = ! $menu->is_available;
        $menu->save();

        $statusStr = $menu->is_available ? 'Tersedia' : 'Habis';

        ActivityLog::create([
            'user_name' => Auth::user()->name,
            'role' => Auth::user()->role,
            'activity' => "Mengubah status ketersediaan menu {$menu->name} menjadi {$statusStr}",
            'activity_type' => 'menu',
            'ip_address' => request()->ip(),
        ]);

        return back()->with('success', "Status menu {$menu->name} diubah menjadi {$statusStr}.");
    }

    public function toggleBestSeller($id)
    {
        $menu = Menu::findOrFail($id);
        $menu->is_best_seller = ! $menu->is_best_seller;
        $menu->save();

        $statusStr = $menu->is_best_seller ? 'Bestseller' : 'Biasa';

        ActivityLog::create([
            'user_name' => Auth::user()->name,
            'role' => Auth::user()->role,
            'activity' => "Mengubah status bestseller menu {$menu->name} menjadi {$statusStr}",
            'activity_type' => 'menu',
            'ip_address' => request()->ip(),
        ]);

        return back()->with('success', "Status bestseller menu {$menu->name} berhasil diubah menjadi {$statusStr}.");
    }

    public function destroy($id)
    {
        $menu = Menu::findOrFail($id);
        $name = $menu->name;
        $menu->deleteImageFile();
        $menu->delete();

        ActivityLog::create([
            'user_name' => Auth::user()->name,
            'role' => Auth::user()->role,
            'activity' => "Menghapus menu: {$name}",
            'activity_type' => 'menu',
            'ip_address' => request()->ip(),
        ]);

        return back()->with('success', "Menu {$name} berhasil dihapus!");
    }

    public function mergeMenus(Request $request, MenuMergeService $merge)
    {
        $data = $request->validate([
            'menu_ids' => 'required|array|min:2',
            'menu_ids.*' => 'required|integer|exists:menu,id',
        ], [
            'menu_ids.min' => 'Pilih minimal dua menu untuk digabungkan.',
        ]);

        try {
            $parent = $merge->mergeMenus($data['menu_ids'], Auth::user());
        } catch (ValidationException $exception) {
            return back()->with('error', collect($exception->errors())->flatten()->first());
        }

        return back()->with('success', "Menu berhasil digabungkan menjadi {$parent->name}.");
    }

    private function validateMenu(Request $request): void
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'variant_group' => 'nullable|string|max:100',
            'variant_label' => 'nullable|string|max:50',
            'category_id' => 'required|exists:kategori,id',
            'price' => 'required|numeric|min:0',
            'description' => 'nullable|string',
            'image_file' => 'nullable|image|max:2048',
            'variants' => 'nullable|array',
        ]);
    }
}
