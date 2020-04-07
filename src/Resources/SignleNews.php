<?php

namespace Scaupize1123\JustOfficalNews\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class SignleNews extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $tags = [];
        $this->tags->each(function($value) use (&$tags) {
            $tags[] = $value->name;
        });

        return [
            'uuid' => $this->uuid,
            'translation' => $this->translation,
            'category' => $this->category,
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
            'news_category_id' => $this->news_category_id,
            'tags' => $tags
        ];
 
    }
}
