<?php

namespace App\Exceptions;

use App\Enums\ApiErrorCode;
use App\Enums\ApiErrorType;
use Exception;

class TokenNotFoundException extends Exception
{
    private $errorObj;
    private const ERROR_TYPE = ApiErrorType::TOKEN_NOT_FOUND_ERROR;
    private const ERROR_CODE = ApiErrorCode::TOKEN_NOT_FOUND_ERROR;
    private const ERROR_HINT = 'Token not found';
    private const ERROR_MESSAGE = 'You need to login first';
    private const ERROR_TARGET = 'query';

    private function initErrorObj()
    {
        $this->errorObj = new \stdClass();
        $this->errorObj->message = self::ERROR_MESSAGE;
        $this->errorObj->hint = self::ERROR_HINT;
        $this->errorObj->type = self::ERROR_TYPE;
        $this->errorObj->code = self::ERROR_CODE;
        $this->errorObj->target = self::ERROR_TARGET;
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
        return response()->json([
            'status' => 'FAIL',
            'status_code' => 401,
            'error' => $this->errorObj
        ], 401);
    }
}
