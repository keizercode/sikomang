<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\MonitoringController;
use App\Http\Controllers\ExcelExportController;

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

// Admin Routes
require __DIR__ . '/admin.php';
