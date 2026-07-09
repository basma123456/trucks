<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ItemResource;
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
use App\Services\ItemsService;
use App\Http\Resources\ItemSinglePageResource;
class ItemController extends Controller
{
    use IntegrateTrait;
    protected $itemsService;

    public function __construct(ItemsService $itemsService)
    {
        $this->itemsService = $itemsService;
    }

    public function index(Request $request)
    {
        if (empty($request->brand_id)) {
            return $this->notFoundResponse();
        }
        /*******************db items query **********/
        $query = $this->itemsService->localItemsQuery($request->brand_id);
        /*******************db items query **********/

        /*******************api items query **********/
        $pricingQuery = $this->itemsService->pricingQuery();
        $query2 = $this->itemsService->apiItemsQuery($pricingQuery, $request->brand_id);
        /**************end api items query**********/

        $allItems = $this->itemsService->items($request, $query, $query2);
        $all = $allItems['all'];
        if ((empty($allItems['apIdataArray']) && empty($allItems['dbItems']))) {
            return $this->notFoundResponse();
        }
        $result = ItemResource::collection($all);
        $paginated = $this->itemsService->paginationOfItems($result, $request->per_page);
        return $this->success($paginated);
    }


    public function show(Request $request)
    {

            $data = Item::where(['code' => $request->item_id , 'type' => $request->type])->first();
            if (!$data) {
                return $this->notFoundResponse();
            }
            return $this->success(new ItemSinglePageResource($data), 'success', 200);
    }


//    public function show(Request $request)
//    {
//        if ($request->type == 'api') {
//            $data = $this->getReturnedData($request, '/items/' . $request->item_id, 'get');
//            $price = $this->getReturnedData($request, '/pricing/' . $request->item_id, 'get');
//
//            if (isset($price['data']) && $price['data']['attributes']['can_purchase']) {
//                $allPrice = $price['data']['attributes']['pricelists'][0]['price'] +
//                    $price['data']['attributes']['pricelists'][0]['price'] +
//                    $price['data']['attributes']['purchase_cost'];
//            } else {
//                $allPrice = 'N/A';
//            }
//
//            if ($data->status() === 401) {
//                return $this->error(null, 'Token expired or invalid', 401);
//            }
//            if (!isset($data->json()['data'])) {
//                return $this->notFoundResponse();
//            }
//            $allData = [ 'allData' => $data->json()['data']  , 'allPrice' => $allPrice];
//            return $this->success(new ItemSinglePageResource($allData), 'success', 200);
//
//        } elseif ($request->type == 'local') {
//            $data = Item::whereHas('brand', function ($q) {
//                $q->where(['status' => 1]);
//            })->where('code' , $request->item_id)->first();
//            if (!$data) {
//                return $this->notFoundResponse();
//            }
//            $allData = [ 'allData' => $data , 'allPrice' => 0];
//            return $this->success(new ItemSinglePageResource($allData), 'success', 200);
//        }
//    }


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



