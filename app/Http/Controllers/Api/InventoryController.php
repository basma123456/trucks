<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\BrandInventoryRequest;
use App\Http\Requests\Api\getRecentlyUpdatedInventoryRequest;
use App\Http\Requests\Api\PriceGroupInventoryRequest;
use App\Http\Traits\IntegrateTrait;
use Illuminate\Http\Request;

class InventoryController extends Controller
{
    use IntegrateTrait;

    public function getAllInventory(Request $request)
    {
        $data = $this->getReturnedData($request, '/inventory', 'get');
        if (!isset($data->json()['data'])) {
            return $this->notFoundResponse('no data');
        }
        return $this->success($data->json(), 'success', 200);
    }


    public function getItemInventory(Request $request)
    {
        $data = $this->getReturnedData($request, '/inventory/' . $request->item_id, 'get');
        if (!isset($data->json()['data'])) {
            return $this->notFoundResponse('no data');
        }
        return $this->success($data->json(), 'success', 200);
    }

    public function getBrandInventory(BrandInventoryRequest $brandInventoryRequest)
    {
        $brandInventoryRequest->validated();
        $brand = $this->getReturnedData($brandInventoryRequest, '/inventory/brand/' . $brandInventoryRequest->brand_id . "?page=" . $brandInventoryRequest->page, 'get');
        if ($brand->status() === 401) {
            return $this->error(null, 'Token expired or invalid', 401);
        }

        if (!isset($brand->json()['data'])) {
            return $this->notFoundResponse();
        }
        return $this->success($brand->json(), 'success', 200);
    }


///v1/inventory/brand/{brand_id}/pricegroup/{pricegroup_id}?page={page}
    public function getPriceGroupInventory(PriceGroupInventoryRequest $priceGroupInventoryRequest)
    {
        $priceGroupInventoryRequest->validated();
        $brand = $this->getReturnedData($priceGroupInventoryRequest, '/inventory/brand/' . $priceGroupInventoryRequest->brand_id  . '/pricegroup/'. $priceGroupInventoryRequest->price_group . "?page=" . $priceGroupInventoryRequest->page, 'get');
        if ($brand->status() === 401) {
            return $this->error(null, 'Token expired or invalid', 401);
        }

        if (!isset($brand->json()['data'])) {
            return $this->notFoundResponse();
        }
        return $this->success($brand->json(), 'success', 200);
    }


    public function getRecentlyUpdatedInventory(getRecentlyUpdatedInventoryRequest $getRecentlyUpdatedInventoryRequest)
    {
        $getRecentlyUpdatedInventoryRequest->validated();
        $brand = $this->getReturnedData($getRecentlyUpdatedInventoryRequest, '/inventory/updates?page=' . $getRecentlyUpdatedInventoryRequest->page  . '&minutes='. $getRecentlyUpdatedInventoryRequest->minutes , 'get');
        if ($brand->status() === 401) {
            return $this->error(null, 'Token expired or invalid', 401);
        }
        if (!isset($brand->json()['data'])) {
            return $this->notFoundResponse();
        }
        return $this->success($brand->json(), 'success', 200);

    }
}



