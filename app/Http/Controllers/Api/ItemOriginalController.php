<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Traits\IntegrateTrait;
use App\Models\Brand;
use App\Models\Item;
use App\Models\PricingList;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use function Ramsey\Collection\Map\keys;
use App\Services\ApiService;
use App\Services\PricingService;

class ItemOriginalController  extends Controller
{
    use IntegrateTrait;
    public function index(Request $request)
    {

        if (empty($request->brand_id)) {
            return $this->notFoundResponse();
        }

        /*************************local items query*************/
        $query = Item::where('type' , 'local')->whereHas('brand', function ($q) use ($request) {
            $q->where(['status' => 1])->whereIn('code', $request->brand_id);
        });

        /*****************end  local items query************/

        /*******************api items query **********/

        $pricingQuery = DB::table('pricing_lists')
            ->select(
                'item_code',
                DB::raw('SUM(price) as total_price'),
//                DB::raw('SUM(purchase_cost) as total_purchase_cost')
                DB::raw('MIN(purchase_cost) as purchase_cost')
            )
            ->groupBy('item_code');

        $query2 = Item::query()
            ->join('brands', 'items.brand_code', '=', 'brands.code')
            ->joinSub($pricingQuery, 'pricing', function ($join) {
                $join->on('items.code', '=', 'pricing.item_code');
            })
//            ->joinSub($pricingQuery, 'pricing', function ($join) {
//                $join->on('items.code', '=', 'pricing.item_code');
//            })

            ->where('brands.status', 1)
            ->where('brands.type', 'api')
            ->whereIn('items.brand_code', $request->brand_id);

        /**************end api items query**********/



        if ($request->price_from) {
            $query->where('price', '>=', $request->price_from);

            $query2->whereRaw(
                '(COALESCE(pricing.total_price,0) + COALESCE(pricing.purchase_cost,0)) >= ?',
                [$request->price_from]
            );
        }

        if ($request->price_to) {
            $query->where('price', '<=', $request->price_to);

            $query2->whereRaw(
                '(COALESCE(pricing.total_price,0) + COALESCE(pricing.purchase_cost,0)) <= ?',
                [$request->price_to]
            );
        }


        if ($request->search) {
            $query->where("product_name", "like", "%" . $request->search . "%");

            $query2->where("product_name", "like", "%" . $request->search . "%");
        }






        $dbItems = $query->select('id',"code" ,'product_name', 'thumbnail',
            'part_description',
            'category', 'subcategory',
            'type',
            'price', 'brand_id', 'brand_code')->get()->toArray();


        $data = $query2->select(
            'items.*',
            'brands.name as brand_name',
            DB::raw('COALESCE(pricing.total_price,0) as total_price'),
            DB::raw('COALESCE(pricing.purchase_cost,0) as purchase_cost')
        )->get();

        $apIdataArray = $data->toArray();
        $all = array_merge($apIdataArray, $dbItems);


        if ((empty($apIdataArray) && empty($dbItems))) {
            return $this->notFoundResponse();
        }

//        return  $this->success(
//            collect($all)->map(function ($item) {
//                $extra = [];
//                if ($item['type'] == 'api') {
//                    $extra = ["price" => $item['total_price'] ?? null,
//                        'purchase_cost' => $item['purchase_cost'] ?? null,
//                        'total_price' => $item['total_price'] + $item['purchase_cost']];
//                } elseif ($item['type'] == 'local') {
//                    $extra = ["price" => $item['price'] ?? null,
//                        'purchase_cost' => 0,
//                        'total_price' => $item['price']];
//                }
//
//                $res = array_merge($extra, [
//
//
//                    'id' => $item['id'] ?? null,
//                    "product_name" => $item['product_name'] ?? null,
//                    "thumbnail" => $item['thumbnail'] ?? '',
//                    "part_description" => $item['part_description'] ?? null,
//                    "category" => $item['category'] ?? null,
//                    "subcategory" => $item['subcategory'] ?? null,
//                    'type' => $item['type'] ?? null,
////                    "price_group_id" => $item['price_group_id'] ?? null,
////                    "price_group" => $item['price_group'] ?? null,
//                    "brand_code" => $item['brand_code'] ?? null,
//
//                ]);
//
//                return $res;
//
//            }), 'success', 200);


        $result = collect($all)->map(function ($item) {

            if ($item['type'] == 'api') {
                $price = $item['total_price'] ?? 0;
                $purchase = $item['purchase_cost'] ?? 0;
            } else {
                $price = $item['price'] ?? 0;
                $purchase = 0;
            }

            return [
                'id' => $item['code'] ?? null,
                'product_name' => $item['product_name'] ?? null,
                'thumbnail' => $item['thumbnail'] ?? '',
                'part_description' => $item['part_description'] ?? null,
                'category' => $item['category'] ?? null,
                'subcategory' => $item['subcategory'] ?? null,
                'type' => $item['type'] ?? null,
                'brand_code' => $item['brand_code'] ?? null,
                'price' => $price,
                'purchase_cost' => $purchase,
                'total_price' => $price + $purchase,
            ];
        });

        $perPage = $request->per_page ??  20;
        $page = LengthAwarePaginator::resolveCurrentPage();

        $paginated = new LengthAwarePaginator(
            $result->forPage($page, $perPage)->values(),
            $result->count(),
            $perPage,
            $page,
            [
                'path' => request()->url(),
                'query' => request()->query(),
            ]
        );

        return $this->success($paginated, 'success', 200);
    }


//    public function index(Request $request)
//    {
//        if (empty($request->brand_id)) {
//            return $this->notFoundResponse();
//        }
//
//        /*************************local items query*************/
//        $query = Item::where('type' , 'local')->whereHas('brand', function ($q) use ($request) {
//            $q->where(['status' => 1])->whereIn('code', $request->brand_id);
//        });
//
//        /*****************end  local items query************/
//
//        /*******************api items query **********/
//        $query2 = Item::join('brands', 'items.brand_code', '=', 'brands.code')
//            ->join('pricing_lists', 'items.code', '=', 'pricing_lists.item_code')
//            ->where('brands.status', 1)
//            ->where('brands.type', 'api')
////            ->where('items.type' , 'api')
//            ->whereIn('items.brand_code', $request->brand_id)
//            ->groupBy('items.id');
//
//        /**************end api items query**********/
//
//
//        if ($request->price_from) {
//            $query->where("price", ">=", $request->price_from);
//
//            $query2->havingRaw(
//                '(SUM(pricing_lists.price) + SUM(pricing_lists.purchase_cost)) >= ?',
//                [$request->price_from]
//            );
//
//        }
//        if ($request->price_to) {
//            $query->where("price", "<=", $request->price_to);
//            $query2->havingRaw(
//                '(SUM(pricing_lists.price) + SUM(pricing_lists.purchase_cost)) <= ?',
//                [$request->price_to]
//            );
//        }
//        if ($request->search) {
//            $query->where("product_name", "like", "%" . $request->search . "%");
//
//            $query2->where("product_name", "like", "%" . $request->search . "%");
//        }
//
//
//        $dbItems = $query->select('id', 'product_name', 'thumbnail',
//            'part_description',
//            'category', 'subcategory',
//            'type',
//            'price', 'brand_id', 'brand_code')->get()->toArray();
//
//        $data = $query2->selectRaw("
//                items.*,
//                brands.name as brand_name,
//                SUM(pricing_lists.price) as total_price,
//                SUM(pricing_lists.purchase_cost) as total_purchase_cost"
//        )
////            ->groupBy('items.id')
//            ->get();
//
//
//        $apIdataArray = $data->toArray();
//        $all = array_merge($apIdataArray, $dbItems);
//
//
//
//        if ((empty($apIdataArray) && empty($dbItems))) {
//            return $this->notFoundResponse();
//        }
//
//        return $this->success(
//            collect($all)->map(function ($item) {
//                $extra = [];
//                if ($item['type'] == 'api') {
//                    $extra = ["price" => $item['total_price'] ?? null,
//                        'purchase_cost' => $item['total_purchase_cost'] ?? null,
//                        'total_price' => $item['total_price'] + $item['purchase_cost']];
//                } elseif ($item['type'] == 'local') {
//                    $extra = ["price" => $item['price'] ?? null,
//                        'purchase_cost' => 0,
//                        'total_price' => $item['price']];
//                }
//
//
//                $res = array_merge($extra, [
//
//
//                    'id' => $item['id'] ?? null,
//                    "product_name" => $item['product_name'] ?? null,
//                    "thumbnail" => $item['thumbnail'] ?? '',
//                    "part_description" => $item['part_description'] ?? null,
//                    "category" => $item['category'] ?? null,
//                    "subcategory" => $item['subcategory'] ?? null,
//                    'type' => $item['type'] ?? null,
////                    "price_group_id" => $item['price_group_id'] ?? null,
////                    "price_group" => $item['price_group'] ?? null,
//                    "brand_code" => $item['brand_code'] ?? null,
//
//                ]);
//                return $res;
//
//
//            }), 'success', 200);
//
//    }


