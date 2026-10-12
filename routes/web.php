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

// Root diarahkan ke halaman login terpadu (peran admin terpilih secara default)
Route::get('/', function () {
    return redirect()->route('login');
});

/*
|--------------------------------------------------------------------------
| Login Terpadu (Admin / Owner, siap untuk Kasir)
|--------------------------------------------------------------------------
| Satu halaman dengan animasi geser role. URL lama tetap berfungsi:
| /admin/login membuka halaman yang sama dengan role admin terpilih,
| /owner/login membuka halaman yang sama dengan role owner terpilih.
*/
Route::get('/login', [LoginController::class, 'show'])->name('login');
Route::post('/login', [LoginController::class, 'login'])->name('login.post');

Route::get('/admin/login', [LoginController::class, 'show'])->name('admin.login');
Route::post('/admin/login', [LoginController::class, 'login'])->name('admin.login.post');
Route::post('/admin/logout', [LoginController::class, 'logout'])->name('admin.logout');

Route::get('/owner/login', [LoginController::class, 'show'])->name('owner.login');
Route::post('/owner/login', [LoginController::class, 'login'])->name('owner.login.post');
Route::post('/owner/logout', [LoginController::class, 'logout'])->name('owner.logout');

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
