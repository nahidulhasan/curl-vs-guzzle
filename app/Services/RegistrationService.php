<?php

namespace App\Services;

use App\Events\SignUpBonus;
use App\Exceptions\BLServiceException;
use App\Exceptions\CurlRequestException;
use App\Exceptions\OldPasswordMismatchException;
use App\Exceptions\TokenInvalidException;
use App\Http\Resources\CustomerResource;
use App\Models\Customer;
use App\Repositories\OtpConfigRepository;
use App\Services\Bonus\BonusApiIntegrationService;
use App\Services\Bonus\IDMIntegrationService;
use Carbon\Carbon;
use App\Enums\HttpStatusCode;
use App\Http\Requests\CustomerRequest;
use App\Models\Otp;
use App\Repositories\CustomerRepository;
use App\Repositories\OtpRepository;
use http\Client\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use JWTAuth;
use Illuminate\Support\Facades\Crypt;
use App\Services\Banglalink\BanglalinkOtpService;
use App\Services\Banglalink\BanglalinkCustomerService;

/**
 * Class RegistrationService
 * @package App\Services
 */
class RegistrationService extends ApiBaseService
{

    /**
     * @var CustomerRepository
     */
    protected $customerRepository;

    /**
     * @var OtpRepository
     */
    protected $otpRepository;

    /**
     * @var BanglalinkOtpService
     */
    protected $blOtpService;

    /**
     * @var OtpConfigRepository
     */
    protected $otpConfigRepository;

    /**
     * @var BanglalinkCustomerService
     */
    protected $blCustomerService;
    /**
     * @var CustomerService
     */
    protected $customerService;

    protected $idmIntegrationService;


    /**
     * RegistrationService constructor.
     * @param CustomerRepository $customerRepository
     * @param OtpRepository $otpRepository
     * @param BanglalinkOtpService $blOtpService
     * @param OtpConfigRepository $otpConfigRepository
     * @param BanglalinkCustomerService $blCustomerService
     * @param CustomerService $customerService
     * @param IDMIntegrationService $idmIntegrationService
     */
    public function __construct(
        CustomerRepository $customerRepository,
        OtpRepository $otpRepository,
        BanglalinkOtpService $blOtpService,
        OtpConfigRepository $otpConfigRepository,
        BanglalinkCustomerService $blCustomerService,
        CustomerService $customerService,
        IDMIntegrationService $idmIntegrationService
    ) {
        $this->customerRepository = $customerRepository;
        $this->otpRepository = $otpRepository;
        $this->blOtpService = $blOtpService;
        $this->otpConfigRepository = $otpConfigRepository;
        $this->blCustomerService = $blCustomerService;
        $this->customerService = $customerService;
        $this->idmIntegrationService = $idmIntegrationService;
    }

    /**
     * Validate number
     *
     * @param $number
     * @return JsonResponse
     * @throws BLServiceException
     * @throws CurlRequestException
     */
    public function validateNumber($number)
    {
        $missdn = "88" . $number;
        $customer = $this->blCustomerService->getCustomerInfoByNumber($missdn);
        $response = $customer->getData();
        if ($response->status == 'FAIL') {
            return $this->sendErrorResponse(
                "Number is Not Valid",
                [
                    'message' => "Number is not valid"
                ],
                HttpStatusCode::BAD_REQUEST
            );
        }

        $data['connectionType'] =  $response->data->connectionType;
        return $this->sendSuccessResponse(
            $data,
            "Number is Valid"
        );
    }

    public function registerUser($request, $customer_info)
    {
        /*
         *  steps
         *  first check if user exist in IDP
         *  if not exist in IDP register in IDP
         *  register in mybl
         */

        $idpCus = IdpIntegrationService::getCustomerBasicInfo($request->username);

        $randomPass = $this->generateRandomPassword(10);

        $data = [
            'mobile' => $request->username,
            'phone' => $request->username,
            'msisdn' => '88' . $request->username,
            'password' => $randomPass,
            'password_confirmation' => $randomPass,
            'username' => $request->username,
            'customer_account_id' => $customer_info->id
        ];

        if ($idpCus['status_code'] == 404) {
            // register to IDP
            $response = IdpIntegrationService::registrationRequest($data);

            if ($response['status_code'] != 201) {
                $errorData = json_decode($response['response'], true);
                return $this->sendErrorResponse(
                    'Login Failed. try again later.',
                    [
                        'message' => 'Login Failed. Try again later.',
                        'hint' => 'Registration Failed in IDP',
                        'details' => $errorData
                    ]
                );
            }
        }

        /*
         *  Already in IDP
         *  Register to MyBl
         */
        $user = $this->customerRepository->create($data);

        if (!$user) {
            return $this->sendErrorResponse(
                'Login Failed. try again later.',
                [
                    'message' => 'Login Failed. Try again later.',
                    'hint' => 'Registration Failed in MYbl',
                    'details' => []
                ]
            );
        }

        $this->requestSignUpBonus($request);

        return true;
    }


