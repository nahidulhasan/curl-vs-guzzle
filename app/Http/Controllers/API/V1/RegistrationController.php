<?php

namespace App\Http\Controllers\API\V1;

use App\Exceptions\BLServiceException;
use App\Exceptions\CurlRequestException;
use App\Http\Controllers\Controller;
use App\Http\Requests\CustomerRequest;
use App\Http\Requests\ForgetPasswordRequest;
use App\Http\Requests\LoginWithOtpRequest;
use App\Http\Requests\OtpTokenRequest;
use App\Http\Requests\PasswordChangeRequest;
use App\Http\Requests\SetPasswordRequest;
use App\Services\RegistrationService;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

/**
 * Class RegistrationController
 * @package App\Http\Controllers\API\V1
 */
class RegistrationController extends Controller
{
    /**
     * @var RegistrationService
     */
    protected $registrationService;


    /**
     * RegistrationController constructor.
     * @param RegistrationService $registrationService
     */
    public function __construct(RegistrationService $registrationService)
    {
        $this->registrationService = $registrationService;
    }


    /**
     * Number validation
     *
     * @param Request $request
     * @return JsonResponse
     * @throws BLServiceException
     * @throws CurlRequestException
     */
    public function validateNumber(Request $request)
    {
        $number = $request->input('phone');

        return $this->registrationService->validateNumber($number);
    }


    /**
     * Send OTP
     *
     * @param Request $request
     * @return string
     */
    public function sendOTP(Request $request)
    {
        $number = $request->input('phone');

        return $this->registrationService->sendOTP($number);
    }


    /**
     * @param Request $request
     * @return string
     */
    public function reSendOTP(Request $request)
    {
        $number = $request->input('phone');

        return $this->registrationService->sendOTP($number);
    }


    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function verifyOTPForLogin(LoginWithOtpRequest $request)
    {
        return $this->registrationService->verifyOTPForLogin($request);
    }


    /**
     * Get a JWT token via given credentials.
     *
     * @param OtpTokenRequest $request
     *
     * @return JsonResponse
     */
    public function getToken(OtpTokenRequest $request)
    {
        $phone = $request->get('phone');

        return $this->registrationService->getToken($phone);
    }


    /**
     * Get a JWT token via given credentials.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function authenticate(Request $request)
    {
        $credentials = $request->only('email', 'password');

        try {
            if (! $token = JWTAuth::attempt($credentials)) {
                return response()->json(['error' => 'invalid_credentials'], 400);
            }
        } catch (JWTException $e) {
            return response()->json(['error' => 'could_not_create_token'], 500);
        }

        return $this->respondWithToken($token);
    }


    /**
     * Register user
     *
     * @param CustomerRequest $request
     * @return JsonResponse
     */

    public function register(CustomerRequest $request)
    {
        return $this->registrationService->registerUser($request);
    }


    /**
     * @param Request $request
     * @return string
     * @throws BLServiceException
     * @throws CurlRequestException
     */
    public function login(Request $request)
    {
        return $this->registrationService->login($request);
    }

    /**
     * @param Request $request
     * @return string
     */
    public function getRefreshToken(Request $request)
    {
        return $this->registrationService->getRefreshToken($request);
    }


    /**
     * Update Password
     *
     * @param Request $request
     * @return JsonResponse
     * @throws \App\Exceptions\TokenInvalidException
     */
    public function forgetPassword(ForgetPasswordRequest $request)
    {
        return $this->registrationService->forgetPassword($request);
    }

    public function setPassword(SetPasswordRequest $request)
    {
        return $this->registrationService->setPassword($request);
    }


    /**
     * Update Password
     *
     * @param PasswordChangeRequest $request
     * @return JsonResponse
     * @throws CurlRequestException
     * @throws \App\Exceptions\OldPasswordMismatchException
     * @throws \App\Exceptions\TokenInvalidException
     * @throws \App\Exceptions\TokenNotFoundException
     * @throws \App\Exceptions\TooManyRequestException
     */
    public function changePassword(PasswordChangeRequest $request)
    {
        return $this->registrationService->changePassword($request);
    }




    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return JsonResponse
     */
    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token
        ]);
    }


    /**
     * @param Request $request
     */
    public function testBonus(Request $request){
        return $this->registrationService->testBonus($request);
    }

    /**
     * Get the guard to be used during authentication.
     *
     * @return Guard
     */
    public function guard()
    {
        return Auth::guard();
    }


    /**
     * Authenticated user list
     *
     * @return JsonResponse
     */
    public function getAuthenticatedUser()
    {
        try {
            if (! $user = JWTAuth::parseToken()->authenticate()) {
                return response()->json(['user_not_found'], 404);
            }
        } catch (Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {
            return response()->json(['token_expired'], $e->getStatusCode());
        } catch (Tymon\JWTAuth\Exceptions\TokenInvalidException $e) {
            return response()->json(['token_invalid'], $e->getStatusCode());
        } catch (Tymon\JWTAuth\Exceptions\JWTException $e) {
            return response()->json(['token_absent'], $e->getStatusCode());
        }

        return response()->json(compact('user'));
    }
}
