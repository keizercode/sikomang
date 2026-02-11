<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\SiteController;
use App\Http\Controllers\Admin\LocationDetailController;
use App\Http\Controllers\Admin\ArticleController;
use App\Http\Controllers\Admin\GalleryController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\PublicReportController;
use App\Http\Controllers\Admin\PublicReportExportController;
use App\Http\Controllers\Auth\CustomLoginController;
use App\Http\Controllers\Frontend\HomeController;
use App\Http\Controllers\Frontend\ArticleController as FrontendArticleController;
use App\Http\Controllers\Frontend\GalleryController as FrontendGalleryController;
use App\Http\Controllers\Frontend\MonitoringController;
use App\Http\Controllers\Frontend\ExcelExportController;
use App\Http\Controllers\Frontend\PublicReportController as FrontendPublicReportController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Auth Routes
Route::get('/login', [CustomLoginController::class, 'index'])->name('login');
Route::post('/login', [CustomLoginController::class, 'post_login'])->name('post_login');

// Admin Routes
Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::post('/logout', [CustomLoginController::class, 'logout'])->name('logout');
    Route::get('/profile', function () {
        return view('pages.admin.placeholder', ['title' => 'Profile']);
    })->name('profile');

    // Monitoring Management
    Route::prefix('monitoring')->name('monitoring.')->group(function () {
        Route::get('/', [SiteController::class, 'index'])->name('index');
        Route::get('/grid', [SiteController::class, 'grid'])->name('grid');
        Route::get('/create', [SiteController::class, 'create'])->name('create');
        Route::post('/store', [SiteController::class, 'store'])->name('store');
        Route::get('/edit/{id}', [SiteController::class, 'edit'])->name('edit');
        Route::get('/delete/{id}', [SiteController::class, 'destroy'])->name('delete');
        Route::get('/damages', [SiteController::class, 'damages'])->name('damages');
        Route::get('/reports', [SiteController::class, 'reports'])->name('reports');

        // Location Details
        Route::get('/{id}/detail', [LocationDetailController::class, 'show'])->name('detail');
        Route::put('/{id}/update-species', [LocationDetailController::class, 'updateSpecies'])->name('update-species');
        Route::put('/{id}/update-activities', [LocationDetailController::class, 'updateActivities'])->name('update-activities');
        Route::put('/{id}/update-programs', [LocationDetailController::class, 'updateOtherDetails'])->name('update-programs');
        Route::post('/{id}/upload-images', [LocationDetailController::class, 'uploadImages'])->name('upload-images');
        Route::delete('/{locationId}/images/{imageId}', [LocationDetailController::class, 'deleteImage'])->name('delete-image');
        Route::post('/{id}/add-damage', [LocationDetailController::class, 'addDamage'])->name('add-damage');
        Route::put('/{id}/damages/{damageId}', [LocationDetailController::class, 'updateDamage'])->name('update-damage');
        Route::get('/{id}/damages/{damageId}/edit', [LocationDetailController::class, 'editDamage'])->name('edit-damage');
        Route::delete('/{id}/damages/{damageId}', [LocationDetailController::class, 'deleteDamage'])->name('delete-damage');
        Route::post('/{id}/damages/{damageId}/add-action', [LocationDetailController::class, 'addAction'])->name('add-action');
    });

    // Public Reports Management (Admin)
    Route::prefix('public-reports')->name('public-reports.')->group(function () {
        Route::get('/', [PublicReportController::class, 'index'])->name('index');
        Route::get('/grid', [PublicReportController::class, 'grid'])->name('grid');
        Route::get('/{id}/detail', [PublicReportController::class, 'show'])->name('detail');
        Route::post('/{id}/verify', [PublicReportController::class, 'verify'])->name('verify');
        Route::post('/{id}/update-status', [PublicReportController::class, 'updateStatus'])->name('update-status');
        Route::post('/{id}/add-note', [PublicReportController::class, 'addNote'])->name('add-note');
        Route::delete('/{id}', [PublicReportController::class, 'destroy'])->name('destroy');

        // Export routes
        Route::get('/export/excel', [PublicReportExportController::class, 'exportExcel'])->name('export-excel');
        Route::get('/export/pdf', [PublicReportExportController::class, 'exportPdf'])->name('export-pdf');
        Route::get('/export/csv', [PublicReportExportController::class, 'exportCsv'])->name('export-csv');
    });

    // Articles Management
    Route::prefix('articles')->name('articles.')->group(function () {
        Route::get('/', [ArticleController::class, 'index'])->name('index');
        Route::get('/create', [ArticleController::class, 'create'])->name('create');
        Route::post('/', [ArticleController::class, 'store'])->name('store');
        Route::get('/{article}', [ArticleController::class, 'show'])->name('show');
        Route::get('/{article}/edit', [ArticleController::class, 'edit'])->name('edit');
        Route::put('/{article}', [ArticleController::class, 'update'])->name('update');
        Route::delete('/{article}', [ArticleController::class, 'destroy'])->name('destroy');
        Route::post('/{article}/toggle-featured', [ArticleController::class, 'toggleFeatured'])->name('toggle-featured');
        Route::patch('/{article}/publish', [ArticleController::class, 'publish'])->name('publish');
        Route::patch('/{article}/unpublish', [ArticleController::class, 'unpublish'])->name('unpublish');
        Route::post('/upload-image', [ArticleController::class, 'uploadImage'])->name('upload-image');
    });

    // Galleries Management
    Route::prefix('galleries')->name('galleries.')->group(function () {
        Route::get('/', [GalleryController::class, 'index'])->name('index');
        Route::get('/create', [GalleryController::class, 'create'])->name('create');
        Route::post('/', [GalleryController::class, 'store'])->name('store');
        Route::get('/{gallery}', [GalleryController::class, 'show'])->name('show');
        Route::get('/{gallery}/edit', [GalleryController::class, 'edit'])->name('edit');
        Route::put('/{gallery}', [GalleryController::class, 'update'])->name('update');
        Route::delete('/{gallery}', [GalleryController::class, 'destroy'])->name('destroy');
        Route::post('/{gallery}/toggle-featured', [GalleryController::class, 'toggleFeatured'])->name('toggle-featured');
        Route::post('/{gallery}/toggle-active', [GalleryController::class, 'toggleActive'])->name('toggle-active');
        Route::post('/bulk-upload', [GalleryController::class, 'bulkUpload'])->name('bulk-upload');
    });

    // Users Management
    Route::prefix('users')->name('users.')->group(function () {
        Route::get('/', [UserController::class, 'index'])->name('index');
        Route::get('/grid', [UserController::class, 'grid'])->name('grid');
        Route::get('/update/{id?}', [UserController::class, 'update'])->name('update');
        Route::post('/store', [UserController::class, 'store'])->name('store');
        Route::get('/delete/{id}', [UserController::class, 'delete'])->name('delete');
    });

    // Settings Management
    Route::prefix('settings')->name('settings.')->group(function () {
        Route::get('/', [SettingController::class, 'index'])->name('index');
        Route::put('/update', [SettingController::class, 'update'])->name('update');
        Route::post('/store', [SettingController::class, 'store'])->name('store');
        Route::delete('/{setting}', [SettingController::class, 'destroy'])->name('destroy');
        Route::get('/clear-cache', [SettingController::class, 'clearCache'])->name('clear-cache');
        Route::get('/export', [SettingController::class, 'export'])->name('export');
        Route::post('/import', [SettingController::class, 'import'])->name('import');
    });
});

