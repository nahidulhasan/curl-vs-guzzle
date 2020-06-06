<?php

namespace App\Services;

use App\Enums\HttpStatusCode;
use App\Exceptions\OldPinInvalidException;
use App\Exceptions\PinAlreadySetException;
use App\Exceptions\PinNotSetException;
use App\Exceptions\TokenInvalidException;
use App\Exceptions\TokenNotFoundException;
use App\Exceptions\TooManyRequestException;
use App\Http\Requests\DeviceTokenRequest;
use App\Http\Requests\SetBalanceTransferPinRequest;
use App\Http\Resources\CustomerResource;
use App\Models\Customer;
use App\Models\Otp;
use App\Repositories\CustomerRepository;
use App\Services\Banglalink\CustomerPackageService;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use http\Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

/**
 * Class BannerService
 * @package App\Services
 */
class CustomerService extends ApiBaseService
{


    /**
     * @var CustomerRepository
     */
    protected $customerRepository;
    /**
     * @var CustomerPackageService
     */
    protected $CustomerPackageService;


    /**
     * CustomerService constructor.
     * @param  CustomerRepository  $customerRepository
     * @param  CustomerPackageService  $customerPackageService
     */
    public function __construct(CustomerRepository $customerRepository, CustomerPackageService $customerPackageService)
    {
        $this->customerRepository = $customerRepository;
        $this->CustomerPackageService = $customerPackageService;
    }


    /**
     *
     *
     * @param $request
     * @return JsonResponse
     */
    public function addNewCustomer($request)
    {
        try {
            $response = $this->customerRepository->create($request->all());
            return $this->sendSuccessResponse($response, 'New Customer');
        } catch (Exception $exception) {
            return $this->sendErrorResponse($exception->getMessage());
        }
    }


    /**
     * @param  Request  $request
     * @return JsonResponse
     * @throws TokenInvalidException
     */
    public function getCustomerDetails(Request $request)
    {
        // validate the token and get details info
        $bearerToken = ['token' => $request->header('authorization')];

        $response = IdpIntegrationService::tokenValidationRequest($bearerToken);

        $data = json_decode($response['response'], true);

        if ($data['token_status'] != 'Valid') {
            throw new TokenInvalidException();
        }


        if ($data['status'] != 'SUCCESS') {
            return $this->sendErrorResponse("IDp service Unavailable", [], 500);
        }

        $msisdn_key = 'mobile';

        $user = Customer::where('phone', $data['user'][$msisdn_key])->first();


        if (!$user) {
            throw new TokenInvalidException();
        }

        return $this->sendSuccessResponse(
            $this->prepareCustomerDetails($user, $data['user']),
            'Customer Details Info'
        );
    }


    /**
     * @param $customer
     * @param $data
     * @return array
     */
    public function prepareCustomerDetails($customer, $data)
    {
        return [
            'id' => $customer->id,
            'customer_account_id' => $customer->customer_account_id,
            'name' => isset($data['name']) ? $data['name'] : null,
            'msisdn_number' => $data['mobile'],
            'connection_type' => Customer::connectionType($customer),
            'email' => $data['email'],
            'birth_date' => isset($data['birth_date']) ? $data['birth_date'] : null,
            'profile_image' => isset($data['profile_image']) ? $data['profile_image'] : null,
            'enable_balance_transfer' => ($customer->balance_transfer_pin) ? true : false,
            'package' => Customer::package($customer),
            'is_password_set' => $data['is_password_set'] ? true : false
        ];
    }


