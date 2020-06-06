<?php

namespace App\Repositories;

use App\Http\Requests\UserNotificationRequest;
use App\Models\User;
use App\Http\Requests\DeviceTokenRequest;
use Illuminate\Http\Request;

/**
 * Class UserRepository
 *
 * @package App\Repositories
 */
class UserRepository
{
    /**
     * @var User
     */
    protected $model;

    /**
     * UserRepository constructor.
     *
     * @param User $model
     */
    public function __construct(User $model)
    {
        $this->model = $model;
    }

    /**
     * Retrieve User info
     *
     * @return mixed
     */
    public function saveDeviceToken(DeviceTokenRequest $request)
    {
        $user = $this->model->where('phone', $request->input('phone'))->first();

        if (empty($user)) {
            abort(404, "User Not Found");
        }

        $user->device_token = $request->input('device_token');
        $user->save();

        return $user;
    }

    public function notifications(Request $request)
    {
/*        $user = $this->model->where('phone', $request->input('phone'))->first();

        if(empty($user)) abort(404, "User Not Found");*/

         $user = $this->model->first();

        if ($user) {
            $notifications = $user->notifications()->latest()->paginate(15);

            dd($notifications->toArray());
        }
    }
}
