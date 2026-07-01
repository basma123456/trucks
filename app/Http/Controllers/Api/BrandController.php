<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\BrandInventoryRequest;
use App\Http\Requests\Api\BrandPriceGroupRequest;
use App\Http\Requests\Api\BrandRequest;
use App\Http\Traits\IntegrateTrait;
use App\Models\Brand;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Http;

class BrandController extends Controller
{
    use IntegrateTrait;

    public function index(Request $request)
    {
        $brands = $this->getReturnedData($request, '/brands', 'get');

        // If response is HTTP response
        if ($brands instanceof \Illuminate\Http\Client\Response) {

            if ($brands->status() === 401) {
                return $this->error(null, 'Token expired or invalid', 401);
            }

            $responseData = $brands->json();

        } else {

            // Collection or array fallback
            $responseData = is_array($brands)
                ? $brands
                : $brands->toArray();
        }

        if (!isset($responseData['data'])) {
            return $this->notFoundResponse();
        }

        $brandsInDb = Brand::where(['status'=> 1 , 'type' => 'api'])
            ->pluck('code')
            ->toArray();


        $brandsLocal = Brand::where(['status' =>1 , 'type' => 'local'])->get();


        $all = collect($responseData['data'])->filter(function ($item) use ($brandsInDb) {
            return in_array($item['id'], $brandsInDb);
        });

        return $this->success(array_merge($all->values()->toArray() , $brandsLocal->toArray()), 'success', 200);
    }


    public function show(BrandRequest $brandRequest)
    {
        $brandRequest->validated();
        $brand = $this->getReturnedData($brandRequest, '/brands/' . $brandRequest->brand_id, 'get');
        if ($brand->status() === 401) {
            return $this->error(null, 'Token expired or invalid', 401);
        }

        if (!isset($brand->json()['data'])) {
            return $this->notFoundResponse();
        }

        return $this->success($brand->json(), 'success', 200);
    }

//GET/v1/brands/{brand_id}/pricegroup/{pricegroup_id
//}
    public function getDetailsOfPriceGroup(BrandPriceGroupRequest $brandPriceGroupRequest)
    {
        $brandPriceGroupRequest->validated();
        $brand = $this->getReturnedData($brandPriceGroupRequest, '/brands/' . $brandPriceGroupRequest->brand_id . '/pricegroup/' . $brandPriceGroupRequest->pricegroup_id, 'get');
        if ($brand->status() === 401) {
            return $this->error(null, 'Token expired or invalid', 401);
        }

        if (!isset($brand->json()['data'])) {
            return $this->notFoundResponse();
        }
        return $this->success($brand->json(), 'success', 200);
    }


}
