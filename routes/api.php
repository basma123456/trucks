<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\BrandController;
use App\Http\Controllers\Api\CreditController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Http;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


//Route::get('/user', function (Request $request) {
//
//    $token = $request->access_token;
//
//    $response = Http::withToken($token)
//        ->get('https://api.turn14.com/v1/');
//
//
//    $data = $response->json();
//    return $data;
//});


//Route::get('/user', function (Request $request) {
//
//    $token = $request->query('access_token'); // safer
//
//    if (!$token) {
//        return response()->json([
//            'error' => 'Access token is required'
//        ], 400);
//    }
//
//    $response = Http::withToken($token)
//        ->get('https://api.turn14.com/v1/products'); // use real endpoint
//
//    if ($response->failed()) {
//        return response()->json([
//            'error' => 'API request failed',
//            'status' => $response->status(),
//            'body' => $response->body()
//        ], $response->status());
//    }
//
//    return $response->json();
//});


//Route::get('get' , [kkj]);

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);
Route::get('brands', [BrandController::class, 'index']);
Route::get('brands/show', [BrandController::class, 'show']);
Route::get('brands/get-details-of-price-group', [BrandController::class, 'getDetailsOfPriceGroup']);
Route::get('credits', [CreditController::class, 'index']);
Route::get('credits/show', [CreditController::class, 'getCreditMemo']);
Route::get('credits/po', [CreditController::class, 'getCreditMemoByPurchaseOrder']);




