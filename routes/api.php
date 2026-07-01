<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\BrandController;
use App\Http\Controllers\Api\CreditController;
use App\Http\Controllers\Api\DocumentController;
use App\Http\Controllers\Api\DropshipController;
use App\Http\Controllers\Api\InventoryController;
use App\Http\Controllers\Api\ItemController;
use App\Http\Controllers\Api\LocationsController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\PaymentController;
use App\Http\Controllers\Api\PricingController;
use App\Http\Controllers\Api\ShippingController;
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
    Route::get('inventory', [InventoryController::class, 'getAllInventory']);
    Route::get('inventory-show', [InventoryController::class, 'getItemInventory']);
    Route::get('items', [ItemController::class, 'index']);
    Route::get('inventory-brand', [InventoryController::class, 'getBrandInventory']);
    Route::get('price-group-inventory', [InventoryController::class, 'getPriceGroupInventory']);
    Route::get('recently-updated-inventory', [InventoryController::class, 'getRecentlyUpdatedInventory']);
    Route::get('items-brand', [ItemController::class, 'getItemsBrand']);
    Route::get('items-brand-price-group', [ItemController::class, 'getItemsOfBrandAndPriceGroup']);
    Route::get('recently-updated-items', [ItemController::class, 'recentlyUpdatedItems']);
    Route::get('items-data', [ItemController::class, 'itemsData']);
    Route::post('items-fitment', [ItemController::class, 'itemsFitment']);
    Route::post('items-fitment-by-brand', [ItemController::class, 'itemsFitmentByBrand']);
    Route::post('items-fitment-by-brand-group-price', [ItemController::class, 'itemsFitmentByBrandAndGroupPrice']);
    Route::post('fitment-item-data', [ItemController::class, 'fitmentItemData']);
    Route::get('locations' , [LocationsController::class , 'index']);
    Route::get('orders' , [OrderController::class , 'index']);
    Route::get('order-show' , [OrderController::class , 'show']);
    Route::get('order-show-by-po' , [OrderController::class , 'getOrderByPurchaseOrder']);
    Route::get('orders-date-range' , [OrderController::class , 'ordersDateRange']);

    Route::get('payments' , [PaymentController::class , 'index']);
    Route::get('payment-show' , [PaymentController::class , 'show']);
    Route::get('payment-show-by-invoice' , [PaymentController::class , 'showByInvoice']);
    Route::get('payments-date-range' , [PaymentController::class , 'paymentsDateRange']);

    Route::get('pricing' , [PricingController::class , 'index']);
    Route::get('pricing-item' , [PricingController::class , 'getPricingItem']);

    Route::get('pricing-brand' , [PricingController::class , 'getPricingBrand']);
    Route::get('pricing-brand-by-group' , [PricingController::class , 'getPricingBrandByGroup']);
    Route::get('shipping' , [ShippingController::class , 'index']);


    Route::get('items/show' , [ItemController::class , 'show']);
    Route::post('items/list-items-of-brands' , [ItemController::class , 'getItemsFromArrayOfBrands']);


//    /v1/pricing/brand/{brand_id}?page={page}
//    pricing/brand/{brand_id}?page={page}

//   /items/fitment?page={page}



//    GET/v1/items/data?page={page}


//    dropship/{dropship_id}
});

Route::get('check', [AuthController::class, 'checkAuthenticationFunc']);


