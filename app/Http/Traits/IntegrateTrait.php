<?php

namespace App\Http\Traits;


use Illuminate\Support\Facades\Cache;
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


//    function checkAuthentication($request){
//        $token = $request->cookie('turn_token');
//        return $token;
//    }

    function checkAuthentication($request){
        $token = $request->turn_token;
        return $token;
    }

    function regenerateToken(){
        /**************start///////////////************/
        $response2 = $this->getResponse('/token');
        $data = $response2->json();
        $response['turn_token'] = $data;
        return  $data['access_token'];
    }









    /************************jobs related functions******************/
    public function autoMaticTokenForJob()
    {
        $token = Cache::remember('turn14_token', 300, function () {

            $response = Http::withoutVerifying()
                ->asForm()
                ->post(config('app.API_URL') . '/token', [
                    "grant_type" => "client_credentials",
                    "client_id" => config('app.client_id'),
                    "client_secret" => config('app.client_secret'),
                ]);

            if (!$response->successful()) {
                throw new \Exception('Token request failed: ');
            }

            return $response->json('access_token');
        });

        return $token;
    }




    /************************end jobs related functions******************/

}
