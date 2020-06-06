<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\BuyInternetPackRequest;
use App\Http\Requests\PriyonjonRewardsRequest;
use App\Services\DummyApiService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class DummyAPIController extends Controller
{

    /**
     * @var DummyApiService
     */
    protected $dummyApiService;

    /**
     * DummyAPIController constructor.
     * @param DummyApiService $dummyApiService
     */
    public function __construct(DummyApiService $dummyApiService)
    {
        $this->dummyApiService = $dummyApiService;
    }

    /**
     * Hello World
     *
     * @return string
     */
    public function index()
    {
        return 'Hello World';
    }


    /**
     * Get Redeem Priyojon points
     *
     * @return mixed
     */
    public function getRedeemPriyojonPoints()
    {
        return $this->dummyApiService->getRedeemPriyojonPoints();
    }


    /**
     * Get Amar offer
     *
     * @return Response
     */
    public function getAmarOffer()
    {
        return $this->dummyApiService->getAmarOffer();
    }


    public function getRoamingUsageSummary()
    {
        return 'ss';
    }

    public function getUsagesSummary()
    {
        return $this->dummyApiService->getUsagesSummary();
    }

    /**
     * Get Usage Details
     *
     * @param $param
     * @param null $roaming_type
     * @return Response
     */
    public function getUsagesDetails($param, $roaming_type = null)
    {
        return $this->dummyApiService->getUsagesDetails($param, $roaming_type);
    }


    /**
     * USSD code list
     *
     * @return JsonResponse
     */
    public function getUssdCode()
    {
        return $this->dummyApiService->getUssdCode();
    }

    public function getPriyojonTiers(Request $request)
    {
        return $this->dummyApiService->getPriyojonTiers();
    }

    public function getPriyojonStatus(Request $request)
    {
        return $this->dummyApiService->getPriyojonStatus($request);
    }

    public function getPriyojonRewards(PriyonjonRewardsRequest $request)
    {
        return $this->dummyApiService->getPriyojonOffers($request);
    }

    /**
     *  get balance summary
     * @return JsonResponse
     */
    public function getBalanceSummary()
    {
        return $this->dummyApiService->getBalanceSummary();
    }

    /*
     *  Balance DETAILS DUMMY APIs
     */

    public function getBalanceDetails($type)
    {
        return $this->dummyApiService->getBalanceDetails($type);
    }

    /*    public function getManageAccounts()
        {
            return $this->dummyApiService->getManageAccounts();
        }*/

    public function requestAdvancedLoan(Request $request)
    {
        return $this->dummyApiService->requestAdvancedLoan($request);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function getUserSubscribedServices(Request $request)
    {
        return $this->dummyApiService->getSubscribedServices($request);
    }

    public function getCustomerCares(Request $request)
    {
        return $this->dummyApiService->getCustomerCares($request);
    }

    public function getInternetPacks(Request $request)
    {
        return $this->dummyApiService->getInternetPacks($request);
    }

    public function buyInternetPack(BuyInternetPackRequest $request)
    {
        return $this->dummyApiService->buyInternetPack($request);
    }
}
