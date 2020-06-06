<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class BannerResource extends JsonResource
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
            'code'                      => $this->name ?? null,
            'image_url'                 => env('IMAGE_HOST') . "/" . $this->image_path ?? null,
            'redirect_url'              => $this->redirect_url ?? null,
        ];
    }
}
