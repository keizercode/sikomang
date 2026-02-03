<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Frontend\HomeController;
use App\Http\Controllers\Frontend\MonitoringController;
use App\Http\Controllers\Frontend\ArticleController;
use App\Http\Controllers\Frontend\GalleryController;
use App\Http\Controllers\Frontend\FrontendController;
use App\Http\Controllers\Frontend\ExcelExportController;

/*
|--------------------------------------------------------------------------
| Frontend Routes
|--------------------------------------------------------------------------
|
| Semua rute ini mengambil data langsung dari database yang sama dengan
| admin dashboard. Data yang dikelola admin otomatis tampil di frontend.
|
*/

// home
Route::get('/', [HomeController::class, 'index'])->name('home');

// ========================================
// MONITORING MANGROVE
// ========================================
Route::prefix('monitoring')->name('monitoring.')->group(function () {
    // Halaman utama monitoring dengan peta
    Route::get('/', [MonitoringController::class, 'index'])->name('index');

    // Hasil pemantauan (list lokasi)
    Route::get('/hasil-pemantauan', [MonitoringController::class, 'hasilPemantauan'])
        ->name('hasil-pemantauan');

    // Detail lokasi berdasarkan slug
    Route::get('/lokasi/{slug}', [MonitoringController::class, 'detailLokasi'])
        ->name('monitoring.detail-lokasi');

    // Export data mangrove berdasarkan kategori (jarang/sedang/lebat)
    Route::get('/export/{category}', [ExcelExportController::class, 'exportMangroveData'])
        ->name('export');
});

// ========================================
// ARTIKEL (dari Admin)
// ========================================
Route::prefix('artikel')->name('articles.')->group(function () {
    // List artikel yang sudah published
    Route::get('/', [ArticleController::class, 'index'])->name('index');

    // Detail artikel berdasarkan slug
    Route::get('/{slug}', [ArticleController::class, 'show'])->name('show');
});

// ========================================
// GALERI (dari Admin)
// ========================================
Route::prefix('galeri')->name('gallery.')->group(function () {
    // List galeri yang aktif
    Route::get('/', [GalleryController::class, 'index'])->name('index');

    // Detail galeri
    Route::get('/{gallery}', [GalleryController::class, 'show'])->name('show');
});

// ========================================
// LOKASI MANGROVE
// ========================================
Route::prefix('lokasi')->name('locations.')->group(function () {
    // Semua lokasi dengan peta
    Route::get('/', [FrontendController::class, 'locations'])->name('index');

    // Detail lokasi
    Route::get('/{id}', [FrontendController::class, 'detail'])->name('detail');
});

// ========================================
// SEARCH
// ========================================
Route::get('/cari', [FrontendController::class, 'search'])->name('search');

// ========================================
// API ENDPOINTS (untuk AJAX/JavaScript)
// ========================================
Route::prefix('api')->name('api.')->group(function () {
    // Get locations as JSON (untuk peta)
    Route::get('/locations', [FrontendController::class, 'apiLocations'])
        ->name('locations');

    // Get statistics
    Route::get('/stats', [FrontendController::class, 'apiStats'])
        ->name('stats');
});
