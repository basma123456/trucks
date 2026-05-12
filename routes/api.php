<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\BrandController;
use App\Http\Controllers\Api\CreditController;
use App\Http\Controllers\Api\DocumentController;
use App\Http\Controllers\Api\DropshipController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Http;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


//Route::get('get' , [kkj]);

//Route::get('/user', function (Request $request) {
//    return $request->user();
//})->middleware('auth:sanctum');

Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);

Route::middleware('turn_auth')->group(function () {
    Route::get('brands', [BrandController::class, 'index']);
    Route::get('brands/show', [BrandController::class, 'show']);
    Route::get('brands/get-details-of-price-group', [BrandController::class, 'getDetailsOfPriceGroup']);
    Route::get('credits', [CreditController::class, 'index']);
    Route::get('credits/show', [CreditController::class, 'getCreditMemo']);
    Route::get('credits/po', [CreditController::class, 'getCreditMemoByPurchaseOrder']);
    Route::get('documents', [DocumentController::class, 'getDocumentsByQuote']);
    Route::get('documents/po', [DocumentController::class, 'getDocumentsByPurchaseOrderNum']);
    Route::get('dropship-show', [DropshipController::class, 'dropShipShow']);

//    dropship/{dropship_id}
});



