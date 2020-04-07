<?php

namespace Scaupize1123\JustOfficalNews;

use Illuminate\Database\Eloquent\Model;

class NewsTranslation extends Model
{
    protected $fillable = [ 'name',
                            'brief',
                            'desc',
                            'language_id',
                            'status',
                            'image_base64',
                            'image',
                            'news_id'];

    protected $table = 'news_translation';

    public function language() {
        return $this->hasOne('App\Language', 'id', 'language_id');
    }
}
