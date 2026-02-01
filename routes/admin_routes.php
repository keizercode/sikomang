<?php

use App\Http\Controllers\Admin\ArticleController;
use App\Http\Controllers\Admin\GalleryController;
use App\Http\Controllers\Admin\SettingController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Admin Routes - Articles, Galleries, Settings
|--------------------------------------------------------------------------
*/

Route::prefix('admin')->name('admin.')->middleware(['auth'])->group(function () {

    // Articles Management
    Route::resource('articles', ArticleController::class);
    Route::post('articles/{article}/toggle-featured', [ArticleController::class, 'toggleFeatured'])->name('articles.toggle-featured');
    Route::patch('articles/{article}/publish', [ArticleController::class, 'publish'])->name('articles.publish');
    Route::patch('articles/{article}/unpublish', [ArticleController::class, 'unpublish'])->name('articles.unpublish');

    // Galleries Management
    Route::resource('galleries', GalleryController::class);
    Route::post('galleries/{gallery}/toggle-featured', [GalleryController::class, 'toggleFeatured'])->name('galleries.toggle-featured');
    Route::post('galleries/{gallery}/toggle-active', [GalleryController::class, 'toggleActive'])->name('galleries.toggle-active');
    Route::post('galleries/update-order', [GalleryController::class, 'updateOrder'])->name('galleries.update-order');
    Route::post('galleries/bulk-upload', [GalleryController::class, 'bulkUpload'])->name('galleries.bulk-upload');

    // Settings Management
    Route::get('settings', [SettingController::class, 'index'])->name('settings.index');
    Route::put('settings', [SettingController::class, 'update'])->name('settings.update');
    Route::post('settings', [SettingController::class, 'store'])->name('settings.store');
    Route::delete('settings/{setting}', [SettingController::class, 'destroy'])->name('settings.destroy');

    // Settings - Specific Groups
    Route::get('settings/general', [SettingController::class, 'general'])->name('settings.general');
    Route::get('settings/contact', [SettingController::class, 'contact'])->name('settings.contact');
    Route::get('settings/social', [SettingController::class, 'social'])->name('settings.social');
    Route::get('settings/seo', [SettingController::class, 'seo'])->name('settings.seo');
    Route::get('settings/mail', [SettingController::class, 'mail'])->name('settings.mail');

    // Settings - Actions
    Route::get('settings/clear-cache', [SettingController::class, 'clearCache'])->name('settings.clear-cache');
    Route::get('settings/export', [SettingController::class, 'export'])->name('settings.export');
    Route::post('settings/import', [SettingController::class, 'import'])->name('settings.import');
    Route::post('settings/reset', [SettingController::class, 'reset'])->name('settings.reset');
});
