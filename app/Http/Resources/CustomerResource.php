<?php

namespace App\Http\Resources;

use App\Models\Customer;
use App\Services\Banglalink\BanglalinkCustomerService;
use App\Services\Banglalink\CustomerPackageService;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class CustomerResource extends JsonResource
{

    /**
     * @var CustomerPackageService
     */

    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id'                  => $this->id,
            'customer_account_id' => $this->customer_account_id,
            'name'                => $this->name,
            'msisdn_number'       => $this->phone,
            'connection_type'     => Customer::connectionType(Customer::find($this->id)),
            'email'               => $this->email,
            'birth_date'          => $this->birth_date,
            'profile_image'       => ($this->profile_image) ? Storage::disk('public')->url($this->profile_image) : null,
            'package'             => Customer::package(Customer::find($this->id))
        ];
    }
}
