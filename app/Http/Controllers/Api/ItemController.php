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
use Illuminate\Support\Facades\Cache;
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

        $brandIds = (array) $request->brand_id;
        sort($brandIds);

        $cacheKey = 'items_' . md5(json_encode([
                'brand_id'   => $brandIds,
                'search'     => $request->search,
                'price_from' => $request->price_from,
                'price_to'   => $request->price_to,
                'page'       => $request->page ?? 1,
                'per_page'   => $request->per_page ?? 20,
            ]));

        $response = Cache::remember($cacheKey, now()->addMinutes(10), function () use ($request, $brandIds) {

            $query = $this->itemsService->localItemsQuery($brandIds);

            $pricingQuery = $this->itemsService->pricingQuery();

            $query2 = $this->itemsService->apiItemsQuery($pricingQuery, $brandIds);

            $paginator = $this->itemsService->items($request, $query, $query2)['all'];

            return [
                'success' => true,
                'message' => null,
                'data' => ItemResource::collection($paginator->items())->resolve(),
                'pagination' => [
                    'current_page'  => $paginator->currentPage(),
                    'last_page'     => $paginator->lastPage(),
                    'per_page'      => $paginator->perPage(),
                    'total'         => $paginator->total(),
                    'from'          => $paginator->firstItem(),
                    'to'            => $paginator->lastItem(),
                    'next_page_url' => $paginator->nextPageUrl(),
                    'prev_page_url' => $paginator->previousPageUrl(),
                ],
            ];
        });

        return response()->json($response);
    }



    public function show(Request $request)
    {

        $data = Item::leftJoin('pricing_lists' , 'items.code' , '=' ,'pricing_lists.item_code')
            ->where(['code' => $request->item_id, 'type' => $request->type])
            ->select('items.*' , DB::raw('SUM(pricing_lists.price) as total_price') , DB::raw('ANY_VALUE(pricing_lists.purchase_cost) as purchase_cost'))
            ->groupBy('items.code')->first();


        if (!$data) {
            return $this->notFoundResponse();
        }
        return $this->success(new ItemSinglePageResource($data), 'success', 200);
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



