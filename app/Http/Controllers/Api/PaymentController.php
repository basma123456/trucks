<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Traits\IntegrateTrait;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    use IntegrateTrait;

    public function index(Request $request)
    {
        $data = $this->getReturnedData($request, '/payments', 'get');
        if ($data->status() === 401) {
            return $this->error(null, 'Token expired or invalid', 401);
        }
        if (!isset($data->json()['data'])) {
//        if (!isset($data->json()['data'])) {
            return $this->notFoundResponse();
        }
        return $this->success($data->json(), 'success', 200);
    }


    public function show(Request $request)
    {
        $data = $this->getReturnedData($request, '/payments/' . $request->payment_id, 'get');
        if ($data->status() === 401) {
            return $this->error(null, 'Token expired or invalid', 401);
        }
        if (!isset($data->json()['data'])) {
            return $this->notFoundResponse("No payments exists for that payment_id");
        }
        return $this->success($data->json(), 'success', 200);
    }


    public function showByInvoice(Request $request)
    {
        $data = $this->getReturnedData($request, '/payments/invoice/' . $request->invoice_id, 'get');
        if ($data->status() === 401) {
            return $this->error(null, 'Token expired or invalid', 401);
        }
        if (empty($data->json()['data'])) {
            return $this->notFoundResponse("No payments exists for that invoice");
        }
        return $this->success($data->json(), 'success', 200);
    }



//payments?start_date={start_date}&end_date={end_date}
public function paymentsDateRange(Request $request)
    {
        $data = $this->getReturnedData($request, '/payments?start_date=' . $request->start_date . '&end_date=' . $request->end_date, 'get');
        if ($data->status() === 401) {
            return $this->error(null, 'Token expired or invalid', 401);
        }
        if (!isset($data->json()['data']['data'])) {
            return $this->notFoundResponse("No payments exists for this range of date inputs");
        }
        return $this->success($data->json(), 'success', 200);
    }


}
