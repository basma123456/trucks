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


    function checkAuthentication($request){
        $token = $request->cookie('turn_token');
        return $token;
    }


    function regenerateToken(){
        /**************start///////////////************/
        $response2 = $this->getResponse('/token');
        $data = $response2->json();
        $response['turn_token'] = $data;
        return  $data['access_token'];
    }
}
