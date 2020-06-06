<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

/**
 * @method static static OptionOne()
 * @method static static OptionTwo()
 * @method static static OptionThree()
 */
final class HttpStatusCode extends Enum
{
    const SUCCESS          =   200;
    const CREATED          =   201;
    const BAD_REQUEST      =   400;
    const VALIDATION_ERROR =   422;
    const UNAUTHORIZED     =   401;
    const NOT_FOUND        =   404;
    const INTERNAL_ERROR   =   500;
}
