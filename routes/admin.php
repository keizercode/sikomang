<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\SiteController;
use App\Http\Controllers\Admin\UserController;
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

    // Placeholder routes for menu items
    Route::get('articles/index', fn() => view('admin.placeholder'))->name('articles.index');
    Route::get('gallery/index', fn() => view('admin.placeholder'))->name('gallery.index');
    Route::get('pages/index', fn() => view('admin.placeholder'))->name('pages.index');
    Route::get('community/index', fn() => view('admin.placeholder'))->name('community.index');
    Route::get('products/index', fn() => view('admin.placeholder'))->name('products.index');
    Route::get('orders/index', fn() => view('admin.placeholder'))->name('orders.index');
    Route::get('sellers/index', fn() => view('admin.placeholder'))->name('sellers.index');
    Route::get('education/courses', fn() => view('admin.placeholder'))->name('education.courses');
    Route::get('education/materials', fn() => view('admin.placeholder'))->name('education.materials');
    Route::get('education/quizzes', fn() => view('admin.placeholder'))->name('education.quizzes');
    Route::get('reports/monitoring', fn() => view('admin.placeholder'))->name('reports.monitoring');
    Route::get('reports/conservation', fn() => view('admin.placeholder'))->name('reports.conservation');
    Route::get('reports/visitors', fn() => view('admin.placeholder'))->name('reports.visitors');
    Route::get('roles/index', fn() => view('admin.placeholder'))->name('roles.index');
    Route::get('settings/index', fn() => view('admin.placeholder'))->name('settings.index');
    Route::get('profile', fn() => view('admin.placeholder'))->name('profile');
    Route::get('settings', fn() => view('admin.placeholder'))->name('settings');
});
