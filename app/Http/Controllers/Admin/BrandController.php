<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Traits\IntegrateTrait;
use App\Jobs\storeBrandsJob;
use App\Models\Brand;
use App\Models\Item;
use App\Models\PriceGroupPricing;
use App\Models\PricingList;
use App\Services\ItemsService;
use App\Services\PricingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class BrandController extends Controller
{
    use IntegrateTrait;

    protected $itemsService, $pricingService;

    public function __construct(ItemsService $itemsService, PricingService $pricingService)
    {
        $this->itemsService = $itemsService;
        $this->pricingService = $pricingService;
    }


    public function listItems(Request $request, $num = null)
    {
        $token = $this->autoMaticTokenForJob();
        Log::info("Starting page {$num}");
        $total = 0;
        if (!$token) {
            throw new \Exception('No access token returned');
        }
        $response = $this->itemsService->responseOfItemsForJob($token, $num);
        if (!$response) {
            return;
        }
        $itemsData = $response->json('data', []);
        Log::info("Page {$num} raw count: " . count($itemsData));

        if (empty($itemsData)) {
            Log::warning("EMPTY RESPONSE PAGE {$num}");
            return;
        }
        $batch = [];
        $batch = $this->itemsService->itemsDataForJobs($itemsData, $total, $batch);
        $collection = collect($batch);
        if ($collection->isEmpty()) {
            Log::warning("Batch is empty, skipping insert");
            return;
        }
        $this->itemsService->collectionOfItemsForJob($collection);
        Log::info("Page {$num} total processed: " . count($itemsData));
        unset($response, $itemsData, $batch);
        return response()->json([
            'message' => 'items synced successfully'
        ]);
    }


    public
    function listBrands(Request $request)
    {


        $response2 = $this->autoMaticTokenForJob();
        $brands = Http::withToken($response2->json()['access_token'])
            ->get(config('app.API_URL') . '/brands');
        if ($brands->status() === 401) {
            return $this->error(null, 'Token expired or invalid', 401);
        }
        if (!isset($brands->json()['data'])) {
            return $this->notFoundResponse();
        }
        $brandsData = $brands->json()['data'];
        $this->itemsService->syncBrandsInDbForJob($brandsData);
        return response()->json([
            'message' => 'Brands synced successfully'
        ]);

    }


//GET/v1/pricing/brand/{brand_id}/pricegroup/{pricegroup_id}?page={page}
    public
    function listPriceGroups(Request $request)
    {
        $response2 = $this->autoMaticTokenForJob();
        $brands = Http::withToken($response2->json()['access_token'])
            ->get(config('app.API_URL') . '/brands');

        if ($brands->status() === 401) {
            return $this->error(null, 'Token expired or invalid', 401);
        }

        if (!isset($brands->json()['data'])) {
            return $this->notFoundResponse();
        }
        $listData = $brands->json()['data'];
        $this->pricingService->pricingServiceListForJob($listData);
        return response()->json([
            'message' => 'pricings  synced successfully'
        ]);

    }


    public
    function pricingItems($num)
    {
        $token = $this->autoMaticTokenForJob();
        Log::info("Starting page {$num}");
        $response = $this->pricingService->responsePricingList($token , $num);
        $itemsData = $response->json('data', []);
        $batch = [];
        $batch = $this->pricingService->batchOfPricngListFuncOnjobs($itemsData, $batch);
        if (empty($batch)) {
            return;
        }
        $this->pricingService->DBPricingslistSyncForJob($batch);
        unset($batch, $itemsData);
        return response()->json([
            'message' => 'prices synced successfully'
        ]);
    }




    public
    function storeBrands(Request $request)
    {
        $data = 'Sample Data';
        storeBrandsJob::dispatch($data);
    }


//GET/v1/pricing?page={page}

}
