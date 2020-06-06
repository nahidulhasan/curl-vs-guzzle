<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\PushNotificationRequest;
use App\Services\PushNotificationService;
use Illuminate\Http\Request;

/**
 * @SWG\Swagger(
 *     basePath="/api/v1",
 *     schemes={"http", "https"},
 *     host=L5_SWAGGER_CONST_HOST,
 *     @SWG\Info(
 *         version="1.0.0",
 *         title="Swagger API",
 *         description="Swagger API description",
 *         @SWG\Contact(
 *             email="arifulislam@bs-23.net"
 *         ),
 *     )
 * )
 */

class PushNotificationController extends Controller
{

    /**
     * Firebase server key
     *
     * @var string
     */
    protected $firebaseServerKey;

    /**
     * PushNotificationController Construct
     */
    public function __construct(PushNotificationService $service)
    {
        $this->pushNotificationService = $service;
        $this->firebaseServerKey = config('app.firebase_server_key');
    }

    /**
    * Send push notification
    *
    * @param \Illuminate\Http\Request $request
    * @return \Illuminate\Http\Response
    *
    *
    * @SWG\Post(
    *      path="/push/notification",
    *      operationId="sendPushNotification",
    *      tags={"Notifications"},
    *      summary="Send notification",
    *      consumes={"application/json"},
    *      produces={"application/json"},
    *      description="Send firebase push notification data",
    *      @SWG\Parameter(
    *         description="Body data",
    *         in="body",
    *         name="body",
    *         required=true,
    *         @SWG\Schema(
    *             properties={
    *               @SWG\Property(property="title", type="string"),
    *               @SWG\Property(property="body", type="string"),
    *               @SWG\Property(property="send_to_type", type="string"),
    *               @SWG\Property(property="recipients", type="array",
    *                  @SWG\Items(
    *                         type="string",
    *                  ),
    *               ),
    *               @SWG\Property(property="is_interactive", type="string"),
    *               @SWG\Property(property="data", type="object",
    *                  @SWG\Property(property="cid", type="integer"),
    *                  @SWG\Property(property="url", type="string"),
    *                  @SWG\Property(property="component", type="string"),
    *               ),
    *             },
    *         ),
    *      ),
    *      @SWG\Parameter(
    *          name="app-key",
    *          description="authorization header",
    *          required=true,
    *          type="string",
    *          in="header"
    *      ),
    *
    *      @SWG\Response(response=200, description="Successful Operation"),
    *      @SWG\Response(response=400, description="Bad Request"),
    *      @SWG\Response(response=401, description="Unauthorized"),
    *      @SWG\Response(response=404, description="Resource Not Found"),
    *      @SWG\Response(response=500, description="Server Error"),
    * )
     */
    public function sendPushNotification(PushNotificationRequest $request)
    {
        return $this->pushNotificationService->sendPushNotification($request);
    }

    /**
    * Details of a notification
    *
    * @param integer $id
    * @return \Illuminate\Http\Response
    *
    * @SWG\Get(
    *      path="/push/notification/{id}",
    *      operationId="getNotificationDetails",
    *      tags={"Notifications"},
    *      summary="Get notification information",
    *      description="Returns notification data",
    *      @SWG\Parameter(
    *          name="id",
    *          description="notification id",
    *          required=true,
    *          type="integer",
    *          in="path"
    *      ),
    *      @SWG\Parameter(
    *          name="app-key",
    *          description="authorization header",
    *          required=true,
    *          type="string",
    *          in="header"
    *      ),
    *      @SWG\Response(response=200, description="Successful Operation"),
    *      @SWG\Response(response=400, description="Bad Request"),
    *      @SWG\Response(response=401, description="Unauthorized"),
    *      @SWG\Response(response=404, description="Resource Not Found"),
    *      @SWG\Response(response=500, description="Server Error"),
    * )
    */
    public function getNotificationDetails($id = null)
    {
        return $this->pushNotificationService->getNotificationDetails($id);
    }
}
