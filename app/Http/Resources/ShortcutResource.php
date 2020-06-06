<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ShortcutResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        //$dial_number = $this->dial_number;

        return [
            'id' => $this->id ?? null,
            'title' => $this->title ?? null,
            'icon' => env('IMAGE_HOST') . "/" . $this->icon ?? null,
            'is_default' => ($this->is_default) ?? null,
            'component_identifier' => $this->component_identifier ?? null,
            'other_info' => ($this->other_info) ? json_decode($this->other_info) : null,
            'sequence' => $this->sequence ?? null,
            'is_enable' => $this->is_enable ?? 0
        ];
    }
}