    /**
     * @param  Request  $request
     * @return JsonResponse
     * @throws TokenInvalidException
     */
    public function updateCustomerDetails(Request $request)
    {
        // validate the token and get details info
        $bearerToken = ['token' => $request->header('authorization')];

        $result = IdpIntegrationService::tokenValidationRequest($bearerToken);

        $data = json_decode($result['response'], true);

        if ($data['token_status'] != 'Valid') {
            throw new TokenInvalidException();
        }

        $msisdn_key = 'mobile';

        $customer = Customer::where('phone', $data['user'][$msisdn_key])->first();

        if (!$customer) {
            throw new TokenInvalidException();
        }

        $storage_path = null;

        $update_data = [];

        if ($request->hasFile('profile_image')) {
            try {
                /*                $file = $request->file('profile_image');
                                $path = $file->storeAs(
                                    'uploads/profile-images',
                                    md5(strtotime(now())) . '.' . $file->getClientOriginalExtension(),
                                    'public'
                                );*/

                $path = $request->file('profile_image')->getRealPath();

                $storage_path = $path;
            } catch (\Exception $e) {
                Log::emergency($e->getMessage());
                return $this->sendErrorResponse("Something unexpected happened. Try later again", [
                    'message' => 'Something unexpected happened. Try later again',
                    'hint' => $e->getMessage(),
                    'target' => 'query'
                ], 500);
            }
        }

        if ($storage_path) {
            //data:image/;base64
            $base64 = base64_encode(file_get_contents($storage_path));
            $update_data [] = [
/*                'Content-type' => 'multipart/form-data',*/
                'name' => 'profile_photo',
/*                'contents' => fopen("data:image/png;base64," . $base64, 'r')*/
                'contents' => $base64
            ];
        }

        if ($request->has('birth_date')) {
            $update_data [] = [
                'name' => 'birth_date',
                'contents' => $request->birth_date
            ];
        }

        if ($request->has('name')) {
            $update_data [] = [
                'name' => 'name',
                'contents' => $request->name
            ];
        }

        if ($request->has('email')) {
            $update_data [] = [
                'name' => 'email',
                'contents' => $request->email
            ];
        }

        $client = new Client();

        try {
            $response = $client->post(
                env('IDP_HOST') . '/api/v1/customers/update/perform',
                [
                    'headers' => [
                        'Accept' => 'application/json',
                        'Authorization' => 'Bearer ' . $request->bearerToken(),
                    ],

                    'multipart' => $update_data
                ]
            );
        } catch (ClientException $e) {
            $response = $e->getResponse();
            $responseBodyAsString = $response->getBody()->getContents();
            $errors = json_decode($responseBodyAsString)->errors;

            if ($e->getCode() == 422) {
                foreach ($errors as $key => $error) {
                    return $this->sendErrorResponse($error[0], [
                        'message' => $error[0],
                        'hint' => $errors,
                        'target' => $key,
                    ], 422);
                }
            }

            return $this->sendErrorResponse("Cannot update profile. try again later", [
                'message' => 'Cannot update profile. try again later',
                'hint' => $response,
                'target' => 'query',
            ], 500);
        }
        $response = json_decode($response->getBody()->getContents(), true);

        if ($response['status'] != 'Success') {
            return $this->sendErrorResponse("Cannot update profile. try again later", [
                'message' => 'Cannot update profile. try again later',
                'hint' => $response,
                'target' => 'query',
            ], 500);
        }

        /*        try {
                    if ($storage_path) {
                        unlink(public_path($storage_path));
                    }
                } catch (Exception $e) {
                    Log::error('Error in deleting old profile photo' . $e->getMessage());
                }*/

        // return customer details data

        $user = Customer::where('phone', $response['data']['mobile'])->first();

        return $this->sendSuccessResponse(
            $this->prepareCustomerDetails($user, $response['data']),
            'Update Customer Info Successfully'
        );
    }

    //setPassword

