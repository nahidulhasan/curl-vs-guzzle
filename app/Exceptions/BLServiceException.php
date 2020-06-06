<?php

namespace App\Exceptions;

use App\Enums\ApiErrorCode;
use App\Enums\ApiErrorType;
use Exception;
use Illuminate\Support\Facades\Log;

class BLServiceException extends Exception
{
    private $errorObj;
    private $response;
    private const ERROR_MESSAGE =  'Service unavailable. Please, Try again later';
    private const ERROR_TYPE = ApiErrorType::BL_SERVICE_ERROR;
    private const ERROR_CODE = ApiErrorCode::BL_SERVICE_ERROR;
    private const ERROR_HINT =  'Getting HTTP Error from Banglalink Service';
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
    public function __construct($response)
    {
        $this->initErrorObj();
        $this->response = $response;
    }

    public function render()
    {
        $this->errorObj->message = self::ERROR_MESSAGE;
        $this->errorObj->type = self::ERROR_TYPE;
        $this->errorObj->code = self::ERROR_CODE;
        $this->errorObj->target = self::ERROR_TARGET;
        $this->errorObj->hint = self::ERROR_HINT;

        try {
            Log::channel('blServiceErrorLog')->error($this->response);
        } catch (Exception $e) {
            Log::error('Log Error Issue');
        }

        return response()->json([
            'status' => 'FAIL',
            'status_code' => 500,
            'error' => $this->errorObj,
            'details' => [
                json_decode($this->response, true)
            ]
        ], 500);
    }
}