// Frontend Routes - Public Access
Route::get('/', [HomeController::class, 'index'])->name('home');

Route::prefix('articles')->name('articles.')->group(function () {
    Route::get('/', [FrontendArticleController::class, 'index'])->name('index');
    Route::get('/{slug}', [FrontendArticleController::class, 'show'])->name('show');
});

Route::prefix('gallery')->name('gallery.')->group(function () {
    Route::get('/', [FrontendGalleryController::class, 'index'])->name('index');
    Route::get('/{gallery}', [FrontendGalleryController::class, 'show'])->name('show');
});

Route::prefix('monitoring')->name('monitoring.')->group(function () {
    Route::get('/', [MonitoringController::class, 'index'])->name('index');
    Route::get('/hasil-pemantauan', [MonitoringController::class, 'hasilPemantauan'])->name('hasil-pemantauan');
    Route::get('/lokasi/{slug}', [MonitoringController::class, 'detailLokasi'])->name('detail-lokasi');
    Route::get('/export/{category}', [ExcelExportController::class, 'exportMangroveData'])->name('export');
});

// Public Reports Routes (Frontend)
Route::prefix('reports')->name('reports.')->group(function () {
    Route::post('/submit', [FrontendPublicReportController::class, 'submit'])->name('submit');
    Route::get('/search-locations', [FrontendPublicReportController::class, 'searchLocations'])->name('search-locations');
    Route::post('/check-status', [FrontendPublicReportController::class, 'checkStatus'])->name('check-status');
});

// Fallback route untuk 404
Route::fallback(function () {
    return response()->view('errors.404', [], 404);
});
