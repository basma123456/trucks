<?php

use App\Jobs\FillBrandsJob;
use App\Jobs\FillPricingItemsJob;
use App\Jobs\FllItemsJob;
use App\Models\SyncJob;
use Illuminate\Support\Facades\Route;
use Illuminate\Bus\Batch;
use Illuminate\Support\Facades\Bus;


Route::view('/', 'welcome')->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::view('dashboard', 'dashboard')->name('dashboard');
});

//Route::get('admin/brands-list', [\App\Http\Controllers\Admin\BrandController::class, 'listBrands']);
//Route::get('admin/items-list', [\App\Http\Controllers\Admin\BrandController::class, 'listItems']);
//Route::get('admin/pricing-items/{num}', [\App\Http\Controllers\Admin\BrandController::class, 'pricingItems']);


Route::get('hit-items', function () {


    $jobs = collect(range(1, 764))
        ->chunk(10) // IMPORTANT
        ->map(function ($chunk) {
            return $chunk->map(fn ($num) => new FllItemsJob($num))->all();
        })
        ->flatten(1)
        ->toArray();

    Bus::batch($jobs)
        ->allowFailures()
        ->dispatch();

    return response()->json([
        'message' => 'Batch dispatched successfully'
    ]);
});


Route::get('hit-brands', function () {
    FillBrandsJob::dispatch();
    return response()->json([
        'message' => 'Job brands queued successfully'
    ]);
});


Route::get('pricing-items-test', function () {
    $jobs = [];

    for ($num = 1; $num <= 764; $num++) {

        $jobs[] = new   FillPricingItemsJob($num);
    }
    Bus::batch($jobs)->dispatch();

    return response()->json([
        'message' => 'Batch of pricing dispatched'
    ]);
});


require __DIR__ . '/settings.php';
