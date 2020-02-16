<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
Route::group(['prefix' => 'user', 'middleware' => 'auth'], function() {
    Route::get('mypage', 'User\InstaController@profile');
    Route::get('mypage/edit', 'User\InstaController@profile_edit');
    Route::post('mypage/edit', 'User\InstaController@profile_update');
    Route::get('post', 'User\InstaController@post');
    Route::post('post', 'User\InstaController@post_create');
    Route::get('post/edit/{id}', 'User\InstaController@post_edit');
    Route::post('post/edit', 'User\InstaController@post_update');
    Route::post('post/delete', 'User\InstaController@post_delete');
    Route::post('follow', 'User\InstaController@follow');
    Route::post('follow/delete', 'User\InstaController@follow_delete');
    Route::post('comment', 'User\CommentController@post_comment');
    Route::post('good', 'User\CommentController@post_good');
});

Auth::routes();

Route::get('/', 'MainController@index');
Route::get('/home', 'HomeController@index')->name('home');
Route::get('/register', 'MainController@new_user')->name('register');
Route::post('data/comments', 'User\CommentController@get_comment');
Route::post('data/goods', 'User\CommentController@get_good');
Route::post('data/follows', 'User\InstaController@get_follow');
Route::post('data/followers', 'User\InstaController@get_follower');
Route::get('userpage/{name}', 'User\InstaController@open_userpage');
