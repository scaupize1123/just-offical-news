<?php

namespace Scaupize1123\JustOfficalNews;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class News extends Model
{
    protected $fillable = ['uuid', 'start_date', 'end_date', 'news_category_id', 'status'];

    public function translation() {
        return $this->hasMany('Scaupize1123\JustOfficalNews\NewsTranslation', 'news_id', 'id');
    }

    public function category() {
        return $this->hasMany('Scaupize1123\JustOfficalNews\NewsCategory', 'id', 'news_category_id');
    }

    public function tags()
    {
        return $this->morphMany('App\Tag', 'tagable');
    }
}
