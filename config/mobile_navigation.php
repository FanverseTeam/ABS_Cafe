<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Mobile Bottom Navigation
    |--------------------------------------------------------------------------
    |
    | One source of truth for the mobile bottom navigation of every role.
    | Routes are resolved defensively, so an item is never rendered when its
    | route is unavailable or the signed-in user does not own the role.
    |
    */

    'roles' => [
        'admin' => [
            'items' => [
                [
                    'key' => 'dashboard',
                    'label' => 'Beranda',
                    'route' => 'admin.dashboard',
                    'icon' => 'layout-dashboard',
                    'active' => ['admin.dashboard'],
                ],
                [
                    'key' => 'catalog',
                    'label' => 'Menu',
                    'route' => 'admin.catalog.index',
                    'icon' => 'coffee',
                    'active' => ['admin.catalog.*'],
                ],
                [
                    'key' => 'staff',
                    'label' => 'Staf',
                    'route' => 'admin.staff.index',
                    'icon' => 'users',
                    'active' => ['admin.staff.*'],
                ],
                [
                    'key' => 'promos',
                    'label' => 'Promo',
                    'route' => 'admin.vouchers.index',
                    'icon' => 'tag',
                    'active' => ['admin.vouchers.*'],
                ],
                [
                    'key' => 'more',
                    'label' => 'Lainnya',
                    'type' => 'more',
                    'icon' => 'menu',
                    'children' => [
                        [
                            'label' => 'Meja & Metode Pembayaran',
                            'route' => 'admin.operations.index',
                            'icon' => 'grid-3x3',
                            'active' => ['admin.operations.*'],
                        ],
                        [
                            'label' => 'Pengaturan Website',
                            'route' => 'admin.website.index',
                            'icon' => 'settings',
                            'active' => ['admin.website.*'],
                        ],
                        [
                            'label' => 'Log Aktivitas',
                            'route' => 'admin.activity.index',
                            'icon' => 'history',
                            'active' => ['admin.activity.*'],
                        ],
                    ],
                ],
            ],
            'legacy_more_items' => [
                [
                    'label' => 'Pesanan',
                    'route' => 'admin.orders.index',
                    'icon' => 'shopping-bag',
                    'active' => ['admin.orders.*'],
                    'legacy' => true,
                ],
                [
                    'label' => 'Menu',
                    'route' => 'admin.menu.index',
                    'icon' => 'coffee',
                    'active' => ['admin.menu.*'],
                    'legacy' => true,
                ],
                [
                    'label' => 'Kategori',
                    'route' => 'admin.category.index',
                    'icon' => 'tags',
                    'active' => ['admin.category.*'],
                    'legacy' => true,
                ],
                [
                    'label' => 'Stok',
                    'route' => 'admin.stock.index',
                    'icon' => 'package',
                    'active' => ['admin.stock.*'],
                    'legacy' => true,
                ],
                [
                    'label' => 'Promo',
                    'route' => 'admin.promo.index',
                    'icon' => 'percent',
                    'active' => ['admin.promo.*'],
                    'legacy' => true,
                ],
                [
                    'label' => 'Pengguna',
                    'route' => 'admin.users.index',
                    'icon' => 'users-round',
                    'active' => ['admin.users.*'],
                    'legacy' => true,
                ],
                [
                    'label' => 'Meja',
                    'route' => 'admin.tables.index',
                    'icon' => 'armchair',
                    'active' => ['admin.tables.*'],
                    'legacy' => true,
                ],
                [
                    'label' => 'Pengaturan',
                    'route' => 'admin.settings.index',
                    'icon' => 'settings-2',
                    'active' => ['admin.settings.*'],
                    'legacy' => true,
                ],
                [
                    'label' => 'Log Aktivitas',
                    'route' => 'admin.logs.index',
                    'icon' => 'history',
                    'active' => ['admin.logs.*'],
                    'legacy' => true,
                ],
            ],
        ],

        'owner' => [
            'items' => [
                [
                    'key' => 'dashboard',
                    'label' => 'Beranda',
                    'route' => 'owner.dashboard',
                    'icon' => 'layout-dashboard',
                    'active' => ['owner.dashboard'],
                ],
                [
                    'key' => 'reports',
                    'label' => 'Laporan',
                    'route' => 'owner.reports.index',
                    'icon' => 'bar-chart-3',
                    'active' => ['owner.reports.*'],
                ],
                [
                    'key' => 'cashiers',
                    'label' => 'Kasir',
                    'route' => 'owner.cashiers.index',
                    'icon' => 'users',
                    'active' => ['owner.cashiers.*'],
                ],
                [
                    'key' => 'promos',
                    'label' => 'Promo',
                    'route' => 'owner.promos.index',
                    'icon' => 'tag',
                    'active' => ['owner.promos.*'],
                    'badge_key' => 'owner_active_promos',
                ],
                [
                    'key' => 'more',
                    'label' => 'Lainnya',
                    'type' => 'more',
                    'icon' => 'menu',
                    'children' => [
                        [
                            'label' => 'Pengaturan Bisnis',
                            'route' => 'owner.settings.index',
                            'icon' => 'briefcase',
                            'active' => ['owner.settings.*'],
                        ],
                    ],
                ],
            ],
        ],

        'kasir' => [
            'items' => [
                [
                    'key' => 'dashboard',
                    'label' => 'Beranda',
                    'route' => 'kasir.dashboard',
                    'icon' => 'layout-dashboard',
                    'active' => ['kasir.dashboard'],
                ],
                [
                    'key' => 'pos',
                    'label' => 'Menu',
                    'route' => 'kasir.pos.index',
                    'icon' => 'calculator',
                    'active' => ['kasir.pos.*'],
                ],
                [
                    'key' => 'orders',
                    'label' => 'Pesanan',
                    'route' => 'kasir.orders.index',
                    'icon' => 'clipboard-list',
                    'active' => ['kasir.orders.*'],
                    'badge_key' => 'kasir_new_orders',
                ],
                [
                    'key' => 'reservations',
                    'label' => 'Meja',
                    'route' => 'kasir.reservations.index',
                    'icon' => 'armchair',
                    'active' => ['kasir.reservations.*'],
                ],
                [
                    'key' => 'more',
                    'label' => 'Lainnya',
                    'type' => 'more',
                    'icon' => 'menu',
                    'badge_key' => 'kasir_unpaid_orders',
                    'children' => [
                        [
                            'label' => 'Pembayaran & Struk',
                            'route' => 'kasir.payments.index',
                            'icon' => 'receipt',
                            'active' => ['kasir.payments.*'],
                        ],
                        [
                            'label' => 'Buka/Tutup Shift & Rekap Kas',
                            'route' => 'kasir.shift.index',
                            'icon' => 'wallet',
                            'active' => ['kasir.shift.*'],
                        ],
                    ],
                ],
            ],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Full-screen screens
    |--------------------------------------------------------------------------
    |
    | Receipt and print screens should not show the persistent bottom nav.
    |
    */
    'hidden_on' => [
        'admin.vouchers.create',
        'admin.vouchers.edit',
        'owner.promos.create',
        'owner.promos.edit',
        'kasir.payments.receipt',
        'kasir.shift.receipt',
        'owner.reports.print',
    ],

    /*
    |--------------------------------------------------------------------------
    | Detail-screen back links
    |--------------------------------------------------------------------------
    |
    | The mobile app bar shows a back button instead of the small logo on
    | detail screens. Only routes with a safe parent screen are listed here.
    |
    */
    'back_routes' => [
        'admin.vouchers.create' => 'admin.vouchers.index',
        'admin.vouchers.edit' => 'admin.vouchers.index',
        'owner.promos.create' => 'owner.promos.index',
        'owner.promos.edit' => 'owner.promos.index',
        'owner.cashiers.show' => 'owner.cashiers.index',
        'kasir.shift.show' => 'kasir.shift.index',
        'kasir.payments.checkout' => 'kasir.orders.index',
        'kasir.payments.receipt' => 'kasir.dashboard',
        'kasir.shift.receipt' => 'kasir.shift.index',
    ],

];
