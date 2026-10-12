<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Menu;
use App\Models\Category;
use App\Models\StockLog;
use App\Models\ActivityLog;

class StockController extends Controller
{
    public function index(Request $request)
    {
        $categories = Category::where('is_active', true)->get();

        $query = Menu::with('category');

        // Search
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where('name', 'like', "%{$search}%");
        }

        // Category Filter
        if ($request->filled('category_id') && $request->input('category_id') !== 'all') {
            $query->where('category_id', $request->input('category_id'));
        }

        $menus = $query->get();

        // Status Filter
        if ($request->filled('status') && $request->input('status') !== 'all') {
            $filterStatus = $request->input('status');
            $menus = $menus->filter(function ($m) use ($filterStatus) {
                return $m->stock_status === $filterStatus;
            });
        }

        $stockLogs = StockLog::with('menu')->orderBy('created_at', 'desc')->take(30)->get();

        return view('admin.stock.index', compact('menus', 'categories', 'stockLogs'));
    }

    public function adjust(Request $request, $id)
    {
        $request->validate([
            'type' => 'required|in:masuk,keluar,koreksi',
            'quantity' => 'required|integer',
            'notes' => 'nullable|string',
        ]);

        $menu = Menu::findOrFail($id);
        $type = $request->input('type');
        $qty = abs($request->input('quantity'));
        $notes = $request->input('notes') ?: 'Penyesuaian stok manual';

        $oldStock = $menu->stock;
        $newStock = $oldStock;

        if ($type === 'masuk') {
            $newStock = $oldStock + $qty;
        } elseif ($type === 'keluar') {
            $newStock = max(0, $oldStock - $qty);
        } elseif ($type === 'koreksi') {
            // koreksi replaces total or applies signed value
            $newStock = max(0, $request->input('quantity'));
        }

        $menu->update([
            'stock' => $newStock,
            'is_available' => $newStock > 0,
        ]);

        StockLog::create([
            'menu_id' => $menu->id,
            'item_name' => $menu->name,
            'type' => $type,
            'quantity' => $qty,
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
}
