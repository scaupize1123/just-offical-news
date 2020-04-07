<?php

namespace Scaupize1123\JustOfficalNews;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class NewsCategory extends Model
{
    protected $fillable = ['name','status'];

    protected $table = 'news_category';

    public function translation() {
        return $this->hasMany('Scaupize1123\JustOfficalNews\NewsCategoryTranslation', 'news_category_id', 'id');
    }

    public function language()
    {
        return $this->hasManyThrough('App\Language', 'Scaupize1123\JustOfficalNews\NewsCategoryTranslation', 'news_category_id', 'id', 'id', 'language_id');
    }
}
