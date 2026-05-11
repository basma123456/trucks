<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TurnAuth
{
    /**
     * Handle an incoming request.
     *
     * @param Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {

//        dd(session('turn_token') , $request->bearerToken());
        if ($request->bearerToken()) {
            return $next($request);
        } else {
            $array = [
                'success' => false,
                'message' => 'authentication failed',
                'data' => null,
            ];
            return response($array, 403);

        }
    }
}
