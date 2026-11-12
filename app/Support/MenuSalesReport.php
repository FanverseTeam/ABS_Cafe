<?php

namespace App\Support;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class MenuSalesReport
{
    /**
     * Ringkas penjualan per menu induk/grup beserta rincian variannya.
     *
     * @return Collection<int, array{name: string, quantity: int, revenue: float, variants: array<int, array{name: string, quantity: int, revenue: float}>}>
     */
    public static function forPeriod(PeriodFilter $period): Collection
    {
        return DB::table('pesanan_item')
            ->join('pesanan', 'pesanan.id', '=', 'pesanan_item.order_id')
            ->leftJoin('menu', 'menu.id', '=', 'pesanan_item.menu_id')
            ->where('pesanan.payment_status', 'lunas')
            ->whereBetween('pesanan.created_at', [$period->start, $period->end])
            ->select(
                'pesanan_item.menu_id',
                'pesanan_item.name as item_name',
                'menu.name as menu_name',
                'menu.variant_group',
                'menu.variant_label',
                DB::raw('SUM(pesanan_item.quantity) as quantity'),
                DB::raw('SUM(pesanan_item.subtotal) as revenue')
            )
            ->groupBy(
                'pesanan_item.menu_id',
                'pesanan_item.name',
                'menu.name',
                'menu.variant_group',
                'menu.variant_label'
            )
            ->get()
            ->groupBy(fn ($row) => $row->variant_group ? 'group-'.$row->variant_group : ($row->menu_id ? 'menu-'.$row->menu_id : 'name-'.($row->menu_name ?: $row->item_name)))
            ->map(function (Collection $rows) {
                $first = $rows->first();
                $displayName = $first->variant_group ?: ($first->menu_name ?: $first->item_name);

                $hasVariants = $rows->contains(fn ($row) => filled($row->variant_label) || filled($row->variant_group));

                $variants = $hasVariants
                    ? $rows
                        ->groupBy(fn ($row) => $row->variant_label ?: $row->item_name)
                        ->map(function (Collection $variantRows) {
                            $variant = $variantRows->first();
                            $name = $variant->variant_label ?: $variant->item_name;

                            return [
                                'name' => $name,
                                'quantity' => (int) $variantRows->sum('quantity'),
                                'revenue' => (float) $variantRows->sum('revenue'),
                            ];
                        })
                        ->sortByDesc('revenue')
                        ->values()
                        ->all()
                    : [];

                return [
                    'name' => $displayName,
                    'quantity' => (int) $rows->sum('quantity'),
                    'revenue' => (float) $rows->sum('revenue'),
                    'variants' => $variants,
                ];
            })
            ->sortByDesc('revenue')
            ->values();
    }
}
