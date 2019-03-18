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

Route::get('/', 'StaticPagesController@home')->name('home');
//帮助页
Route::get('/help', 'StaticPagesController@help')->name('help');
//关于页
Route::get('/about', 'StaticPagesController@about')->name('about');
//注册页
Route::get('signup','UsersController@create')->name('signup');
Route::resource('users','UsersController');
Route::get('/users/{user}','UsersController@show')->name('users.show');
//登录
Route::get('login','SessionsController@create')->name('login');
Route::post('login','SessionsController@store')->name('login');
//退出
Route::delete('logout','SessionsController@destory')->name('logout');

