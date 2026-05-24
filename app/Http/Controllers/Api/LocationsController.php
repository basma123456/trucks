<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Traits\IntegrateTrait;
use Illuminate\Http\Request;

class LocationsController extends Controller
{
    use IntegrateTrait;

    public function index(Request $request)
    {
        $data = $this->getReturnedData($request, '/locations', 'get');
        if ($data->status() === 401) {
            return $this->error(null, 'Token expired or invalid', 401);
        }
        if (!isset($data->json()['data'])) {
            return $this->notFoundResponse();
        }
        return $this->success($data->json(), 'success', 200);
    }


}
