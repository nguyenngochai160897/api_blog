<?php

namespace App\Http\Services;

use App\Models\Category;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;

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
        $category = (Category::where('id', '!=', 1)->where('id', $id)->with('categories')->first());
        try{
            if(isAdmin()){
                $category->news->put('count', $category->news->count());
            }
        }catch (JWTException $e) {
            if($category->active == 0) $category = [];
        }

        return [
            'data' => $category,
            'status_code' => 200
        ];
    }

    public function createCategory($data){
        try {
            if(isAdmin()){
                $category = Category::create($data);
                return [
                    'data' => $category,
                    'status_code' => 200
                ];
            }
        } catch (JWTException $e) {
            return [
                'error' => 'You are not admin',
                'status_code' => 403
            ];
        }


    }

    public function updateCategory($data, $id){
        try {
            if(isAdmin()){
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
        } catch (JWTException $e) {
            return [
                'error' => 'You are not admin',
                'status_code' => 403
            ];
        }

        return [
            'error' => 'You are not admin',
            'status_code' => 403
        ];
    }
}
