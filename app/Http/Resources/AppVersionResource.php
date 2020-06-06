<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class AppVersionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        if ($this->force_update) {
            $force_update = true;
        } else {
            $force_update = false;
        }
        return [
            'platform'                => $this->platform ?? null,
            'current_version'         => $this->current_version ?? null,
            'force_update'            => $force_update,
            'message'                 => $this->message ?? null,
        ];
    }
}
