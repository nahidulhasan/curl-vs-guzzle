<?php

namespace App\Http\Resources;

use App\Models\Customer;
use App\Models\NotificationCategory;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\AdditionalAccount;
use Illuminate\Http\Resources\Json\JsonResource;

class NotificationListResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array
     */

    public function getCategoryIDBySlug($slug)
    {
        $category = NotificationCategory::where('slug', $slug)->first();
        if ($category) {
            return $category->id;
        }

        return null;
    }
    public function toArray($request)
    {
        if ($this->notification->title == "Switch Account") {
            $request_phone = explode(' ', $this->notification->body);

            $customer = Customer::where('phone', $request_phone[0])->first();

            $add_account = AdditionalAccount::where('customer_id', $customer->id)
                ->select('is_approve')->first();

            if ($add_account) {
                $status = $add_account->is_approve;
            } else {
                $status = 2;
            }

            return [
                'id' => $this->id,
                'category' => [
                    'id' => $this->getCategoryIDBySlug($this->notification->category_slug),
                    'slug' => $this->notification->category_slug,
                    'name' => $this->notification->category_name,
                ],
                'title' => $this->notification->title,
                'body' => $this->notification->body,
                'date' => Carbon::parse($this->created_at, 'UTC')->toDateTimeString(),
                'is_read' => ($this->is_read) ? true : false,
                'notification_type' => "switch_account",
                'request_phone' => $request_phone[0],
                'is_approve' => $status,
            ];
        } else {
            return [
                'id' => $this->id,
                'category' => [
                    'id' => $this->getCategoryIDBySlug($this->notification->category_slug),
                    'slug' => $this->notification->category_slug,
                    'name' => $this->notification->category_name,
                ],
                'title' => $this->notification->title,
                'body' => $this->notification->body,
                'date' => Carbon::parse($this->created_at, 'UTC')->toDateTimeString(),
                'is_read' => ($this->is_read) ? true : false
            ];
        }
    }
}
