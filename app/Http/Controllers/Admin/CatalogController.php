<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Category;
use App\Models\Menu;
use App\Models\Stock;
use App\Models\StockLog;
use App\Models\Supplier;
use App\Services\MenuMergeService;
use App\Services\MenuVariantService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class CatalogController extends Controller
{
    public function __construct(private readonly MenuVariantService $variants) {}

    public function index(Request $request)
    {
        $activeCategories = Category::where('is_active', true)->orderBy('order', 'asc')->get();
        $categories = Category::withCount('menus')->orderBy('order', 'asc')->get();
        $stocks = Stock::where('is_active', true)->orderBy('name')->get();

        $menuQuery = Menu::with(['category', 'variants', 'recipes.stock', 'recipes.variant']);

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

        $menus = $menuQuery->get();
        $viewMode = $request->input('view', 'grid');

        $stockQuery = Menu::with('category');

        if ($request->filled('stock_search')) {
            $stockQuery->where('name', 'like', '%'.$request->input('stock_search').'%');
        }

        if ($request->filled('stock_category_id') && $request->input('stock_category_id') !== 'all') {
            $stockQuery->where('category_id', $request->input('stock_category_id'));
        }

        $stockMenus = $stockQuery->get();

        if ($request->filled('stock_status') && $request->input('stock_status') !== 'all') {
            $stockMenus = $stockMenus->filter(
                fn (Menu $menu) => $menu->stock_status === $request->input('stock_status')
            );
        }

        $stockLogs = StockLog::with('menu')->orderBy('created_at', 'desc')->take(30)->get();

        $supplierQuery = Supplier::query();

        if ($request->filled('supplier_search')) {
            $supplierQuery->search($request->input('supplier_search'));
        }

        if ($request->filled('supplier_status') && $request->input('supplier_status') !== 'all') {
            $supplierQuery->where('is_active', $request->input('supplier_status') === 'aktif');
        }

        $suppliers = $supplierQuery->orderBy('name')->get();

        return view('admin.catalog.index', compact(
            'menus',
            'categories',
            'activeCategories',
            'stocks',
            'viewMode',
            'stockMenus',
            'stockLogs',
            'suppliers'
        ));
    }

    public function storeMenu(Request $request)
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
            'stock' => $request->input('stock', 30),
            'min_stock' => $request->input('min_stock', 10),
            'unit' => $request->input('unit', 'porsi'),
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

    public function updateMenu(Request $request, $id)
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
            'stock' => $request->input('stock', $menu->stock),
            'min_stock' => $request->input('min_stock', $menu->min_stock),
            'unit' => $request->input('unit', $menu->unit),
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

        $status = $menu->is_available ? 'Tersedia' : 'Habis';

        ActivityLog::create([
            'user_name' => Auth::user()->name,
            'role' => Auth::user()->role,
            'activity' => "Mengubah status menu {$menu->name} menjadi {$status}",
            'activity_type' => 'menu',
            'ip_address' => request()->ip(),
        ]);

        return back()->with('success', "Status {$menu->name} diubah menjadi {$status}.");
    }

    public function toggleMenuBestSeller($id)
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

    public function destroyMenu($id)
    {
        $menu = Menu::findOrFail($id);
        $name = $menu->name;
        $menu->forceDelete();

        ActivityLog::create([
            'user_name' => Auth::user()->name,
            'role' => Auth::user()->role,
            'activity' => "Menghapus menu {$name}",
            'activity_type' => 'menu',
            'ip_address' => request()->ip(),
        ]);

        return back()->with('success', "Menu {$name} telah berhasil dihapus.");
    }

    public function mergeMenus(Request $request, MenuMergeService $merge)
    {
        $data = $request->validate([
            'menu_ids' => 'required|array|min:2',
            'menu_ids.*' => 'required|integer|exists:menus,id',
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

    private function validateMenu(Request $request): void
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
            'variants' => 'nullable|array',
            'variants.*.id' => 'nullable|integer|exists:menu_variants,id',
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

    public function storeCategory(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'icon' => 'nullable|string|max:50',
            'order' => 'nullable|integer',
            'description' => 'nullable|string',
        ]);

        $maxOrder = Category::max('order') ?? 0;

        $category = Category::create([
            'name' => $request->input('name'),
            'icon' => $request->input('icon', 'coffee'),
            'order' => $request->input('order', $maxOrder + 1),
            'is_active' => $request->boolean('is_active', true),
            'description' => $request->input('description'),
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
        $request->validate([
            'name' => 'required|string|max:100',
            'icon' => 'nullable|string|max:50',
            'order' => 'nullable|integer',
            'description' => 'nullable|string',
        ]);

        $category = Category::findOrFail($id);
        $category->update([
            'name' => $request->input('name'),
            'icon' => $request->input('icon', $category->icon),
            'order' => $request->input('order', $category->order),
            'is_active' => $request->boolean('is_active'),
            'description' => $request->input('description'),
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
            return back()->with('error', "Gagal menghapus! Kategori \"{$category->name}\" masih memiliki {$category->menus_count} menu aktif di dalamnya. Pindahkan atau hapus menu tersebut terlebih dahulu.");
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

    public function adjustStock(Request $request, $id)
    {
        $request->validate([
            'type' => 'required|in:masuk,keluar,koreksi',
            'quantity' => 'required|integer',
            'notes' => 'nullable|string',
        ]);

        $menu = Menu::findOrFail($id);
        $type = $request->input('type');
        $quantity = abs((int) $request->input('quantity'));
        $notes = $request->input('notes') ?: 'Penyesuaian stok manual';
        $oldStock = $menu->stock;

        $newStock = match ($type) {
            'masuk' => $oldStock + $quantity,
            'keluar' => max(0, $oldStock - $quantity),
            'koreksi' => max(0, (int) $request->input('quantity')),
        };

        $menu->update([
            'stock' => $newStock,
            'is_available' => $newStock > 0,
        ]);

        StockLog::create([
            'menu_id' => $menu->id,
            'item_name' => $menu->name,
            'type' => $type,
            'quantity' => $quantity,
            'stock_before' => $oldStock,
            'stock_after' => $newStock,
            'notes' => $notes,
            'actor' => Auth::user()->name,
        ]);

        ActivityLog::create([
            'user_name' => Auth::user()->name,
            'role' => Auth::user()->role,
            'activity' => "Penyesuaian stok {$menu->name}: {$type} ({$oldStock} -> {$newStock} {$menu->unit})",
            'activity_type' => 'stok',
            'ip_address' => $request->ip(),
        ]);

        return back()->with('success', "Stok {$menu->name} berhasil diperbarui menjadi {$newStock} {$menu->unit}.");
    }

    public function setMinStock(Request $request, $id)
    {
        $request->validate([
            'min_stock' => 'required|integer|min:1',
        ]);

        $menu = Menu::findOrFail($id);
        $min = $request->input('min_stock');

        $menu->update(['min_stock' => $min]);

        ActivityLog::create([
            'user_name' => Auth::user()->name,
            'role' => Auth::user()->role,
            'activity' => "Mengubah batas stok minimum {$menu->name} menjadi {$min}",
            'activity_type' => 'stok',
            'ip_address' => $request->ip(),
        ]);

        return back()->with('success', "Batas minimum stok {$menu->name} berhasil diatur ke {$min}.");
    }

    public function storeSupplier(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'contact_person' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:30',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string',
            'notes' => 'nullable|string',
            'is_active' => 'nullable|boolean',
        ]);

        $supplier = Supplier::create([
            ...$validated,
            'is_active' => $request->boolean('is_active', true),
        ]);

        ActivityLog::create([
            'user_name' => Auth::user()->name,
            'role' => Auth::user()->role,
            'activity' => "Menambahkan supplier baru: {$supplier->name}",
            'activity_type' => 'stok',
            'ip_address' => $request->ip(),
        ]);

        return back()->with('success', "Supplier {$supplier->name} berhasil ditambahkan!");
    }

    public function updateSupplier(Request $request, $id)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'contact_person' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:30',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string',
            'notes' => 'nullable|string',
            'is_active' => 'nullable|boolean',
        ]);

        $supplier = Supplier::findOrFail($id);
        $supplier->update([
            ...$validated,
            'is_active' => $request->boolean('is_active'),
        ]);

        ActivityLog::create([
            'user_name' => Auth::user()->name,
            'role' => Auth::user()->role,
            'activity' => "Memperbarui supplier: {$supplier->name}",
            'activity_type' => 'stok',
            'ip_address' => $request->ip(),
        ]);

        return back()->with('success', "Supplier {$supplier->name} berhasil diperbarui.");
    }

    public function toggleSupplierStatus($id)
    {
        $supplier = Supplier::findOrFail($id);
        $supplier->is_active = ! $supplier->is_active;
        $supplier->save();

        $status = $supplier->is_active ? 'aktif' : 'nonaktif';

        ActivityLog::create([
            'user_name' => Auth::user()->name,
            'role' => Auth::user()->role,
            'activity' => "Mengubah status supplier {$supplier->name} menjadi {$status}",
            'activity_type' => 'stok',
            'ip_address' => request()->ip(),
        ]);

        return back()->with('success', "Status supplier {$supplier->name} diubah menjadi {$status}.");
    }

    public function destroySupplier($id)
    {
        $supplier = Supplier::findOrFail($id);
        $name = $supplier->name;
        $supplier->delete();

        ActivityLog::create([
            'user_name' => Auth::user()->name,
            'role' => Auth::user()->role,
            'activity' => "Menghapus supplier: {$name}",
            'activity_type' => 'stok',
            'ip_address' => request()->ip(),
        ]);

        return back()->with('success', "Supplier {$name} telah berhasil dihapus.");
    }
}
