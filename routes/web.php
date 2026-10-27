<?php

use App\Http\Controllers\Admin\ActivityController;
use App\Http\Controllers\Admin\ActivityLogController;
use App\Http\Controllers\Admin\CatalogController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\MenuController;
use App\Http\Controllers\Admin\OperationsController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\PromoController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\StaffController;
use App\Http\Controllers\Admin\StockController;
use App\Http\Controllers\Admin\TableController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\VoucherController;
use App\Http\Controllers\Admin\WebsiteController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Kasir\DashboardController as KasirDashboardController;
use App\Http\Controllers\Kasir\OrderController as KasirOrderController;
use App\Http\Controllers\Kasir\PaymentController as KasirPaymentController;
use App\Http\Controllers\Kasir\PosController as KasirPosController;
use App\Http\Controllers\Kasir\ReservationController as KasirReservationController;
use App\Http\Controllers\Kasir\ShiftController as KasirShiftController;
use App\Http\Controllers\Kasir\TableController as KasirTableController;
use App\Http\Controllers\Owner\CashAdjustmentController as OwnerCashAdjustmentController;
use App\Http\Controllers\Owner\CashierController as OwnerCashierController;
use App\Http\Controllers\Owner\DashboardController as OwnerDashboardController;
use App\Http\Controllers\Owner\PromoController as OwnerPromoController;
use App\Http\Controllers\Owner\ReportController as OwnerReportController;
use App\Http\Controllers\Owner\SettingController as OwnerSettingController;
use App\Http\Controllers\Pelanggan\PelangganController;
use Illuminate\Support\Facades\Route;

// Splash / Layar Animasi Selamat Datang Staf (Portal Staf ABS Cafe)
// Catatan: Untuk mengaktifkan kembali landing lama, ubah return view('staf.splash') menjadi return view('landing')
Route::middleware('noindex.staf')->group(function () {
    Route::get('/staf', function () {
        if (auth()->check()) {
            return redirect(LoginController::dashboardFor(auth()->user()));
        }

        return view('staf.splash');
    })->name('staf.landing');

    Route::get('/login', [LoginController::class, 'show'])->name('login');
    Route::post('/login', [LoginController::class, 'login'])->name('login.post')->middleware('throttle:10,1');

    Route::get('/admin/login', fn () => redirect()->route('login'))->name('admin.login');
    Route::post('/admin/login', [LoginController::class, 'login'])->name('admin.login.post')->middleware('throttle:10,1');
    Route::post('/admin/logout', [LoginController::class, 'logout'])->name('admin.logout');

    Route::get('/owner/login', fn () => redirect()->route('login'))->name('owner.login');
    Route::post('/owner/login', [LoginController::class, 'login'])->name('owner.login.post')->middleware('throttle:10,1');
    Route::post('/owner/logout', [LoginController::class, 'logout'])->name('owner.logout');

    Route::get('/kasir/login', fn () => redirect()->route('login'))->name('kasir.login');
    Route::post('/kasir/logout', [LoginController::class, 'logout'])->name('kasir.logout');
});

// Halaman Publik Customer
Route::get('/', [PelangganController::class, 'index'])->name('pelanggan.beranda');
Route::get('/menu', [PelangganController::class, 'menu'])->name('pelanggan.menu');
Route::get('/keranjang', [PelangganController::class, 'keranjang'])->name('pelanggan.keranjang');
Route::get('/reservasi', [PelangganController::class, 'reservasi'])->name('pelanggan.reservasi');

// robots.txt dilayani via route agar dapat diuji dan di-override per environment
Route::get('/robots.txt', function () {
    return response(file_get_contents(public_path('robots.txt')), 200)
        ->header('Content-Type', 'text/plain');
})->name('robots.txt');

