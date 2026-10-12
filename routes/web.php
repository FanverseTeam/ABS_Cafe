<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\MenuController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\StockController;
use App\Http\Controllers\Admin\PromoController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\TableController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\ActivityLogController;
use App\Http\Controllers\Owner\DashboardController as OwnerDashboardController;
use App\Http\Controllers\Owner\ReportController as OwnerReportController;
use App\Http\Controllers\Owner\CashierController as OwnerCashierController;
use App\Http\Controllers\Owner\PromoController as OwnerPromoController;
use App\Http\Controllers\Owner\SettingController as OwnerSettingController;
use App\Http\Controllers\Owner\ProfileController as OwnerProfileController;
use App\Http\Controllers\Kasir\DashboardController as KasirDashboardController;
use App\Http\Controllers\Kasir\ShiftController as KasirShiftController;
use App\Http\Controllers\Kasir\PosController as KasirPosController;
use App\Http\Controllers\Kasir\OrderController as KasirOrderController;
use App\Http\Controllers\Kasir\PaymentController as KasirPaymentController;
use App\Http\Controllers\Kasir\ReservationController as KasirReservationController;
use App\Http\Controllers\Kasir\TableController as KasirTableController;
use App\Http\Controllers\Kasir\ProfileController as KasirProfileController;

// Root diarahkan ke halaman login terpadu (peran admin terpilih secara default)
Route::get('/', function () {
    return redirect()->route('login');
});

/*
|--------------------------------------------------------------------------
| Login Terpadu (Admin / Owner / Kasir)
|--------------------------------------------------------------------------
| Satu halaman untuk semua peran. URL lama berfungsi sebagai pengalih:
| /admin/login, /owner/login, /kasir/login semuanya mengarah ke /login.
*/
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

/*
|--------------------------------------------------------------------------
| Back-Office Admin
|--------------------------------------------------------------------------
*/
Route::prefix('admin')->name('admin.')->middleware('admin.auth')->group(function () {
    // Dashboard
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    // Pesanan
    Route::prefix('pesanan')->name('orders.')->group(function () {
        Route::get('/', [OrderController::class, 'index'])->name('index');
        Route::get('/export', [OrderController::class, 'exportCsv'])->name('export');
        Route::get('/{id}', [OrderController::class, 'show'])->name('show');
        Route::post('/{id}/status', [OrderController::class, 'updateStatus'])->name('status');
        Route::post('/{id}/cancel', [OrderController::class, 'cancel'])->name('cancel');
    });

    // Menu
    Route::prefix('menu')->name('menu.')->group(function () {
        Route::get('/', [MenuController::class, 'index'])->name('index');
        Route::post('/store', [MenuController::class, 'store'])->name('store');
        Route::post('/{id}/update', [MenuController::class, 'update'])->name('update');
        Route::post('/{id}/duplicate', [MenuController::class, 'duplicate'])->name('duplicate');
        Route::post('/{id}/toggle-available', [MenuController::class, 'toggleAvailable'])->name('toggle.available');
        Route::post('/{id}/toggle-bestseller', [MenuController::class, 'toggleBestSeller'])->name('toggle.bestseller');
        Route::delete('/{id}', [MenuController::class, 'destroy'])->name('destroy');
    });

    // Kategori
    Route::prefix('kategori')->name('category.')->group(function () {
        Route::get('/', [CategoryController::class, 'index'])->name('index');
        Route::post('/store', [CategoryController::class, 'store'])->name('store');
        Route::post('/{id}/update', [CategoryController::class, 'update'])->name('update');
        Route::delete('/{id}', [CategoryController::class, 'destroy'])->name('destroy');
    });

    // Stok
    Route::prefix('stok')->name('stock.')->group(function () {
        Route::get('/', [StockController::class, 'index'])->name('index');
        Route::post('/{id}/adjust', [StockController::class, 'adjust'])->name('adjust');
        Route::post('/{id}/min-stock', [StockController::class, 'setMinStock'])->name('min_stock');
    });

    // Promo
    Route::prefix('promo')->name('promo.')->group(function () {
        Route::get('/', [PromoController::class, 'index'])->name('index');
        Route::post('/store', [PromoController::class, 'store'])->name('store');
        Route::post('/{id}/update', [PromoController::class, 'update'])->name('update');
        Route::post('/{id}/toggle-active', [PromoController::class, 'toggleActive'])->name('toggle.active');
        Route::delete('/{id}', [PromoController::class, 'destroy'])->name('destroy');
    });

    // Pengguna (Staf/Kasir/Admin)
    Route::prefix('pengguna')->name('users.')->group(function () {
        Route::get('/', [UserController::class, 'index'])->name('index');
        Route::post('/store', [UserController::class, 'store'])->name('store');
        Route::post('/{id}/update', [UserController::class, 'update'])->name('update');
        Route::post('/{id}/toggle-status', [UserController::class, 'toggleStatus'])->name('toggle.status');
        Route::post('/{id}/reset-password', [UserController::class, 'resetPassword'])->name('reset_password');
        Route::delete('/{id}', [UserController::class, 'destroy'])->name('destroy');
    });

    // Meja
    Route::prefix('meja')->name('tables.')->group(function () {
        Route::get('/', [TableController::class, 'index'])->name('index');
        Route::post('/store', [TableController::class, 'store'])->name('store');
        Route::post('/{id}/update', [TableController::class, 'update'])->name('update');
        Route::post('/{id}/status', [TableController::class, 'updateStatus'])->name('status');
        Route::delete('/{id}', [TableController::class, 'destroy'])->name('destroy');
    });

    // Pengaturan Cafe
    Route::prefix('pengaturan')->name('settings.')->group(function () {
        Route::get('/', [SettingController::class, 'index'])->name('index');
        Route::post('/profile', [SettingController::class, 'updateProfile'])->name('profile');
        Route::post('/tax', [SettingController::class, 'updateTax'])->name('tax');
        Route::post('/payment', [SettingController::class, 'updatePayment'])->name('payment');
        Route::post('/receipt', [SettingController::class, 'updateReceipt'])->name('receipt');
        Route::post('/display', [SettingController::class, 'updateDisplay'])->name('display');
        Route::post('/account', [SettingController::class, 'updateAccount'])->name('account');
    });

    // Log Aktivitas
    Route::prefix('log')->name('logs.')->group(function () {
        Route::get('/', [ActivityLogController::class, 'index'])->name('index');
        Route::get('/export', [ActivityLogController::class, 'exportCsv'])->name('export');
    });
});

