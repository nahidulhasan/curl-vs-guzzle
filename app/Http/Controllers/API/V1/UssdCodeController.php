<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Services\OtpService;
use App\Services\UssdCodeService;

/**
 * Class OtpController
 * @package App\Http\Controllers\API\V1
 */
class UssdCodeController extends Controller
{

    protected $ussdCodeService;


    /**
     * UssdCodeController constructor.
     * @param UssdCodeService $ussdCodeService
     */
    public function __construct(UssdCodeService $ussdCodeService)
    {
        $this->ussdCodeService = $ussdCodeService;
    }

    /**
     * Retrieve a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function getUssdCode()
    {
        return $this->ussdCodeService->getUssdCodeList();
    }
}
