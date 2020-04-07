<?php

namespace Scaupize1123\JustOfficalNews\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class NewsCategory extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        //\Log::info($this->collection);
        return [
            'id' => $this->id,
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
            'translation' => $this->translation
        ];
 
    }
}
