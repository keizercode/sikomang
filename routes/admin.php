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
| Admin Routes
|--------------------------------------------------------------------------
*/

// Authentication Routes
Route::get('login', [CustomLoginController::class, 'index'])->name('login');
Route::post('login', [CustomLoginController::class, 'post_login'])->name('post_login');
Route::get('logout', [CustomLoginController::class, 'logout'])->name('admin.logout');

// Protected Admin Routes
Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {

    // Dashboard
    Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Monitoring Management
    Route::prefix('monitoring')->name('monitoring.')->group(function () {
        Route::get('/', [SiteController::class, 'index'])->name('index');
        Route::get('/grid', [SiteController::class, 'grid'])->name('grid');
        Route::get('/create', [SiteController::class, 'create'])->name('create');
        Route::get('/edit/{id}', [SiteController::class, 'edit'])->name('edit');
        Route::post('/store', [SiteController::class, 'store'])->name('store');
        Route::get('/delete/{id}', [SiteController::class, 'destroy'])->name('delete');
        Route::get('/damages', [SiteController::class, 'damages'])->name('damages');
        Route::get('/reports', [SiteController::class, 'reports'])->name('reports');
    });

    // User Management
    Route::prefix('users')->name('users.')->group(function () {
        Route::get('/', [UserController::class, 'index'])->name('index');
        Route::get('/grid', [UserController::class, 'grid'])->name('grid');
        Route::get('/update/{id?}', [UserController::class, 'update'])->name('update');
        Route::post('/store', [UserController::class, 'store'])->name('store');
        Route::get('/delete/{id}', [UserController::class, 'delete'])->name('delete');
        Route::get('/forcelogin/{id}', [UserController::class, 'forcelogin'])->name('forcelogin');
    });

    // Article Management
    Route::prefix('articles')->name('articles.')->group(function () {
        Route::get('/', fn() => view('admin.placeholder'))->name('index');
        Route::get('/create', fn() => view('admin.placeholder'))->name('create');
        Route::post('/store', fn() => redirect()->back())->name('store');
        Route::get('/edit/{id}', fn() => view('admin.placeholder'))->name('edit');
        Route::get('/delete/{id}', fn() => redirect()->back())->name('delete');
    });

    // Gallery Management
    Route::prefix('gallery')->name('gallery.')->group(function () {
        Route::get('/', fn() => view('admin.placeholder'))->name('index');
        Route::get('/upload', fn() => view('admin.placeholder'))->name('upload');
        Route::post('/store', fn() => redirect()->back())->name('store');
        Route::get('/delete/{id}', fn() => redirect()->back())->name('delete');
    });

    // Settings
    Route::prefix('settings')->name('settings.')->group(function () {
        Route::get('/', fn() => view('admin.placeholder'))->name('index');
        Route::post('/update', fn() => redirect()->back())->name('update');
    });

    // Profile
    Route::get('profile', fn() => view('admin.placeholder'))->name('profile');
});
