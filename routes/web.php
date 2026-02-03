<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Main routing file yang mengatur semua routes aplikasi
|
*/

// Include Frontend Routes (Public)
require __DIR__ . '/frontend.php';

// Include Admin Routes (Protected)
require __DIR__ . '/admin.php';

// Fallback route untuk 404
Route::fallback(function () {
    return response()->view('errors.404', [], 404);
});
