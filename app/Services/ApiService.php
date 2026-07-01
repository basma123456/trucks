<?php


namespace App\Services;

use Illuminate\Support\Facades\Http;

class ApiService
{
    public function token()
    {
        $response = Http::withoutVerifying()->asForm()->post(config('app.API_URL') . '/token', [
            "grant_type" => "client_credentials",
            "client_id" => config('app.client_id'),
            "client_secret" => config('app.client_secret'),
        ]);
        return $response['access_token'];
    }

    public function get($url)
    {
        $token = $this->token();

        return Http::withToken($token)
            ->get(config('app.API_URL') . $url)
            ->json();
    }
}
