<?php

namespace App\Http\Services;

use App\Models\Category;
use Illuminate\Support\Facades\Gate;

class CategoryService{

    public function __construct()
    {
    }

    public function getAllCategories(){
        $categories = collect(Category::with('news')->where('id', '!=', 1)->get());
        $categories = $categories->map(function($category){
            $category->news->put('count', $category->news->count());
            return $category;
        });
        return [
            'data' => $categories,
            'status_code' => 200
        ];
    }

    public function getCategory($id){
        $category = Category::where('id', '!=', 1)->where('id', $id)->with('news')->first();
        $category->news->put('count', $category->news->count());
        return [
            'data' => $category,
            'status_code' => 200
        ];
    }

    public function createCategory($data){
        if(Gate::allows("isAdmin")){
            $category = Category::create($data);
            return [
                'data' => $category,
                'status_code' => 200
            ];
        }
        return [
            'error' => 'You are not admin',
            'status_code' => 403
        ];
    }

    public function updateCategory($data, $id){
        if(Gate::allows("isAdmin")){
            $category = Category::where('id', '!=', 1)->where('id', $id)->count();
            if($category > 0){
                $category = Category::where('id', $id)->update($data);
                return [
                    'data' => $category,
                    'status_code' => 200
                ];
            }
            return [
                'error' => 'Not found object with id='.$id,
                'status_code' => 404
            ];

        }
        return [
            'error' => 'You are not admin',
            'status_code' => 403
        ];
    }
}
