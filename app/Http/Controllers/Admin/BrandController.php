<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Traits\IntegrateTrait;
use App\Jobs\storeBrandsJob;
use App\Models\Brand;
use App\Models\Item;
use App\Models\PriceGroupPricing;
use App\Models\PricingList;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class BrandController extends Controller
{
    use IntegrateTrait;

    public function listItems(Request $request, $num = null)
    {
        $token = Cache::remember('turn14_token', 300, function () {

            $response = Http::withoutVerifying()
                ->asForm()
                ->post(config('app.API_URL') . '/token', [
                    "grant_type" => "client_credentials",
                    "client_id" => config('app.client_id'),
                    "client_secret" => config('app.client_secret'),
                ]);

            if (!$response->successful()) {
                throw new \Exception('Token request failed: ');
            }

            return $response->json('access_token');
        });


        Log::info("Starting page {$num}");

//        Log::info('Token status: ' . $response->status());
//        Log::info('Token body: ' . $response->body());
        $total = 0;

//        foreach ($brandsData as $brand) {
//
//            Item::updateOrCreate(
//                [
//                    'code' => $brand['id'], // unique field
//
//                ],
//                [
//
//
//                    'product_name' => $brand['attributes']['product_name'] ?? null,
//                    'part_number' => $brand['attributes']['part_number'] ?? null,
//                    'category' => $brand['attributes']['category'] ?? null,
//                    'subcategory' => $brand['attributes']['subcategory'] ?? null,
//                    'brand_code' => $brand['attributes']['brand_id'] ?? null,
//                    'part_description' => $brand['attributes']['part_description'] ?? null,
//                    'price' => $brand['attributes']['price'] ?? null,
//                    'thumbnail' => $brand['attributes']['thumbnail'] ?? null,
//                    'price_group_id' => $brand['attributes']['price_group_id'] ?? null,
//                    'price_group' => $brand['attributes']['price_group'] ?? null,
//                    'type' => 'api',
//
//
//                ]
//            );
//        }

//        $token = $response2->json('access_token');

        if (!$token) {
            throw new \Exception('No access token returned');
        }
//        for ($num = 1; $num <= 764; $num++) {


        try {
            $response = Http::withToken($token)
                ->connectTimeout(60)
                ->timeout(300)
                ->retry(5, 5000)
                ->get(config('app.API_URL') . '/items?page=' . $num);
        } catch (\Exception $e) {

            Log::error("Page {$num} failed", [
                'message' => $e->getMessage()
            ]);
            return;
        }

        $itemsData = $response->json('data', []);
        Log::info("Page {$num} raw count: " . count($itemsData));

        if (empty($itemsData)) {
            Log::warning("EMPTY RESPONSE PAGE {$num}");
            return;
        }


        $batch = [];

        foreach ($itemsData as $item) {
            $total++;
            /******log info*********/

            $batch[] = ['code' => $item['id'],
                'product_name' => $item['attributes']['product_name'] ?? null,
                'part_number' => $item['attributes']['part_number'] ?? null,
                'category' => $item['attributes']['category'] ?? null,
                'subcategory' => $item['attributes']['subcategory'] ?? null,
                'brand_code' => $item['attributes']['brand_id'] ?? null,
                'part_description' => $item['attributes']['part_description'] ?? null,
                'price' => $item['attributes']['price'] ?? null,
                'thumbnail' => $item['attributes']['thumbnail'] ?? null,
                'price_group_id' => $item['attributes']['price_group_id'] ?? null,
                'price_group' => $item['attributes']['price_group'] ?? null,
                'type' => 'api',];
        }

// Insert in chunks (important)
//        collect($batch)->chunk(500)->each(function ($chunk) {
//            Item::upsert(
//                $chunk->toArray(),
//                ['code'],
//                [
//                    'product_name',
//                    'part_number',
//                    'category',
//                    'subcategory',
//                    'brand_code',
//                    'part_description',
//                    'price',
//                    'thumbnail',
//                    'price_group_id',
//                    'price_group',
//                    'type',
//                ]
//            );
//        });
//        collect($batch)
//            ->chunk(500)
//            ->each(function ($chunk) {
//
//                Item::upsert(
//                    $chunk->values()->all(),
//                    ['code'],
//                    [
//                        'product_name',
//                        'part_number',
//                        'category',
//                        'subcategory',
//                        'brand_code',
//                        'part_description',
//                        'price',
//                        'thumbnail',
//                        'price_group_id',
//                        'price_group',
//                        'type',
//                    ]
//                );
//
//                Log::info("Inserting chunk of " . $chunk->count() . " items");
//            });
//
        $collection = collect($batch);

        if ($collection->isEmpty()) {
            Log::warning("Batch is empty, skipping insert");
            return;
        }

        $collection->chunk(500)->each(function ($chunk) {

            $data = $chunk->values()->all();

            try {
                Item::upsert(
                    $data,
                    ['code'],
                    [
                        'product_name',
                        'part_number',
                        'category',
                        'subcategory',
                        'brand_code',
                        'part_description',
                        'price',
                        'thumbnail',
                        'price_group_id',
                        'price_group',
                        'type',
                    ]
                );

                Log::info("Inserted chunk: " . count($data));

            } catch (\Throwable $e) {
                Log::error("Chunk insert failed", [
                    'message' => $e->getMessage(),
                ]);
            }
        });


        Log::info("Page {$num} total processed: " . count($itemsData));

        unset($response, $itemsData, $batch);
//        }
//        for ($num = 1; $num <= 764; $num++) {
//
//            $response = Http::withToken($token)
//                ->get(config('app.API_URL') . '/items?page=' . $num);
//
//            $itemsData = $response->json('data', []);
//
//            foreach ($itemsData as $item) {
//
//                Item::updateOrCreate(
//                    [
//                        'code' => $item['id'],
//                    ],
//                    [
//                        'product_name' => $item['attributes']['product_name'] ?? null,
//                        'part_number' => $item['attributes']['part_number'] ?? null,
//                        'category' => $item['attributes']['category'] ?? null,
//                        'subcategory' => $item['attributes']['subcategory'] ?? null,
//                        'brand_code' => $item['attributes']['brand_id'] ?? null,
//                        'part_description' => $item['attributes']['part_description'] ?? null,
//                        'price' => $item['attributes']['price'] ?? null,
//                        'thumbnail' => $item['attributes']['thumbnail'] ?? null,
//                        'price_group_id' => $item['attributes']['price_group_id'] ?? null,
//                        'price_group' => $item['attributes']['price_group'] ?? null,
//                        'type' => 'api',
//                    ]
//                );
//            }
//
//            unset($response, $itemsData);
//        }


        return response()->json([
            'message' => 'items synced successfully'
        ]);

    }


    public
    function listBrands(Request $request)
    {


        $response2 = Http::withoutVerifying()->asForm()->post(config('app.API_URL') . '/token', [
            "grant_type" => "client_credentials",
            "client_id" => config('app.client_id'),
            "client_secret" => config('app.client_secret'),
        ]);

        //        $brands = $this->getReturnedData($request, '/brands', 'get');

        $brands = Http::withToken($response2->json()['access_token'])
            ->get(config('app.API_URL') . '/brands');
//dd($brands->json());
        if ($brands->status() === 401) {
            return $this->error(null, 'Token expired or invalid', 401);
        }


        if (!isset($brands->json()['data'])) {
            return $this->notFoundResponse();
        }


        $brandsData = $brands->json()['data'];


        foreach ($brandsData as $brand) {

            Brand::updateOrCreate(
                [
                    'code' => $brand['id'], // unique field
                ],
                [
                    'name' => $brand['attributes']['name'] ?? null,
                    'logo' => $brand['attributes']['logo'] ?? null,
                ]
            );
        }

        return response()->json([
            'message' => 'Brands synced successfully'
        ]);

    }


//GET/v1/pricing/brand/{brand_id}/pricegroup/{pricegroup_id}?page={page}
    public
    function listPriceGroups(Request $request)
    {


        $response2 = Http::withoutVerifying()->asForm()->post(config('app.API_URL') . '/token', [
            "grant_type" => "client_credentials",
            "client_id" => config('app.client_id'),
            "client_secret" => config('app.client_secret'),
        ]);

        //        $brands = $this->getReturnedData($request, '/brands', 'get');

        $brands = Http::withToken($response2->json()['access_token'])
            ->get(config('app.API_URL') . '/brands');
//dd($brands->json());
        if ($brands->status() === 401) {
            return $this->error(null, 'Token expired or invalid', 401);
        }


        if (!isset($brands->json()['data'])) {
            return $this->notFoundResponse();
        }


        $brandsData = $brands->json()['data'];


        foreach ($brandsData as $brand) {

            PriceGroupPricing::updateOrCreate(
                [
                    'code' => $brand['id'], // unique field
                ],
                [
                    'name' => $brand['attributes']['name'] ?? null,
                    'logo' => $brand['attributes']['logo'] ?? null,
                ]
            );
        }

        return response()->json([
            'message' => 'Brands synced successfully'
        ]);

    }


    public
    function pricingItems($num)
    {


//        $tokenResponse = Http::withoutVerifying()
//            ->asForm()
//            ->post(config('app.API_URL') . '/token', [
//                "grant_type" => "client_credentials",
//                "client_id" => config('app.client_id'),
//                "client_secret" => config('app.client_secret'),
//            ]);
//
//        $token = $tokenResponse->json('access_token');
        $token = Cache::remember('turn14_token', 300, function () {

            $response = Http::withoutVerifying()
                ->asForm()
                ->post(config('app.API_URL') . '/token', [
                    "grant_type" => "client_credentials",
                    "client_id" => config('app.client_id'),
                    "client_secret" => config('app.client_secret'),
                ]);

            if (!$response->successful()) {
                throw new \Exception('Token request failed: ' . $response->body());
            }

            return $response->json('access_token');
        });


        Log::info("Starting page {$num}");


        $response = Http::withToken($token)
            ->retry(3, 1000)
            ->timeout(300)
            ->connectTimeout(30)
            ->get(config('app.API_URL') . '/pricing?page=' . $num);

        $itemsData = $response->json('data', []);

        $batch = [];

        foreach ($itemsData as $item) {

            foreach ($item['attributes']['pricelists'] ?? [] as $priceList) {

                $batch[] = [
                    'item_code' => $item['id'],
                    'price_name' => $priceList['name'],
                    'price' => $priceList['price'] ?? 0,
                    'can_purchase' => $item['attributes']['can_purchase'] ?? false,
                    'purchase_cost' => $item['attributes']['purchase_cost'] ?? null,
                    'has_map' => $item['attributes']['has_map'] ?? false,
                ];
            }
        }

        // chunk DB inserts
        collect($batch)->chunk(500)->each(function ($smallChunk) {
            PricingList::upsert(
                $smallChunk->toArray(),
                ['item_code', 'price_name'],
                ['price', 'can_purchase', 'purchase_cost', 'has_map']
            );
        });

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