/*
|--------------------------------------------------------------------------
| Back-Office Admin (7 active features)
|--------------------------------------------------------------------------
| Admin memiliki Dashboard + enam modul aktif. Halaman lama tetap tersedia
| di bawah sebagai grup LEGACY ADMIN yang dikendalikan FEATURE_LEGACY_ADMIN.
*/
Route::prefix('admin')->name('admin.')->middleware(['admin.auth', 'reject.emoji', 'noindex.staf'])->group(function () {
    // ACTIVE ADMIN: Dashboard tetap aktif meskipun FEATURE_LEGACY_ADMIN=false.
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    // 1. Manajemen User Staf & Hak Akses
    Route::prefix('staf')->name('staff.')->group(function () {
        Route::get('/', [StaffController::class, 'index'])->name('index');
        Route::get('/tambah', [StaffController::class, 'create'])->name('create');
        Route::post('/store', [StaffController::class, 'store'])->name('store');
        Route::get('/{id}/edit', [StaffController::class, 'edit'])->name('edit');
        Route::post('/{id}/update', [StaffController::class, 'update'])->name('update');
        Route::post('/{id}/toggle-status', [StaffController::class, 'toggleStatus'])->name('toggle.status');
        Route::post('/{id}/reset-password', [StaffController::class, 'resetPassword'])->name('reset_password');
        Route::delete('/{id}', [StaffController::class, 'destroy'])->name('destroy');
    });

    // 2. Manajemen Menu (Menu & Kategori | Stok & Supplier)
    Route::prefix('katalog')->name('catalog.')->group(function () {
        Route::get('/', [CatalogController::class, 'index'])->name('index');

        Route::prefix('menu')->name('menu.')->group(function () {
            Route::get('/create', [CatalogController::class, 'createMenu'])->name('create');
            Route::post('/store', [CatalogController::class, 'storeMenu'])->name('store');
            Route::get('/{id}/edit', [CatalogController::class, 'editMenu'])->name('edit');
            Route::post('/gabungkan', [CatalogController::class, 'mergeMenus'])->name('merge');
            Route::post('/{id}/update', [CatalogController::class, 'updateMenu'])->name('update');
            Route::post('/{id}/duplicate', [CatalogController::class, 'duplicateMenu'])->name('duplicate');
            Route::post('/{id}/toggle-available', [CatalogController::class, 'toggleMenuAvailable'])->name('toggle.available');
            Route::post('/{id}/toggle-bestseller', [CatalogController::class, 'toggleMenuBestSeller'])->name('toggle.bestseller');
            Route::get('/{id}/resep', [CatalogController::class, 'recipes'])->name('recipes');
            Route::post('/{id}/resep', [CatalogController::class, 'saveRecipes'])->name('recipes.save');
            Route::delete('/{id}', [CatalogController::class, 'destroyMenu'])->name('destroy');
        });

        Route::prefix('kategori')->name('category.')->group(function () {
            Route::post('/store', [CatalogController::class, 'storeCategory'])->name('store');
            Route::post('/{id}/update', [CatalogController::class, 'updateCategory'])->name('update');
            Route::delete('/{id}', [CatalogController::class, 'destroyCategory'])->name('destroy');
        });

        Route::prefix('stok')->name('stock.')->group(function () {
            Route::post('/{id}/adjust', [CatalogController::class, 'adjustStock'])->name('adjust');
            Route::post('/{id}/min-stock', [CatalogController::class, 'setMinStock'])->name('min_stock');
        });

        Route::prefix('supplier')->name('supplier.')->group(function () {
            Route::post('/store', [CatalogController::class, 'storeSupplier'])->name('store');
            Route::post('/{id}/update', [CatalogController::class, 'updateSupplier'])->name('update');
            Route::post('/{id}/toggle-status', [CatalogController::class, 'toggleSupplierStatus'])->name('toggle.status');
            Route::delete('/{id}', [CatalogController::class, 'destroySupplier'])->name('destroy');
        });
    });

    // 3. Meja
    Route::prefix('operasional')->name('operations.')->group(function () {
        Route::get('/', [OperationsController::class, 'index'])->name('index');

        Route::prefix('meja')->name('tables.')->group(function () {
            Route::post('/store', [OperationsController::class, 'storeTable'])->name('store');
            Route::post('/{id}/update', [OperationsController::class, 'updateTable'])->name('update');
            Route::post('/{id}/status', [OperationsController::class, 'updateTableStatus'])->name('status');
            Route::delete('/{id}', [OperationsController::class, 'destroyTable'])->name('destroy');
        });

        Route::prefix('pembayaran')->name('payments.')->group(function () {
            Route::post('/store', [OperationsController::class, 'storePaymentMethod'])->name('store');
            Route::post('/{id}/update', [OperationsController::class, 'updatePaymentMethod'])->name('update');
            Route::post('/{id}/toggle-active', [OperationsController::class, 'togglePaymentMethod'])->name('toggle.active');
            Route::delete('/{id}', [OperationsController::class, 'destroyPaymentMethod'])->name('destroy');
        });
    });

    // 4. Promo / Voucher
    Route::prefix('promo-voucher')->name('vouchers.')->group(function () {
        Route::get('/', [VoucherController::class, 'index'])->name('index');
        Route::get('/tambah', [VoucherController::class, 'create'])->name('create');
        Route::post('/store', [VoucherController::class, 'store'])->name('store');
        Route::get('/{promo}/ubah', [VoucherController::class, 'edit'])->name('edit');
        Route::post('/{id}/update', [VoucherController::class, 'update'])->name('update');
        Route::post('/{id}/toggle-active', [VoucherController::class, 'toggleActive'])->name('toggle.active');
        Route::delete('/{id}', [VoucherController::class, 'destroy'])->name('destroy');
    });

    // 5. Pengaturan Website
    Route::prefix('website')->name('website.')->group(function () {
        Route::get('/', [WebsiteController::class, 'index'])->name('index');
        Route::post('/profile', [WebsiteController::class, 'updateProfile'])->name('profile');
        Route::post('/tax', [WebsiteController::class, 'updateTax'])->name('tax');
        Route::post('/payment', [WebsiteController::class, 'updatePayment'])->name('payment');
        Route::post('/receipt', [WebsiteController::class, 'updateReceipt'])->name('receipt');
        Route::post('/display', [WebsiteController::class, 'updateDisplay'])->name('display');
        Route::post('/account', [WebsiteController::class, 'updateAccount'])->name('account');
    });

    // 6. Log Aktivitas
    Route::prefix('aktivitas')->name('activity.')->group(function () {
        Route::get('/', [ActivityController::class, 'index'])->name('index');
        Route::get('/export', [ActivityController::class, 'exportCsv'])->name('export');
    });
});

