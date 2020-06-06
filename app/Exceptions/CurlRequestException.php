<?php

namespace App\Exceptions;

use App\Enums\ApiErrorCode;
use App\Enums\ApiErrorType;
use Exception;
use Illuminate\Support\Facades\Log;

class CurlRequestException extends Exception
{
    private $errorObj;
    private $response;
    private const ERROR_MESSAGE =  'Something unexpected happened. Please try again later.';
    private const ERROR_TYPE = ApiErrorType::CURL_EXCEPTION_ERROR;
    private const ERROR_CODE = ApiErrorCode::CURL_EXCEPTION_ERROR;
    private const ERROR_HINT =  'Curl request Exception.Failed to connect host';
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
            Log::channel('errorLog')->error($this->response);
        } catch (Exception $e) {
            Log::error('Log Error Issue');
        }

        return response()->json([
            'status' => 'FAIL',
            'status_code' => 505,
            'error' => $this->errorObj,
            'details' => [
                'url'       => $this->response['url'],
                'http_code' => $this->response['http_code']
            ]
        ], 505);
    }
}
