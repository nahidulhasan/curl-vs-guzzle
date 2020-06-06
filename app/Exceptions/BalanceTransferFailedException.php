<?php

namespace App\Exceptions;

use App\Enums\ApiErrorCode;
use App\Enums\ApiErrorType;
use Exception;
use Illuminate\Support\Facades\Log;

class BalanceTransferFailedException extends Exception
{
    private $errorObj;
    private $response;

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
        $this->errorObj->message = $this-> response ['data']['errors'][0]['detail'];
        $this->errorObj->type = 400;
        $this->errorObj->code = "";
        $this->errorObj->target = 'query';
        $this->errorObj->hint = "";

        return response()->json([
            'status' => 'FAIL',
            'status_code' => 400,
            'error' => $this->errorObj,
            'details' => $this->response
        ], 400);
    }
}
