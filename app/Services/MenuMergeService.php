<?php

namespace App\Services;

use App\Models\ActivityLog;
use App\Models\Favorite;
use App\Models\Menu;
use App\Models\Promo;
use App\Models\User;
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
        $menus = Menu::with(['category'])->orderBy('category_id')->orderBy('id')->get();

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
     * Gabungkan 2+ menu menjadi satu menu induk dengan variant_group.
     * Setiap menu anggota tetap punya PK sendiri; variant_group dan
     * variant_label diset untuk pengelompokan tampilan.
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
            $menus = Menu::whereIn('id', $menuIds)->get()->keyBy('id');

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
            $sortedLabels = $this->sortLabels($labels->all());

            // Set variant_group and variant_label on every member.
            // The menus keep their own PK; they are just grouped visually.
            foreach ($ordered as $menu) {
                $label = $parsed[$menu->id]['variant'];
                $menu->forceFill([
                    'variant_group' => $baseName,
                    'variant_label' => $label,
                ])->save();
            }

            $this->moveFavorites($menuIds, $ordered->first());
            Promo::whereIn('target_menu_id', $menuIds)->update(['target_menu_id' => $ordered->first()->id]);

            $this->log($actor, "Mengelompokkan menu {$ordered->pluck('name')->implode(', ')} dalam grup {$baseName}");

            return $ordered->first()->fresh();
        });
    }

    /**
     * Lepas semua menu dari grupnya (hapus variant_group dan variant_label).
     */
    public function rollback(Menu $firstMember, ?User $actor = null): int
    {
        return DB::transaction(function () use ($firstMember, $actor) {
            $group = $firstMember->variant_group;

            if (! $group) {
                return 0;
            }

            $count = Menu::withTrashed()
                ->where('variant_group', $group)
                ->update(['variant_group' => null, 'variant_label' => null]);

            $this->log($actor, "Membatalkan pengelompokan grup \"{$group}\"");

            return $count;
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
     * @param  array<int, int>  $menuIds
     */
    private function moveFavorites(array $menuIds, Menu $representative): void
    {
        $favorites = Favorite::whereIn('menu_id', $menuIds)->get();

        foreach ($favorites as $favorite) {
            Favorite::firstOrCreate([
                'user_id' => $favorite->user_id,
                'menu_id' => $representative->id,
            ]);

            if ($favorite->menu_id !== $representative->id) {
                $favorite->delete();
            }
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
