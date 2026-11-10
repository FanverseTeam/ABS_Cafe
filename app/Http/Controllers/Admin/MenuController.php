<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Category;
use App\Models\Menu;
use App\Services\MenuMergeService;
use App\Services\MenuVariantService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class MenuController extends Controller
{
    public function __construct(private readonly MenuVariantService $variants) {}

    public function index(Request $request)
    {
        $categories = Category::where('is_active', true)->orderBy('order', 'asc')->get();

        $query = Menu::with(['category', 'variants']);

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

        $menu = Menu::create([
            'name' => $request->input('name'),
            'category_id' => $request->input('category_id'),
            'price' => $request->input('price'),
            'description' => $request->input('description'),
            'image' => $request->input('image') ?: 'https://images.unsplash.com/photo-1509042239860-f550ce710b93?w=500&auto=format&fit=crop&q=80',
            'is_available' => $request->boolean('is_available', true),
            'is_best_seller' => $request->boolean('is_best_seller', false),
            'sold_count' => 0,
        ]);

        $this->variants->sync($menu, $this->variantPayload($request));

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

        $menu->update([
            'name' => $request->input('name'),
            'category_id' => $request->input('category_id'),
            'price' => $request->input('price'),
            'description' => $request->input('description'),
            'image' => $request->input('image') ?: $menu->image,
            'is_available' => $request->boolean('is_available'),
            'is_best_seller' => $request->boolean('is_best_seller'),
        ]);

        if ($request->has('variants') || $request->boolean('variants_present')) {
            $this->variants->sync($menu, $this->variantPayload($request));
        }

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
            'category_id' => 'required|exists:kategori,id',
            'price' => 'required|numeric|min:0',
            'description' => 'nullable|string',
            'image' => 'nullable|string',
            'variants' => 'nullable|array',
            'variants.*.id' => 'nullable|integer|exists:menu_varian,id',
            'variants.*.name' => 'nullable|string|max:100',
            'variants.*.size' => 'nullable|string|max:50',
            'variants.*.price' => 'nullable|numeric|min:0',
            'variants.*.is_available' => 'nullable|boolean',
            'variants.*.sort_order' => 'nullable|integer|min:0',
        ]);
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function variantPayload(Request $request): array
    {
        if (! $request->has('variants')) {
            return [];
        }

        return collect($request->input('variants', []))
            ->filter(fn ($variant) => filled($variant['name'] ?? null))
            ->values()
            ->all();
    }
}
