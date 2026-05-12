<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\DropShipRequest;
use App\Http\Traits\IntegrateTrait;
use Illuminate\Http\Request;

class DropshipController extends Controller
{
    use IntegrateTrait;

    public function dropShipShow(DropShipRequest $dropShipRequest)
    {
        $data = $this->getReturnedData($dropShipRequest, '/dropship/' . $dropShipRequest->dropship_id, 'get');
        if (!isset($data->json()['data'])) {
            return $this->notFoundResponse('No dropship controller exists for that  ' . $dropShipRequest->dropship_id);
        }
        return $this->success($data->json(), 'success', 200);
    }
}
