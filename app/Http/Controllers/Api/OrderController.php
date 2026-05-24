<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Traits\IntegrateTrait;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    use IntegrateTrait;

    public function index(Request $request)
    {
        $data = $this->getReturnedData($request, '/orders', 'get');
        if ($data->status() === 401) {
            return $this->error(null, 'Token expired or invalid', 401);
        }
        if (!isset($data->json()['data']['data'])) {
//        if (!isset($data->json()['data'])) {
            return $this->notFoundResponse();
        }
        return $this->success($data->json(), 'success', 200);
    }


    public function show(Request $request)
    {
        $data = $this->getReturnedData($request, '/orders/' . $request->order_id, 'get');
        if ($data->status() === 401) {
            return $this->error(null, 'Token expired or invalid', 401);
        }
        if (!isset($data->json()['data'])) {
            return $this->notFoundResponse("No order exists for that order_id");
        }
        return $this->success($data->json(), 'success', 200);
    }


    public function getOrderByPurchaseOrder(Request $request)
    {
        $data = $this->getReturnedData($request, '/orders/po/' . $request->purchase_order_number, 'get');
        if ($data->status() === 401) {
            return $this->error(null, 'Token expired or invalid', 401);
        }
        if (!isset($data->json()['data']['data'])) {
            return $this->notFoundResponse("No order exists for that purchase order");
        }
        return $this->success($data->json(), 'success', 200);

    }


    public function ordersDateRange(Request $request)
    {
        $data = $this->getReturnedData($request, '/orders?start_date=' . $request->start_date . '&end_date=' . $request->end_date, 'get');
        if ($data->status() === 401) {
            return $this->error(null, 'Token expired or invalid', 401);
        }
        if (!isset($data->json()['data']['data'])) {
            return $this->notFoundResponse("No orders exists for this range of date inputs");
        }
        return $this->success($data->json(), 'success', 200);

    }


}
