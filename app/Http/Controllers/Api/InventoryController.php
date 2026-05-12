<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
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

}
