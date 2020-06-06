<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

final class ApiErrorCode extends Enum
{
    public const VALIDATION_FAILED_ERROR   = 'ERROR_1001';
    public const TOKEN_NOT_FOUND_ERROR     = 'ERROR_4001';
    public const TOKEN_INVALID_ERROR       = 'ERROR_4002';
    public const NOT_FOUND_ERROR           = 'ERROR_4004';
    public const METHOD_NOT_ALLOWED_ERROR  = 'ERROR_4006';
    public const BL_SERVICE_ERROR          = 'ERROR_5001';
    public const INTERNAL_SERVICE_ERROR    = 'ERROR_5000';
    public const CURL_EXCEPTION_ERROR      = 'ERROR_9000';
}
