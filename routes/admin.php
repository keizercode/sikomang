<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\SiteController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\ArticleController;
use App\Http\Controllers\Admin\GalleryController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Auth\CustomLoginController;

/*
|--------------------------------------------------------------------------
| Admin Routes - FIXED VERSION
|--------------------------------------------------------------------------
*/

// Authentication Routes
Route::get('login', [CustomLoginController::class, 'index'])->name('admin.login');
Route::post('login', [CustomLoginController::class, 'post_login'])->name('post_login');
Route::post('logout', [CustomLoginController::class, 'logout'])->name('admin.logout');

// Protected Admin Routes
Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {

    // Dashboard
    Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Monitoring Management (Semua Lokasi)
    Route::prefix('monitoring')->name('monitoring.')->group(function () {
        Route::get('/', [SiteController::class, 'index'])->name('index');
        Route::get('/grid', [SiteController::class, 'grid'])->name('grid');
        Route::get('/create', [SiteController::class, 'create'])->name('create');
        Route::get('/edit/{id}', [SiteController::class, 'edit'])->name('edit');
        Route::post('/store', [SiteController::class, 'store'])->name('store');
        Route::get('/delete/{id}', [SiteController::class, 'destroy'])->name('delete');
        Route::delete('/{id}', [SiteController::class, 'destroy'])->name('destroy');
        Route::get('/damages', [SiteController::class, 'damages'])->name('damages');
        Route::get('/reports', [SiteController::class, 'reports'])->name('reports');
    });

    // Article Management (Konten - Artikel)
    Route::resource('articles', ArticleController::class);
    Route::post('articles/{article}/toggle-featured', [ArticleController::class, 'toggleFeatured'])->name('articles.toggle-featured');
    Route::patch('articles/{article}/publish', [ArticleController::class, 'publish'])->name('articles.publish');
    Route::patch('articles/{article}/unpublish', [ArticleController::class, 'unpublish'])->name('articles.unpublish');

    // Gallery Management (Konten - Galeri)
    Route::resource('galleries', GalleryController::class);
    Route::post('galleries/{gallery}/toggle-featured', [GalleryController::class, 'toggleFeatured'])->name('galleries.toggle-featured');
    Route::post('galleries/{gallery}/toggle-active', [GalleryController::class, 'toggleActive'])->name('galleries.toggle-active');
    Route::post('galleries/update-order', [GalleryController::class, 'updateOrder'])->name('galleries.update-order');
    Route::post('galleries/bulk-upload', [GalleryController::class, 'bulkUpload'])->name('galleries.bulk-upload');

    // User Management (Manajemen User)
    Route::prefix('users')->name('users.')->group(function () {
        Route::get('/', [UserController::class, 'index'])->name('index');
        Route::get('/grid', [UserController::class, 'grid'])->name('grid');
        Route::get('/update/{id?}', [UserController::class, 'update'])->name('update');
        Route::post('/store', [UserController::class, 'store'])->name('store');
        Route::get('/delete/{id}', [UserController::class, 'delete'])->name('delete');
        Route::delete('/{id}', [UserController::class, 'delete'])->name('destroy');
        Route::get('/forcelogin/{id}', [UserController::class, 'forcelogin'])->name('forcelogin');
    });

    // Settings Management (Pengaturan)
    Route::get('settings', [SettingController::class, 'index'])->name('settings.index');
    Route::put('settings', [SettingController::class, 'update'])->name('settings.update');
    Route::post('settings', [SettingController::class, 'store'])->name('settings.store');
    Route::delete('settings/{setting}', [SettingController::class, 'destroy'])->name('settings.destroy');
    Route::get('settings/clear-cache', [SettingController::class, 'clearCache'])->name('settings.clear-cache');
    Route::get('settings/export', [SettingController::class, 'export'])->name('settings.export');
    Route::post('settings/import', [SettingController::class, 'import'])->name('settings.import');
    Route::get('settings/general', [SettingController::class, 'general'])->name('settings.general');
    Route::get('settings/contact', [SettingController::class, 'contact'])->name('settings.contact');
    Route::get('settings/social', [SettingController::class, 'social'])->name('settings.social');
    Route::get('settings/seo', [SettingController::class, 'seo'])->name('settings.seo');
    Route::get('settings/mail', [SettingController::class, 'mail'])->name('settings.mail');
    Route::post('settings/reset', [SettingController::class, 'reset'])->name('settings.reset');

    // Profile
    Route::get('profile', fn() => view('admin.placeholder', ['title' => 'Profile']))->name('profile');
});
