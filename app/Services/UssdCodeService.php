<?php

namespace App\Services;

use App\Enums\HttpStatusCode;
use App\Http\Resources\UssdCodeResource;
use App\Repositories\UssdCodeRepository;

class UssdCodeService extends ApiBaseService
{

    /**
     * @var UssdCodeRepository
     */
    protected $ussdCodeRepository;

    /**
     * UssdCodeService constructor.
     * @param UssdCodeRepository $ussdCodeRepository
     */
    public function __construct(UssdCodeRepository $ussdCodeRepository)
    {
        $this->ussdCodeRepository = $ussdCodeRepository;
    }


    /**
     * Version Info
     * @return mixed
     */
    public function getUssdCodeList()
    {
        try {
            $data = $this->ussdCodeRepository->getUssdCode();

            $formatted_data = UssdCodeResource::collection($data);

            return $this->sendSuccessResponse($formatted_data, 'USSD Code List', [], HttpStatusCode::SUCCESS);
        } catch (Exception $exception) {
            return $this->sendErrorResponse($exception->getMessage(), [], HttpStatusCode::INTERNAL_ERROR);
        }
    }

}