/*
|--------------------------------------------------------------------------
| LEGACY ADMIN
|--------------------------------------------------------------------------
| Old back-office pages. Disabled by default so their URLs return 404 and
| their controllers/views/queries are never reached. Set
| FEATURE_LEGACY_ADMIN=true to restore the previous admin experience.
*/
if (config('features.legacy_admin')) {
    Route::prefix('admin')->name('admin.')->middleware(['admin.auth', 'reject.emoji', 'noindex.staf'])->group(function () {
        // LEGACY ADMIN: Pesanan
        Route::prefix('pesanan')->name('orders.')->group(function () {
            Route::get('/', [OrderController::class, 'index'])->name('index');
            Route::get('/export', [OrderController::class, 'exportCsv'])->name('export');
            Route::get('/{id}', [OrderController::class, 'show'])->name('show');
            Route::post('/{id}/status', [OrderController::class, 'updateStatus'])->name('status');
            Route::post('/{id}/cancel', [OrderController::class, 'cancel'])->name('cancel');
        });

        // LEGACY ADMIN: Menu standalone
        Route::prefix('menu')->name('menu.')->group(function () {
            Route::get('/', [MenuController::class, 'index'])->name('index');
            Route::post('/store', [MenuController::class, 'store'])->name('store');
            Route::post('/gabungkan', [MenuController::class, 'mergeMenus'])->name('merge');
            Route::post('/{id}/update', [MenuController::class, 'update'])->name('update');
            Route::post('/{id}/duplicate', [MenuController::class, 'duplicate'])->name('duplicate');
            Route::post('/{id}/toggle-available', [MenuController::class, 'toggleAvailable'])->name('toggle.available');
            Route::post('/{id}/toggle-bestseller', [MenuController::class, 'toggleBestSeller'])->name('toggle.bestseller');
            Route::get('/{id}/resep', [MenuController::class, 'recipes'])->name('recipes');
            Route::post('/{id}/resep', [MenuController::class, 'saveRecipes'])->name('recipes.save');
            Route::delete('/{id}', [MenuController::class, 'destroy'])->name('destroy');
        });

        // LEGACY ADMIN: Kategori standalone
        Route::prefix('kategori')->name('category.')->group(function () {
            Route::get('/', [CategoryController::class, 'index'])->name('index');
            Route::post('/store', [CategoryController::class, 'store'])->name('store');
            Route::post('/{id}/update', [CategoryController::class, 'update'])->name('update');
            Route::delete('/{id}', [CategoryController::class, 'destroy'])->name('destroy');
        });

        // LEGACY ADMIN: Stok standalone
        Route::prefix('stok')->name('stock.')->group(function () {
            Route::get('/', [StockController::class, 'index'])->name('index');
            Route::post('/{id}/adjust', [StockController::class, 'adjust'])->name('adjust');
            Route::post('/{id}/min-stock', [StockController::class, 'setMinStock'])->name('min_stock');
        });

        // LEGACY ADMIN: Promo standalone
        Route::prefix('promo')->name('promo.')->group(function () {
            Route::get('/', [PromoController::class, 'index'])->name('index');
            Route::post('/store', [PromoController::class, 'store'])->name('store');
            Route::post('/{id}/update', [PromoController::class, 'update'])->name('update');
            Route::post('/{id}/toggle-active', [PromoController::class, 'toggleActive'])->name('toggle.active');
            Route::delete('/{id}', [PromoController::class, 'destroy'])->name('destroy');
        });

        // LEGACY ADMIN: Pengguna (Staf) standalone
        Route::prefix('pengguna')->name('users.')->group(function () {
            Route::get('/', [UserController::class, 'index'])->name('index');
            Route::post('/store', [UserController::class, 'store'])->name('store');
            Route::post('/{id}/update', [UserController::class, 'update'])->name('update');
            Route::post('/{id}/toggle-status', [UserController::class, 'toggleStatus'])->name('toggle.status');
            Route::post('/{id}/reset-password', [UserController::class, 'resetPassword'])->name('reset_password');
            Route::delete('/{id}', [UserController::class, 'destroy'])->name('destroy');
        });

        // LEGACY ADMIN: Meja standalone
        Route::prefix('meja')->name('tables.')->group(function () {
            Route::get('/', [TableController::class, 'index'])->name('index');
            Route::post('/store', [TableController::class, 'store'])->name('store');
            Route::post('/{id}/update', [TableController::class, 'update'])->name('update');
            Route::post('/{id}/status', [TableController::class, 'updateStatus'])->name('status');
            Route::delete('/{id}', [TableController::class, 'destroy'])->name('destroy');
        });

        // LEGACY ADMIN: Pengaturan Cafe standalone
        Route::prefix('pengaturan')->name('settings.')->group(function () {
            Route::get('/', [SettingController::class, 'index'])->name('index');
            Route::post('/profile', [SettingController::class, 'updateProfile'])->name('profile');
            Route::post('/tax', [SettingController::class, 'updateTax'])->name('tax');
            Route::post('/payment', [SettingController::class, 'updatePayment'])->name('payment');
            Route::post('/receipt', [SettingController::class, 'updateReceipt'])->name('receipt');
            Route::post('/display', [SettingController::class, 'updateDisplay'])->name('display');
            Route::post('/account', [SettingController::class, 'updateAccount'])->name('account');
        });

        // LEGACY ADMIN: Log Aktivitas standalone
        Route::prefix('log')->name('logs.')->group(function () {
            Route::get('/', [ActivityLogController::class, 'index'])->name('index');
            Route::get('/export', [ActivityLogController::class, 'exportCsv'])->name('export');
        });
    });
}

