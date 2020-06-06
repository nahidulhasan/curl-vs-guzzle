<?php

namespace App\Services;

use App\Enums\HttpStatusCode;
use App\Http\Resources\NotificationCategoryResource;
use App\Http\Resources\NotificationListCollection;
use App\Jobs\MarkSeenNotifications;
use App\Models\NotificationCategory;
use App\Models\NotificationUser;
use App\Models\UserMuteNotificationCategory;
use App\Repositories\BannerRepository;
use App\Repositories\CustomerRepository;
use App\Repositories\NotificationRepository;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * Class NotificationService
 * @package App\Services
 */
class NotificationService extends ApiBaseService
{

    /**
     * @var BannerRepository
     */
    protected $notificationRepository;

    /**
     * @var CustomerRepository
     */
    protected $customerRepository;
    /**
     * @var CustomerService
     */
    protected $customerService;


    /**
     * NotificationService constructor.
     * @param CustomerService $customerService
     * @param NotificationRepository $notificationRepository
     * @param CustomerRepository $customerRepository
     */
    public function __construct(
        CustomerService $customerService,
        NotificationRepository $notificationRepository,
        CustomerRepository $customerRepository
    ) {
        $this->notificationRepository = $notificationRepository;
        $this->customerRepository = $customerRepository;
        $this->customerService = $customerService;
    }

