<?php

namespace App\Http\Controllers\API\V1;

use App\Exceptions\BLServiceException;
use App\Exceptions\CurlRequestException;
use App\Http\Controllers\Controller;
use App\Http\Requests\FnfManageRequest;
use App\Services\Banglalink\FnfService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

/**
 * Class FnfController
 * @package App\Http\Controllers\API\V1
 */
class FnfController extends Controller
{
    /**
     * @var FnfService
     */
    protected $fnfService;


    /**
     * FnfController constructor.
     * @param FnfService $fnfService
     */
    public function __construct(FnfService $fnfService)
    {
        $this->fnfService = $fnfService;

       // $this->middleware('idp.verify');
    }

    /**
     * Retrieve a listing of the resource.
     *
     * @param Request $request
     * @return Response|string
     * @throws BLServiceException
     * @throws CurlRequestException
     */
    public function index(Request $request)
    {
        return $this->fnfService->getFnfList($request);
    }

    /**
     * Manage FNF
     * @param FnfManageRequest $request
     * @return string
     * @throws BLServiceException
     * @throws CurlRequestException
     */
    public function manageFnf(FnfManageRequest $request)
    {
        $type = "fnf";
        return $this->fnfService->manageFnf($request, $type);
    }


    /**
     * Manage  Super FNF
     * @param FnfManageRequest $request
     * @return string
     * @throws BLServiceException
     * @throws CurlRequestException
     */
    public function manageSuperFnf(FnfManageRequest $request)
    {
        $type = "super_fnf";
        return $this->fnfService->manageFnf($request, $type);
    }
}
