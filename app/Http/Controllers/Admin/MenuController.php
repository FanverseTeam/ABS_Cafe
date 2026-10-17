<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Category;
use App\Models\Menu;
use App\Models\Stock;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class MenuController extends Controller
{
    public function index(Request $request)
    {
        $categories = Category::where('is_active', true)->orderBy('order', 'asc')->get();
        $stocks = Stock::where('is_active', true)->orderBy('name')->get();

        $query = Menu::with(['category', 'variants', 'recipes.stock', 'recipes.variant']);

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

        return view('admin.menu.index', compact('menus', 'categories', 'stocks', 'viewMode'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'price' => 'required|numeric|min:0',
            'description' => 'nullable|string',
            'image' => 'nullable|string',
            'stock' => 'nullable|integer|min:0',
            'min_stock' => 'nullable|integer|min:1',
            'unit' => 'nullable|string',
        ]);

        $menu = Menu::create([
            'name' => $request->input('name'),
            'category_id' => $request->input('category_id'),
            'price' => $request->input('price'),
            'description' => $request->input('description'),
            'image' => $request->input('image') ?: 'https://images.unsplash.com/photo-1509042239860-f550ce710b93?w=500&auto=format&fit=crop&q=80',
            'is_available' => $request->boolean('is_available', true),
            'is_best_seller' => $request->boolean('is_best_seller', false),
            'stock' => $request->input('stock', 30),
            'min_stock' => $request->input('min_stock', 10),
            'unit' => $request->input('unit', 'porsi'),
            'sold_count' => 0,
        ]);

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
        $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'price' => 'required|numeric|min:0',
            'description' => 'nullable|string',
            'image' => 'nullable|string',
            'stock' => 'nullable|integer|min:0',
            'min_stock' => 'nullable|integer|min:1',
            'unit' => 'nullable|string',
        ]);

        $menu = Menu::findOrFail($id);

        $menu->update([
            'name' => $request->input('name'),
            'category_id' => $request->input('category_id'),
            'price' => $request->input('price'),
            'description' => $request->input('description'),
            'image' => $request->input('image') ?: $menu->image,
            'is_available' => $request->boolean('is_available'),
            'is_best_seller' => $request->boolean('is_best_seller'),
            'stock' => $request->input('stock', $menu->stock),
            'min_stock' => $request->input('min_stock', $menu->min_stock),
            'unit' => $request->input('unit', $menu->unit),
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
            'activity' => "Mengubah status menu {$menu->name} menjadi {$statusStr}",
            'activity_type' => 'menu',
            'ip_address' => request()->ip(),
        ]);

        return back()->with('success', "Status {$menu->name} diubah menjadi {$statusStr}.");
    }

    public function toggleBestSeller($id)
    {
        $menu = Menu::findOrFail($id);
        $menu->is_best_seller = ! $menu->is_best_seller;
        $menu->save();

        $tag = $menu->is_best_seller ? 'ditandai sebagai' : 'dihapus dari';

        ActivityLog::create([
            'user_name' => Auth::user()->name,
            'role' => Auth::user()->role,
            'activity' => "Menu {$menu->name} {$tag} Best Seller",
            'activity_type' => 'menu',
            'ip_address' => request()->ip(),
        ]);

        return back()->with('success', "Status Best Seller {$menu->name} diperbarui.");
    }

    public function destroy($id)
    {
        $menu = Menu::findOrFail($id);
        $name = $menu->name;
        $menu->delete();

        ActivityLog::create([
            'user_name' => Auth::user()->name,
            'role' => Auth::user()->role,
            'activity' => "Menghapus menu {$name}",
            'activity_type' => 'menu',
            'ip_address' => request()->ip(),
        ]);

        return back()->with('success', "Menu {$name} telah berhasil dihapus.");
    }

    public function recipes($id)
    {
        $menu = Menu::with(['variants', 'recipes.stock', 'recipes.variant'])->findOrFail($id);
        $stocks = Stock::where('is_active', true)->orderBy('name')->get();

        return response()->json([
            'menu' => $menu,
            'variants' => $menu->variants,
            'recipes' => $menu->recipes,
            'stocks' => $stocks,
        ]);
    }

    public function saveRecipes(Request $request, $id)
    {
        $menu = Menu::findOrFail($id);

        $data = $request->validate([
            'recipes' => 'nullable|array',
            'recipes.*.stock_id' => 'required|exists:stocks,id',
            'recipes.*.menu_variant_id' => 'nullable|exists:menu_variants,id',
            'recipes.*.amount' => 'required|numeric|min:0.01',
        ]);

        DB::transaction(function () use ($menu, $data) {
            $menu->recipes()->delete();

            if (! empty($data['recipes'])) {
                foreach ($data['recipes'] as $item) {
                    $menu->recipes()->create([
                        'menu_variant_id' => ! empty($item['menu_variant_id']) ? $item['menu_variant_id'] : null,
                        'stock_id' => $item['stock_id'],
                        'amount' => $item['amount'],
                    ]);
                }
            }
        });

        ActivityLog::create([
            'user_name' => Auth::user()->name,
            'role' => Auth::user()->primaryRole(),
            'activity' => "Memperbarui resep bahan untuk menu {$menu->name}",
            'activity_type' => 'menu',
            'ip_address' => $request->ip(),
        ]);

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'message' => "Resep untuk menu {$menu->name} berhasil disimpan."]);
        }

        return back()->with('success', "Resep untuk menu {$menu->name} berhasil diperbarui.");
    }
}
