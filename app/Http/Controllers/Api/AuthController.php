<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;

use App\Http\Requests\Api\LoginRequest;
use App\Http\Requests\Api\RegisterRequest;
use App\Http\Traits\IntegrateTrait;
use App\Models\User;
use App\Models\Visitor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;


class AuthController extends Controller
{
    use IntegrateTrait;

    public function register(RegisterRequest $request)
    {

//        $validated = $request->validate([
//            'name' => 'required|string|max:255',
//            'email' => 'required|email|unique:visitors,email',
//            'password' => 'required|string|min:6|confirmed',
//        ]);
//        $validated = $request->validated();

//        $visitor = \App\Models\Visitor::create([
//            'name' => $validated['name'],
//            'email' => $validated['email'],
//            'password' => bcrypt($validated['password']),
//        ]);

//        $token = $visitor->createToken('visitor-token')->plainTextToken;

//        return response()->json([
//            'message' => 'Visitor registered successfully',
//            'token' => $token,
//            'visitor' => $visitor,
//        ], 201);


        $request->validated();
//        $user = User::create([
//            'name' => $request->name,
//            'email' => $request->email,
//            'password' => bcrypt($request->password),
//
//        ]);
        $user = \App\Models\Visitor::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
        ]);

        $response = [];
        $response['token'] = $user->createToken('visitor-token')->plainTextToken;
        $response['name'] = $user->name;
        $response['email'] = $user->email;


        if ($response['token']) {
            /**************start///////////////************/
            $response2 = $this->getResponse('/token');
            $data = $response2->json();
            $response['turn_token'] = $data;
            session()->put('turn_token', $data['access_token']);
        }


        return $this->successLogin($response, 'user registered successfully', 201);

    }


    public function login(LoginRequest $request)
    {
//        $visitor = Visitor::where('email', $request->email)->first();
//
//        if (! $visitor || ! Hash::check($request->password, $visitor->password)) {
//            return response()->json([
//                'message' => 'Invalid credentials'
//            ], 401);
//        }
//
//        $token = $visitor->createToken('visitor-token')->plainTextToken;
//
//        return response()->json([
//            'token' => $token,
//            'visitor' => $visitor,
//        ]);


        $request->validated();
        //  dd('dsd');
        $user = Visitor::where('email', $request->email)->first();
        if (!$user || !Hash::check($request->password, $user->password)) {
//            return response()->json([
//                'message' => 'Invalid credentials'
//            ], 401);
            return $this->error(null, 'authentication error', 404);

        }

//        if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
//            $user = Auth::user();
        $response = [];
        $response['token'] = $user->createToken('visitor-token')->plainTextToken;
        $response['name'] = $user->name;
        $response['email'] = $user->email;


        if ($response['token']) {
            /**************start///////////////************/
            $response2 = $this->getResponse('/token');
            $data = $response2->json();
            $response['turn_token'] = $data;
            session()->put('turn_token', $data['access_token']);

        }
        /************end ****************/

        return $this->successLogin($response, 'user logged in successfully', 200);

//        }

//        return $this->error(null, 'authentication error', 404);

    }


    public function checkAuthenticationFunc(Request $request)
    {

        if ($this->checkAuthentication($request)) {
            return $this->success(true, 'token is still valid', 200);
        } else {
            $data = $this->regenerateToken();
            return $this->successLogin($data, 're generated successfully', 201);
        }

    }

}
