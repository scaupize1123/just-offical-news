<?php

namespace Scaupize1123\JustOfficalNews\Controllers\Api;

use Scaupize1123\JustOfficalNews\News;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Scaupize1123\JustOfficalNews\Interfaces\NewsRepositoryInterface;
use App\Exceptions\Api\BadRequestException;
use App\Helpers;
use Scaupize1123\JustOfficalNews\Resources\News as NewsResources;
use Scaupize1123\JustOfficalNews\Resources\SignleNews as SingleNewsResources;
use App\Traits\ImageTrait;
use Validator;
use Storage;
use Illuminate\Support\Str;

class NewsController extends \App\Http\Controllers\Controller
{
    use ImageTrait;

    private $newsRepo = null;

    public function __construct(NewsRepositoryInterface $newsRepo) 
    {
        $this->newsRepo = $newsRepo;
    }

    public function showPage()
    {
        $filter = [];
        $filter['news_category_id'] = Input::get('news_category_id') ?? null;
        $filter['size'] = Input::get('size') ?? 10;
        $filter['text'] = Input::get('text') ?? '';
        $filter['lang'] = Input::get('lang') ?? '';
        $filter['page'] = Input::get('page') ?? 1;
        $filter['langSort'] = Input::get('langSort') ?? '1';

        if (Input::get('sort') === 'sort_date_desc') {
            $sort_name = 'created_at';
            $sort_type = 'desc';
        } else if (Input::get('sort') === 'sort_date_asc') {
            $sort_name = 'created_at';
            $sort_type = 'asc';
        } else if (Input::get('sort') === 'sort_title_desc') {
            $sort_name = 'news_translation.name';
            $sort_type = 'desc';
        } else if (Input::get('sort') === 'sort_title_asc') {
            $sort_name = 'news_translation.name';
            $sort_type = 'asc';
        } 

        $filter['sort_name'] = $sort_name ?? 'created_at';
        $filter['sort_type'] = $sort_type ?? 'desc';
        $result = $this->newsRepo->getListPage($filter);
        \Log::info($result);
        return NewsResources::collection($result);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\news  $news
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $uuid)
    {
        $message = [
            'name.required' => '名稱為必填',
            'brief.required' => '簡述為必填',
            'start_date.required' => '開始時間為必填',
            'end_date.required' => '結束時間為必填',
            'category.required' => '類別為必填',
            'lang.required' => '語言為必填',
        ];
        $validator = Validator::make($request->all(), [
            'lang' => 'required',
            'name' => 'required',
            'brief' => 'required',
            'start_date' => 'required',
            'end_date' => 'required',
            'category' => 'required',
        ],$message);

        if (!$validator->fails()) {
            $thisNews = $this->newsRepo->getByUUID($uuid, $request->input('lang'));
            if(empty($thisNews)) {
                throw new \Illuminate\Database\Eloquent\ModelNotFoundException("news not found");
            }
            $update = [];
            if($request->hasFile('image')) {
                if(!empty($thisNews->translation[0]->image)) {
                    $fileSplit = explode('/', $thisNews->translation[0]->image);
                    $filename = $fileSplit[count($fileSplit)-1];
                    ImageTrait::deleteFile('/news/'.$uuid.'/'.$filename);
                }
                $image = $request->file('image');
                $mimeType = $image->getMimeType();
                $filename = ImageTrait::saveFile($image, storage_path('app/public').'/news/'.$uuid, ImageTrait::transMimeType($mimeType));
                $update['image'] = Storage::url('news/'.$uuid.'/'.$filename);
                $update['originalFileName'] = $image->getClientOriginalName();
            }else{
                $update['image'] = $thisNews->translation[0]->image ?? null;
                $update['originalFileName'] = $thisNews->translation[0]->image_name ?? null;
            }
            $update['uuid'] = $uuid;
            $update['lang'] = $request->input('lang');
            $update['name'] = $request->input('name');
            $update['brief'] = $request->input('brief');
            $update['start_date'] = $request->input('start_date');
            $update['end_date'] = $request->input('end_date');
            $update['category'] = $request->input('category');
            $update['tags'] = $request->input('tags') ?? null;
            $update['desc'] = $request->input('desc') ?? null;
            $news = $this->newsRepo->update($update);
            return response()->json(['ReturnCode' => 0, 'news' => $news]);
            
        } else {
            throw new BadRequestException($validator->errors());
        }
    }
    /**
     * @params
     * name required
     * brief required
     * start_date required
     * end_date required
     * category required
     * lang required
     * 
     * desc optional
     * image optional
     * tag optional
     */
    public function create(Request $request)
    {
        $message = [
            'name.required' => '名稱為必填',
            'brief.required' => '簡述為必填',
            'start_date.required' => '開始時間為必填',
            'end_date.required' => '結束時間為必填',
            'category.required' => '類別為必填',
            'lang' => '語言為必填',
        ];
        $validator = Validator::make($request->all(), [
            'lang' => 'required',
            'name' => 'required',
            'brief' => 'required',
            'start_date' => 'required',
            'end_date' => 'required',
            'category' => 'required',
        ],$message);

        if (!$validator->fails()) {
            $create = [];
            $uuid = Str::uuid();;
            if($request->hasFile('image')) {
                $image = $request->file('image');
                $mimeType = $image->getMimeType();
                $filename = ImageTrait::saveFile($image, storage_path('app/public').'/news/'.$uuid, ImageTrait::transMimeType($mimeType));
                $update['image'] = Storage::url('news/'.$uuid.'/'.$filename);
                $update['originalFileName'] = $image->getClientOriginalName();
            }
            $create['uuid'] = $uuid;
            $create['lang'] = $request->input('lang');
            $create['name'] = $request->input('name');
            $create['brief'] = $request->input('brief');
            $create['start_date'] = $request->input('start_date');
            $create['end_date'] = $request->input('end_date');
            $create['category'] = $request->input('category');
            $create['tags'] = $request->input('tags') ?? null;
            $create['desc'] = $request->input('desc') ?? null;
            $news = $this->newsRepo->create($create);
            return response()->json(['ReturnCode' => 0, 'news' => $news]);
        } else {
            throw new BadRequestException($validator->errors());
        }
    }


