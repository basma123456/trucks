<?php

namespace App\Http\Traits;


use Illuminate\Support\Facades\Http;

trait IntegrateTrait
{

    function getResponse($url, $method = 'post')
    {
        $response2 = Http::withoutVerifying()->asForm()->$method(config('app.API_URL') . $url, [
            "grant_type" => "client_credentials",
            "client_id" => config('app.client_id'),
            "client_secret" => config('app.client_secret'),
        ]);
        return $response2;
    }


    function getReturnedData($request, $url, $method)
    {
        return Http::withToken($request->bearerToken())
            ->$method(config('app.API_URL') . $url);
    }
}
