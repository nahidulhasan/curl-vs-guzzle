<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class BonusResource extends JsonResource
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

            'volume'              => $this->volume ?? null,
            'title'               => ($this->title) ?? null,
            'type'                => $this->type ?? null
        ];
    }
}
