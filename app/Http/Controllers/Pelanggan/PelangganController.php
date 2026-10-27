<?php

namespace App\Http\Controllers\Pelanggan;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Menu;
use App\Support\LandingData;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class PelangganController extends Controller
{
    /**
     * Halaman Beranda / Landing Customer (Publik).
     */
    public function index(): View
    {
        return view('pelanggan.beranda', [
            'landing' => LandingData::get(),
        ]);
    }

    /**
     * Halaman Menu Customer (Publik).
     */
    public function menu(Request $request): View
    {
        $searchQuery = trim((string) $request->query('q', ''));
        $categorySlug = trim((string) $request->query('kategori', ''));

        // Ambil kategori aktif yang memiliki menu (tidak termasuk menu gabungan anak)
        // Diambil langsung dari DB untuk menghindari isu serialisasi objek Eloquent pada cache file
        $categories = Category::where('is_active', true)
            ->whereHas('menus', fn ($q) => $q->whereNull('merged_into_menu_id'))
            ->orderBy('order')
            ->get();

        $baseMenuQuery = Menu::query()
            ->whereNull('merged_into_menu_id')
            ->with([
                'variants' => fn ($q) => $q->orderBy('sort_order')->orderBy('id'),
                'category',
            ]);

        $selectedCategory = null;
        $isSearching = $searchQuery !== '';
        $isCategoryFilter = $categorySlug !== '' && $categorySlug !== 'semua';

        // 1. Mode Pencarian (?q=...)
        if ($isSearching) {
            $menus = (clone $baseMenuQuery)
                ->where(function ($q) use ($searchQuery) {
                    $q->where('name', 'like', "%{$searchQuery}%")
                        ->orWhere('description', 'like', "%{$searchQuery}%");
                })
                ->orderByDesc('is_best_seller')
                ->orderBy('name')
                ->paginate(20)
                ->withQueryString();

            return view('pelanggan.menu', [
                'landing' => LandingData::get(),
                'categories' => $categories,
                'searchQuery' => $searchQuery,
                'selectedCategorySlug' => $categorySlug,
                'selectedCategory' => null,
                'menus' => $menus,
                'mode' => 'search',
            ]);
        }

        // 2. Mode Filter Kategori (?kategori=slug atau ?kategori=rekomendasi)
        if ($isCategoryFilter) {
            if ($categorySlug === 'rekomendasi') {
                $menus = (clone $baseMenuQuery)
                    ->where('is_best_seller', true)
                    ->orderByDesc('sold_count')
                    ->orderBy('name')
                    ->paginate(20)
                    ->withQueryString();

                if ($menus->isEmpty()) {
                    $menus = (clone $baseMenuQuery)
                        ->where('sold_count', '>', 0)
                        ->orderByDesc('sold_count')
                        ->paginate(20)
                        ->withQueryString();
                }

                return view('pelanggan.menu', [
                    'landing' => LandingData::get(),
                    'categories' => $categories,
                    'searchQuery' => '',
                    'selectedCategorySlug' => 'rekomendasi',
                    'selectedCategory' => null,
                    'menus' => $menus,
                    'mode' => 'rekomendasi',
                ]);
            }

            // Cari kategori berdasarkan slug atau id
            $selectedCategory = $categories->first(function ($cat) use ($categorySlug) {
                return $cat->slug === $categorySlug || (string) $cat->id === $categorySlug;
            });

            if ($selectedCategory) {
                $menus = (clone $baseMenuQuery)
                    ->where('category_id', $selectedCategory->id)
                    ->orderByDesc('is_best_seller')
                    ->orderBy('name')
                    ->paginate(20)
                    ->withQueryString();

                return view('pelanggan.menu', [
                    'landing' => LandingData::get(),
                    'categories' => $categories,
                    'searchQuery' => '',
                    'selectedCategorySlug' => $selectedCategory->slug,
                    'selectedCategory' => $selectedCategory,
                    'menus' => $menus,
                    'mode' => 'category',
                ]);
            }
        }

        // 3. Mode Tampilan Utama Semua Kategori
        // Ambil semua menu aktif sekali query, lalu kelompokkan berdasarkan kategori (tanpa N+1)
        $allMenus = (clone $baseMenuQuery)
            ->orderByDesc('is_best_seller')
            ->orderBy('name')
            ->get();

        $menusGroupedByCategory = $allMenus->groupBy('category_id');

        // Rekomendasi: Menu best seller; jika kosong, 4-6 menu terlaris dari pesanan; jika masih kosong disembunyikan
        $recommendedMenus = $allMenus->where('is_best_seller', true)->take(4)->values();
        if ($recommendedMenus->isEmpty()) {
            $recommendedMenus = $allMenus->where('sold_count', '>', 0)
                ->sortByDesc('sold_count')
                ->take(4)
                ->values();
        }

        // Siapkan kategori yang memiliki menu dengan maksimal 4 menu per bagian
        $categoriesWithMenus = $categories->filter(function ($cat) use ($menusGroupedByCategory) {
            return $menusGroupedByCategory->has($cat->id) && $menusGroupedByCategory->get($cat->id)->isNotEmpty();
        })->map(function ($cat) use ($menusGroupedByCategory) {
            $cat->preview_menus = $menusGroupedByCategory->get($cat->id)->take(4);

            return $cat;
        })->values();

        return view('pelanggan.menu', [
            'landing' => LandingData::get(),
            'categories' => $categories,
            'searchQuery' => '',
            'selectedCategorySlug' => 'semua',
            'selectedCategory' => null,
            'recommendedMenus' => $recommendedMenus,
            'categoriesWithMenus' => $categoriesWithMenus,
            'mode' => 'all',
        ]);
    }

    /**
     * Halaman Keranjang (Placeholder).
     */
    public function keranjang(): View
    {
        return view('pelanggan.placeholder', [
            'title' => 'Keranjang Belanja',
            'subtitle' => 'Halaman keranjang belanja dan pemesanan online sedang dipersiapkan dan akan segera hadir.',
            'landing' => LandingData::get(),
        ]);
    }

    /**
     * Halaman Reservasi (Placeholder).
     */
    public function reservasi(): View
    {
        return view('pelanggan.placeholder', [
            'title' => 'Reservasi Meja',
            'subtitle' => 'Layanan reservasi meja kafe secara daring sedang dipersiapkan dan akan segera hadir.',
            'landing' => LandingData::get(),
        ]);
    }
}
