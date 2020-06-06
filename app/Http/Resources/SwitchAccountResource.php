<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class SwitchAccountResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id'                        => $this->id ?? null,
            'name'                      => $this->name ?? null,
            'mobile'                    => $this->mobile ?? null,
            'email'                     => $this->email ?? null,
            'customer_account_id'       => $this->customer_account_id ?? null,

        ];
    }
}
