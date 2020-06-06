<?php

namespace App\Http\Middleware;

use App\Enums\HttpStatusCode;
use App\Exceptions\TokenInvalidException;
use App\Exceptions\TokenNotFoundException;
use App\Services\ApiBaseService;
use App\Services\IdpIntegrationService;
use Closure;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\Request;

class VerifyUserViaIDP extends ApiBaseService
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     * @return mixed
     * @throws TokenInvalidException
     * @throws TokenNotFoundException
     */
    public function handle($request, Closure $next)
    {

        if (!$request->header('authorization')) {
            throw new TokenNotFoundException();
        }

        $bearerToken = ['token' => $request->header('authorization')];

        $response = IdpIntegrationService::tokenValidationRequest($bearerToken);

        $response_data = json_decode($response['response']);

        if (isset($response_data->user) && $response_data->token_status == 'Valid') {
               return $next($request);
        }

        throw new TokenInvalidException();
    }
}
