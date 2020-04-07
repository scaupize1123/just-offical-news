<?php

namespace Scaupize1123\JustOfficalNews\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class News extends JsonResource
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
            'uuid' => $this->uuid,
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at->format('Y-m-d H:i:s'),
            'translation' => $this->translation,
            'category' => $this->category,
            // 'translation' => NewsLang::collection($this->whenLoaded('translation')),
            // 'category' => NewsCategory1::collection($this->whenLoaded('category')),
        ];
 
    }
}
