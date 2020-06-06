<?php

namespace App\Repositories;

use App\Models\Customer;
use App\Models\User;
use App\Models\Notification;
use App\Models\NotificationDetails;
use App\Http\Requests\PushNotificationRequest;
use Edujugon\PushNotification\PushNotification;
use Illuminate\Database\Eloquent\ModelNotFoundException;

/**
 * Class PushNotificationRepository
 *
 * @package App\Repositories
 */
class PushNotificationRepository
{
    /**
     * @var Notification
     */
    protected $model;

    /**
     * PushNotificationRepository constructor.
     *
     * @param Notification $model
     */
    public function __construct(Notification $model)
    {
        $this->model = $model;
        $this->firebaseServerKey = config('app.firebase_server_key');
    }

    /**
     * Retrieve notification info
     *
     * @return integer $id
     */
    public function getNotificationDetails($id = 0)
    {
        $failed_msisdn = array();
        $data = array();
        $notification = new Notification();

        if ($id != 0) {
            $notification = $this->model->find($id);
        }

        if (!$notification) {
            abort(404, 'Notification not found');
        } elseif (empty($id)) {
            abort(400, 'Valid notification id (integer) is required');
        } elseif ($id == 0) {
            abort(422, 'Valid notification id (integer) is required');
        }

        if ($notification->status == $this->model::PERTIALLY_SUCCESSFULL) {
            $allNotificationDetails = $notification->details()->get();

            foreach ($allNotificationDetails as $notificationDetails) {
                if (!empty($notificationDetails->message)) {
                    $failed_msisdn[][$notificationDetails->phone] = $notificationDetails->message;
                }
            }
        }

        $data[0] = $notification;
        $data[1] = $failed_msisdn;

        return $data;
    }

    /**
     * Send firebase push notification to all or individuals
     *
     * @param \App\Http\Requests\PushNotificationRequest $request
     * @return \App\Models\Notification $notification
     */
    public function sendPushNotification(PushNotificationRequest $request)
    {
        $push = new PushNotification('fcm');
        $data = $request->input('data');

        $notification = new Notification();
        $notification->save();

        if ($request->input('send_to_type') == 'ALL') {
            $push->setMessage([
                'notification' => [
                    'title' => $request->input('title'),
                    'body' =>  $request->input('body'),
                    'sound' => 'default'
                ],
                'data' => [
                        'cid' => $data["cid"],
                        'url' => $data["url"],
                        'component' => $data["component"]
                        ]
                ])
                ->setApiKey($this->firebaseServerKey)
                ->sendByTopic('all');

                $notificationDetals = new NotificationDetails();
                $notificationDetals->phone = 'all';
                $notificationDetals->notification_id = $notification->id;
                $notificationDetals->status = 1;


                $notification->status = Notification::SUCCESSFUL;
                $notification->title = $request->input('title');
                $notification->body = $request->input('body');

                if ($request->has('category_slug')) {
                    $notification->category_slug = $request->input('category_slug');
                }

                if ($request->has('category_name')) {
                    $notification->category_name = $request->input('category_name');
                }



                $notification->save();
                $notificationDetals->save();
        } else {
            $recipients = $request->input('recipients');
            $status = 1;
            $failed = 0;
            $success = 0;

            foreach ($recipients as $recipient) {
                if (preg_match('/^[1-9][0-9]*$/', $recipient)) {
                    $recipient = "0" . $recipient;
                }

                $user = Customer::where('phone', $recipient)->first();
                $notificationDetals = new NotificationDetails();
                $notificationDetals->phone = $recipient;
                $notificationDetals->notification_id = $notification->id;

                if (empty($user)) {
                    $failed = 1;
                    $notificationDetals->status = 0;
                    $notificationDetals->message = 'User is not registered';
                } else {
                    $push->setMessage([
                        'notification' => [
                            'title' => $request->input('title'),
                            'body' =>  $request->input('body'),
                            'sound' => 'default'
                        ],
                        'data' => [
                                'cid' => $data["cid"],
                                'url' => $data["url"],
                                'component' => $data["component"]
                                ]
                        ])
                        ->setApiKey($this->firebaseServerKey)
                        ->setDevicesToken($user->device_token)
                        ->send();

                    $status = ($push->getFeedback()->success) ? $push->getFeedback()->success :
                                            $push->getFeedback()->failure;

                    if ($status == 1) {
                        $success = 1;
                        $notificationDetals->status = $status;
                    } else {
                        $failed = 1;
                        $notificationDetals->status = $status;
                    }
                }

                $notificationDetals->save();
            }

            if ($failed == 1 && $success == 1) {
                $notification->status = Notification::PERTIALLY_SUCCESSFULL;
            } elseif ($failed == 1 && $success == 0) {
                $notification->status = Notification::FAILED;
            } else {
                $notification->status = Notification::SUCCESSFUL;
            }

            $notification->title = $request->input('title');
            $notification->body = $request->input('body');

            if ($request->has('category_slug')) {
                $notification->category_slug = $request->input('category_slug');
            }

            if ($request->has('category_name')) {
                $notification->category_name = $request->input('category_name');
            }


            $notification->save();
        }

        return $notification;
    }


    /**
     * Attach Notification to user
     *
     * @param $notification_id
     * @param $user_phone
     * @return string
     */
    public function attachmentNotificationToUser($notification_id, $user_phone)
    {

        $notification = $this->model::find($notification_id);

        $users = Customer::whereIn('phone', $user_phone)->select('id')->get();

        $user_ids = array_map(function ($user) {
            return $user['id'];
        }, $users->toArray());

        $notification->users()->attach($user_ids);

        return 'success';
    }
}