/*
|--------------------------------------------------------------------------
| Portal Owner (Analisis Bisnis)
|--------------------------------------------------------------------------
| Owner bersifat melihat & menganalisis. Tidak mengelola user staf, menu,
| atau stok. Semua data bersumber dari modul admin.
*/
Route::prefix('owner')->name('owner.')->middleware(['auth', 'role:owner', 'noindex.staf'])->group(function () {
    // 1. Dashboard Bisnis
    Route::get('/', [OwnerDashboardController::class, 'index'])->name('dashboard');

    // 2. Laporan (Penjualan / Keuangan / Stok)
    Route::prefix('laporan')->name('reports.')->group(function () {
        Route::get('/', [OwnerReportController::class, 'index'])->name('index');
        Route::get('/export', [OwnerReportController::class, 'exportCsv'])->name('export');
        Route::get('/cetak', [OwnerReportController::class, 'print'])->name('print');
    });

    // 3. Kinerja Kasir
    Route::get('/kinerja-kasir', [OwnerCashierController::class, 'index'])->name('cashiers.index');
    Route::get('/kinerja-kasir/{id}', [OwnerCashierController::class, 'show'])->name('cashiers.show');

    // 4. Promo & Diskon
    Route::prefix('promo')->name('promos.')->group(function () {
        Route::get('/', [OwnerPromoController::class, 'index'])->name('index');
        Route::get('/tambah', [OwnerPromoController::class, 'create'])->name('create');
        Route::post('/store', [OwnerPromoController::class, 'store'])->name('store');
        Route::get('/{promo}/ubah', [OwnerPromoController::class, 'edit'])->name('edit');
        Route::post('/{id}/update', [OwnerPromoController::class, 'update'])->name('update');
        Route::post('/{id}/toggle-active', [OwnerPromoController::class, 'toggleActive'])->name('toggle');
        Route::delete('/{id}', [OwnerPromoController::class, 'destroy'])->name('destroy');
    });

    // 5. Pengaturan Bisnis
    Route::get('/pengaturan', [OwnerSettingController::class, 'index'])->name('settings.index');
    Route::post('/pengaturan', [OwnerSettingController::class, 'update'])->name('settings.update');
    Route::post('/pengaturan/akun', [OwnerSettingController::class, 'updateAccount'])->name('settings.account');
    Route::post('/pengaturan/akun/password', [OwnerSettingController::class, 'updatePassword'])->name('settings.account.password');

    // 6. Penyesuaian Kas Laci
    Route::get('/penyesuaian-kas', [OwnerCashAdjustmentController::class, 'index'])->name('cash_adjustments.index');
    Route::post('/penyesuaian-kas', [OwnerCashAdjustmentController::class, 'store'])->name('cash_adjustments.store');

    // Logout (nama route khusus owner agar view mudah dipakai)
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
});

