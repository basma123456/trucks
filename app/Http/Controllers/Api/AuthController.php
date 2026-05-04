<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;

use App\Http\Requests\Api\LoginRequest;
use App\Http\Requests\Api\RegisterRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;


class AuthController extends Controller
{
    public function register(RegisterRequest $request)
    {

        $request->validated();
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),

        ]);

        $response = [];
        $response['token'] = $user->createToken('MyApp')->plainTextToken;
        $response['name'] = $user->name;
        $response['email'] = $user->email;

        return $this->success($response, 'user registered successfully', 200);

    }


    public function login(LoginRequest $request)
    {
        $request->validated();
        if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            $user = Auth::user();
            $response = [];
            $response['token'] = $user->createToken('MyApp')->plainTextToken;
            $response['name'] = $user->name;
            $response['email'] = $user->email;


            if ($response['token']) {
                /**************start///////////////************/
                $response2 = Http::withoutVerifying()->asForm()->post('https://api.turn14.com/v1/token', [
                    "grant_type" => "client_credentials",
                    "client_id" => config('app.client_id'),
                    "client_secret" => config('app.client_secret'),


                ]);

                $data = $response2->json();
                $response['turn_token'] = $data;
                session()->put('turn_token', $data['access_token']);
            }
            /************end ****************/

            return $this->success($response, 'user logged in successfully', 200);

        }

        return $this->error(null, 'authentication error', 404);

    }


}
