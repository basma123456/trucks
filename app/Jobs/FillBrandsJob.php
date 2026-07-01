<?php

namespace App\Jobs;

use App\Http\Controllers\Admin\BrandController;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Http\Request;

class FillBrandsJob implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(Request $request): void
    {
        $controller = app(BrandController::class);
        $controller->listBrands($request);
        // Your background task logic here
        \Log::info("Processing filling all brands ");
    }
}
