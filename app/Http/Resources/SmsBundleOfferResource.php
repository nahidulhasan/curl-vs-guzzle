<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SmsBundleOfferResource extends JsonResource
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
            'id'            => $this->id,
            'title'         => $this->commercial_name_en,
            'sms'           => $this->sms_volume,
            'validity'      => $this->validity,
            'validity_unit' => ucfirst($this->validity_unit),
            'price'         => $this->mrp_price,
            'tag'           => $this->blProduct->tag,
            'points'        => null,
            'product_code'  => $this->product_code,
            'has_autorenew' => ($this->renew_product_code) ? true : false,
            'image'         => ($this->blProduct->media) ?
                                env('IMAGE_HOST') . '/storage/' . $this->blProduct->media : null,
        ];
    }
}
