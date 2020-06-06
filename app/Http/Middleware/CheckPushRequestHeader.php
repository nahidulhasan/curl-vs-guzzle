<?php

namespace App\Http\Middleware;

use App\Enums\HttpStatusCode;
use Closure;

class CheckPushRequestHeader
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if ($request->header('app-key') != config('pushnotification.token')) {
            $errors[] = [
                'code' => 'ERR_2001',
                'message' => 'Unauthorized'
            ];

            $response = [
                'status' => 'FAIL',
                'errors' => $errors,
            ];

            return response()->json($response, HttpStatusCode::UNAUTHORIZED);
        }

        return $next($request);
    }
}