    public function setPassword(Request $request)
    {
        if (!$request->bearerToken()) {
            throw new TokenNotFoundException();
        }

        // validate the token and get details info
        $bearerToken = ['token' => $request->header('authorization')];

        $result = IdpIntegrationService::tokenValidationRequest($bearerToken);

        $data = json_decode($result['response'], true);

        if ($data['token_status'] != 'Valid') {
            throw new TokenInvalidException();
        }

        $msisdn_key = 'mobile';

        $customer = Customer::where('phone', $data['user'][$msisdn_key])->first();

        if (!$customer) {
            throw new TokenInvalidException();
        }

        // validate otp token first
        $token_exist = Otp::where('phone', $customer->phone)->first();

        if (!($token_exist && $request->otp_token == $token_exist->token)) {
            return $this->sendErrorResponse("OTP token is invalid", [
                'message' => "OTP token is invalid",
                'hint' => 'OTP token is invalid',
                'target' => 'query'
            ], 400);
        }

        $token_exist->delete();

        $idp_customer_info = $data['user'];

        if ($idp_customer_info['is_password_set']) {
            return $this->sendErrorResponse("Password is already set", [
                'message' => "Password is already set",
                'hint' => 'Password is already set',
                'target' => 'query'
            ], 400);
        }

        $client = new Client();

        $data['otp'] = $request->otp;
        $data['password'] = $request->password;

        try {
            $response = $client->post(
                env('IDP_HOST') . '/api/v1/customers/set/password',
                [
                    'headers' => [
                        'Accept' => 'application/json',
                        'Authorization' => 'Bearer ' . $request->bearerToken(),
                    ],

                    'form_params' => $data
                ]
            );
        } catch (ClientException $e) {
            $response = $e->getResponse();
            $responseBodyAsString = $response->getBody()->getContents();
            $error = json_decode($responseBodyAsString);

            return $this->sendErrorResponse($error->message, [
                'message' => $error->message,
                'hint' => $response,
                'target' => 'query',
                'details' => $error
            ], 500);
        }

        return $this->sendSuccessResponse([], 'Password set Successfully');
    }

    /**
     * Authenticate customer Info
     *
     * @param $request
     * @return mixed
     * @throws TokenInvalidException
     * @throws TooManyRequestException
     * @throws TokenNotFoundException
     */
    public function getAuthenticateCustomer($request)
    {
        if (!$request->bearerToken()) {
            throw new TokenNotFoundException();
        }

        $bearerToken = ['token' => $request->header('authorization')];

        $response = IdpIntegrationService::tokenValidationRequest($bearerToken);

        if ($response['status_code'] == 429) {
            throw new TooManyRequestException();
        }

        $data = json_decode($response['response'], true);

        if ($data['token_status'] != 'Valid') {
            throw new TokenInvalidException();
        }

        return $this->customerRepository->getCustomerInfoByPhone($data['user']['mobile']);
    }


    /**
     * Saving device token
     * @param  DeviceTokenRequest  $request
     * @return JsonResponse
     */
    public function saveDeviceToken($request)
    {
        try {
            $data = $this->customerRepository->saveDeviceToken($request);
            return $this->sendSuccessResponse($data, 'Customer Device Token Saved');
        } catch (Exception $exception) {
            return $this->sendErrorResponse($exception->getMessage(), [], $exception->getStatusCode());
        }
    }


    /**
     * @param Request $request
     * @return JsonResponse
     * @throws PinAlreadySetException
     * @throws TokenInvalidException
     * @throws TokenNotFoundException
     * @throws TooManyRequestException
     */
    public function setTransferPin(Request $request)
    {
        $user = $this->getAuthenticateCustomer($request);

        if (!$user) {
            return $this->sendErrorResponse("User not found", [], HttpStatusCode::UNAUTHORIZED);
        }

        if ($user->balance_transfer_pin) {
            throw new PinAlreadySetException();
        }

        $user->balance_transfer_pin = Hash::make($request->pin);

        $user->save();

        return $this->sendSuccessResponse([], 'Balance Transfer Pin is Saved Successfully');
    }