    public function showSingle($uuid)
    {
        $filter = [];
        $filter['lang'] = Input::get('lang') ?? null;

        $data_array = array(
            "uuid" => $uuid,
        );

        $validator = Validator::make($data_array, [
            'uuid' => 'required',
        ]);

        if (!$validator->fails()) {
            $news = $this->newsRepo->getByUUID($uuid, $filter['lang']);
            if(empty($news)) {
                throw new \Illuminate\Database\Eloquent\ModelNotFoundException("news not found");
            }
            //return $news;
            return new SingleNewsResources($news);
        } else {
            throw new BadRequestException($validator->errors());
        }
    }

    public function delete($uuid, Request $request)
    {
        $data = [];
        $data['lang'] = $request->input("lang") ?? "";
        $data['uuid'] = $uuid;

        $validator = Validator::make($data, [
            'uuid' => 'required',
        ]);

        if (!$validator->fails()) {
            if(!empty($data['lang'])) {
                if($this->newsRepo->checkOneLangNews($data['uuid'], $data['lang']) == 0) {
                    throw new \Illuminate\Database\Eloquent\ModelNotFoundException("news not found");
                }
                $this->newsRepo->deleteLanguage($data['uuid'], $data['lang']);
            }else{
                if($this->newsRepo->checkNews($data['uuid']) == 0) {
                    throw new \Illuminate\Database\Eloquent\ModelNotFoundException("news not found");
                }
                $this->newsRepo->delete($data['uuid']);
            }
            
            return response()->json(['ReturnCode' => 0]);
        } else {
            throw new BadRequestException($validator->errors());
        }
    }
}