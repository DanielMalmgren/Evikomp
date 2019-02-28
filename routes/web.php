<?php

Auth::routes();

Route::get('/',                                 'HomeController@index');
Route::get('/about',                            'HomeController@about');
Route::get('/logout',                           'HomeController@logout');

//TrackController
Route::get('/tracks',                           'TrackController@index');
Route::get('/track/{track}',                    'TrackController@show');

//LessonController
Route::get('/lessons/create/{track}',           'LessonController@create')->middleware('permission:manage lessons');
Route::post('/lessons',                         'LessonController@store')->middleware('permission:manage lessons');
Route::get('/lessons/{lesson}',                 'LessonController@show');
Route::get('/lessons/{lesson}/edit',            'LessonController@edit')->middleware('permission:manage lessons');
Route::put('/lessons/{lesson}',                 'LessonController@update')->middleware('permission:manage lessons');
Route::get('/lessons/{lesson}/editquestions',   'LessonController@editquestions')->middleware('permission:manage lessons');
Route::put('/lessons/{lesson}/vote',            'LessonController@vote');
Route::post('/lessons/reorder',                 'LessonController@reorder')->middleware('permission:manage lessons');

//TestController
Route::get('/test/{lesson}',                    'TestController@show');
Route::post('/test/storeResponse',              'TestController@store');

//QuestionController
Route::get('/test/question/create',             'QuestionController@create')->middleware('permission:manage lessons');
Route::post('/test/question',                   'QuestionController@store')->middleware('permission:manage lessons');
Route::get('/test/question/{question}',         'QuestionController@show');
Route::get('/test/question/{question}/edit',    'QuestionController@edit')->middleware('permission:manage lessons');
Route::put('/test/question/{question}',         'QuestionController@update')->middleware('permission:manage lessons');
Route::delete('/test/question/{question}',      'QuestionController@destroy')->middleware('permission:manage lessons');
Route::post('/test/question/reorder',           'QuestionController@reorder')->middleware('permission:manage lessons');

//TestResultController
Route::get('/test/result/{test_session}',       'TestResultController@show');

//FeedbackController
Route::get('/feedback',                         'FeedbackController@create');
Route::post('/feedback',                        'FeedbackController@post');

//UsersControler
//Route::get('/userinfo/{user?}',                 'UsersController@show');
Route::get('/listusers',                        'UsersController@index')->middleware('permission:manage users');
Route::get('/exportusers',                      'UsersController@export')->middleware('permission:manage users');

//ActiveTimeController
Route::get('/activetime',                       'ActiveTimeController@show');
Route::post('/activetime',                      'ActiveTimeController@store');
Route::get('/exportactivetime/{user?}',         'ActiveTimeController@export');

//TimeSummaryController
Route::get('/timesummary',                      'TimeSummaryController@show');
Route::get('/exporttimesummary',                'TimeSummaryController@export');

//ProjectTimeController
Route::get('/projecttime/create',               'ProjectTimeController@create');
Route::post('/projecttime/{workplace}',         'ProjectTimeController@store');
Route::get('/projecttimeajax/{workplace}',      'ProjectTimeController@ajax');

//FirstLoginController
Route::get('/firstlogin',                       'FirstLoginController@show');
Route::post('storefirstloginlanguage',          'FirstLoginController@storeLanguage');
Route::post('storegdpraccept',                  'FirstLoginController@storeGdprAccept');

//SettingsController
Route::get('/settings',                         'SettingsController@edit');
Route::post('storesettings',                    'SettingsController@store');
Route::post('storelanguage',                    'SettingsController@storeLanguage');

//WorkplaceController
Route::get('/workplace/create',                 'WorkplaceController@create')->middleware('permission:add workplaces');
Route::post('/workplace',                       'WorkplaceController@store')->middleware('permission:add workplaces');
Route::get('/workplace',                        'WorkplaceController@edit')->middleware('permission:edit workplaces');
Route::put('/workplace/{workplace}',            'WorkplaceController@update')->middleware('permission:edit workplaces');
Route::get('/workplaceajax/{workplace}',        'WorkplaceController@ajax')->middleware('permission:edit workplaces');

//AnnouncementsController
Route::resource('announcements',                'AnnouncementsController');
