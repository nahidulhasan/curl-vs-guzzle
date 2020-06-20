<?php

namespace App\Services\Banglalink;

use App\Enums\HttpStatusCode;
use App\Exceptions\CurlRequestException;
use App\Models\Customer;
use App\Models\ProductCore;
use App\Services\ApiBaseService;
use App\Services\CustomerService;
use Illuminate\Http\Request;

/**
 * Class FnfService
 * @package App\Services\Banglalink
 */
class InternetOfferService extends BaseService
{

    /**
     * @var ApiBaseService
     */
    protected $apiBaseService;

    /**
     * @var CustomerService
     */
    protected $customerService;

    /**
     * @var BanglalinkCustomerService
     */
    protected $blCustomerService;

    protected $balanceService;

    protected const CUSTOMER_ENDPOINT   = "/customer-information/customer-information";
    protected const INTERNET_GIFT_ENDPOINT = "/provisioning/provisioning/gift-to";
    protected const INTERNET_TRANSFER_ENDPOINT = "/provisioning/transfer/data-volume-transfer";


    /**
     * FnfService constructor.
     * @param  ApiBaseService  $apiBaseService
     * @param  CustomerService  $customerService
     * @param  BalanceService  $balanceService
     * @param  BanglalinkCustomerService  $blCustomerService
     */
    public function __construct(
        ApiBaseService $apiBaseService,
        CustomerService $customerService,
        BalanceService $balanceService,
        BanglalinkCustomerService $blCustomerService
    ) {
        $this->apiBaseService = $apiBaseService;
        $this->customerService = $customerService;
        $this->blCustomerService = $blCustomerService;
        $this->balanceService = $balanceService;
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \App\Exceptions\BLServiceException
     * @throws CurlRequestException
     */
    public function giftInternetPack(Request $request)
    {
        $customer = $this->customerService->getAuthenticateCustomer($request);

        if (!$customer) {
            return $this->apiBaseService->sendErrorResponse("Token Invalid", [], HttpStatusCode::UNAUTHORIZED);
        }

        $mobile = "88" . $customer->phone;

        $customer_info = $this->blCustomerService->getCustomerInfoByNumber($mobile);

        if ($customer_info->getData()->status == "FAIL") {
            return $this->apiBaseService->sendErrorResponse(
                "Internal server error",
                [
                    "message" => 'Currently Service unavailable. Try again later.'
                ],
                HttpStatusCode::INTERNAL_ERROR
            );
        }

        $data = $request->all();

        $product_code = $data['product_code'];
        $customer_msisdn = $mobile;
        $gift_to_msisdn =  "88" . $data['gift_to_number'];

        $product = ProductCore::where('product_code', $product_code)->first();

        $customer_type = Customer::connectionType(Customer::find($customer->id));
        $customer_id = $customer->customer_account_id;
        // check the balance
        if ($customer_type == 'PREPAID') {
            $balance = $this->balanceService->getPrepaidBalance($customer_id);
            $product_price = $product->mrp_price;

            if ($product_price > $balance) {
                return $this->apiBaseService->sendSuccessResponse(
                    [
                        'pack_price'       => $product_price,
                        'current_balance'  => $balance,
                    ],
                    "You don't have enough balance to gift this package.",
                    [],
                    220,
                    220
                );
            }
        }

        if ($customer_type == 'POSTPAID') {
            $balance = $this->balanceService->getPostpaidBalance($customer_id);
            $product_price = $product->mrp_price;
            if ($product_price > $balance) {
                return $this->apiBaseService->sendSuccessResponse(
                    [
                        'pack_price'       => $product_price,
                        'current_balance'  => $balance,
                    ],
                    "You don't have enough balance to gift this package.",
                    [],
                    220,
                    220
                );
            }
        }

        $param = [
            'id' => $product_code,
            'subscriptionId' => $customer_msisdn,
            'giftTo' => $gift_to_msisdn
        ];

        $result = $this->post(self::INTERNET_GIFT_ENDPOINT, $param);

        if ($result['status_code'] == 200) {
            return $this->apiBaseService->sendSuccessResponse(
                json_decode($result['response'], true),
                "Internet Pack gift successfully",
                [],
                HttpStatusCode::SUCCESS
            );
        }

        return $this->apiBaseService->sendErrorResponse(
            "Internal server error",
            [
                "message" => 'Currently Service unavailable. Try again later.'
            ],
            HttpStatusCode::INTERNAL_ERROR
        );
    }


    /**
     * @param $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \App\Exceptions\BLServiceException
     * @throws CurlRequestException
     */
    public function transferInternetPack($request)
    {
        $customer = $this->customerService->getAuthenticateCustomer($request);

        if (!$customer) {
            return $this->apiBaseService->sendErrorResponse("Token Invalid", [], HttpStatusCode::UNAUTHORIZED);
        }

        $mobile = "88" . $customer->phone;

        $customer_info = $this->blCustomerService->getCustomerInfoByNumber($mobile);

        if ($customer_info->getData()->status == "FAIL") {
            return $this->apiBaseService->sendErrorResponse(
                "Internal server error",
                [],
                HttpStatusCode::INTERNAL_ERROR
            );
        }

        $data = $request->all();

        $product_code = $data['dataVolumeId'];
        $customer_msisdn = $mobile;
        $receiver_msisdn =  "88" . $data['receiverMsisdn'];

        $param = [
            'dataVolumeId' => $product_code,
            'senderSubscriptionId' => $customer_msisdn,
            'receiverMsisdn' => $receiver_msisdn
        ];

        $result = $this->post(self::INTERNET_TRANSFER_ENDPOINT, $param);

        $response = json_decode($result['response'], true);

        if ($result['status_code'] == 200) {
            if ($response['messgeCode'] == 200 &&  $response['message'] == "OK") {
                return $this->apiBaseService->sendSuccessResponse(
                    json_decode($result['response'], true),
                    "Internet Pack transfer successfully",
                    [],
                    HttpStatusCode::SUCCESS
                );
            }
            return $this->apiBaseService->sendErrorResponse(
                "Internal server error",
                [],
                HttpStatusCode::INTERNAL_ERROR
            );
        }

        return $this->apiBaseService->sendErrorResponse(
            "Internal server error",
            [],
            HttpStatusCode::INTERNAL_ERROR
        );
    }
}
