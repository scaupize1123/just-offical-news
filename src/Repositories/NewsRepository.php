<?php

namespace Scaupize1123\JustOfficalNews\Repositories;

use Illuminate\Support\Str;
use Scaupize1123\JustOfficalNews\News;
use Scaupize1123\JustOfficalNews\Helpers;
use Scaupize1123\JustOfficalNews\NewsCategory;
use Scaupize1123\JustOfficalNews\NewsTranslation;
use Scaupize1123\JustOfficalNews\Interfaces\NewsRepositoryInterface;

class NewsRepository implements NewsRepositoryInterface
{
    public function translationExists($filter) {
        return function($q) use ($filter) {
            if(!empty($filter['lang'])){
                $q->where('language_id', $filter['lang']);
            }
            if(!empty($filter['text'])) {
                $q->where(function($query) use ($filter) {
                   $query->where('name', 'like', '%'.$filter['text'].'%');
               });
           }
           $q->where('status', 1);
           
        };
    }

    public function getTranslation($filter) {
        return function($q) use ($filter) {
            if(!empty($filter['lang'])){
                $q->where('language_id', $filter['lang']);
            }
            if(!empty($filter['text'])) {
                $q->where(function($query) use ($filter) {
                   $query->where('name', 'like', '%'.$filter['text'].'%');
               });
           }
           $q->where('status', 1);
        };
    }

    public function getListPage($filter) {
        // $query_builder = News::join('news_translation','news.id','=','news_translation.news_id')
        //     ->where('news.status', 1)
        //     ->where('news_translation.language_id', $filter['langSort'])
        //     ->orderBy($filter['sort_name'], $filter['sort_type'])
        //     ->selectRaw('DISTINCT news.id, news.created_at, news.updated_at, uuid')
        //     ->WhereHas('translation', $this->translationExists($filter))
        //     ->with(['translation' => $this->getTranslation($filter),
        //         'category.translation' => function($q) use ($filter) {
        //         if(!empty($filter['lang'])){
        //             $q->where('language_id', $filter['lang']);
        //         }
        //     }, 'translation.language', 'category.translation.language']);

        $query_builder = News::where('status', 1)
            ->whereHas('translation', $this->translationExists($filter))
            ->with(['translation' => $this->getTranslation($filter),
                'category.translation' => function($q) use ($filter) {
                if(!empty($filter['lang'])){
                    $q->where('language_id', $filter['lang']);
                }
            }, 'translation.language', 'category.translation.language', 'tags']);

        if($filter['news_category_id'] !== null){
            $newsList = $query_builder->where('news_category_id', $filter['news_category_id'])->paginate($filter['size']);
        }else{
            $newsList = $query_builder->paginate($filter['size']);
        }
        return $newsList;
    }

    public function getByUUID($uuid, $lang = null) {
        $filter = ['lang' => $lang];
        $queryBuilder = News::where('status', 1)
            ->whereHas('translation', $this->translationExists($filter))
            ->where('uuid', $uuid)
            ->with(['translation' => function($q) use ($lang) {
                if(!empty($lang)){
                    $q->where('language_id', $lang);
                }
                $q->where('status', 1);
            }, 'category.translation' => function($q) use ($lang) {
                if(!empty($lang)){
                    $q->where('language_id', $lang);
                }
            }, 'translation.language', 'category.translation.language', 'tags' => function($q) use ($lang) {
                if(!empty($lang)){
                    $q->where('language_id', $lang);
                }
            }]);
        return $queryBuilder->first();
    }

    public function delete($uuid, $lang = null) {
        if(empty($lang)) {
            $news = News::where('uuid', $uuid)->first();
            $news->tags()->delete();
            $news->translation()->where('news_id', $news->id)->update(['status' => 0]);
            News::where('uuid', $uuid)
                ->update(['status' => 0]);
        } else {
            $news = News::where('uuid', $uuid)->first();
            $news->translation()
                ->where('language_id', $lang)
                ->update(['status' => 0]);
            $news->tags()
                ->where('language_id', $lang)
                ->where('tagable_id', $news->id)->delete();
        }
    }

    public function checkOneLangNews($uuid, $lang) {
        $data = News::where('uuid', $uuid)->whereHas('translation', function($q) use ($lang) {
            $q->where('language_id', $lang);
        })->get();

        if($data->isEmpty()) {
            return false;
        }
        return true;
    }

    public function checkNews($uuid) {
        $isExisted =  News::where('uuid', $uuid)
            ->get()->count();

        if(empty($isExisted)) {
            return false;
        }
        return true;
    }

    public function update($update) {
        $news = News::updateOrCreate([
                'uuid' => $update['uuid']
            ],[
                'news_category_id' => $update['category'],
                'start_date' => $update['start_date'],
                'end_date' => $update['end_date'],
            ]);

        $news->translation()
            ->updateOrCreate([
                'news_id' => $news->id,
                'language_id' => $update['lang']
            ],[
                'name' => $update['name'],
                'brief' => $update['brief'],
                'desc' => $update['desc'],
                'image_name' => $update['image_name'] ?? null,
                'image' => $update['image'] ?? null,
                'status' => 1
            ]);

        $news->tags()->delete();
        
        $lang = $update['lang'];
        $id = $news->id;
        if(!empty($update['tags'])) {
            $tags = explode(',', $update['tags']);
            collect($tags)->each(function($value) use ($lang, $id, $news) {
                $tagArray = [
                    'name' => $value,
                    'language_id' => $lang,
                    'tagable_id' => $id
                ] ;
                $news->tags()->create($tagArray);
            });
        }
        return $news;
    }

    public function create($create) {
        $uuid =  $create['uuid'];
        $news = News::create([
            'uuid' => $uuid,
            'status' => 1,
            'news_category_id' => $create['category'],
            'start_date' => $create['start_date'],
            'end_date' => $create['end_date'],
        ]);

        $news->translation()->create([
            'name' => $create['name'],
            'brief' => $create['brief'],
            'desc' => $create['desc'],
            'language_id' => $create['lang'],
            'image_name' => $create['image_name'] ?? null,
            'image' => $create['image'] ?? null,
            'status' => 1,
            'news_id' => $news->id
        ]);

        $lang = $create['lang'];
        $id = $news->id;
        if(!empty($create['tags'])) {
            $tags = explode(',', $create['tags']);
            collect($tags)->each(function($value) use ($lang, $id, $news) {
                $tagArray = [
                    'name' => $value,
                    'language_id' => $lang,
                    'tagable_id' => $id
                ] ;
                $news->tags()->create($tagArray);
            });
        }
        return $news;
    }
}
