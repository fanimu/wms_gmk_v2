<?php

/**
 * ==========================================================
 * WMS GMK v2 - Route Definitions
 * ==========================================================
 * Semua route aplikasi WMS didefinisikan di sini.
 * Menggunakan middleware auth untuk proteksi halaman.
 * ==========================================================
 */

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\GudangController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\ProdukController;
use App\Http\Controllers\TransaksiController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return redirect()->route('login');
});

/*
|--------------------------------------------------------------------------
| Authenticated Routes
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'verified'])->group(function () {

    // ── Dashboard ──────────────────────────────────────────
    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->name('dashboard');

    // ── Profile ────────────────────────────────────────────
    Route::get('/profile', [ProfileController::class, 'edit'])
        ->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])
        ->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])
        ->name('profile.destroy');

    // ── Master Data (Admin & Staff) ────────────────────────
    Route::middleware(['role:admin,staff'])->group(function () {

        // Gudang
        Route::resource('gudang', GudangController::class);

        // Supplier
        Route::resource('supplier', SupplierController::class);

        // Produk
        Route::resource('produk', ProdukController::class);

        // Transaksi
        Route::resource('transaksi', TransaksiController::class);
        Route::post('/transaksi/{transaksi}/confirm', [TransaksiController::class, 'confirm'])
            ->name('transaksi.confirm');
        Route::post('/transaksi/{transaksi}/cancel', [TransaksiController::class, 'cancel'])
            ->name('transaksi.cancel');
    });

    // ── Laporan (Semua Role) ───────────────────────────────
    Route::prefix('laporan')->name('laporan.')->group(function () {
        Route::get('/stok', [LaporanController::class, 'stok'])
            ->name('stok');
        Route::get('/transaksi', [LaporanController::class, 'transaksi'])
            ->name('transaksi');
        Route::get('/penjualan', [LaporanController::class, 'penjualan'])
            ->name('penjualan');
    });

    // ── MARKETPLACE ──
    Route::prefix('marketplace')->name('marketplace.')->group(function () {
        Route::get('/', [\App\Http\Controllers\MarketplaceController::class, 'index'])->name('index');
        Route::get('/shopee/connect', [\App\Http\Controllers\MarketplaceController::class, 'connectShopee'])->name('shopee.connect');
        Route::get('/shopee/callback', [\App\Http\Controllers\MarketplaceController::class, 'shopeeCallback'])->name('shopee.callback');
        Route::get('/tiktok/connect', [\App\Http\Controllers\MarketplaceController::class, 'connectTiktok'])->name('tiktok.connect');
        Route::get('/tiktok/callback', [\App\Http\Controllers\MarketplaceController::class, 'tiktokCallback'])->name('tiktok.callback');
    });
});

require __DIR__.'/auth.php';
