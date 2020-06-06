<?php

namespace App\Services;

use Exception;
use App\Repositories\UserRepository;
use App\Http\Requests\DeviceTokenRequest;

/**
 * Class BannerService
 * @package App\Services
 */
class UserService extends ApiBaseService
{

    /**
     * @var UserRepository
     */
    protected $userRepository;


    /**
     * UserService constructor.
     *
     * @param UserRepository $UserRepository
     */
    public function __construct(UserRepository $UserRepository)
    {
        $this->userRepository = $UserRepository;
    }

    /**
     * Saving device token
     *
     * @return mixed|string
     */
    public function saveDeviceToken(DeviceTokenRequest $request)
    {
        try {
            $data = $this->userRepository->saveDeviceToken($request);
            return $this->sendSuccessResponse($data, 'User Device Token Saved');
        } catch (Exception $exception) {
            return $this->sendErrorResponse($exception->getMessage(), [], $exception->getStatusCode());
        }
    }
}
