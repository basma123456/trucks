<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\BrandPriceGroupRequest;
use App\Http\Requests\Api\BrandRequest;
use App\Http\Traits\IntegrateTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class BrandController extends Controller
{
    use IntegrateTrait;

    public function index(Request $request)
    {
        $brands = $this->getReturnedData($request, '/brands', 'get');
        return $this->success($brands->json()['data'], 'success', 200);
    }

    public function show(BrandRequest $brandRequest)
    {
        $brandRequest->validated();
        $brand = $this->getReturnedData($brandRequest, '/brands/' . $brandRequest->brand_id, 'get');
        return $this->success($brand->json()['data'], 'success', 200);
    }

//GET/v1/brands/{brand_id}/pricegroup/{pricegroup_id}
    public function getDetailsOfPriceGroup(BrandPriceGroupRequest $brandPriceGroupRequest)
    {
        $brandPriceGroupRequest->validated();
        $brand = $this->getReturnedData($brandPriceGroupRequest, '/brands/' . $brandPriceGroupRequest->brand_id . '/pricegroup/' . $brandPriceGroupRequest->pricegroup_id, 'get');
        return $this->success($brand->json()['data'], 'success', 200);
    }
}
