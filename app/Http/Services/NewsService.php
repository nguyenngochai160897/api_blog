<?php
namespace App\Http\Services;

use App\Models\News;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;

class NewsService{
    public function __construct()
    {

    }
    /**
     * option = array(offset, limit, search = [])
    */
    public function getAllNews($option){
        $news = News::with('category');
        $searchTerm = ['title', 'short_description'];
        foreach($searchTerm as $term){
            if(isset($option[$term])) $news = $news->where($term,'like', "%{$option[$term]}%");
        }
        if(isset($option['offset'])){
            if(!isset($option['limit'])) $option['limit'] = 2;
            $news = $news->skip($option['offset'])->take($option['limit']);
        }
        // $news = News::with('category')->where([$option['search']])->get()->slice($option['offset'], $option['limit']);
        $news = $news->get();
        try{
            isAdmin();
        } catch (\Throwable $th) {
            $news = $news->where('active', 1);
        }
        return [
            'data' => $news,
            'status_code' => 200
        ];
    }

    private function uploadFile($data){
        $path = Storage::putFileAs(
            'public/news-image',
            $data,
            $data->getClientOriginalName()
        );
        return $path;
    }

    public function getNews($id){
        $news = collect(News::with('category')->where('id', $id)->get());
        $news = $news->map(function($new) {
            unset($new->category->id);
            if($new->category->parent_id == 1) unset($new->category->parent_id);
            return $new;
        });
        return [
            'data' => $news,
            'status_code' => 200
        ];
    }

    public function createNews($data){
        try {
            if(isAdmin()){
                $image = $this->uploadFile($data['image']);
                $data['image'] = Storage::url($image);
                $news = News::create($data);
                return [
                    'data' => $news,
                    'status_code' => 200
                ];
            }
        } catch (\Throwable $th) {
            return [
                'error' => 'You are not admin',
                'status_code' => 403
            ];
        }


    }

    public function updateNews($data, $id){
        try {
            if(isAdmin()){
                if(isset($data['image'])){
                    dd("f");
                    $image = $this->uploadFile($data['image']);
                    $data['image'] = Storage::url($image);
                }
                $news = News::where('id', $id)->update($data);
                return [
                    'data' => $news,
                    'status_code' => 200
                ];
            }
        } catch (\Throwable $th) {
            return [
                'error' => 'You are not admin',
                'status_code' => 403
            ];
        }


    }
}
