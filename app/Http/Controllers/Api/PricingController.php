<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Traits\IntegrateTrait;
use Illuminate\Http\Request;

class PricingController extends Controller
{
//pricing?page={page}

    use IntegrateTrait;

    public function index(Request $request)
    {
        $data = $this->getReturnedData($request, '/pricing?page=' . $request->page, 'get');
        if ($data->status() === 401) {
            return $this->error(null, 'Token expired or invalid', 401);
        }
        if (!isset($data->json()['data'])) {
//        if (!isset($data->json()['data'])) {
            return $this->notFoundResponse();
        }
        return $this->success($data->json(), 'success', 200);
    }


    public function getPricingItem(Request $request)
    {
        $data = $this->getReturnedData($request, '/pricing/' . $request->item_id, 'get');
        if ($data->status() === 401) {
            return $this->error(null, 'Token expired or invalid', 401);
        }
        if (!isset($data->json()['data'])) {
//        if (!isset($data->json()['data'])) {
            return $this->notFoundResponse();
        }
        return $this->success($data->json(), 'success', 200);

    }


    public function getPricingBrand(Request $request)
    {
        $data = $this->getReturnedData($request, '/pricing/brand/' . $request->brand_id . "?page=" . $request->page, 'get');
        if ($data->status() === 401) {
            return $this->error(null, 'Token expired or invalid', 401);
        }
        if (empty($data->json()['data'])) {
            return $this->notFoundResponse("No pricing exists for this brand");
        }
        return $this->success($data->json(), 'success', 200);
    }


//pricing/brand/{brand_id}/pricegroup/{pricegroup_id}?page={page}
    public function getPricingBrandByGroup(Request $request)
    {
        $data = $this->getReturnedData($request, '/pricing/brand/' . $request->brand_id . "?page=" . $request->page, 'get');
        if ($data->status() === 401) {
            return $this->error(null, 'Token expired or invalid', 401);
        }
        if (empty($data->json()['data'])) {
            return $this->notFoundResponse("No pricing exists for this brand");
        }
        return $this->success($data->json(), 'success', 200);
    }



}
