<?php

namespace App\Exceptions;

use Exception;

class TooManyRequestException extends Exception
{
    private $errorObj;
    private const ERROR_HINT = 'Too many attempts issue from IDP ';
    private const ERROR_MESSAGE = 'Temporary service down.Please, try later';
    private const ERROR_TARGET = 'query';

    private function initErrorObj()
    {
        $this->errorObj = new \stdClass();
        $this->errorObj->message = self::ERROR_MESSAGE;
        $this->errorObj->hint = self::ERROR_HINT;
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
            'status_code' => 429,
            'error' => $this->errorObj
        ], 429);
    }
}
