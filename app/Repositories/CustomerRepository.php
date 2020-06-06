<?php

namespace App\Repositories;

use App\Models\Customer;

/**
 * Class CustomerRepository
 * @package App\Repositories
 */
class CustomerRepository extends BaseRepository
{

    /**
     * @var Customer
     */
    protected $model;


    /**
     * CustomerRepository constructor.
     * @param Customer $model
     */
    public function __construct()
    {
        $this->model = new Customer();
    }


    /**
     * @param $phone
     * @return mixed
     */
    public function getCustomerInfoByPhone($phone)
    {
        return $this->model::where('phone', $phone)->first();
    }

    /**
     * Save Device Token
     *
     * @param $request
     * @return mixed
     */
    public function saveDeviceToken($request)
    {
        $user = $this->model->where('phone', $request->input('phone'))->first();

        if (empty($user)) {
            abort(404, "User Not Found");
        }

        $user->device_token = $request->input('device_token');
        $user->save();

        return $user;
    }

    /***
     * @param $customer_id
     * @param $now
     */
    public function updateCustomerLastLogin($customer_id, $now)
    {
        $this->model->where('customer_account_id', $customer_id)->update(['last_login_at' => $now]);
    }
}
