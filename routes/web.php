<?php

use App\Http\Controllers\Auth\CustomLoginController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\SiteController;
use App\Http\Controllers\Admin\UserController;

// Auth Routes
Route::get('/login', [CustomLoginController::class, 'index'])->name('login');
Route::post('/login', [CustomLoginController::class, 'post_login'])->name('post_login');
Route::get('/logout', [CustomLoginController::class, 'logout'])->name('logout');

// Admin Routes (Protected)
Route::prefix('admin')->middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');

    // Monitoring
    Route::prefix('monitoring')->name('admin.monitoring.')->group(function () {
        Route::get('/', [SiteController::class, 'index'])->name('index');
        Route::get('/grid', [SiteController::class, 'grid'])->name('grid');
        Route::get('/create', [SiteController::class, 'create'])->name('create');
        Route::post('/store', [SiteController::class, 'store'])->name('store');
        Route::get('/{id}/edit', [SiteController::class, 'edit'])->name('edit');
        Route::get('/{id}/delete', [SiteController::class, 'destroy'])->name('delete');
    });

    // Users
    Route::prefix('users')->name('admin.users.')->group(function () {
        Route::get('/', [UserController::class, 'index'])->name('index');
        Route::get('/grid', [UserController::class, 'grid'])->name('grid');
        Route::get('/update/{id?}', [UserController::class, 'update'])->name('update');
        Route::post('/store', [UserController::class, 'store'])->name('store');
        Route::get('/delete/{id}', [UserController::class, 'delete'])->name('delete');
    });
});
