<?php

namespace App\Support;

use App\Models\Order;
use App\Models\Promo;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

class MobileNavigation
{
    /**
     * @return array{
     *     items: array<int, array<string, mixed>>,
     *     hidden: bool
     * }
     */
    public static function build(string $role): array
    {
        $user = Auth::user();

        if (! $user || $user->primaryRole() !== $role) {
            return ['items' => [], 'hidden' => false];
        }

        $configuration = config("mobile_navigation.roles.{$role}", []);
        $configuredItems = $configuration['items'] ?? [];

        if ($role === 'admin' && config('features.legacy_admin')) {
            $legacyItems = $configuration['legacy_more_items'] ?? [];

            foreach ($configuredItems as $index => $item) {
                if (($item['type'] ?? 'link') !== 'more') {
                    continue;
                }

                $configuredItems[$index]['children'] = array_merge(
                    $item['children'] ?? [],
                    $legacyItems,
                );
            }
        }

        $items = [];

        foreach ($configuredItems as $item) {
            $resolved = self::resolveItem($item);

            if ($resolved !== null) {
                $items[] = $resolved;
            }
        }

        return [
            'items' => $items,
            'hidden' => self::isHiddenOnCurrentScreen(),
        ];
    }

    /**
     * @param  array<string, mixed>  $item
     * @return array<string, mixed>|null
     */
    private static function resolveItem(array $item): ?array
    {
        $type = $item['type'] ?? 'link';

        if ($type === 'more') {
            $children = [];

            foreach ($item['children'] ?? [] as $child) {
                if (($child['legacy'] ?? false) && ! config('features.legacy_admin')) {
                    continue;
                }

                $resolvedChild = self::resolveItem($child);

                if ($resolvedChild !== null) {
                    $children[] = $resolvedChild;
                }
            }

            if ($children === []) {
                return null;
            }

            return [
                'key' => $item['key'] ?? 'more',
                'label' => $item['label'],
                'icon' => $item['icon'],
                'type' => 'more',
                'badge' => self::resolveBadge($item['badge_key'] ?? null),
                'active' => collect($children)->contains(fn (array $child) => $child['active']),
                'children' => $children,
            ];
        }

        $routeName = $item['route'] ?? null;

        if (! is_string($routeName) || ! Route::has($routeName)) {
            return null;
        }

        return [
            'key' => $item['key'] ?? $routeName,
            'label' => $item['label'],
            'icon' => $item['icon'],
            'type' => $type,
            'url' => route($routeName, $item['route_params'] ?? []),
            'badge' => self::resolveBadge($item['badge_key'] ?? null),
            'active' => self::isActive($item['active'] ?? []),
        ];
    }

    /**
     * @param  array<int, string>  $patterns
     */
    private static function isActive(array $patterns): bool
    {
        foreach ($patterns as $pattern) {
            if (request()->routeIs($pattern)) {
                return true;
            }
        }

        return false;
    }

    private static function resolveBadge(?string $key): ?int
    {
        if ($key === null) {
            return null;
        }

        $count = match ($key) {
            'owner_active_promos' => Promo::query()
                ->where('is_active', true)
                ->whereDate('start_date', '<=', now())
                ->whereDate('end_date', '>=', now())
                ->count(),
            'kasir_new_orders' => Order::query()
                ->where('source', 'web')
                ->where('order_status', 'baru')
                ->count(),
            'kasir_unpaid_orders' => Order::query()
                ->where('payment_status', 'menunggu_pembayaran')
                ->where('order_status', '!=', 'selesai')
                ->count(),
            default => 0,
        };

        return $count > 0 ? $count : null;
    }

    private static function isHiddenOnCurrentScreen(): bool
    {
        foreach (config('mobile_navigation.hidden_on', []) as $pattern) {
            if (request()->routeIs($pattern)) {
                return true;
            }
        }

        return false;
    }

    public static function backUrlForCurrentRoute(): ?string
    {
        foreach (config('mobile_navigation.back_routes', []) as $pattern => $routeName) {
            if (! request()->routeIs($pattern) || ! Route::has($routeName)) {
                continue;
            }

            return route($routeName);
        }

        return null;
    }
}
