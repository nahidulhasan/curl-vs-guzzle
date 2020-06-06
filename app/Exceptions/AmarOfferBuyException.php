<?php

namespace App\Exceptions;

use App\Enums\ApiErrorCode;
use App\Enums\ApiErrorType;
use Exception;

class AmarOfferBuyException extends Exception
{

    private $errorObj;
    private const ERROR_MESSAGE =  'Purchased Failed.Try later again.';
    private const ERROR_TYPE = ApiErrorType::TOKEN_INVALID_ERROR;
    private const ERROR_CODE = ApiErrorCode::TOKEN_INVALID_ERROR;
    private const ERROR_HINT =  'Purchase API status failed';
    private const ERROR_TARGET = 'query';

    private function initErrorObj()
    {
        $this->errorObj = new \stdClass();
        $this->errorObj->type = "";
        $this->errorObj->code = "";
        $this->errorObj->message = "";
        $this->errorObj->target = "";
    }

    /**
     * TokenNotFoundException constructor.
     */
    public function __construct()
    {
        $this->initErrorObj();
    }

    public function render()
    {
        $this->errorObj->message = self::ERROR_MESSAGE;
        $this->errorObj->hint = self::ERROR_HINT;
        $this->errorObj->type = self::ERROR_TYPE;
        $this->errorObj->code = self::ERROR_CODE;
        $this->errorObj->target = self::ERROR_TARGET;

        return response()->json([
            'status' => 'FAIL',
            'status_code' => 400,
            'error' => $this->errorObj
        ], 400);
    }
}
