<?php

namespace App\Services;

use App\Models\ActivityLog;
use App\Models\Favorite;
use App\Models\Menu;
use App\Models\MenuVariant;
use App\Models\OrderItem;
use App\Models\Promo;
use App\Models\Recipe;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class MenuMergeService
{
    private const VARIANTS = [
        'hot' => 'Hot',
        'panas' => 'Hot',
        'ice' => 'Ice',
        'dingin' => 'Ice',
    ];

    /**
     * Analisis pasangan menu tanpa mengubah data.
     *
     * @return array{
     *     groups: array<int, array<string, mixed>>,
     *     ambiguous: array<int, array<string, mixed>>,
     *     category_suggestions: array<int, array<string, mixed>>
     * }
     */
    public function analyze(): array
    {
        $menus = Menu::with(['category', 'variants'])->orderBy('category_id')->orderBy('id')->get();

        $groups = [];
        $baseOnly = [];
        $ambiguous = [];

        foreach ($menus as $menu) {
            $parsed = $this->parseVariantName($menu->name);
            $categoryKey = (string) $menu->category_id;

            if (! $parsed) {
                $baseOnly[$categoryKey.'|'.$this->normalizeBase($menu->name)][] = $menu;

                continue;
            }

            if ($this->parseVariantName($parsed['base'])) {
                $ambiguous[] = $this->ambiguousRow($menu, 'Nama memiliki lebih dari satu penanda varian.');

                continue;
            }

            $key = $categoryKey.'|'.$this->normalizeBase($parsed['base']);

            if (! isset($groups[$key])) {
                $groups[$key] = [
                    'category_id' => $menu->category_id,
                    'category_name' => $menu->category?->name ?? 'Tanpa Kategori',
                    'base' => $parsed['base'],
                    'menus' => [],
                ];
            }

            $groups[$key]['menus'][] = [
                'id' => $menu->id,
                'name' => $menu->name,
                'variant' => $parsed['variant'],
                'price' => (float) $menu->price,
                'is_available' => (bool) $menu->is_available,
            ];
        }

        $accepted = [];

        foreach ($groups as $key => $group) {
            if (! empty($baseOnly[$key])) {
                $baseOnlyMenus = collect($baseOnly[$key])->map(fn (Menu $menu) => [
                    'id' => $menu->id,
                    'name' => $menu->name,
                    'variant' => '-',
                    'price' => (float) $menu->price,
                    'is_available' => (bool) $menu->is_available,
                ])->all();

                $ambiguous[] = [
                    'category_name' => $group['category_name'],
                    'base' => $group['base'],
                    'reason' => 'Ada menu tanpa penanda varian dengan nama dasar yang sama.',
                    'menus' => array_merge($group['menus'], $baseOnlyMenus),
                ];

                continue;
            }

            if (count($group['menus']) < 2) {
                continue;
            }

            $labels = collect($group['menus'])->pluck('variant');

            if ($labels->unique()->count() !== $labels->count()) {
                $ambiguous[] = [
                    'category_name' => $group['category_name'],
                    'base' => $group['base'],
                    'reason' => 'Ada lebih dari satu menu dengan varian yang sama.',
                    'menus' => $group['menus'],
                ];

                continue;
            }

            $labels = $this->sortLabels($labels->all());
            $group['variant_labels'] = $labels;
            $group['display_name'] = $this->displayName($group['base'], $labels);
            $accepted[] = $group;
        }

        return [
            'groups' => $accepted,
            'ambiguous' => $ambiguous,
            'category_suggestions' => $this->categorySuggestions(),
        ];
    }

    /**
     * Gabungkan semua grup yang lolos aturan deteksi ketat.
     *
     * @return array<int, Menu>
     */
    public function mergeAllDetected(?User $actor = null): array
    {
        $merged = [];

        foreach ($this->analyze()['groups'] as $group) {
            $ids = collect($group['menus'])->pluck('id')->all();
            $merged[] = $this->mergeMenus($ids, $actor);
        }

        return $merged;
    }

    /**
     * Gabungkan 2+ menu menjadi satu menu induk dengan varian.
     *
     * @param  array<int, int>  $menuIds
     */
    public function mergeMenus(array $menuIds, ?User $actor = null): Menu
    {
        $menuIds = array_values(array_unique(array_map('intval', $menuIds)));

        if (count($menuIds) < 2) {
            throw ValidationException::withMessages(['menus' => 'Pilih minimal dua menu untuk digabungkan.']);
        }

        return DB::transaction(function () use ($menuIds, $actor) {
            $menus = Menu::with(['variants', 'recipes'])
                ->whereIn('id', $menuIds)
                ->get()
                ->keyBy('id');

            if ($menus->count() !== count($menuIds)) {
                throw ValidationException::withMessages(['menus' => 'Sebagian menu tidak ditemukan atau sudah diarsipkan.']);
            }

            $ordered = collect($menuIds)->map(fn ($id) => $menus->get($id));
            $categoryIds = $ordered->pluck('category_id')->unique();

            if ($categoryIds->count() !== 1) {
                throw ValidationException::withMessages(['menus' => 'Menu yang digabungkan harus berada di kategori yang sama.']);
            }

            $parsed = [];
            $normalizedBases = [];

            foreach ($ordered as $menu) {
                $result = $this->parseVariantName($menu->name);

                if (! $result) {
                    throw ValidationException::withMessages([
                        'menus' => "Menu \"{$menu->name}\" tidak memiliki penanda varian Hot/Ice/Panas/Dingin yang jelas.",
                    ]);
                }

                $parsed[$menu->id] = $result;
                $normalizedBases[] = $this->normalizeBase($result['base']);
            }

            if (count(array_unique($normalizedBases)) !== 1) {
                throw ValidationException::withMessages([
                    'menus' => 'Nama dasar menu berbeda. Menu hanya bisa digabung bila nama dasarnya sama.',
                ]);
            }

            $labels = collect($parsed)->pluck('variant');
            if ($labels->unique()->count() !== $labels->count()) {
                throw ValidationException::withMessages([
                    'menus' => 'Setiap menu harus mewakili varian yang berbeda.',
                ]);
            }

            $baseName = $parsed[$ordered->first()->id]['base'];
            $labels = $this->sortLabels($labels->all());
            $first = $ordered->first();

            $parent = Menu::create([
                'category_id' => $first->category_id,
                'name' => $this->displayName($baseName, $labels),
                'description' => $ordered->pluck('description')->filter()->first(),
                'image' => $ordered->pluck('image')->filter()->first(),
                'price' => (float) $ordered->min('price'),
                'is_available' => $ordered->contains(fn (Menu $menu) => $menu->is_available),
                'is_best_seller' => $ordered->contains(fn (Menu $menu) => $menu->is_best_seller),
                'stock' => (int) $ordered->sum('stock'),
                'min_stock' => (int) $ordered->sum('min_stock'),
                'unit' => $first->unit ?: 'porsi',
                'sold_count' => (int) $ordered->sum('sold_count'),
            ]);

            $sourceMap = [];
            $sortOrder = 0;

            foreach ($ordered as $menu) {
                $label = $parsed[$menu->id]['variant'];
                $sourceRows = [];

                $sourceVariants = $menu->variants->sortBy('sort_order')->values();

                if ($sourceVariants->isEmpty()) {
                    $newVariant = $parent->variants()->create([
                        'name' => $label,
                        'size' => null,
                        'price' => (float) $menu->price,
                        'is_default' => $sortOrder === 0,
                        'is_available' => (bool) $menu->is_available,
                        'sort_order' => $sortOrder++,
                    ]);

                    $sourceRows[] = [
                        'source_variant_id' => null,
                        'new_variant' => $newVariant,
                    ];
                } else {
                    foreach ($sourceVariants as $sourceVariant) {
                        $variantName = $label;
                        $size = $sourceVariant->size;

                        $canonical = $this->canonicalVariant($sourceVariant->name);
                        if ($canonical) {
                            $variantName = $canonical;
                        } elseif (! $size && $sourceVariants->count() === 1) {
                            $size = $sourceVariant->name;
                        }

                        $newVariant = $parent->variants()->create([
                            'name' => $variantName,
                            'size' => $size ?: null,
                            'price' => (float) $sourceVariant->price,
                            'is_default' => $sortOrder === 0,
                            'is_available' => (bool) $sourceVariant->is_available,
                            'sort_order' => $sortOrder++,
                        ]);

                        $sourceRows[] = [
                            'source_variant_id' => $sourceVariant->id,
                            'new_variant' => $newVariant,
                        ];
                    }
                }

                $variantIdMap = [];
                foreach ($sourceRows as $row) {
                    if ($row['source_variant_id'] !== null) {
                        $variantIdMap[$row['source_variant_id']] = $row['new_variant']->id;
                    }
                }

                $this->moveRecipes($menu, $parent, $sourceRows);

                $sourceMap[$menu->id] = [
                    'rows' => $sourceRows,
                    'variant_map' => $variantIdMap,
                    'default_variant_id' => $sourceRows[0]['new_variant']->id,
                ];

                $this->moveOrderItems($menu, $parent, $sourceRows, $variantIdMap);
            }

            $parent->update(['price' => (float) $parent->variants()->min('price')]);

            $this->moveFavorites($menuIds, $parent);
            Promo::whereIn('target_menu_id', $menuIds)->update(['target_menu_id' => $parent->id]);

            foreach ($ordered as $menu) {
                $menu->forceFill([
                    'merged_into_menu_id' => $parent->id,
                    'variant_id' => $sourceMap[$menu->id]['default_variant_id'],
                ])->save();

                $menu->delete();
            }

            $this->log($actor, "Menggabungkan menu {$ordered->pluck('name')->implode(', ')} menjadi {$parent->name}");

            return $parent->fresh(['variants', 'recipes']);
        });
    }

    /**
     * Batalkan penggabungan dan kembalikan menu lama.
     */
    public function rollback(Menu $parent, ?User $actor = null): int
    {
        return DB::transaction(function () use ($parent, $actor) {
            $parent = Menu::withTrashed()->with('variants')->findOrFail($parent->id);
            $archived = Menu::withTrashed()
                ->with('variants')
                ->where('merged_into_menu_id', $parent->id)
                ->orderBy('id')
                ->get();

            if ($archived->isEmpty()) {
                return 0;
            }

            $firstArchived = $archived->first();

            foreach ($archived as $oldMenu) {
                $newVariant = $parent->variants->firstWhere('id', $oldMenu->variant_id);
                $oldVariant = $oldMenu->variants->first();

                if ($newVariant && $oldMenu->variants->isNotEmpty()) {
                    $oldVariant = $oldMenu->variants->first(
                        fn (MenuVariant $variant) => $variant->name === $newVariant->name
                            && (string) $variant->size === (string) $newVariant->size
                    ) ?: $oldVariant;
                }

                OrderItem::where('menu_id', $parent->id)
                    ->when($newVariant, fn ($query) => $query->where('variant_id', $newVariant->id))
                    ->update([
                        'menu_id' => $oldMenu->id,
                        'variant_id' => $oldVariant?->id,
                        'name' => $oldMenu->name,
                    ]);

                if ($newVariant) {
                    Recipe::where('menu_id', $parent->id)
                        ->where('menu_variant_id', $newVariant->id)
                        ->update([
                            'menu_id' => $oldMenu->id,
                            'menu_variant_id' => $oldVariant?->id,
                        ]);
                }

                $oldMenu->forceFill([
                    'merged_into_menu_id' => null,
                    'variant_id' => null,
                    'deleted_at' => null,
                ])->save();
            }

            $this->moveFavoritesBack($parent, $firstArchived);
            Promo::where('target_menu_id', $parent->id)->update(['target_menu_id' => $firstArchived->id]);

            MenuVariant::where('menu_id', $parent->id)->delete();
            $parent->forceFill(['deleted_at' => now()])->save();

            $this->log($actor, "Membatalkan penggabungan menu {$parent->name}");

            return $archived->count();
        });
    }

    /**
     * @return array{base: string, variant: string, token: string}|null
     */
    public function parseVariantName(string $name): ?array
    {
        $original = trim((string) preg_replace('/\s+/', ' ', $name));

        $patterns = [
            '/\s*[\(\[]\s*(hot|ice|panas|dingin)\s*[\)\]]\s*$/i',
            '/\s*[-–—\/]\s*(hot|ice|panas|dingin)\s*$/i',
            '/\s+(hot|ice|panas|dingin)\s*$/i',
            '/^\s*[\(\[]\s*(hot|ice|panas|dingin)\s*[\)\]]\s*/i',
            '/^\s*(hot|ice|panas|dingin)\s*[-–—\/:]?\s+/i',
        ];

        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $original, $matches)) {
                $base = trim((string) preg_replace($pattern, '', $original));
                $base = trim($base, " \t\n\r\0\x0B-–—/()[]");

                if ($base === '') {
                    return null;
                }

                $token = strtolower($matches[1]);

                return [
                    'base' => $base,
                    'variant' => self::VARIANTS[$token],
                    'token' => $token,
                ];
            }
        }

        return null;
    }

    public function canonicalVariant(string $name): ?string
    {
        return self::VARIANTS[strtolower(trim($name))] ?? null;
    }

    public function normalizeBase(string $name): string
    {
        return Str::of($name)
            ->lower()
            ->replaceMatches('/[^a-z0-9]+/', ' ')
            ->squish()
            ->toString();
    }

    public function displayName(string $base, array $labels): string
    {
        return trim($base).' ('.implode(' / ', $labels).')';
    }

    public function snapshotName(Menu $menu, MenuVariant $variant): string
    {
        return $menu->variantBaseName().' - '.$variant->name.($variant->size ? ' - '.$variant->size : '');
    }

    /**
     * @param  array<int, string>  $labels
     * @return array<int, string>
     */
    private function sortLabels(array $labels): array
    {
        $priority = ['Hot' => 0, 'Ice' => 1];

        usort($labels, fn ($a, $b) => ($priority[$a] ?? 99) <=> ($priority[$b] ?? 99) ?: strcmp($a, $b));

        return array_values(array_unique($labels));
    }

    /**
     * @param  Collection<int, Menu>  $menus
     * @return array<int, array<string, mixed>>
     */
    private function categorySuggestions(): array
    {
        $groups = [];

        foreach (Menu::with('category')->whereHas('category')->get() as $menu) {
            $categoryName = $menu->category?->name;
            if (! $categoryName) {
                continue;
            }

            $parsed = $this->parseVariantName($categoryName);
            if (! $parsed) {
                continue;
            }

            $key = $this->normalizeBase($parsed['base']);
            $groups[$key]['base'] = $parsed['base'];
            $groups[$key]['categories'][$menu->category_id] = $categoryName;
            $groups[$key]['variants'][$menu->category_id] = $parsed['variant'];
        }

        return collect($groups)
            ->filter(fn ($group) => count($group['categories']) >= 2 && count(array_unique($group['variants'])) >= 2)
            ->map(fn ($group) => [
                'base' => $group['base'],
                'categories' => array_values($group['categories']),
                'suggested_name' => $this->displayName($group['base'], $this->sortLabels(array_values($group['variants']))),
            ])
            ->values()
            ->all();
    }

    /**
     * @return array<string, mixed>
     */
    private function ambiguousRow(Menu $menu, string $reason): array
    {
        return [
            'category_name' => $menu->category?->name ?? 'Tanpa Kategori',
            'base' => $menu->name,
            'reason' => $reason,
            'menus' => [[
                'id' => $menu->id,
                'name' => $menu->name,
                'variant' => '-',
                'price' => (float) $menu->price,
                'is_available' => (bool) $menu->is_available,
            ]],
        ];
    }

    /**
     * @param  array<int, array{source_variant_id: int|null, new_variant: MenuVariant}>  $sourceRows
     */
    private function moveRecipes(Menu $menu, Menu $parent, array $sourceRows): void
    {
        $recipes = $menu->recipes;

        foreach ($sourceRows as $row) {
            $specific = $recipes->where('menu_variant_id', $row['source_variant_id']);
            $source = $specific->isNotEmpty() ? $specific : $recipes->whereNull('menu_variant_id');

            foreach ($source as $recipe) {
                Recipe::create([
                    'menu_id' => $parent->id,
                    'menu_variant_id' => $row['new_variant']->id,
                    'stock_id' => $recipe->stock_id,
                    'amount' => $recipe->amount,
                ]);
            }
        }

        Recipe::where('menu_id', $menu->id)->delete();
    }

    /**
     * @param  array<int, array{source_variant_id: int|null, new_variant: MenuVariant}>  $sourceRows
     * @param  array<int, int>  $variantIdMap
     */
    private function moveOrderItems(Menu $menu, Menu $parent, array $sourceRows, array $variantIdMap): void
    {
        $defaultVariant = $sourceRows[0]['new_variant'] ?? null;

        OrderItem::where('menu_id', $menu->id)
            ->get()
            ->each(function (OrderItem $item) use ($parent, $variantIdMap, $defaultVariant) {
                $targetId = $item->variant_id ? ($variantIdMap[$item->variant_id] ?? null) : null;
                $target = $targetId ? MenuVariant::find($targetId) : $defaultVariant;

                if (! $target) {
                    return;
                }

                $item->update([
                    'menu_id' => $parent->id,
                    'variant_id' => $target->id,
                    'name' => $this->snapshotName($parent, $target),
                ]);
            });
    }

    /**
     * @param  array<int, int>  $menuIds
     */
    private function moveFavorites(array $menuIds, Menu $parent): void
    {
        $favorites = Favorite::whereIn('menu_id', $menuIds)->get();

        foreach ($favorites as $favorite) {
            Favorite::firstOrCreate([
                'user_id' => $favorite->user_id,
                'menu_id' => $parent->id,
            ]);

            $favorite->delete();
        }
    }

    private function moveFavoritesBack(Menu $parent, Menu $oldMenu): void
    {
        foreach (Favorite::where('menu_id', $parent->id)->get() as $favorite) {
            Favorite::firstOrCreate([
                'user_id' => $favorite->user_id,
                'menu_id' => $oldMenu->id,
            ]);

            $favorite->delete();
        }
    }

    private function log(?User $actor, string $activity): void
    {
        if (! $actor) {
            return;
        }

        ActivityLog::create([
            'user_name' => $actor->name,
            'role' => $actor->primaryRole(),
            'activity' => $activity,
            'activity_type' => 'menu',
            'ip_address' => request()->ip(),
        ]);
    }
}
