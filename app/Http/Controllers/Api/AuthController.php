<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;

use App\Http\Requests\Api\LoginRequest;
use App\Http\Requests\Api\RegisterRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;


class AuthController extends Controller
{
    public function register(RegisterRequest $request)
    {

        $request->validated();
//        $validator = Validator::make($request->all(), [
//            'name' => 'required',
//            'email' => 'required|email|unique:users,email',
//            'password' => 'required|string',
//            'confirm_password' => 'required|same:password',
//        ]);
//
//        if ($validator->fails()) {
//            return $this->error($validator->errors()->all(), 'validation errors', 400);
//        }


        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),

        ]);

        $response = [];
        $response['token'] = $user->createToken('MyApp')->plainTextToken;
        $response['name'] = $user->name;
        $response['email'] = $user->email;
//        return response()->json([
//            'status' => 1,
//            'message' => 'user registered',
//            'data' => $response
//
//        ]);

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

//            return response()->json([
//                'status' => 1,
//                'message' => 'user registered',
//                'data' => $response,
//            ]);
            return $this->success($response, 'user logged in successfully', 200);

        }

//        return response()->json([
//            'status' => 0,
//            'message' => 'authentication error',
//            'data' => null,
//        ]);
        return $this->error(null, 'authentication error', 404);

    }
}
