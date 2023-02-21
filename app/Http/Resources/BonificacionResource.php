<?php

namespace App\Http\Resources;

use App\Models\Bonificacion;
use Illuminate\Http\Resources\Json\JsonResource;

class BonificacionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return parent::toArray($request);
    }
}
