<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Category;
use App\Models\ActivityLog;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::withCount('menus')->orderBy('order', 'asc')->get();
        return view('admin.category.index', compact('categories'));
    }

    public function store(Request $request)
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

    public function update(Request $request, $id)
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

    public function destroy($id)
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
}