    public function show(Request $request) //here
    {
        if ($request->type == 'api') {
            $data = $this->getReturnedData($request, '/items/' . $request->item_id, 'get');
            $price = $this->getReturnedData($request, '/pricing/' . $request->item_id, 'get');

            if ($price['data']['attributes']['can_purchase']) {
                $allPrice = $price['data']['attributes']['pricelists'][0]['price'] +
                    $price['data']['attributes']['pricelists'][0]['price'] +
                    $price['data']['attributes']['purchase_cost'];
            } else {
                $allPrice = 'N/A';
            }

            if ($data->status() === 401) {
                return $this->error(null, 'Token expired or invalid', 401);
            }
            if (!isset($data->json()['data'])) {
                return $this->notFoundResponse();
            }
            $all = collect($data->json())->map(function ($item) use ($allPrice) {
                return [
                    "product_name" => $item['attributes']['product_name'],
                    "part_number" => $item['attributes']['part_number'],
                    "mfr_part_number" => $item['attributes']['mfr_part_number'],
                    "part_description" => $item['attributes']['part_description'],
                    "category" => $item['attributes']['category'],
                    "subcategory" => $item['attributes']['subcategory'],
                    'type' => 'from America',
                    'price' => is_numeric($allPrice) ? round($allPrice, 2) : 'N/A', //whole price
//                    "price_group_id" => $item['attributes']['price_group_id'],
//                    "price_group" => $item['attributes']['price_group'],
                ];
            });
            return $this->success($all['data'], 'success', 200);
        } elseif ($request->type == 'local') {
            $data = Item::whereHas('brand', function ($q) {
                $q->where(['status' => 1, 'type' => 'local']);
            })->find($request->item_id);
            if (!$data) {
                return $this->notFoundResponse();
            }
            return $this->success($data, 'success', 200);

        }
    }