    /**
     * @param Request $request
     * @return JsonResponse
     * @throws OldPinInvalidException
     * @throws PinNotSetException
     * @throws TokenInvalidException
     * @throws TokenNotFoundException
     * @throws TooManyRequestException
     */
    public function changeTransferPin(Request $request)
    {
        $user = $this->getAuthenticateCustomer($request);

        if (!$user) {
            throw new TokenInvalidException();
        }

        if (!$user->balance_transfer_pin) {
            throw new PinNotSetException();
        }

        $hashed_password = $user->balance_transfer_pin;

        if (!Hash::check($request->old_pin, $hashed_password)) {
            throw new OldPinInvalidException();
        }

        $user->balance_transfer_pin = Hash::make($request->new_pin);

        $user->save();

        return $this->sendSuccessResponse([], 'Balance Transfer Pin is changed Successfully');
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @throws TokenInvalidException
     */
    public function getCustomerBasicInfo(Request $request)
    {
        $bearerToken = ['token' => $request->header('authorization')];

        $response = IdpIntegrationService::tokenValidationRequest($bearerToken);

        $data = json_decode($response['response'], true);

        if ($data['token_status'] != 'Valid') {
            throw new TokenInvalidException();
        }

        if ($data['status'] != 'SUCCESS') {
            return $this->sendErrorResponse("IDp service Unavailable", [], 500);
        }

        $msisdn_key = 'mobile';
        $user = Customer::where('phone', $data['user'][$msisdn_key])->first();

        if (!$user) {
            throw new TokenInvalidException();
        }

        return $this->sendSuccessResponse(
            $this->prepareCustomerBasicInfo($user, $data['user']),
            'Customer Details Info'
        );
    }


    /**
     * @param $customer
     * @param $data
     * @return array
     */
    public function prepareCustomerBasicInfo($customer, $data)
    {
        return [
            'id' => $customer->id,
            'customer_account_id' => $customer->customer_account_id,
            'name' => isset($data['name']) ? $data['name'] : null,
            'msisdn_number' => $data['mobile'],
            'connection_type' => Customer::connectionType($customer),
            'email' => $data['email'],
            'birth_date' => isset($data['birth_date']) ? $data['birth_date'] : null,
            'enable_balance_transfer' => ($customer->balance_transfer_pin) ? true : false,
            'package' => Customer::package($customer),
            'is_password_set' => $data['is_password_set'] ? true : false
        ];
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @throws TokenInvalidException
     */
    public function getCustomerProfileImage(Request $request)
    {
        $bearerToken = ['token' => $request->header('authorization')];

        $response = IdpIntegrationService::tokenValidationRequest($bearerToken);

        $data = json_decode($response['response'], true);

        if ($data['token_status'] != 'Valid') {
            throw new TokenInvalidException();
        }


        if ($data['status'] != 'SUCCESS') {
            return $this->sendErrorResponse("IDp service Unavailable", [], 500);
        }

        $msisdn_key = 'mobile';

        $user = Customer::where('phone', $data['user'][$msisdn_key])->first();


        if (!$user) {
            throw new TokenInvalidException();
        }

        $response = IdpIntegrationService::getCustomerProfileImage($data['user'][$msisdn_key]);

        if ($response['status_code'] != 200) {
            return $this->sendErrorResponse('IDP Customer info Problem', [
                'message' => 'Something went wrong. try again later',
                'hint' => 'Getting HTTP error from IDP',
                'details' => []
            ], 400);
        }

        $data = json_decode($response['response'], true);

        $customer = $this->prepareCustomerProfileImage($user, $data['data']);

        return $this->sendSuccessResponse($customer, 'Customer Profile Image');
    }


    /**
     * @param $customer
     * @param $data
     * @return array
     */
    public function prepareCustomerProfileImage($customer, $data)
    {
        return [
            'name' => isset($data['name']) ? $data['name'] : null,
            'mobile' =>   isset($data['mobile']) ? $data['mobile'] : null,
            'profile_image' => isset($data['profile_image']) ? $data['profile_image'] : null
        ];
    }
}
