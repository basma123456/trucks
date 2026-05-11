<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\GetDocumentsByPurchaseOrderNumRequest;
use App\Http\Requests\Api\getDocumentsByQuoteRequest;
use App\Http\Traits\IntegrateTrait;
use Illuminate\Http\Request;

class DocumentController extends Controller
{
    use IntegrateTrait;

    public function getDocumentsByQuote(getDocumentsByQuoteRequest $getDocumentsByQuoteRequest)
    {
        $getDocumentsByQuoteRequest->validated();
        $credits = $this->getReturnedData($getDocumentsByQuoteRequest, '/documents/' . $getDocumentsByQuoteRequest->quote_id, 'get');
        if (!isset($credits->json()['data'])) {
            return $this->notFoundResponse('No orders could be found related to this quote ' . $getDocumentsByQuoteRequest->quote_id);
        }
        return $this->success($credits->json() ?? null, 'success', 200);
    }

//documents/po/{purchase_order_number}
    public function getDocumentsByPurchaseOrderNum(GetDocumentsByPurchaseOrderNumRequest $getDocumentsByPurchaseOrderNumRequest)
    {
        $getDocumentsByPurchaseOrderNumRequest->validated();
        $credits = $this->getReturnedData($getDocumentsByPurchaseOrderNumRequest, '/documents/po/' . $getDocumentsByPurchaseOrderNumRequest->purchase_order_number, 'get');
        if (!isset($credits->json()['data'])) {
            return $this->notFoundResponse('No orders could be found related to this purchase order ' . $getDocumentsByPurchaseOrderNumRequest->purchase_order_number);
        }
        return $this->success($credits->json() ?? null, 'success', 200);
    }


}
