<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\GetCreditMemoByPurchaseOrderRequest;
use App\Http\Requests\Api\GetCreditMemoRequest;
use App\Http\Requests\Api\getCreditRequest;
use App\Http\Traits\IntegrateTrait;
use Illuminate\Http\Request;

class CreditController extends Controller
{
    use IntegrateTrait;

    public function index(GetCreditRequest $getCreditRequest)
    {
        $getCreditRequest->validated();
        $credits = $this->getReturnedData($getCreditRequest, '/credits', 'get');
        if ($credits->status() === 401) {
            return $this->error(null , 'Token expired or invalid' , 401);
        }

        if (!isset($credits->json()['data']['data'])) {
            return $this->notFoundResponse();
        }
        return $this->success($credits->json() ?? null, 'success', 200);
    }


    public function getCreditMemo(GetCreditMemoRequest $getCreditMemoRequest)
    {
        $getCreditMemoRequest->validated();
        $credits = $this->getReturnedData($getCreditMemoRequest, '/credits/' . $getCreditMemoRequest->credit_id, 'get');
        if ($credits->status() === 401) {
            return $this->error(null , 'Token expired or invalid' , 401);
        }

        if (!isset($credits->json()['data'])) {
            return $this->notFoundResponse();
        }
        return $this->success($credits->json() ?? null, 'success', 200);
    }


    public function getCreditMemoByPurchaseOrder(GetCreditMemoByPurchaseOrderRequest $getCreditMemoByPurchaseOrderRequest)
    {
        $getCreditMemoByPurchaseOrderRequest->validated();
        $credits = $this->getReturnedData($getCreditMemoByPurchaseOrderRequest, '/credits/po/' . $getCreditMemoByPurchaseOrderRequest->purchase_order, 'get');
        if ($credits->status() === 401) {
            return $this->error(null , 'Token expired or invalid' , 401);
        }

        if (!isset($credits->json()['data']['data'])) {
            return $this->notFoundResponse();
        }
        return $this->success($credits->json() ?? null, 'success', 200);
    }

}
