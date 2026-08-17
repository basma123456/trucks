<?php


namespace App\Services;


use App\Models\Brand;
use App\Models\Item;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ItemsService
{
    public function items($request, $query, $query2)
    {

        if ($request->price_from) {
            $query->where('price', '>=', $request->price_from);

            $query2->whereRaw(
                '(pricing_lists.price  + COALESCE(pricing_lists.purchase_cost,0)) >= ?',
                [$request->price_from]
            );
        }

        if ($request->price_to) {
            $query->where('price', '<=', $request->price_to);

            $query2->whereRaw(
                '(pricing_lists.price  + COALESCE(pricing_lists.purchase_cost,0)) <= ?',
                [$request->price_to]
            );
        }


        if ($request->search) {
            $query->where("product_name", "like", "%" . $request->search . "%");

            $query2->where("product_name", "like", "%" . $request->search . "%");
        }


        $dbItems = $query->select(
            'items.*',
            'brand_id as brand_name',
            DB::raw('price as total_price'),
            DB::raw('0 as purchase_cost')
        );


        $data = $query2->select(
            'items.*',
            'brands.name as brand_name',
            DB::raw('SUM(pricing_lists.price) as total_price'),
            DB::raw('ANY_VALUE(pricing_lists.purchase_cost) as purchase_cost')
        );

        $apIdataArray = $data;
        $all = $dbItems->unionAll($data)->paginate(request('per_page') ?? 20)->withQueryString();
        return ['all' => $all, 'apIdataArray' => $apIdataArray, 'dbItems' => $dbItems];

    }


    /******************************jobs part**************/
    public function itemsDataForJobs($itemsData, $total, $batch)
    {
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

        return $batch;
    }


    public function collectionOfItemsForJob($collection)
    {
      return  $collection->chunk(500)->each(function ($chunk) {

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

    }


    public function responseOfItemsForJob($token , $num)
    {
        try {
            $response = Http::withToken($token)
                ->connectTimeout(60)
                ->timeout(300)
                ->retry(5, 5000)
                ->get(config('app.API_URL') . '/items?page=' . $num);
            return $response;
        } catch (\Exception $e) {

            Log::error("Page {$num} failed", [
                'message' => $e->getMessage()
            ]);
            return null;
        }

    }


    public function syncBrandsInDbForJob($brandsData)
    {
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
    }



    public function localItemsQuery($brandId)
    {
        return Item::query()
            ->join('brands', 'items.brand_id', '=', 'brands.id')
            ->where('items.type', 'local')
            ->where('brands.status', 1)
            ->whereIn('brands.code', $brandId);
    }

    public function pricingQuery()
    {
        return DB::table('pricing_lists')
            ->select(
                'item_code',
                DB::raw('SUM(price) as total_price'),
                DB::raw('MIN(purchase_cost) as purchase_cost')
            )
            ->groupBy('item_code');
    }


    public function apiItemsQuery($pricingQuery, $brandId)
    {
//        return Item::query()
//            ->join('brands', 'items.brand_code', '=', 'brands.code')
//            ->joinSub($pricingQuery, 'pricing', function ($join) {
//                $join->on('items.code', '=', 'pricing_lists.item_code');
//            })
//            ->where('brands.status', 1)
//            ->where('brands.type', 'api')
//            ->whereIn('items.brand_code', $brandId);


        $query2 = Item::join('brands', 'items.brand_code', '=', 'brands.code')
            ->join('pricing_lists', 'items.code', '=', 'pricing_lists.item_code')
            ->where('brands.status', 1)
//            ->where('brands.type', 'api')
            ->where('items.type', 'api')
            ->whereIn('items.brand_code', $brandId)
            ->groupBy('items.code');

        return $query2;
    }
}











