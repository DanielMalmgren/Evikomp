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

Route::get('/', 'PagesController@index');

Route::get('/about', 'PagesController@about');

Route::get('/tracks', 'PagesController@tracks');

Route::get('/track/{track_id}', 'PagesController@track');

Route::get('/lesson/{lesson_id}', 'PagesController@lesson');

Route::get('/userinfo/{user_id?}', 'PagesController@userinfo');

Route::get('/settings', 'PagesController@settings');

Route::get('/listusers', 'PagesController@listUsers')->middleware('permission:list users');

Auth::routes();

//Route::get('/home', 'HomeController@index')->name('home');

Route::post('storeFirstLogin', 'PagesController@storeFirstLogin');

Route::post('storeSettings', 'PagesController@storeSettings');
