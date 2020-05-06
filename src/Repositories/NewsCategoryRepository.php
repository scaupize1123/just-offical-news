<?php

namespace Scaupize1123\JustOfficalNews\Repositories;

use Scaupize1123\JustOfficalNews\Language;
use Scaupize1123\JustOfficalNews\NewsCategory;
use Scaupize1123\JustOfficalNews\NewsCategoryTranslation;
use Scaupize1123\JustOfficalNews\Interfaces\NewsCategoryRepositoryInterface;
use Scaupize1123\JustOfficalNewsResources\NewsCategory as NewsCategoryResources;

class NewsCategoryRepository implements NewsCategoryRepositoryInterface
{
    private function translationExists($filter) {
        return function($q) use ($filter) {
            if(!empty($filter['lang'])) {
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

    private function getTranslation($filter) {
        return function($q) use ($filter) {
            if(!empty($filter['lang'])) {
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
    
    // get all list
    public function getList($filter) {
        $newsCategory = NewsCategory::where('status', 1)
            ->whereHas('translation', $this->translationExists($filter))
            ->with(['translation' => function($q) use ($filter) {
                if(!empty($filter['lang'])) {
                    $q->where('language_id', $filter['lang']);
                }
            }, 'translation.language'])
            ->orderBy($filter['sort_name'], $filter['sort_type'])->get();

        return $newsCategory;
    }

    // get paginate list
    public function getListPage($filter) {
        // $newsCategory = NewsCategory::join('news_category_translation','news_category.id','=','news_category_translation.news_category_id')
        //     ->where('news_category_translation.language_id', $filter['langSort'])
        //     ->orderBy($filter['sort_name'], $filter['sort_type'])
        //     ->selectRaw('DISTINCT news_category.id, news_category.created_at')
        //     ->WhereHas('translation', $this->translationExists($filter))
        //     ->with(['translation' => $this->getTranslation($filter), 'translation.language'])
        //     ->paginate($filter['size']);
        $newsCategory = NewsCategory::where('status', 1)
            ->whereHas('translation', $this->translationExists($filter))
            ->with(['translation' => $this->getTranslation($filter), 'translation.language'])
            ->paginate($filter['size']);
        
        return $newsCategory;
    }

    public function delete($id) {
        NewsCategory::where('id', $id)->update(['status' => 0]);
        NewsCategoryTransLation::where('news_category_id', $id)->update(['status' => 0]);
    }

    public function create($category) {
        $newsCategory = NewsCategory::create([
            "status" => 1,
        ]);
        $id = $newsCategory->id;

        $creates = [];
        collect($category)->each(function($value) use ($id, &$creates) {
            $create = [
                "name" => $value['name'],
                "language_id" => $value['lang'],
                "news_category_id" => $id,
                'status' => 1,
                'created_at' => date('Y-m-d H:I:s'),
                'updated_at' => date('Y-m-d H:I:s')
            ];
           $creates[] = $create; 
        });
        NewsCategoryTransLation::insert($creates);
        return $newsCategory->where('id', $id)->with('translation')->get();
    }

    public function update($category, $id) {
        $newCategory = NewsCategory::where('id', $id)
            ->where('status', '1')
            ->with('translation')
            ->first();

        collect($category)->each(function($value) use ($id) {
            $update = [
                "name" => $value['name'],
                "language_id" => $value['lang'],
                "news_category_id" => $id,
                'status' => 1,
            ];
            NewsCategoryTransLation::updateOrCreate([
                'news_category_id' => $id,
                'language_id' => $value['lang']
            ], $update);
        });
    }

    public function getGroupByLang() {
        $categories = NewsCategory::where('status', 1)->with('translation', 'translation.language')->get();
        
        $langList = collect([]);
        $categories->each(function($category) use (&$langList) {
            $category->translation->each(function($translation) use (&$langList) {
                $pushData = clone $translation;
                unset($pushData['language']);
                if(empty($langList[$translation->language[0]->name])) {
                    $langList[$translation->language[0]->name] = collect([$pushData]);
                }else{
                    $langList[$translation->language[0]->name]->push($pushData);
                }
                
            });  
        });
        return $langList;
    }

    public function get($data) {
        $lang = $data['lang'];
        $categories = NewsCategory::where('status', 1)
            ->where('id', $data['id'])
            ->WhereHas('translation', function($q) use ($lang) {
                $q->where('language_id', $lang);
            })->with(['translation' => function($q) use ($lang) {
                $q->where('language_id', $lang);
            }])->get();
        return $categories;
    }
}