    /**
     * @param $request
     * @return string
     */
    public function testBonus($request)
    {
        return $password = bcrypt('Banglalink019');

        $signup_bonus_data = $request->all();

        try {
            event(new SignUpBonus($signup_bonus_data));
            Log::info('Success: Sign up bonus');
        } catch(Exception $ex) {
            Log::info('Error : Sign up bonus '. $ex->getMessage());
        }

    }


    /**
     * Login via Idp
     *
     * @param $request
     * @return string
     * @throws BLServiceException
     * @throws CurlRequestException
     */
    public function login($request)
    {
        $number = $request->input('username');

        $login_response = IdpIntegrationService::loginRequest($request->all());

        $login_response = json_decode($login_response['response'], true);


        if (isset($login_response['error'])) {
            return $this->sendErrorResponse(
                $login_response['message'],
                [
                    'message' => "User Credentials are not correct"
                ],
                HttpStatusCode::UNAUTHORIZED
            );
        }

        $response = IdpIntegrationService::getCustomerBasicInfo($number);

        $customer_info = json_decode($response['response'], true);

        if ($response['status_code'] != 200) {
            return $this->sendErrorResponse(
                'Something went wrong. Try again later',
                [
                    'message' => "Something went wrong. Try again later",
                    'details' => $customer_info
                ],
                HttpStatusCode::BAD_REQUEST
            );
        }

        $user = Customer::where('phone', $customer_info['data']['mobile'])->first();

        $customer = $this->blCustomerService->getCustomerInfoByNumber($customer_info['data']['msisdn']);
        $response = $customer->getData();

        if ($response->status == 'FAIL') {
            return $this->sendErrorResponse(
                "Something went wrong. Try again later",
                [
                    'message' => "Something went wrong. Try again later"
                ],
                HttpStatusCode::BAD_REQUEST
            );
        }

        if (!$user) {
            // register to MYBL
            $data = [
                'phone' => $customer_info['data']['mobile'],
                'mobile' => $customer_info['data']['mobile'],
                'msisdn' => '88' . $customer_info['data']['mobile'],
                'customer_account_id' => $response->data->id,
            ];

            $user = $this->customerRepository->create($data);
        }

        $customer = $this->customerService->prepareCustomerBasicInfo($user, $customer_info['data']);

        $final_data = [
            'token' => $login_response,
            'customer' => $customer,
        ];

        return $this->sendSuccessResponse(
            $final_data,
            "Login Successfully"
        );
    }


    /**
     * Send OTP
     *
     * @param $number
     * @return string
     */
    public function sendOTP($number)
    {
        $otp_config = $this->otpConfigRepository->getOtpConfig();

        $conf = $otp_config->toArray();

        if (isset($conf[0]['validation_time'])) {
            $validation_time = $conf[0]['validation_time'];
            $otp_bl = $this->blOtpService->sendOtp($number, $conf[0]['token_length_string'], "#", $validation_time);
        } else {
            $validation_time = 300;
            $otp_bl = $this->blOtpService->sendOtp($number);
        }


        $token = $this->generateOtpToken(18);

        $encrypted_token = Crypt::encryptString($token);

        $otp = $this->generateNumericOTP(6);

        $this->otpRepository->createOtp($number, $otp, $encrypted_token);

        $data = [
            'validation_time' => $validation_time,
            'otp_token' => $encrypted_token
        ];

        return $this->sendSuccessResponse($data, 'OTP Send Successfully', [], HttpStatusCode::SUCCESS);
    }


    /**
     * @param $mobile
     * @return bool
     */
    public function isUserExist($mobile)
    {
        $user = Customer::where('msisdn', '88' . $mobile)->first();

        return $user ? true : false;
    }