    /**
     * @return JsonResponse
     */
    public function getCategories()
    {
        $notifications = NotificationCategoryResource::collection(NotificationCategory::all());

        return $this->sendSuccessResponse(
            $notifications,
            'All Notification category',
            [],
            HttpStatusCode::SUCCESS
        );
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @throws \App\Exceptions\TokenInvalidException
     */
    public function getNotificationByUser(Request $request)
    {
        $builder = new NotificationUser();
        $page_no = 1;
        $item_per_page = 10;

        $user = $this->customerService->getAuthenticateCustomer($request);

        if (!$user) {
            return $this->sendErrorResponse("User not found", [], HttpStatusCode::UNAUTHORIZED);
        }

        if ($request->has('filters') && $request->filters) {
            $types = explode(',', $request->filters);
            $builder = $builder->where('user_id', $user->id)->whereHas('notification', function ($q) use ($types) {
                $q->whereIn('category_slug', $types);
            })->with('notification', 'notification.category');
        } else {
            $builder = $builder->where('user_id', $user->id)->whereHas('notification')->with(
                'notification',
                'notification.category'
            );
        }

        if ($request->has('page_no')) {
            $page_no = $request->page_no;
        }

        if ($request->has('item_per_page')) {
            $item_per_page = $request->item_per_page;
        }

        $data = $builder->latest()->paginate($item_per_page, ['*'], null, $page_no);


        $formatted_data = new NotificationListCollection($data);

        MarkSeenNotifications::dispatch($formatted_data);

        return $this->sendSuccessResponse(
            $formatted_data,
            'All latest Notifications',
            [],
            HttpStatusCode::SUCCESS
        );
    }


    /**
     * @param Request $request
     * @return JsonResponse
     * @throws \App\Exceptions\TokenInvalidException
     */
    public function getNotificationCount(Request $request)
    {
        $builder = new NotificationUser();

        $user =  $this->customerService->getAuthenticateCustomer($request);


        if (!$user) {
            return $this->sendErrorResponse("User not found", [], HttpStatusCode::UNAUTHORIZED);
        }


        $unseen = $builder->where('user_id', $user->id)->unseen()->count();
        $unread = $builder->where('user_id', $user->id)->unread()->count();

        return $this->sendSuccessResponse(
            [
                'unseen' => $unseen,
                'unread' => $unread,
            ],
            'All Notifications Count',
            [],
            HttpStatusCode::SUCCESS
        );
    }


    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function markReadAllNotifications(Request $request)
    {

        $user =  $this->customerService->getAuthenticateCustomer($request);

        if (!$user) {
            return $this->sendErrorResponse("User not found", [], HttpStatusCode::UNAUTHORIZED);
        }


        try {
            NotificationUser::where('user_id', $user->id)->update([
                'is_read' => 1
            ]);

            return $this->sendSuccessResponse(
                [],
                'All Notifications are marked as read',
                [],
                HttpStatusCode::SUCCESS
            );
        } catch (Exception $e) {
            return $this->sendErrorResponse(
                $e->getMessage(),
                [],
                HttpStatusCode::INTERNAL_ERROR
            );
        }
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function markReadNotifications(Request $request)
    {

        $user =  $this->customerService->getAuthenticateCustomer($request);

        if (!$user) {
            return $this->sendErrorResponse("User not found", [], HttpStatusCode::UNAUTHORIZED);
        }

        try {
            $notification = NotificationUser::where([
                'user_id' => $user->id,
                'id' => $request->notification_id
            ])->first();

            if (!$notification) {
                return $this->sendErrorResponse(
                    "This notification not belongs to user",
                    [],
                    HttpStatusCode::VALIDATION_ERROR
                );
            }

            $notification->update([
                'is_read' => 1
            ]);

            return $this->sendSuccessResponse(
                [],
                'The notification is marked as read',
                [],
                HttpStatusCode::SUCCESS
            );
        } catch (Exception $e) {
            return $this->sendErrorResponse(
                $e->getMessage(),
                [],
                HttpStatusCode::INTERNAL_ERROR
            );
        }
    }


    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function deleteAllNotifications(Request $request)
    {

        $user =  $this->customerService->getAuthenticateCustomer($request);

        if (!$user) {
            return $this->sendErrorResponse("User not found", [], HttpStatusCode::UNAUTHORIZED);
        }

        try {
            NotificationUser::where('user_id', $user->id)->delete();

            return $this->sendSuccessResponse(
                [],
                'All Notifications are deleted',
                [],
                HttpStatusCode::SUCCESS
            );
        } catch (Exception $e) {
            return $this->sendErrorResponse(
                $e->getMessage(),
                [],
                HttpStatusCode::INTERNAL_ERROR
            );
        }
    }


    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function deleteNotifications(Request $request)
    {
        $user =  $this->customerService->getAuthenticateCustomer($request);

        if (!$user) {
            return $this->sendErrorResponse("User not found", [], HttpStatusCode::UNAUTHORIZED);
        }

        try {
            $notification = NotificationUser::where([
                'user_id' => $user->id,
                'id' => $request->notification_id

            ])->first();

            if (!$notification) {
                return $this->sendErrorResponse(
                    "This notification not belongs to user",
                    [],
                    HttpStatusCode::VALIDATION_ERROR
                );
            }

            $notification->delete();


            return $this->sendSuccessResponse(
                [],
                'The Notification is  deleted',
                [],
                HttpStatusCode::SUCCESS
            );
        } catch (Exception $e) {
            return $this->sendErrorResponse(
                $e->getMessage(),
                [],
                HttpStatusCode::INTERNAL_ERROR
            );
        }
    }


    public function updateNotificationPreference(Request $request)
    {

        $user =  $this->customerService->getAuthenticateCustomer($request);

        if (!$user) {
            return $this->sendErrorResponse("User not found", [], HttpStatusCode::UNAUTHORIZED);
        }

        try {
            $setting = UserMuteNotificationCategory::where([
                'user_id' => $user->id,
                'category_id' => $request->category_id,
            ]);

            if ($request->action == 'on') {
                // remove from user_mute_notification_category
                if (!$setting->first()) {
                    return $this->sendErrorResponse(
                        'This notification is already subscribed.',
                        [],
                        HttpStatusCode::BAD_REQUEST
                    );
                }

                $setting->delete();

                return $this->sendSuccessResponse(
                    [],
                    'The Notification Preference is  updated',
                    [],
                    HttpStatusCode::SUCCESS
                );
            }

            // if off  yo need to delete

            if ($setting->first()) {
                return $this->sendErrorResponse(
                    'This notification is already unsubscribed.',
                    [],
                    HttpStatusCode::BAD_REQUEST
                );
            }

            UserMuteNotificationCategory::create([
                'user_id' => $user->id,
                'category_id' => $request->category_id,
            ]);

            return $this->sendSuccessResponse(
                [],
                'The Notification Preference is  updated',
                [],
                HttpStatusCode::SUCCESS
            );
        } catch (Exception $e) {
            return $this->sendErrorResponse(
                $e->getMessage(),
                [],
                HttpStatusCode::INTERNAL_ERROR
            );
        }
    }


    public function getUserNotificationPreference(Request $request)
    {
        $user =  $this->customerService->getAuthenticateCustomer($request);

        if (!$user) {
            return $this->sendErrorResponse("User not found", [], HttpStatusCode::UNAUTHORIZED);
        }

        $preference = DB::select("select
                            notifications_category.id,
                             notifications_category.name,
                             notifications_category.slug,
                             user_mute_notification_category.id settings_id
                       from notifications_category
                       LEFT JOIN user_mute_notification_category
                       ON notifications_category.id = user_mute_notification_category.category_id
                       AND user_mute_notification_category.user_id = $user->id ORDER BY notifications_category.name");

        $data_array = [];

        foreach ($preference as $setting) {
            $data_array [] = [
                'id' => $setting->id,
                'name' => $setting->name,
                'slug' => $setting->slug,
                'subscribed' => ($setting->settings_id == null) ? true : false
            ];
        }

        return $this->sendSuccessResponse(
            $data_array,
            'User Notification Preference List',
            [],
            HttpStatusCode::SUCCESS
        );
    }

    public function resetNotificationPreference(Request $request)
    {
        $user =  $this->customerService->getAuthenticateCustomer($request);

        if (!$user) {
            return $this->sendErrorResponse("User not found", [], HttpStatusCode::UNAUTHORIZED);
        }

        $action = $request->action;

        try {
            if ($action == 'on') {
                // delete all from user_mute_notification_category for this user
                UserMuteNotificationCategory::where('user_id', $user->id)->delete();
            } else {
                $categories = NotificationCategory::all();

                foreach ($categories as $category) {
                    UserMuteNotificationCategory::updateOrCreate([
                        'user_id' => $user->id,
                        'category_id' => $category->id
                    ]);
                }
            }
            return $this->sendSuccessResponse(
                [],
                'User Notification Preference List Updated',
                [],
                HttpStatusCode::SUCCESS
            );
        } catch (Exception $e) {
            return $this->sendErrorResponse(
                $e->getMessage(),
                [],
                HttpStatusCode::INTERNAL_ERROR
            );
        }
    }

    /**
     * @param NotificationListCollection $notifications
     */
/*    public function markAsSeenNotifications(NotificationListCollection $notifications)
    {
        $ids = [];
        foreach ($notifications as $notification) {
            $ids [] = $notification->id;
        }
        NotificationUser::whereIN('id', $ids)->update([
            'is_seen' => 1
        ]);
    }*/


    /**
     * Attach Notification to user
     *
     * @param $notification_id
     * @param $user_phone
     * @return string
     */
    public function attachNotificationToUser($notification_id, $user_phone)
    {
        return $this->notificationRepository->attachmentNotificationToUser($notification_id, $user_phone);
    }
}
