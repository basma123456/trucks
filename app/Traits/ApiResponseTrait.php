<?php

namespace App\Traits;


use App\Http\Resources\ItemResource;
use Illuminate\Pagination\LengthAwarePaginator;

trait ApiResponseTrait
{

    public function success($data = [], string $message = null, $code = 200, $token = null)
    {
//        $array = [
//            'success' => in_array($code, $this->successCode()) ?true : false,
//            'message' => $message,
//            'data' => $data,
//        ];
//        return response($array, $code);
        $response = [
            'success' => in_array($code, $this->successCode()),
            'message' => $message,
        ];

        // Check if paginated
        if ($data instanceof \Illuminate\Pagination\LengthAwarePaginator) {

            $response['data'] = $data->items();

            $response['pagination'] = [
                'current_page' => $data->currentPage(),
                'last_page' => $data->lastPage(),
                'per_page' => $data->perPage(),
                'total' => $data->total(),
                'from' => $data->firstItem(),
                'to' => $data->lastItem(),
                'next_page_url' => $data->nextPageUrl(),
                'prev_page_url' => $data->previousPageUrl(),
            ];

        } else {

            $response['data'] = $data;
        }

        if ($token) {
            $response['token'] = $token;
        }

        return response()->json($response, $code);
    }
    public function successLogin($data = [], string $message = null, $code = 200, $token = null)
    {
//        $array = [
//            'success' => in_array($code, $this->successCode()) ?true : false,
//            'message' => $message,
//            'data' => $data,
//        ];
//        return response($array, $code);
        $response = [
            'success' => in_array($code, $this->successCode()),
            'message' => $message,
        ];

        // Check if paginated
        if ($data instanceof \Illuminate\Pagination\LengthAwarePaginator) {

            $response['data'] = $data->items();

            $response['pagination'] = [
                'current_page' => $data->currentPage(),
                'last_page' => $data->lastPage(),
                'per_page' => $data->perPage(),
                'total' => $data->total(),
                'from' => $data->firstItem(),
                'to' => $data->lastItem(),
                'next_page_url' => $data->nextPageUrl(),
                'prev_page_url' => $data->previousPageUrl(),
            ];

        } else {

            $response['data'] = $data;
        }

        if ($token) {
            $response['token'] = $token;
        }

        return response()->json($response, $code)->cookie(
            'turn_token',
            true,
            2 // minutes = 3600 seconds
        );
    }

    public function error($data = [], string $message = null, $code = 400)
    {
        $array = [
            'success' => in_array($code, $this->successCode()) ?true : false,
            'message' => $message,
            'data' => $data,
        ];
        return response($array, $code);
    }

    public function successCode(){
        return [
            200, 201, 202
        ];
    }

    public function notFoundResponse($message = 'not found !'){
        return $this->error(null, $message , 404);
    }

    public function deleteResponse(){
        return $this->error(null, "Delete success !", 404);
    }



//return ['data' => ['cart_id' => $cart->id, 'type' => 'success'], 'cookeries' => $cookieValue, 'message' => __('success'), 'code' => 200];

    public function successWithCookie($data ,$message    , $code=200  ,  $token = null){
        $array = [
            'success' => in_array($code, $this->successCode()) ?true : false,
            'message' => $message,
            'data' => $data,
        ];
        return response($array , $code)
            ->withCookie(cookie($data['cookie_name']??null, $data['cookie_value']??null, 5));


    }



    public function successWithCookies($data ,$message    , $code=200  ,  $token = null){
        $array = [
            'success' => in_array($code, $this->successCode()) ?true : false,
            'message' => $message,
            'data' => $data,
        ];
        return response($array , $code)
            ->withCookie(cookie($data['cookie_name']??null, $data['cookie_value']??null, 5))
            ->withCookie(cookie($data['cookie_name2']??null, $data['cookie_value2']??null, 5));


    }


    public function paginateSuccess($data = [], string $message = null, $code = 200, $token = null)
    {


//        return response()->json([
//            'success' => true,
//            'message' => null,
//            'data' => $data,
//            'pagination' => [
//                'current_page' => $data->currentPage(),
//                'last_page' => $data->lastPage(),
//                'per_page' => $data->perPage(),
//                'total' => $data->total(),
//                'from' => $data->firstItem(),
//                'to' => $data->lastItem(),
//                'next_page_url' => $data->nextPageUrl(),
//                'prev_page_url' => $data->previousPageUrl(),
//            ],
//        ]);

        return [
            'success' => true,
            'message' => null,
            'data' => ItemResource::collection($data->items())->resolve(),
            'pagination' => [
                'current_page' => $data->currentPage(),
                'last_page' => $data->lastPage(),
                'per_page' => $data->perPage(),
                'total' => $data->total(),
                'from' => $data->firstItem(),
                'to' => $data->lastItem(),
                'next_page_url' => $data->nextPageUrl(),
                'prev_page_url' => $data->previousPageUrl(),
            ],
        ];
    }

}
