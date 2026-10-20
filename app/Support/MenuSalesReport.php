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
        return DB::table('order_items')
            ->join('orders', 'orders.id', '=', 'order_items.order_id')
            ->leftJoin('menus', 'menus.id', '=', 'order_items.menu_id')
            ->leftJoin('menu_variants', 'menu_variants.id', '=', 'order_items.variant_id')
            ->where('orders.payment_status', 'lunas')
            ->whereBetween('orders.created_at', [$period->start, $period->end])
            ->select(
                'order_items.menu_id',
                'order_items.name as item_name',
                'order_items.variant_id',
                'menus.name as menu_name',
                'menu_variants.name as variant_name',
                'menu_variants.size as variant_size',
                DB::raw('SUM(order_items.quantity) as quantity'),
                DB::raw('SUM(order_items.subtotal) as revenue')
            )
            ->groupBy(
                'order_items.menu_id',
                'order_items.name',
                'order_items.variant_id',
                'menus.name',
                'menu_variants.name',
                'menu_variants.size'
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