/*
|--------------------------------------------------------------------------
| Portal Owner (Analisis Bisnis)
|--------------------------------------------------------------------------
| Owner bersifat melihat & menganalisis. Tidak mengelola user staf, menu,
| atau stok. Semua data bersumber dari modul admin.
*/
Route::prefix('owner')->name('owner.')->middleware(['auth', 'role:owner'])->group(function () {
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
        Route::post('/store', [OwnerPromoController::class, 'store'])->name('store');
        Route::post('/{id}/update', [OwnerPromoController::class, 'update'])->name('update');
        Route::post('/{id}/toggle-active', [OwnerPromoController::class, 'toggleActive'])->name('toggle');
        Route::delete('/{id}', [OwnerPromoController::class, 'destroy'])->name('destroy');
    });

    // 5. Pengaturan Bisnis
    Route::get('/pengaturan', [OwnerSettingController::class, 'index'])->name('settings.index');
    Route::post('/pengaturan', [OwnerSettingController::class, 'update'])->name('settings.update');

    // Profil & Ganti Password
    Route::get('/profil', [OwnerProfileController::class, 'index'])->name('profile.index');
    Route::post('/profil', [OwnerProfileController::class, 'updateProfile'])->name('profile.update');
    Route::post('/profil/password', [OwnerProfileController::class, 'updatePassword'])->name('profile.password');

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
Route::prefix('kasir')->name('kasir.')->middleware(['auth', 'role:kasir'])->group(function () {
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
        Route::get('/{cashShift}/struk', [KasirShiftController::class, 'receipt'])->name('receipt');
        Route::get('/{cashShift}', [KasirShiftController::class, 'history'])->name('show');
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

    // Profil & Ganti Password
    Route::get('/profil', [KasirProfileController::class, 'index'])->name('profile.index');
    Route::post('/profil', [KasirProfileController::class, 'updateProfile'])->name('profile.update');
    Route::post('/profil/password', [KasirProfileController::class, 'updatePassword'])->name('profile.password');

    // Logout (nama route khusus kasir agar view mudah dipakai)
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
});
