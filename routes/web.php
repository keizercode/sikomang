<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\MonitoringController;
use App\Http\Controllers\ExcelExportController;
use App\Http\Controllers\Frontend\FrontendController;

// Home
Route::get('/', [HomeController::class, 'index'])->name('home');

// Monitoring Routes
Route::prefix('monitoring')->group(function () {
    Route::get('/', [MonitoringController::class, 'index'])->name('monitoring');
    Route::get('/hasil-pemantauan', [MonitoringController::class, 'hasilPemantauan'])->name('hasil-pemantauan');
    Route::get('/lokasi/{slug}', [MonitoringController::class, 'detailLokasi'])->name('detail-lokasi');

    // Export Excel
    Route::get('/export/{category}', [ExcelExportController::class, 'exportMangroveData'])->name('monitoring.export');
});

// Articles
Route::prefix('artikel')->group(function () {
    Route::get('/', function () {
        return view('pages.articles.index');
    })->name('articles.index');

    Route::get('/{slug}', function ($slug) {
        return view('pages.articles.show', compact('slug'));
    })->name('articles.show');
});

// FRONTEND ROUTES //

// Homepage
Route::get('/', [FrontendController::class, 'index'])->name('frontend.home');

// Location pages
Route::get('/lokasi', [FrontendController::class, 'locations'])->name('frontend.locations');
Route::get('/lokasi/{id}', [FrontendController::class, 'detail'])->name('frontend.detail');

// Gallery
Route::get('/galeri', [FrontendController::class, 'gallery'])->name('frontend.gallery');

// Monitoring (Damage Reports)
Route::get('/monitoring', [FrontendController::class, 'monitoring'])->name('frontend.monitoring');

// About
Route::get('/tentang', [FrontendController::class, 'about'])->name('frontend.about');

// Contact
Route::get('/kontak', [FrontendController::class, 'contact'])->name('frontend.contact');

// Search
Route::get('/cari', [FrontendController::class, 'search'])->name('frontend.search');

/*
|--------------------------------------------------------------------------
| API Routes (for AJAX/JSON requests)
|--------------------------------------------------------------------------
*/

Route::prefix('api')->group(function () {
    Route::get('/locations', [FrontendController::class, 'apiLocations'])->name('api.locations');
    Route::get('/stats', [FrontendController::class, 'apiStats'])->name('api.stats');
});

// Admin Routes
require __DIR__ . '/admin.php';
