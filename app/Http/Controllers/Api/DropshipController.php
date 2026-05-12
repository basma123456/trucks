<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Traits\IntegrateTrait;
use Illuminate\Http\Request;

class DropshipController extends Controller
{
    use IntegrateTrait;

    public function dropShipShow(Request $request)
    {
        $data = $this->getReturnedData($request , '/dropship/' . $request->dropship_id , 'get');
        return $this->success($data->json() ,'success' , 200);
    }
}
