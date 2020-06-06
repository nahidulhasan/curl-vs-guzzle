<?php

namespace App\Jobs;

use App\Http\Resources\NotificationListCollection;
use App\Models\NotificationUser;
use App\Repositories\CustomerRepository;
use App\Repositories\NotificationRepository;
use App\Services\NotificationService;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Log;

class MarkSeenNotifications implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    /**
     * @var NotificationService
     */
    protected $service;
    /**
     * @var NotificationListCollection
     */
    protected $notifications;

    /**
     * Create a new job instance.
     *
     * @param NotificationListCollection $notifications
     */
    public function __construct(NotificationListCollection $notifications)
    {
        $this->notifications = $notifications;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $ids = [];
        foreach ($this->notifications as $notification) {
            $ids [] = $notification->id;
        }
        NotificationUser::whereIN('id', $ids)->update([
            'is_seen' => 1
        ]);
    }
}