    public function itemsData(Request $request)
    {
        $data = $this->getReturnedData($request, '/items/data?page=' . $request->page, 'get');
        if ($data->status() === 401) {
            return $this->error(null, 'Token expired or invalid', 401);
        }
        if (!isset($data->json()['data'])) {
            return $this->notFoundResponse();
        }
        return $this->success($data->json(), 'success', 200);
    }


    public function getItemsBrand(Request $request)
    {
        $data = $this->getReturnedData($request, '/items/brand/' . $request->brand_id . '?page=' . $request->page, 'get');
        if ($data->status() === 401) {
            return $this->error(null, 'Token expired or invalid', 401);
        }
        if (!isset($data->json()['data'])) {
            return $this->notFoundResponse();
        }
        return $this->success($data->json(), 'success', 200);
    }


    public function getItemsFromArrayOfBrands(Request $request)
    {
        $brands = [];
        $brandsAllResponse = [];
        if (!empty($request->brand_id)) {
            foreach ($request->brand_id as $brand) {
                $item = $this->getReturnedData($request, '/items/brand/' . (int)$brand . '?page=' . $request->page, 'get');
                $brands[] = $item->json()['data'];
                $brandsAllResponse[] = $item;
            }
        }
        $data = $brands;
        if ($brandsAllResponse[0]->status() === 401) {
            return $this->error(null, 'Token expired or invalid', 401);
        }
        if (!isset($brandsAllResponse[0]->json()['data'])) {
            return $this->notFoundResponse();
        }

        return $this->success($data, 'success', 200);
    }


