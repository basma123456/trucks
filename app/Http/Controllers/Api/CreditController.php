<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\GetCreditMemoByPurchaseOrderRequest;
use App\Http\Requests\Api\GetCreditMemoRequest;
use App\Http\Traits\IntegrateTrait;
use Illuminate\Http\Request;

class CreditController extends Controller
{
    use IntegrateTrait;

    public function index(Request $request)
    {
        $credits = $this->getReturnedData($request, '/credits', 'get');
        if(!isset($credits->json()['data']['data'])){
            return $this->notFoundResponse();
        }
        return $this->success($credits->json() ?? null, 'success', 200);
    }


    public function getCreditMemo(GetCreditMemoRequest $getCreditMemoRequest)
    {
        $getCreditMemoRequest->validated();
        $credits = $this->getReturnedData($getCreditMemoRequest, '/credits/' . $getCreditMemoRequest->credit_id, 'get');
        if(!isset($credits->json()['data'])){
            return $this->notFoundResponse();
        }
        return $this->success($credits->json() ?? null, 'success', 200);
    }


    public function getCreditMemoByPurchaseOrder(GetCreditMemoByPurchaseOrderRequest $getCreditMemoByPurchaseOrderRequest)
    {
        $getCreditMemoByPurchaseOrderRequest->validated();
        $credits = $this->getReturnedData($getCreditMemoByPurchaseOrderRequest, '/credits/po/' . $getCreditMemoByPurchaseOrderRequest->purchase_order, 'get');
        if(!isset($credits->json()['data']['data'])){
            return $this->notFoundResponse();
        }
        return $this->success($credits->json() ?? null, 'success', 200);
    }

}
