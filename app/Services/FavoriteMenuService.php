<?php

namespace App\Services;

use App\Models\Menu;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class FavoriteMenuService
{
    /**
     * Ambil menu teratas yang paling sering dipesan (penjualan terbanyak).
     * Diperbarui per hari melalui cache yang kedaluwarsa pada akhir hari (endOfDay).
     * Tidak menggunakan input manual (is_best_seller) sama sekali.
     *
     * @param  int  $limit  Jumlah menu yang ingin diambil (bawaan: 3)
     * @return Collection<int, Menu>
     */
    public static function getDailyTopFavorites(int $limit = 3): Collection
    {
        $cacheKey = 'daily_top_favorites_'.Carbon::today()->format('Y-m-d').'_'.$limit;

        return Cache::remember($cacheKey, Carbon::now()->endOfDay(), function () use ($limit) {
            return self::calculateTopFavorites($limit);
        });
    }

    /**
     * Hitung menu terlaris secara dinamis berdasarkan data penjualan pesanan lunas.
     *
     * @return Collection<int, Menu>
     */
    public static function calculateTopFavorites(int $limit = 3): Collection
    {
        // 1. Ambil agregat penjualan terbanyak dari pesanan_item pada pesanan yang sudah lunas
        $topSoldMenuIds = DB::table('pesanan_item')
            ->join('pesanan', 'pesanan.id', '=', 'pesanan_item.order_id')
            ->join('menu', 'menu.id', '=', 'pesanan_item.menu_id')
            ->where('pesanan.payment_status', 'lunas')
            ->whereNull('menu.deleted_at')
            ->where('menu.is_available', true)
            ->whereNull('menu.merged_into_menu_id')
            ->select('pesanan_item.menu_id', DB::raw('SUM(pesanan_item.quantity) as total_sold'))
            ->groupBy('pesanan_item.menu_id')
            ->orderByDesc('total_sold')
            ->limit($limit)
            ->pluck('menu_id')
            ->all();

        $menus = collect();

        if (! empty($topSoldMenuIds)) {
            $fetched = Menu::with('category')
                ->whereIn('id', $topSoldMenuIds)
                ->whereNull('deleted_at')
                ->where('is_available', true)
                ->get()
                ->sortBy(fn (Menu $m) => array_search($m->id, $topSoldMenuIds))
                ->values();

            $menus = $menus->merge($fetched);
        }

        // 2. Jika transaksi pesanan belum mencapai limit (misal saat data awal),
        // isi sisa slot berdasarkan sold_count menu yang aktif, lalu urutan ID
        if ($menus->count() < $limit) {
            $existingIds = $menus->pluck('id')->all();
            $needed = $limit - $menus->count();

            $fallback = Menu::with('category')
                ->whereNull('deleted_at')
                ->where('is_available', true)
                ->whereNull('merged_into_menu_id')
                ->when(! empty($existingIds), fn ($q) => $q->whereNotIn('id', $existingIds))
                ->orderByDesc('sold_count')
                ->orderBy('id', 'asc')
                ->limit($needed)
                ->get();

            $menus = $menus->merge($fallback);
        }

        return $menus->take($limit);
    }
}
