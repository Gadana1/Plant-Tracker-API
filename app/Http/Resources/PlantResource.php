<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PlantResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return $this->processItems($this->resource, $request);
    }
    
    /**
     * Process Items
     *
     * @param \App\Models\v1\Plant $item
     * @param  \Illuminate\Http\Request  $request
     * @return \App\Models\v1\Plant
     */
    public function processItems($item, $request)
    {
        $item->image = url($item->image);
        return $item;
    }
}