    public function getItemsOfBrandAndPriceGroup(Request $request)
    {
        $data = $this->getReturnedData($request, '/items/brand/' . $request->brand_id . '/pricegroup/' . $request->pricegroup_id . '?page=' . $request->page, 'get');
        if ($data->status() === 401) {
            return $this->error(null, 'Token expired or invalid', 401);
        }
        if (!isset($data->json()['data'])) {
            return $this->notFoundResponse();
        }
        return $this->success($data->json(), 'success', 200);
    }


///v1/items/updates?page={page}&days={days}
    public function recentlyUpdatedItems(Request $request)
    {
        $data = $this->getReturnedData($request, '/items/updates' . '?page=' . $request->page . '&days=' . $request->days, 'get');
        if ($data->status() === 401) {
            return $this->error(null, 'Token expired or invalid', 401);
        }
        if (!isset($data->json()['data'])) {
            return $this->notFoundResponse();
        }
        return $this->success($data->json(), 'success', 200);
    }

///v1/items/fitment?page={page}
    public function itemsFitment(Request $request)
    {
        $data = $this->getReturnedData($request, '/items/fitment' . '?page=' . $request->page, 'get');
        if ($data->status() === 401) {
            return $this->error(null, 'Token expired or invalid', 401);
        }
        if (!isset($data->json()['data'])) {
            return $this->notFoundResponse();
        }
        return $this->success($data->json(), 'success', 200);
    }


    //items/fitment/brand/{brand_id}?page={page}
    public function itemsFitmentByBrand(Request $request)
    {
        $data = $this->getReturnedData($request, '/items/fitment/brand/' . $request->brand_id . '?page=' . $request->page, 'get');
        if ($data->status() === 401) {
            return $this->error(null, 'Token expired or invalid', 401);
        }
        if (!isset($data->json()['data'])) {
            return $this->notFoundResponse();
        }
        return $this->success($data->json(), 'success', 200);
    }


///v1/items/fitment/brand/{brand_id}/pricegroup/{pricegroup_id}?page={page}
    public function itemsFitmentByBrandAndGroupPrice(Request $request)
    {
        $data = $this->getReturnedData($request, '/items/fitment/brand/' . $request->brand_id . "/pricegroup/" . $request->price_group_id . '?page=' . $request->page, 'get');
        if ($data->status() === 401) {
            return $this->error(null, 'Token expired or invalid', 401);
        }
        if (!isset($data->json()['data'])) {
            return $this->notFoundResponse();
        }
        return $this->success($data->json(), 'success', 200);
    }


    public function fitmentItemData(Request $request)
    {
//        v1/items/fitment/{item_id}
        $data = $this->getReturnedData($request, '/items/fitment/' . $request->item_id, 'get');
        if ($data->status() === 401) {
            return $this->error(null, 'Token expired or invalid', 401);
        }
        if (!isset($data->json()['data'])) {
            return $this->notFoundResponse();
        }
        return $this->success($data->json(), 'success', 200);
    }

}






//product_name
//part_description  //add this
//type
//price



