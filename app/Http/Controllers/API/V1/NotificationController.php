<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Requests\DeleteAllNotificationRequest;
use App\Http\Requests\MarkReadAllNotificationRequest;
use App\Http\Requests\MarkReadNotificationRequest;
use App\Http\Requests\NotificationCountRequest;
use App\Http\Requests\NotificationPreferenceRequest;
use App\Http\Requests\NotificationPreferenceResetRequest;
use App\Http\Requests\UpdateNotificationPreferenceRequest;
use App\Http\Requests\UserNotificationRequest;
use App\Services\NotificationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class NotificationController extends Controller
{
    /**
     * @var NotificationService
     */
    protected $service;

    /**
     * NotificationController constructor.
     * @param NotificationService $service
     */
    public function __construct(NotificationService $service)
    {
        $this->service = $service;
        $this->middleware('idp.verify')->except('getNotificationCategory');
    }


    /**
     * @param UserNotificationRequest $request
     * @return JsonResponse
     */
    public function getNotificationByUser(UserNotificationRequest $request)
    {
        return $this->service->getNotificationByUser($request);
    }


    /**
     * Retrieve notification category
     * @return JsonResponse
     */
    public function getNotificationCategory()
    {
        return $this->service->getCategories();
    }


    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function getNotificationCount(Request $request)
    {
        return $this->service->getNotificationCount($request);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function markReadAllNotifications(Request $request)
    {
        return $this->service->markReadAllNotifications($request);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function deleteAllNotifications(Request $request)
    {
        return $this->service->deleteAllNotifications($request);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function markReadNotifications(Request $request)
    {
        return $this->service->markReadNotifications($request);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function deleteNotifications(Request $request)
    {
        return $this->service->deleteNotifications($request);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function updateNotificationPreference(Request $request)
    {
        return $this->service->updateNotificationPreference($request);
    }


    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function getUserNotificationPreference(Request $request)
    {
        return $this->service->getUserNotificationPreference($request);
    }

    /**
     * @param NotificationPreferenceResetRequest $request
     * @return JsonResponse
     */
    public function resetNotificationPreference(NotificationPreferenceResetRequest $request)
    {
        return $this->service->resetNotificationPreference($request);
    }
}
