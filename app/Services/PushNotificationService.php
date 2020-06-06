<?php

namespace App\Services;

use App\Http\Requests\PushNotificationRequest;
use App\Models\Notification;
use App\Repositories\PushNotificationRepository;
use Illuminate\Http\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

class PushNotificationService
{

    /**
     * @var PushNotificationRepository
     */
    protected $pushNotificationRepository;

    /**
     * PushNotificationService constructor.
     *
     * @param PushNotificationRepository $pushNotificationRepository
     */
    public function __construct(PushNotificationRepository $pushNotificationRepository)
    {
        $this->pushNotificationRepository = $pushNotificationRepository;
    }

    /**
     * Send firebase push notification to all or individuals
     *
     * @param \App\Http\Requests\PushNotificationRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function sendPushNotification(PushNotificationRequest $request)
    {
        try {
            $data_request =  $request->all();
            $recipient_phone = $data_request['recipients'] ?? null;
            $notification = $this->pushNotificationRepository->sendPushNotification($request);

            if( !isset($data_request['sending_from']) &&  $data_request['send_to_type'] == "INDIVIDUALS") {
                $this->pushNotificationRepository->attachmentNotificationToUser($notification->id, $recipient_phone);
            }

            return response()->json([
                'status' => 'SUCCESS',
                'notification_id' => $notification->id
            ], Response::HTTP_OK);
        } catch (Exception $exception) {
            return response()->json([
                'status' => 'FAIL',
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Details of a notification
     *
     * @param integer $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function getNotificationDetails($id = null)
    {

        try {
            $notification = $this->pushNotificationRepository->getNotificationDetails($id);

            if ($notification[0]->status == Notification::PERTIALLY_SUCCESSFULL) {
                return response()->json([
                    'status' => 'SUCCESS',
                    'notification_id' => $notification[0]->id,
                    'notification_status' => $notification[0]->status,
                    'failed_msisdn' => $notification[1]
                ], Response::HTTP_OK);
            } else {
                return response()->json([
                    'status' => 'SUCCESS',
                    'notification_id' => $notification[0]->id,
                    'notification_status' => $notification[0]->status,
                ], Response::HTTP_OK);
            }
        } catch (HttpException $exception) {
            $statusCode = $exception->getStatusCode();

            if ($exception->getStatusCode() == Response::HTTP_NOT_FOUND) {
                $errors[] = [
                    'message' => $exception->getMessage()
                ];
            } elseif ($exception->getStatusCode() == Response::HTTP_UNPROCESSABLE_ENTITY) {
                $statusCode = 400;
                $errors[] = [
                    'code' => 'ERR_1002',
                    'message' => $exception->getMessage()
                ];
            } elseif ($exception->getStatusCode() == Response::HTTP_BAD_REQUEST) {
                $errors[] = [
                    'code' => 'ERR_1001',
                    'message' => $exception->getMessage()
                ];
            }

            return response()->json([
                'status' => 'FAIL',
                'errors' => $errors
            ], $statusCode);
        }
    }
}
