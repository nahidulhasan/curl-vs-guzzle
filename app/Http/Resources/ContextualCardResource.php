<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ContextualCardResource extends JsonResource
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
            'title'                     => $this->title ?? null,
            'body'                      => $this->description ?? null,
            'first_action_text'         => $this->first_action_text ?? null,
            'second_action_text'        => $this->second_action_text ?? null,
            'first_action'              => $this->first_action ?? null,
            'second_action'             => $this->second_action ?? null,
            'image_url'                 => env('IMAGE_HOST') . "/" . $this->image_url ?? null,

        ];
    }
}
