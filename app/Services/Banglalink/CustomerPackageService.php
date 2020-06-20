<?php

namespace App\Services\Banglalink;

use App\Enums\HttpStatusCode;
use App\Exceptions\BLServiceException;
use App\Models\ProductCore;
use App\Repositories\CustomerRepository;
use App\Services\ApiBaseService;
use App\Services\IdpIntegrationService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class CustomerPackageService extends BaseService
{
    protected $responseFormatter;
    protected const CUSTOMER_PACKAGE_API_ENDPOINT = "/customer-information/customer-information";
    /**
     * @var CustomerRepository
     */
    protected $customerRepository;

    public function __construct()
    {
        $this->responseFormatter = new ApiBaseService();
        $this->customerRepository = new CustomerRepository();
    }

    private function getPackageInfoUrl($customer_id)
    {
        return self::CUSTOMER_PACKAGE_API_ENDPOINT . '/' . $customer_id . '/packages';
    }

    private function getAvailablePackageInfoUrl($customer_id)
    {
        return self::CUSTOMER_PACKAGE_API_ENDPOINT . '/' . $customer_id . '/available-packages';
    }

    public function getPackageInfo($customer_id)
    {
        $response = $this->get($this->getPackageInfoUrl($customer_id));
        $response = json_decode($response['response']);

        $package = [
            'title' => null,
            'code' => null
        ];
        if (!$response) {
            return $package;
        }
        if (isset($response->error)) {
            return $package;
        }

        $tariff = [];

        if (!empty($response->tariffs)) {
            $collection = collect($response->tariffs)->where('category', '<>', null);

            foreach ($collection as $val) {
                $tariff [] = [
                   'category' => $val->category,
                   'details'  => $val->details
                ];
            }
        }

        //$package = [$this->getPackageDetailsByCode($response->code)];
        $package = [
            'code'    => $response->code,
            'title'   => $response->name,
            'details' => null,
            'image'   => null
        ];

        $package = array_merge($package, ['tariffs' => $tariff]);

        return $package;
    }

    public function getPackageDetailsByCode($code)
    {
        $package = ProductCore::whereHas(
            'blProduct',
            function ($q) {
                $q->where('status', 1);
            }
        )->with('blProduct')->where('product_code', $code)->first();

        $data = [];

        if ($package) {
            $data = [
                'code'    => $package->product_code,
                'title'    => $package->name,
                'details' => $package->blProduct->description,
                'image'   => asset($package->blProduct->media)
            ];
        }

        return $data;
    }


    public function getCustomerEligiblePackages($customer_id)
    {
        $response = $this->get($this->getAvailablePackageInfoUrl($customer_id));
        $response = json_decode($response['response']);
        $packages = [];

        if (!$response) {
            return $packages;
        }

        if (isset($response->error)) {
            return new BLServiceException($response);
        }

        $collection = collect($response)->filter(function ($value) {
            return !empty($value->tariffs);
        });

        foreach ($collection as $plan) {
            $data = $this->getPackageDetailsByCode($plan->code);
            if (!empty($data)) {
                $packages [] = $data;
            }
        }

        return $packages;
    }
}