    /**
     * @param int $length
     * @return mixed|string
     */
    public function generateRandomPassword($length = 8)
    {
        $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ@#$1234567890';
        $small_letters = 'abcdefghijklmnopqrstuvwxyz';
        $digits = '1234567890';
        $special_characters = '@#$';
        $charactersLength = strlen($characters);
        $randomString = $digits[rand(0, strlen($digits) - 1)];
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        $randomString .= $small_letters[rand(0, strlen($small_letters) - 1)];
        $randomString .= $special_characters[rand(0, strlen($special_characters) - 1)];
        return $randomString;
    }

    /**
     * @param $msisdn
     * @return JsonResponse
     * @throws BLServiceException
     * @throws CurlRequestException
     */
    public function validateBlNumber($msisdn)
    {
        return $this->blCustomerService->getCustomerInfoByNumber($msisdn);
    }


    /**
     * Verify OTP for Login
     *
     * @param $request
     * @return JsonResponse
     * @throws BLServiceException
     * @throws CurlRequestException
     */
    public function verifyOTPForLogin($request)
    {
        $number = $request->input('username');
        // validate the number
        $validate_response = $this->validateBlNumber('88' . $number);
        $response_status = $validate_response->getData()->status;
        if ($response_status != 'SUCCESS') {
            return $this->sendErrorResponse(
                "The number is not valid banglalink number",
                [
                    'message' => 'The number is not valid banglalink number'
                ],
                HttpStatusCode::BAD_REQUEST
            );
        }

        $customer_info = $validate_response->getData()->data;

        if (!$this->isUserExist($number)) {
            // register user
            $this->registerUser($request, $customer_info);
        }

        //login with otp perform

        $otp_grant_data = [
            'grant_type' => 'otp_grant',
            'client_id' => $request->client_id,
            'client_secret' => $request->client_secret,
            'otp' => $request->otp,
            'username' => $request->username,
            'provider' => $request->provider,
        ];

        $token = IdpIntegrationService::otpGrantTokenRequest($otp_grant_data);

        if ($token['status_code'] != 200) {
            $error_response = json_decode($token['response'], true);
            $message = $error_response['message'];
            if ($error_response['error'] == 'invalid_otp') {
                $message = 'Your OTP is invalid';
            }
            return $this->sendErrorResponse($message, [
                'message' => $message,
                'hint' => 'Getting HTTP error from IDP',
                'details' => $error_response
            ], 400);
        }

        $user = Customer::where('phone', $number)->first();

        $response = IdpIntegrationService::getCustomerBasicInfo($number);

        if ($response['status_code'] != 200) {
            return $this->sendErrorResponse('IDP Customer info Problem', [
                'message' => 'Something went wrong. try again later',
                'hint' => 'Getting HTTP error from IDP',
                'details' => []
            ], 400);
        }

        $data = json_decode($response['response'], true);

        $customer = $this->customerService->prepareCustomerBasicInfo($user, $data['data']);

        return $this->sendSuccessResponse([
            'token' => json_decode($token['response']),
            'customer' => $customer,
        ], 'Customer login with OTP');
    }


    /**
     * Generate jwt token for otp
     *
     * @param $phone
     * @return mixed
     */
    public function getToken($phone)
    {
        $otpInfo = $this->otpRepository->getOtpInfo($phone);

        if (!$otpInfo) {
            $otp = Otp::create(['phone' => $phone]);
        } else {
            $otp = $otpInfo;
        }

        $token = JWTAuth::fromUser($otp);

        return $token;
    }


    /**
     * @param $request
     * @param $number
     * @return JsonResponse
     */
    public function getCustomerInfoWithToken($request, $number)
    {
        $response = IdpIntegrationService::getCustomerBasicInfo($number);

        $data = json_decode($response['response'], true);

        if (isset($data['status'])) {
            $user = Customer::where('phone', $data['data']['mobile'])->first();

            if (!$user) {
                return $this->sendErrorResponse("User Credentials Invalid", [], HttpStatusCode::UNAUTHORIZED);
            }

            $customer = $this->customerService->prepareCustomerBasicInfo($user, $data['data']);
        } else {
            $customer = null;
        }

        $data = $request->all();
        $data['otp'] = $request->input('otp');
        $token = IdpIntegrationService::otpGrantTokenRequest($data);

        return [
            'token' => json_decode($token['response']),
            'customer' => $customer,
        ];
    }


