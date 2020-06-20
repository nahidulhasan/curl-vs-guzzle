<?php

namespace App\Services\Banglalink;

use App\Enums\HttpStatusCode;
use App\Repositories\CustomerRepository;
use App\Services\ApiBaseService;
use App\Services\IdpIntegrationService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;

class CustomerConnectionTypeService extends BaseService
{
    protected $responseFormatter;
    protected const CUSTOMER_ENDPOINT = "/customer-information/customer-information";
    /**
     * @var CustomerRepository
     */
    protected $customerRepository;

    public function __construct()
    {
        $this->responseFormatter = new ApiBaseService();
        $this->customerRepository = new CustomerRepository();
    }

    private function getConnectionTypeInfoUrl($msisdn)
    {
        return self::CUSTOMER_ENDPOINT . "?include=SUBSCRIPTION_TYPES&msisdn=" . $msisdn;
    }

    public function getConnectionTypeInfo($msisdn)
    {
        if (!$connection_type = Redis::get('connection_type:' . $msisdn)) {
            $response = $this->get($this->getConnectionTypeInfoUrl($msisdn));
            $response = json_decode($response['response']);
            $connection_type = null;
            if (!$response) {
                return $connection_type;
            }
            if (isset($response->error)) {
                return $connection_type;
            }

            Redis::setex('connection_type:' . $msisdn, 60 * 60 * 24, $response->connectionType);
            return $response->connectionType;
        }

        return $connection_type;
    }

    public function getConnectionInfo($msisdn)
    {
        $response = $this->get($this->getConnectionTypeInfoUrl($msisdn));
        $response = json_decode($response['response'], true);
        if (!$response) {
            return [];
        }
        if (isset($response->error)) {
            return [];
        }

        return $response;
    }
}
