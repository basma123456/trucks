<?php

namespace App\Jobs;

use App\Http\Controllers\Admin\BrandController;
use Illuminate\Bus\Batchable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class FillPricingItemsJob implements ShouldQueue
{
    use Queueable , Batchable;

    /**
     * Create a new job instance.
     */
    public $tries = 2;   // or 3 max

    protected $num;

    public function __construct($num)
    {
        $this->num = $num;
    }


    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $controller = app(BrandController::class);
        $controller->pricingItems($this->num);
        // Your background task logic here
//        \Log::info("Processing  items page number : {$this->num}");
        \Log::info("Processing filling all items ");
    }
}
