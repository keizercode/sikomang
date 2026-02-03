<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Frontend\HomeController;
use App\Http\Controllers\Frontend\MonitoringController;
use App\Http\Controllers\Frontend\ExcelExportController;
use App\Http\Controllers\Frontend\FrontendController;

// Home
Route::get('/', [HomeController::class, 'index'])->name('home');

// Monitoring
Route::prefix('monitoring')->group(function () {
    Route::get('/', [MonitoringController::class, 'index'])->name('monitoring');
    Route::get('/hasil-pemantauan', [MonitoringController::class, 'hasilPemantauan'])->name('hasil-pemantauan');
    Route::get('/lokasi/{slug}', [MonitoringController::class, 'detailLokasi'])->name('detail-lokasi');
    Route::get('/export/{category}', [ExcelExportController::class, 'exportMangroveData'])->name('monitoring.export');
});

// Articles
Route::prefix('artikel')->group(function () {
    Route::get('/', [FrontendController::class, 'articlesIndex'])->name('articles.index');
    Route::get('/{slug}', [FrontendController::class, 'articlesShow'])->name('articles.show');
});

// Location pages
Route::get('/lokasi', [FrontendController::class, 'locations'])->name('frontend.locations');
Route::get('/lokasi/{id}', [FrontendController::class, 'detail'])->name('frontend.detail');

// Gallery
Route::get('/galeri', [FrontendController::class, 'gallery'])->name('frontend.gallery');

// About, Contact, Search
Route::get('/tentang', [FrontendController::class, 'about'])->name('frontend.about');
Route::get('/kontak', [FrontendController::class, 'contact'])->name('frontend.contact');
Route::get('/cari', [FrontendController::class, 'search'])->name('frontend.search');

// API Routes
Route::prefix('api')->group(function () {
    Route::get('/locations', [FrontendController::class, 'apiLocations'])->name('api.locations');
    Route::get('/stats', [FrontendController::class, 'apiStats'])->name('api.stats');
});
