<?php

namespace App\Services;

use App\Models\Menu;
use App\Models\MenuVariant;
use Illuminate\Support\Facades\DB;

class MenuVariantService
{
    /**
     * Sinkronkan daftar varian dari form Admin tanpa menghapus varian yang masih dipakai.
     *
     * @param  array<int, array<string, mixed>>  $variants
     */
    public function sync(Menu $menu, array $variants): void
    {
        DB::transaction(function () use ($menu, $variants) {
            $keptIds = [];

            foreach (array_values($variants) as $index => $data) {
                $name = trim((string) ($data['name'] ?? ''));
                if ($name === '') {
                    continue;
                }

                $attributes = [
                    'name' => $name,
                    'size' => filled($data['size'] ?? null) ? trim((string) $data['size']) : null,
                    'price' => (float) ($data['price'] ?? 0),
                    'is_default' => $index === 0,
                    'is_available' => filter_var($data['is_available'] ?? true, FILTER_VALIDATE_BOOL),
                    'sort_order' => (int) ($data['sort_order'] ?? $index),
                ];

                $variantId = isset($data['id']) ? (int) $data['id'] : null;
                $variant = $variantId
                    ? $menu->variants()->whereKey($variantId)->first()
                    : null;

                if ($variant) {
                    $variant->update($attributes);
                } else {
                    $variant = $menu->variants()->create($attributes);
                }

                $keptIds[] = $variant->id;
            }

            $menu->variants()->whereNotIn('id', $keptIds)->delete();

            $prices = $menu->variants()->pluck('price');
            if ($prices->isNotEmpty()) {
                $menu->update([
                    'price' => (float) $prices->min(),
                    'is_available' => $menu->is_available && $menu->variants()->where('is_available', true)->exists(),
                ]);
            }
        });
    }

    /**
     * @return array<int, array{id: int|null, name: string, size: string|null, price: float, is_available: bool, sort_order: int}>
     */
    public function payload(Menu $menu): array
    {
        return $menu->variants()
            ->get()
            ->map(fn (MenuVariant $variant) => [
                'id' => $variant->id,
                'name' => $variant->name,
                'size' => $variant->size,
                'price' => (float) $variant->price,
                'is_available' => $variant->is_available,
                'sort_order' => $variant->sort_order,
            ])
            ->values()
            ->all();
    }
}
