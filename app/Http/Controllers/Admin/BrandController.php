<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Traits\IntegrateTrait;
use App\Jobs\storeBrandsJob;
use App\Models\Brand;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class BrandController extends Controller
{
    use IntegrateTrait;

    public function listBrands(Request $request)
    {


        $response2 = Http::withoutVerifying()->asForm()->post(config('app.API_URL') . '/token', [
            "grant_type" => "client_credentials",
            "client_id" => config('app.client_id'),
            "client_secret" => config('app.client_secret'),
        ]);

        //        $brands = $this->getReturnedData($request, '/brands', 'get');

        $brands = Http::withToken($response2->json()['access_token'])
            ->get(config('app.API_URL') . '/brands');
//dd($brands->json());
        if ($brands->status() === 401) {
            return $this->error(null, 'Token expired or invalid', 401);
        }


        if (!isset($brands->json()['data'])) {
            return $this->notFoundResponse();
        }


        $brandsData = $brands->json()['data'];


        foreach ($brandsData as $brand) {

            Brand::updateOrCreate(
                [
                    'code' => $brand['id'], // unique field
                ],
                [
                    'name' => $brand['attributes']['name'] ?? null,
                    'logo' => $brand['attributes']['logo'] ?? null,
                ]
            );
        }

        return response()->json([
            'message' => 'Brands synced successfully'
        ]);

    }


    public function storeBrands(Request $request)
    {
        $data = 'Sample Data';
        storeBrandsJob::dispatch($data);
    }
}
