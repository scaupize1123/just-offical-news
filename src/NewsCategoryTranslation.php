<?php

namespace Scaupize1123\JustOfficalNews;

use Illuminate\Database\Eloquent\Model;

class NewsCategoryTranslation extends Model
{
    protected $fillable = ['name', 'language_id', 'status', 'news_category_id'];

    protected $table = 'news_category_translation';
    
    public function language() {
        return $this->hasMany('App\Language', 'id', 'language_id');
    }
}
