<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UssdCodeResource extends JsonResource
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
            'name'       => $this->title ?? null,
            'ussd_code'         => $this->code ?? null,
            'purpose'       => $this->purpose ?? null,
            'provider'         => $this->provider ?? null
        ];
    }
}
