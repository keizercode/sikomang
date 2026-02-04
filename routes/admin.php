<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\SiteController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\ArticleController;
use App\Http\Controllers\Admin\GalleryController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\LocationDetailController;
use App\Http\Controllers\Auth\CustomLoginController;

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
*/

// // Authentication Routes
// Route::get('login', [CustomLoginController::class, 'index'])->name('admin.login');
// Route::post('login', [CustomLoginController::class, 'post_login'])->name('post_login');
// Route::post('logout', [CustomLoginController::class, 'logout'])->name('admin.logout');

// // Protected Admin Routes
// Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {

//     // Dashboard
//     Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');

//     // Monitoring Management
//     Route::prefix('monitoring')->name('monitoring.')->group(function () {
//         Route::get('/', [SiteController::class, 'index'])->name('index');
//         Route::get('/grid', [SiteController::class, 'grid'])->name('grid');
//         Route::get('/create', [SiteController::class, 'create'])->name('create');
//         Route::get('/edit/{id}', [SiteController::class, 'edit'])->name('edit');
//         Route::post('/store', [SiteController::class, 'store'])->name('store');
//         Route::get('/delete/{id}', [SiteController::class, 'destroy'])->name('delete');
//         Route::delete('/{id}', [SiteController::class, 'destroy'])->name('destroy');
//         Route::get('/damages', [SiteController::class, 'damages'])->name('damages');
//         Route::get('/reports', [SiteController::class, 'reports'])->name('reports');

//         // Location Detail Management
//         Route::get('/{id}/detail', [LocationDetailController::class, 'show'])->name('detail');
//         Route::put('/{id}/species', [LocationDetailController::class, 'updateSpecies'])->name('update-species');
//         Route::put('/{id}/activities', [LocationDetailController::class, 'updateActivities'])->name('update-activities');
//         Route::put('/{id}/programs', [LocationDetailController::class, 'updateOtherDetails'])->name('update-programs');

//         // Images
//         Route::post('/{id}/images', [LocationDetailController::class, 'uploadImages'])->name('upload-images');
//         Route::delete('/{locationId}/images/{imageId}', [LocationDetailController::class, 'deleteImage'])->name('delete-image');

//         // Damages
//         Route::post('/{id}/damages', [LocationDetailController::class, 'addDamage'])->name('add-damage');
//         Route::put('/{id}/damages/{damageId}', [LocationDetailController::class, 'updateDamage'])->name('update-damage');
//         Route::delete('/{id}/damages/{damageId}', [LocationDetailController::class, 'deleteDamage'])->name('delete-damage');

//         // Actions
//         Route::post('/{id}/damages/{damageId}/actions', [LocationDetailController::class, 'addAction'])->name('add-action');
//     });

//     // Article Management
//     Route::prefix('articles')->name('articles.')->group(function () {
//         // Standard CRUD routes
//         Route::get('/', [ArticleController::class, 'index'])->name('index');
//         Route::get('/create', [ArticleController::class, 'create'])->name('create');
//         Route::post('/', [ArticleController::class, 'store'])->name('store');
//         Route::get('/{article}', [ArticleController::class, 'show'])->name('show');
//         Route::get('/{article}/edit', [ArticleController::class, 'edit'])->name('edit');
//         Route::put('/{article}', [ArticleController::class, 'update'])->name('update');
//         Route::delete('/{article}', [ArticleController::class, 'destroy'])->name('destroy');

//         // Additional actions
//         Route::post('/{article}/toggle-featured', [ArticleController::class, 'toggleFeatured'])->name('toggle-featured');
//         Route::patch('/{article}/publish', [ArticleController::class, 'publish'])->name('publish');
//         Route::patch('/{article}/unpublish', [ArticleController::class, 'unpublish'])->name('unpublish');

//         // Soft delete management routes
//         Route::get('/trashed/list', [ArticleController::class, 'trashed'])->name('trashed');
//         Route::post('/{id}/restore', [ArticleController::class, 'restore'])->name('restore');
//         Route::delete('/{id}/force-delete', [ArticleController::class, 'forceDelete'])->name('force-delete');
//     });

//     // Gallery Management
//     Route::prefix('galleries')->name('galleries.')->group(function () {
//         Route::get('/', [GalleryController::class, 'index'])->name('index');
//         Route::get('/create', [GalleryController::class, 'create'])->name('create');
//         Route::post('/', [GalleryController::class, 'store'])->name('store');
//         Route::get('/{gallery}', [GalleryController::class, 'show'])->name('show');
//         Route::get('/{gallery}/edit', [GalleryController::class, 'edit'])->name('edit');
//         Route::put('/{gallery}', [GalleryController::class, 'update'])->name('update');
//         Route::delete('/{gallery}', [GalleryController::class, 'destroy'])->name('destroy');
//         Route::post('/{gallery}/toggle-featured', [GalleryController::class, 'toggleFeatured'])->name('toggle-featured');
//         Route::post('/{gallery}/toggle-active', [GalleryController::class, 'toggleActive'])->name('toggle-active');
//         Route::post('/update-order', [GalleryController::class, 'updateOrder'])->name('update-order');
//         Route::post('/bulk-upload', [GalleryController::class, 'bulkUpload'])->name('bulk-upload');
//     });

//     // User Management
//     Route::prefix('users')->name('users.')->group(function () {
//         Route::get('/', [UserController::class, 'index'])->name('index');
//         Route::get('/grid', [UserController::class, 'grid'])->name('grid');
//         Route::get('/update/{id?}', [UserController::class, 'update'])->name('update');
//         Route::post('/store', [UserController::class, 'store'])->name('store');
//         Route::get('/delete/{id}', [UserController::class, 'delete'])->name('delete');
//         Route::delete('/{id}', [UserController::class, 'delete'])->name('destroy');
//         Route::get('/forcelogin/{id}', [UserController::class, 'forcelogin'])->name('forcelogin');
//     });

//     // Settings Management
//     Route::prefix('settings')->name('settings.')->group(function () {
//         Route::get('/', [SettingController::class, 'index'])->name('index');
//         Route::put('/', [SettingController::class, 'update'])->name('update');
//         Route::post('/', [SettingController::class, 'store'])->name('store');
//         Route::delete('/{setting}', [SettingController::class, 'destroy'])->name('destroy');
//         Route::get('/clear-cache', [SettingController::class, 'clearCache'])->name('clear-cache');
//         Route::get('/export', [SettingController::class, 'export'])->name('export');
//         Route::post('/import', [SettingController::class, 'import'])->name('import');
//         Route::get('/general', [SettingController::class, 'general'])->name('general');
//         Route::get('/contact', [SettingController::class, 'contact'])->name('contact');
//         Route::get('/social', [SettingController::class, 'social'])->name('social');
//         Route::get('/seo', [SettingController::class, 'seo'])->name('seo');
//         Route::get('/mail', [SettingController::class, 'mail'])->name('mail');
//         Route::post('/reset', [SettingController::class, 'reset'])->name('reset');
//     });

//     // Profile
//     Route::get('profile', fn() => view('admin.placeholder', ['title' => 'Profile']))->name('profile');

//     // Quick Stats API
//     Route::get('api/stats', [DashboardController::class, 'getStats'])->name('api.stats');
// });