/*
|--------------------------------------------------------------------------
| Portal Kasir (Operasional Harian)
|--------------------------------------------------------------------------
| Kasir menangani shift, POS, pesanan, pembayaran, reservasi & meja.
| Rute transaksi (POS, pembayaran, ubah status, catat kas) mengharuskan
| shift aktif melalui middleware 'kasir.shift'.
*/
Route::prefix('kasir')->name('kasir.')->middleware(['auth', 'role:kasir', 'noindex.staf'])->group(function () {
    // 1. Dashboard Shift
    Route::get('/', [KasirDashboardController::class, 'index'])->name('dashboard');
    Route::get('/notifikasi/pending', [KasirDashboardController::class, 'pendingCount'])->name('notifications.pending');

    // 2. Shift & Rekap Kas
    Route::prefix('shift')->name('shift.')->group(function () {
        Route::get('/', [KasirShiftController::class, 'index'])->name('index');
        Route::get('/buka', [KasirShiftController::class, 'showOpen'])->name('open');
        Route::post('/buka', [KasirShiftController::class, 'open'])->name('open.store');
        Route::get('/tutup', [KasirShiftController::class, 'showClose'])->name('close')->middleware('kasir.shift');
        Route::post('/tutup', [KasirShiftController::class, 'close'])->name('close.store')->middleware('kasir.shift');
        Route::post('/kas', [KasirShiftController::class, 'movement'])->name('movement')->middleware('kasir.shift');
        Route::get('/{shift}/struk', [KasirShiftController::class, 'receipt'])->name('receipt');
        Route::get('/{shift}', [KasirShiftController::class, 'history'])->name('show');
    });

    // 3. POS / Input Pesanan
    Route::prefix('pos')->name('pos.')->middleware('kasir.shift')->group(function () {
        Route::get('/', [KasirPosController::class, 'index'])->name('index');
        Route::post('/totals', [KasirPosController::class, 'totals'])->name('totals');
        Route::post('/store', [KasirPosController::class, 'store'])->name('store');
    });

    // 4. Pesanan Masuk & Status
    Route::prefix('pesanan')->name('orders.')->group(function () {
        Route::get('/', [KasirOrderController::class, 'index'])->name('index');
        Route::get('/{order}/data', [KasirOrderController::class, 'show'])->name('data');
        Route::post('/{order}/terima', [KasirOrderController::class, 'accept'])->name('accept')->middleware('kasir.shift');
        Route::post('/{order}/status', [KasirOrderController::class, 'status'])->name('status')->middleware('kasir.shift');
        Route::post('/{order}/batal', [KasirOrderController::class, 'cancel'])->name('cancel')->middleware('kasir.shift');
    });

    // 5. Pembayaran & Cetak Struk
    Route::prefix('pembayaran')->name('payments.')->middleware('kasir.shift')->group(function () {
        Route::get('/', [KasirPaymentController::class, 'index'])->name('index');
        Route::get('/{order}/bayar', [KasirPaymentController::class, 'checkout'])->name('checkout');
        Route::post('/{order}/bayar', [KasirPaymentController::class, 'pay'])->name('pay');
        Route::get('/{order}/struk', [KasirPaymentController::class, 'receipt'])->name('receipt');
        Route::get('/{order}/data', [KasirPaymentController::class, 'receiptJson'])->name('data');
    });

    // 6. Reservasi & Meja
    Route::prefix('reservasi')->name('reservations.')->group(function () {
        Route::get('/', [KasirReservationController::class, 'index'])->name('index');
        Route::post('/store', [KasirReservationController::class, 'store'])->name('store');
        Route::post('/{reservation}/update', [KasirReservationController::class, 'update'])->name('update');
        Route::post('/{reservation}/status', [KasirReservationController::class, 'status'])->name('status');
        Route::delete('/{reservation}', [KasirReservationController::class, 'destroy'])->name('destroy');
    });

    // Meja: ubah status meja
    Route::post('/meja/{table}/status', [KasirTableController::class, 'update'])->name('tables.status');

    // Logout (nama route khusus kasir agar view mudah dipakai)
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
});
