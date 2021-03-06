<?php

namespace Scaupize1123\JustOfficalNews\Controllers\Api;

use Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use App\Exceptions\Api\BadRequestException;
use Scaupize1123\JustOfficalNews\NewsCategory;
use Scaupize1123\JustOfficalNews\Interfaces\NewsCategoryRepositoryInterface;
use Scaupize1123\JustOfficalNews\Resources\NewsCategory as NewsCategoryResources;

class NewsCategoryController extends \App\Http\Controllers\Controller
{

    public function __construct(NewsCategoryRepositoryInterface $newsCategoryRepo) 
    {
        $this->newsCategoryRepo = $newsCategoryRepo;
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $messages = [
            'category.present' => '必須包含資料',
            'category.min' => '必須包含資料',
            'category.*.name.required' => '必須包含名稱',
            'category.*.lang.required' => '必須包含語言',
        ];

        $validator = Validator::make(request()->all(), [
            'category' => 'present|array|min:1',
            'category.*.name' => 'required',
            'category.*.lang' => 'required',
        ],$messages);

        if (!$validator->fails()) {
            $data = request()->input('category');
            $newsCategory = $this->newsCategoryRepo->create($data);
            $result = [];
            $result['data'] = $newsCategory;
            $result['ReturnCode'] = 0;
            return $result;
        } else {
            throw new BadRequestException($validator->errors());
        }
    }

    public function update($id)
    {
        $messages = [
            'category.present' => '必須包含資料',
            'category.min' => '必須包含資料',
            'category.*.name.required' => '必須包含名稱',
            'category.*.lang.required' => '必須包含語言',
            'id.required' => '必須包含id',
        ];
        $data = [];
        $data['category'] = request()->input('category');
        $data['id'] = $id;
        
        $validator = Validator::make($data, [
            'category' => 'present|array|min:1',
            'category.*.name' => 'required',
            'category.*.lang' => 'required',
            'id' => 'required',
        ],$messages);

        if (!$validator->fails()) {
            $this->newsCategoryRepo->update($data['category'], $data['id']);
            $result = [];
            $result['ReturnCode'] = 0;
            return $result;
        } else {
            throw new BadRequestException($validator->errors());
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\NewsCategory  $news_category
     * @return \Illuminate\Http\Response
     */
    public function delete($id)
    {
        $messages = [
            'id.required' => '必須包含id',
        ];
        $data = [];
        $data['id'] = $id;

        $validator = Validator::make($data, [
            'id' => 'required',
        ],$messages);

        if (!$validator->fails()) {
            $this->newsCategoryRepo->delete($data['id']);
            $result = [];
            $result['ReturnCode'] = 0;
            return $result;
        } else {
            throw new BadRequestException($validator->errors());
        }
    }

    public function show()
    {
        $filter = [];
        $filter['page'] = Input::get('page') ?? '';
        $filter['size'] = Input::get('size') ?? 10;
        $filter['text'] = Input::get('text') ?? '';
        $filter['sort'] = Input::get('sort') ?? 'sort_date_desc';
        $filter['lang'] = Input::get('lang') ?? '1';

        if (Input::get('sort') === 'sort_date_desc') {
            $sort_name = 'created_at';
            $sort_type = 'desc';
        } else if (Input::get('sort') === 'sort_date_asc') {
            $sort_name = 'created_at';
            $sort_type = 'asc';
        } else if (Input::get('sort') === 'sort_title_desc') {
            $sort_name = 'news_category_translation.name';
            $sort_type = 'desc';
        } else if (Input::get('sort') === 'sort_title_asc') {
            $sort_name = 'news_category_translation.name';
            $sort_type = 'asc';
        }

        $filter['sort_name'] = $sort_name ?? 'created_at';
        $filter['sort_type'] = $sort_type ?? 'desc';
        if (empty($filter['page'])) {
            return NewsCategoryResources::collection($this->newsCategoryRepo->getList($filter));
        } else {
            return NewsCategoryResources::collection($this->newsCategoryRepo->getListPage($filter));
        }
    }

    public function showSingle($id)
    {
        $messages = [
            'lang.required' => '必須包含語言',
        ];
        $data = [];
        $data['lang'] = Input::get('lang') ?? '';
        $data['id'] = $id;
        $validator = Validator::make($data, [
            'lang' => 'required',
        ],$messages);

        if (!$validator->fails()) {
            $category = $this->newsCategoryRepo->get($data);
            $result = [];
            $result['data'] = $category;
            return $result;
        } else {
            throw new BadRequestException($validator->errors());
        }
    }

}