    /**
     * Get Refresh Token
     *
     * @param $request
     * @return JsonResponse
     */
    public function getRefreshToken($request)
    {
        $data = $request->all();
        $token = IdpIntegrationService::otpRefreshTokenRequest($data);
        $response = json_decode($token['response'], true);

        if (isset($response['error'])) {
            return $this->sendErrorResponse(
                $response['message'],
                [
                    'message' => "The refresh token is invalid."
                ],
                HttpStatusCode::UNAUTHORIZED
            );
        }

        return $this->sendSuccessResponse(
            json_decode($token['response']),
            "Refresh Token",
            [],
            HttpStatusCode::SUCCESS
        );
    }

    /**
     * Forgot password
     *
     * @param $request
     * @return JsonResponse
     * @throws TokenInvalidException
     */
    public function forgetPassword($request)
    {
        $data = $request->all();

        $data['provider'] = "users";

        $data['otp'] = $request->input('otp');

        $data['mobile'] = $request->input('phone');

        $data['password'] = $request->input('password');

        $data['password_confirmation'] = $request->input('password');

        $response = IdpIntegrationService::forgetPasswordRequest($data);

        if ($response['status_code'] == 200) {
            return $this->sendSuccessResponse(
                [],
                "Password updated successfully!"
            );
        }

        $errors = json_decode($response['response'], true);

        $message = "Password reset failed. please try again later";

        if ($errors ['error'] == 'invalid_otp') {
            $message = "Your OTP is invalid";
        }

        return $this->sendErrorResponse(
            $message,
            [
                'message' => $message,
                'hint' => $errors['error_description'],
                'details' => $errors
            ],
            HttpStatusCode::BAD_REQUEST
        );
    }


    /**
     * Change Password
     *
     * @param $request
     * @return JsonResponse
     * @throws CurlRequestException
     * @throws OldPasswordMismatchException
     * @throws TokenInvalidException
     * @throws \App\Exceptions\TokenNotFoundException
     * @throws \App\Exceptions\TooManyRequestException
     */
    public function changePassword($request)
    {
        $user = $this->customerService->getAuthenticateCustomer($request);

        if (!$user) {
            return $this->sendErrorResponse("User not found", [], HttpStatusCode::UNAUTHORIZED);
        }

        $data['oldPassword'] = $request->old_password;
        $data['newPassword'] = $request->new_password;
        $data['newPassword_confirmation'] = $request->new_password;
        $data['mobile'] = $user->phone;
        $data['customer_token'] = $request->bearerToken();

        $response = IdpIntegrationService::changePasswordRequest($data);

        if ($response['status_code'] != 200) {
            $errors = json_decode($response ['response'], true);
            if ($errors['error'] == 'Invalid password') {
                throw new OldPasswordMismatchException();
            }

            $errorObj = new \stdClass();
            $errorObj->message = $errors['message'];
            $errorObj->hint = $errors['message'];
            $errorObj->code = 500;
            $errorObj->target = 'query';

            return response()->json([
                'status' => 'FAIL',
                'status_code' => 400,
                'error' => $errorObj
            ], 400);
        }

        return $this->sendSuccessResponse(
            [],
            "Password updated successfully!"
        );
    }

    /**
     * Generate OTP
     *
     * @param $n
     * @return string
     */
    public function generateNumericOTP($n)
    {
        $generator = "1357902468";

        $result = "";

        for ($i = 1; $i <= $n; $i++) {
            $result .= substr($generator, (rand() % (strlen($generator))), 1);
        }

        return $result;
    }


    /**
     * Generate OTP Token
     *
     * @param  int  $length
     * @return string
     */
    public function generateOtpToken($length = 10)
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';

        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }

        return $randomString;
    }

    /**
     * Send request
     *
     * @param $request
     */
    public function requestSignUpBonus($request): void
    {
        $bonus_data = [
            'msisdn' => substr($request->username, 1),
            'date' => date("d-M-Y"),
            'bonustype' => "SignUp"
        ];

        // call event here
        try {
            event(new SignUpBonus($bonus_data));
            Log::info('Success: Sign up bonus');
        } catch (Exception $ex) {
            Log::info('Error : Sign up bonus ' . $ex->getMessage());
        }
    }
}
