<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Pagination\AbstractPaginator;

class PlantResourceList extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        if($this->resource instanceof AbstractPaginator){
            $items = array_replace($this->resource->toArray(), [self::$wrap => $this->processItems($this->resource->items(), $request)]);
        }
        else {
            $items = $this->processItems($this->resource, $request);
        }
        return $items;
    }
    
    /**
     * Process Items
     *
     * @param \App\Models\v1\Plant[] $items
     * @param  \Illuminate\Http\Request  $request
     * @return \App\Models\v1\Plant[]
     */
    public function processItems($items, $request)
    {
        if($items){
            foreach($items as &$item){
                $item = (new PlantResource($item))->toArray($request);
            }
        }
        return $items;
    }
}
