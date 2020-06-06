<?php

namespace App\Http\Controllers\API\V1;

use App\Enums\HttpStatusCode;
use App\Exceptions\BLServiceException;
use App\Exceptions\CurlRequestException;
use App\Http\Requests\ChangeBalanceTransferPinRequest;
use App\Http\Requests\CustomerRequest;
use App\Http\Requests\DeviceTokenRequest;
use App\Http\Requests\SetBalanceTransferPinRequest;
use App\Http\Requests\SetPasswordRequest;
use App\Http\Requests\UpdateCustomerDetailsRequest;
use App\Services\Banglalink\BanglalinkCustomerService;
use App\Services\Banglalink\BaringService;
use App\Services\Banglalink\SimService;
use App\Services\CustomerService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class CustomerController extends Controller
{

    /**
     * @var CustomerService
     */
    protected $customerService;
    /**
     * @var SimService
     */
    protected $simService;

    /**
     * @var BaringService
     */
    protected $baringService;


    protected $blCustomerService;


    /**
     * CustomerController constructor.
     * @param CustomerService $customerService
     * @param SimService $simService
     * @param BaringService $baringService
     * @param BanglalinkCustomerService $blCustomerService
     */
    public function __construct(
        CustomerService $customerService,
        SimService $simService,
        BaringService $baringService,
        BanglalinkCustomerService $blCustomerService
    ) {
        $this->customerService = $customerService;
        $this->simService = $simService;
        $this->baringService = $baringService;
        $this->blCustomerService = $blCustomerService;
       // $this->middleware('idp.verify')->except('store', 'saveDeviceToken');
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param CustomerRequest $request
     * @return JsonResponse
     */
    public function store(CustomerRequest $request)
    {
        return $this->customerService->addNewCustomer($request);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @throws \App\Exceptions\TokenInvalidException
     */
    public function getDetails(Request $request)
    {
        return $this->customerService->getCustomerDetails($request);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @throws \App\Exceptions\TokenInvalidException
     */
    public function getCustomerBasicInfo(Request $request)
    {
        return $this->customerService->getCustomerBasicInfo($request);
    }


    /**
     * @param Request $request
     * @return JsonResponse
     * @throws \App\Exceptions\TokenInvalidException
     */
    public function getCustomerProfileImage(Request $request)
    {
        return $this->customerService->getCustomerProfileImage($request);
    }

    /**
     * @param UpdateCustomerDetailsRequest $request
     * @return JsonResponse
     * @throws \App\Exceptions\TokenInvalidException
     */
    public function updateDetails(UpdateCustomerDetailsRequest $request)
    {
        return $this->customerService->updateCustomerDetails($request);
    }

    /**
     * @param SetPasswordRequest $request
     * @return JsonResponse
     * @throws \App\Exceptions\TokenInvalidException
     * @throws \App\Exceptions\TokenNotFoundException
     */
    public function setPassword(SetPasswordRequest $request)
    {
        return $this->customerService->setPassword($request);
    }


    /**
     * Saving device token
     *
     * @param DeviceTokenRequest $request
     * @return JsonResponse
     */
    public function saveDeviceToken(DeviceTokenRequest $request)
    {
        return $this->customerService->saveDeviceToken($request);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function getCustomerSimInfo(Request $request)
    {
        return $this->simService->getSimInfo($request);
    }


    /**
     * Get Baring service info
     *
     * @param Request $request
     * @return string
     */
    public function getCustomerBaringInfo(Request $request)
    {
        return $this->baringService->getBaringService($request);
    }

    /**
     * Activate Baring Service for Lost Sim
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function reportForLostSim(Request $request)
    {
        return $this->baringService->baringServiceActiveForLostSim($request);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @throws BLServiceException
     * @throws CurlRequestException
     * @throws \App\Exceptions\TokenInvalidException
     * @throws \App\Exceptions\TokenNotFoundException
     * @throws \App\Exceptions\TooManyRequestException
     */
    public function deviceSetting(Request $request)
    {
        return $this->blCustomerService->deviceSetting($request);
    }

    /**
     * @param SetBalanceTransferPinRequest $request
     * @return JsonResponse
     * @throws \App\Exceptions\PinAlreadySetException
     */
    public function generateCustomerPin(SetBalanceTransferPinRequest $request)
    {
        return $this->customerService->setTransferPin($request);
    }

    /**
     * @param ChangeBalanceTransferPinRequest $request
     * @return JsonResponse
     * @throws \App\Exceptions\OldPinInvalidException
     * @throws \App\Exceptions\PinNotSetException
     * @throws \App\Exceptions\TokenInvalidException
     */
    public function changeCustomerPin(ChangeBalanceTransferPinRequest $request)
    {
        return $this->customerService->changeTransferPin($request);
    }
}
