<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StoreLocatorResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'cc_code'  => $this->cc_code,
            'cc_name'  => $this->cc_name,
            'district' => $this->district,
            'thana'    => $this->thana,
            'address'    => $this->address,
            'longitude'    => $this->longitude,
            'latitude'    => $this->latitude,
            'opening_time'    => $this->opening_time,
            'closing_time'    => $this->closing_time,
            'holiday'    => $this->holiday,
            'half_holiday'    => $this->half_holiday,
            'half_holiday_opening_time'    => $this->half_holiday_opening_time,
            'half_holiday_closing_time'    => $this->half_holiday_closing_time,
        ];
    }
}
