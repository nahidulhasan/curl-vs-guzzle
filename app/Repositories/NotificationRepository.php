<?php

namespace App\Repositories;

use App\Models\Customer;
use App\Models\Notification;

/**
 * Class NotificationRepository
 * @package App\Repositories
 */
class NotificationRepository
{

    /**
     * @var Notification
     */
    protected $model;


    /**
     * NotificationRepository constructor.
     */
    public function __construct()
    {
        $this->model = new Notification();
    }


    /**
     * Retrieve Notification list
     *
     * @return mixed
     */
    public function getAllNotificationsWithPagination()
    {
        return $this->model->paginate(15);
    }

    /**
     * Attach Notification to user
     *
     * @param $notification_id
     * @param $mobile
     * @return string
     */
    public function attachmentNotificationToUser($notification_id, $mobile)
    {

        $notification = $this->model::find($notification_id);

        $users = Customer::where('phone', $mobile)->select('id')->get();

        $user_ids = array_map(function ($user) {
            return $user['id'];
        }, $users->toArray());

        $notification->users()->attach($user_ids);

        return 'success';
    }
}
