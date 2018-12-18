<?php

Auth::routes();

Route::get('/',                                 'HomeController@index');
Route::get('/about',                            'HomeController@about');

//TrackController
Route::get('/tracks',                           'TrackController@index');
Route::get('/track/{track_id}',                 'TrackController@show');

//LessonController
Route::get('/lesson/{lesson_id}',               'LessonController@show');

//TestController
Route::get('/test/{lesson_id}/{question_id?}',  'TestController@show');
Route::post('storetestresponse',                'TestController@store');

//TestResultController
Route::get('/testresult/{lesson_id}',           'TestResultController@show');

//UsersControler
Route::get('/userinfo/{user_id?}',              'UsersController@show');
Route::get('/listusers',                        'UsersController@index')->middleware('permission:list users');
Route::get('/exportusers',                      'UsersController@export')->middleware('permission:list users');

//FirstLoginController
Route::get('/firstlogin',                       'FirstLoginController@show');
Route::post('storefirstlogin',                  'FirstLoginController@store');
Route::post('storefirstloginlanguage',          'FirstLoginController@storeLanguage');

//SettingsController
Route::get('/settings',                         'SettingsController@edit');
Route::post('storesettings',                    'SettingsController@store');
