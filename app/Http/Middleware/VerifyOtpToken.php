<?php

namespace App\Http\Middleware;

use App\Enums\HttpStatusCode;
use App\Models\Otp;
use App\Services\ApiBaseService;
use Closure;
use Illuminate\Http\Request;

class VerifyOtpToken extends ApiBaseService
{
    /**
     * Handle an incoming request.
     *
     * @param  Request  $request
     * @param Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {

        if (!$request->input('otp_token')) {
            return $this->sendErrorResponse(
                "OTP Token id required",
                [
                    'message' => 'OTP Token id required'
                ],
                HttpStatusCode::VALIDATION_ERROR
            );
        }

        $otp_Token = $request->input('otp_token');

        $phone = $request->input('username');

        if (empty($phone)) {
            $phone = $request->input('phone');
        }

        $token_exist = Otp::where('phone', $phone)->first();

        if (isset($token_exist->token) && $otp_Token == $token_exist->token) {
            return $next($request);
        }

        return $this->sendErrorResponse(
            "OTP Token is Invalid",
            [
                'message' => 'OTP Token is Invalid'
            ],
            HttpStatusCode::VALIDATION_ERROR
        );
    }
}
