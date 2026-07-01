<?php

namespace App\Jobs;

use App\Http\Controllers\Admin\BrandController;
use Illuminate\Bus\Batchable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Http\Request;

class FllItemsJob implements ShouldQueue
{
    use Queueable , Batchable;

    /**
     * Create a new job instance.
     */
    protected $num;
    public $tries = 2;   // or 3 max
//
    public function __construct($num)
    {
        $this->num = $num;
    }
//
    /**
     * Execute the job.
     */
    public function handle(Request $request): void
    {
        $controller = app(BrandController::class);
        $controller->listItems($request , $this->num);
        // Your background task logic here
//        \Log::info("Processing  items page number : {$this->num}");
        \Log::info("Processing filling all items ");

    }

}
