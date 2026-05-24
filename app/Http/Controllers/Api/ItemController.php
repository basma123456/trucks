<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Traits\IntegrateTrait;
use Illuminate\Http\Request;

class ItemController extends Controller
{
    use IntegrateTrait;

    public function index(Request $request)
    {
        $data = $this->getReturnedData($request, '/items', 'get');
        if ($data->status() === 401) {
            return $this->error(null, 'Token expired or invalid', 401);
        }
        if (!isset($data->json()['data'])) {
            return $this->notFoundResponse();
        }
        return $this->success($data->json(), 'success', 200);
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
        $data = $this->getReturnedData($request, '/items/fitment/' . $request->item_id , 'get');
        if ($data->status() === 401) {
            return $this->error(null, 'Token expired or invalid', 401);
        }
        if (!isset($data->json()['data'])) {
            return $this->notFoundResponse();
        }
        return $this->success($data->json(), 'success', 200);
    }

}
