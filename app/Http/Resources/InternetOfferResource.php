<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class InternetOfferResource extends JsonResource
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
            'id'                  => $this->id ?? null,
            'volume'              => $this->volume ?? null,
            'validity'            => ($this->validity) ?? null,
            'price'               => $this->price ?? null,
            'bonus' => BonusResource::collection($this->bonus),
        ];
    }
}
