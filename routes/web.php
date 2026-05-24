<?php

use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome')->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::view('dashboard', 'dashboard')->name('dashboard');
});

Route::get('admin/brands-list', [\App\Http\Controllers\Admin\BrandController::class , 'listBrands']);

require __DIR__ . '/settings.php';
