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

class CatalogController extends Controller
{
    public function index(Request $request)
    {
        $activeCategories = Category::where('is_active', true)->orderBy('order', 'asc')->get();
        $categories = Category::withCount('menus')->orderBy('order', 'asc')->get();

        $menuQuery = Menu::with('category');

        if ($request->filled('search')) {
            $search = $request->input('search');
            $menuQuery->where(function ($query) use ($search) {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($request->filled('category_id') && $request->input('category_id') !== 'all') {
            $menuQuery->where('category_id', $request->input('category_id'));
        }

        match ($request->input('sort', 'terlaris')) {
            'nama' => $menuQuery->orderBy('name', 'asc'),
            'harga_asc' => $menuQuery->orderBy('price', 'asc'),
            'harga_desc' => $menuQuery->orderBy('price', 'desc'),
            default => $menuQuery->orderBy('sold_count', 'desc'),
        };

        $menus = $menuQuery->paginate(20)->withQueryString();
        $viewMode = $request->input('view', 'grid');

        return view('admin.catalog.index', compact(
            'menus',
            'categories',
            'activeCategories',
            'viewMode'
        ));
    }

    public function createMenu()
    {
        $categories = Category::where('is_active', true)->orderBy('order', 'asc')->get();
        if ($categories->isEmpty()) {
            $categories = Category::orderBy('order', 'asc')->get();
        }

        return view('admin.catalog.create', compact('categories'));
    }

    public function editMenu($id)
    {
        $menu = Menu::findOrFail($id);
        $categories = Category::where('is_active', true)->orderBy('order', 'asc')->get();
        if ($categories->isEmpty()) {
            $categories = Category::orderBy('order', 'asc')->get();
        }

        return view('admin.catalog.edit', compact('menu', 'categories'));
    }

    public function storeMenu(Request $request)
    {
        $this->validateMenu($request);

        $variantGroup = $request->filled('variant_group') ? trim($request->input('variant_group')) : null;
        $variantLabel = $request->filled('variant_label') ? trim($request->input('variant_label')) : null;

        // Auto-detect group and label from name if not provided
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

        return redirect()->route('admin.catalog.index')->with('success', "Menu {$menu->name} berhasil ditambahkan!");
    }

    public function updateMenu(Request $request, $id)
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

        return redirect()->route('admin.catalog.index')->with('success', "Perubahan menu {$menu->name} berhasil disimpan.");
    }

    public function duplicateMenu($id)
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

    public function toggleMenuAvailable($id)
    {
        $menu = Menu::findOrFail($id);
        $menu->is_available = ! $menu->is_available;
        $menu->save();

        $status = $menu->is_available ? 'tersedia' : 'habis';

        ActivityLog::create([
            'user_name' => Auth::user()->name,
            'role' => Auth::user()->role,
            'activity' => "Mengubah status ketersediaan menu {$menu->name} menjadi {$status}",
            'activity_type' => 'menu',
            'ip_address' => request()->ip(),
        ]);

        return back()->with('success', "Status menu {$menu->name} diubah menjadi {$status}.");
    }

    public function toggleMenuBestSeller($id)
    {
        $menu = Menu::findOrFail($id);
        $menu->is_best_seller = ! $menu->is_best_seller;
        $menu->save();

        $status = $menu->is_best_seller ? 'bestseller' : 'reguler';

        ActivityLog::create([
            'user_name' => Auth::user()->name,
            'role' => Auth::user()->role,
            'activity' => "Mengubah status bestseller menu {$menu->name} menjadi {$status}",
            'activity_type' => 'menu',
            'ip_address' => request()->ip(),
        ]);

        return back()->with('success', "Status bestseller menu {$menu->name} berhasil diperbarui.");
    }

    public function destroyMenu($id)
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

        return back()->with('success', "Menu {$name} telah berhasil dihapus.");
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

    public function storeCategory(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'icon' => 'nullable|string|max:50',
            'description' => 'nullable|string',
            'order' => 'nullable|integer',
            'is_active' => 'nullable|boolean',
        ]);

        $category = Category::create([
            'name' => $validated['name'],
            'icon' => $validated['icon'] ?: 'coffee',
            'description' => $validated['description'] ?? null,
            'order' => $validated['order'] ?? 0,
            'is_active' => $request->boolean('is_active', true),
        ]);

        ActivityLog::create([
            'user_name' => Auth::user()->name,
            'role' => Auth::user()->role,
            'activity' => "Menambahkan kategori baru: {$category->name}",
            'activity_type' => 'menu',
            'ip_address' => $request->ip(),
        ]);

        return back()->with('success', "Kategori {$category->name} berhasil ditambahkan!");
    }

    public function updateCategory(Request $request, $id)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'icon' => 'nullable|string|max:50',
            'description' => 'nullable|string',
            'order' => 'nullable|integer',
            'is_active' => 'nullable|boolean',
        ]);

        $category = Category::findOrFail($id);
        $category->update([
            'name' => $validated['name'],
            'icon' => $validated['icon'] ?: $category->icon,
            'description' => $validated['description'] ?? $category->description,
            'order' => $validated['order'] ?? $category->order,
            'is_active' => $request->boolean('is_active'),
        ]);

        ActivityLog::create([
            'user_name' => Auth::user()->name,
            'role' => Auth::user()->role,
            'activity' => "Memperbarui kategori: {$category->name}",
            'activity_type' => 'menu',
            'ip_address' => $request->ip(),
        ]);

        return back()->with('success', "Kategori {$category->name} berhasil diperbarui.");
    }

    public function destroyCategory($id)
    {
        $category = Category::withCount('menus')->findOrFail($id);

        if ($category->menus_count > 0) {
            return back()->with('error', "Kategori {$category->name} tidak dapat dihapus karena masih memiliki {$category->menus_count} menu terkait.");
        }

        $name = $category->name;
        $category->delete();

        ActivityLog::create([
            'user_name' => Auth::user()->name,
            'role' => Auth::user()->role,
            'activity' => "Menghapus kategori: {$name}",
            'activity_type' => 'menu',
            'ip_address' => request()->ip(),
        ]);

        return back()->with('success', "Kategori {$name} telah berhasil dihapus.");
    }
}
