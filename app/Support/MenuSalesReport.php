<?php

namespace App\Support;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class MenuSalesReport
{
    /**
     * Ringkas penjualan per menu induk beserta rincian variannya.
     *
     * @return Collection<int, array{name: string, quantity: int, revenue: float, variants: array<int, array{name: string, quantity: int, revenue: float}>}>
     */
    public static function forPeriod(PeriodFilter $period): Collection
    {
        return DB::table('pesanan_item')
            ->join('pesanan', 'pesanan.id', '=', 'pesanan_item.order_id')
            ->leftJoin('menu', 'menu.id', '=', 'pesanan_item.menu_id')
            ->leftJoin('menu_varian', 'menu_varian.id', '=', 'pesanan_item.variant_id')
            ->where('pesanan.payment_status', 'lunas')
            ->whereBetween('pesanan.created_at', [$period->start, $period->end])
            ->select(
                'pesanan_item.menu_id',
                'pesanan_item.name as item_name',
                'pesanan_item.variant_id',
                'menu.name as menu_name',
                'menu_varian.name as variant_name',
                'menu_varian.size as variant_size',
                DB::raw('SUM(pesanan_item.quantity) as quantity'),
                DB::raw('SUM(pesanan_item.subtotal) as revenue')
            )
            ->groupBy(
                'pesanan_item.menu_id',
                'pesanan_item.name',
                'pesanan_item.variant_id',
                'menu.name',
                'menu_varian.name',
                'menu_varian.size'
            )
            ->get()
            ->groupBy(fn ($row) => $row->menu_id ? 'menu-'.$row->menu_id : 'name-'.$row->menu_name)
            ->map(function (Collection $rows) {
                $first = $rows->first();
                $variants = $rows
                    ->filter(fn ($row) => $row->variant_id || $row->variant_name)
                    ->groupBy(fn ($row) => ($row->variant_name ?: 'Varian').'|'.($row->variant_size ?: ''))
                    ->map(function (Collection $variantRows) {
                        $variant = $variantRows->first();
                        $name = $variant->variant_name ?: 'Varian';

                        if ($variant->variant_size) {
                            $name .= ' - '.$variant->variant_size;
                        }

                        return [
                            'name' => $name,
                            'quantity' => (int) $variantRows->sum('quantity'),
                            'revenue' => (float) $variantRows->sum('revenue'),
                        ];
                    })
                    ->sortByDesc('revenue')
                    ->values()
                    ->all();

                return [
                    'name' => $first->menu_name,
                    'quantity' => (int) $rows->sum('quantity'),
                    'revenue' => (float) $rows->sum('revenue'),
                    'variants' => $variants,
                ];
            })
            ->sortByDesc('revenue')
            ->values();
    }
}
