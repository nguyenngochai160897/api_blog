<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::prefix('user')->namespace('Backend')->group(function() {
    Route::post('register', 'UserController@register');
    Route::post('login', 'UserController@login');
    Route::post('create-user', 'UserController@createUser');
});

Route::resource('category', 'Backend\CategoryController');

Route::resource('news', 'Backend\NewsController');

// Route::prefix('news')->namespace('Backend')->group(function(){
//     Route::resource('/', 'NewsController');
// });