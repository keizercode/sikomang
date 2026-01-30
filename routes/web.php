<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\MonitoringController;
use App\Http\Controllers\ExcelExportController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

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

// Marketplace
Route::get('/marketplace', function () {
    return view('pages.marketplace');
})->name('marketplace');

// Komunitas
Route::get('/komunitas', function () {
    return view('pages.komunitas');
})->name('komunitas');

// Edukasi
Route::get('/edukasi', function () {
    return view('pages.edukasi');
})->name('edukasi');

// Articles
Route::prefix('artikel')->group(function () {
    Route::get('/', function () {
        return view('pages.articles.index');
    })->name('articles.index');

    Route::get('/{slug}', function ($slug) {
        return view('pages.articles.show', compact('slug'));
    })->name('articles.show');
});
