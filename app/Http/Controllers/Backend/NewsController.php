<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreateNewsRequest;
use App\Http\Services\NewsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class NewsController extends Controller
{
    private $newsService;

    public function __construct(NewsService $newsService)
    {
        $this->newsService = $newsService;
        $this->middleware("auth.jwt")->only(["store", "update", "destroy"]);
    }

    public function displayImage(Request $request){
        return view('display-image', ['image' => Storage::url('public/avatars/avatar.jpeg')]);
    }

    public function upload(Request $request){
        $path = Storage::putFileAs(
            'public/avatars', $request->file('avatar'), $request->file('avatar')->getClientOriginalName(), "public"
        );
        return $path;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $data = $this->newsService->getAllNews($request->all());
        return responseHelper($data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CreateNewsRequest $request)
    {
        $input = $request->only(['title','short_description','description', 'active', 'category_id']);
        $input['image'] =  $request->file('image');
        $data = $this->newsService->createNews($input);
        return responseHelper($data);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $data = $this->newsService->getNews($id);
        return responseHelper($data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(CreateNewsRequest $request, $id)
    {
        $input = $request->only(['title','short_description','description', 'active', 'category_id']);
        if ($request->hasFile('image')) {
            $input['image'] =  $request->file('image');
        }
        $data = $this->newsService->updateNews($input, $id);
        return responseHelper($data);
    }

}
