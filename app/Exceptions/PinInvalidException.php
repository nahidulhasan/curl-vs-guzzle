<?php

namespace App\Exceptions;

use Exception;

class PinInvalidException extends Exception
{

    private $errorObj;
    private const ERROR_MESSAGE =  'Invalid Pin. Check and try again';
    private const ERROR_TYPE = 400;
    private const ERROR_CODE = 'ERR_PIN_2220';
    private const ERROR_HINT =  'Invalid Pin. Check and try again';
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
