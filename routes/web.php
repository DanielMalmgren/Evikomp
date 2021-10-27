<?php

Auth::routes();

Route::get('/unsecurelogin',                    'HomeController@unsecurelogin');

Route::get('/',                                 'HomeController@index');
Route::get('/about',                            'HomeController@about');
Route::get('/logout',                           'HomeController@logout');
Route::get('/prelogout',                        'HomeController@prelogout');

Route::permanentRedirect('/attest', '/timeattestlevel1/create');

//TrackController
Route::get('/tracks/create',                    'TrackController@create')->middleware('permission:manage lessons');
Route::post('/tracks',                          'TrackController@store')->middleware('permission:manage lessons');
Route::get('/tracks',                           'TrackController@index');
Route::get('/tracks/{track}/compilationxls',    'TrackController@compilationXls');
Route::get('/tracks/{track}',                   'TrackController@show');
Route::get('/tracks/{track}/edit',              'TrackController@edit')->middleware('permission:manage lessons');
Route::put('/tracks/{track}',                   'TrackController@update')->middleware('permission:manage lessons');
Route::get('/tracks/{track}/pdfdiploma',        'TrackController@pdfdiploma');
Route::post('/tracks/reorder',                  'TrackController@reorder')->middleware('permission:manage lessons');

//LessonController
Route::get('/lessons/create/{track}',           'LessonController@create');
Route::post('/lessons',                         'LessonController@store');
Route::get('/lessons/{lesson}/edit',            'LessonController@edit');
Route::put('/lessons/{lesson}',                 'LessonController@update');
Route::get('/lessons/{lesson}/editquestions',   'LessonController@editquestions');
Route::put('/lessons/{lesson}/vote',            'LessonController@vote');
//Route::get('/lessons/{lesson}/finish',          'LessonController@finish');
Route::post('/lessons/reorder',                 'LessonController@reorder');
Route::get('/lessons/{lesson}/replicate',       'LessonController@replicate');
Route::get('/lessons/{lesson}/{page?}',         'LessonController@show');
Route::post('/lessons/replicateQuestions',      'LessonController@replicateQuestions');
Route::delete('/lessons/{lesson}',              'LessonController@destroy');

//NotificationReceiversController
Route::get('/notificationreceivers/{lesson}/edit', 'NotificationReceiversController@edit');
Route::put('/notificationreceivers/{lesson}',   'NotificationReceiversController@update');

//TestController
Route::get('/test/{lesson}',                    'TestController@show');
Route::post('/test/{lesson}/number_of_questions', 'TestController@set_number_of_questions');
Route::post('/test/{lesson}/test_required_percent', 'TestController@set_test_required_percent');
Route::post('/test/storeResponse',              'TestController@store');

//QuestionController
Route::get('/test/question/create',             'QuestionController@create');
Route::post('/test/question',                   'QuestionController@store');
Route::get('/test/question/{question}',         'QuestionController@show');
Route::get('/test/question/{question}/edit',    'QuestionController@edit');
Route::put('/test/question/{question}',         'QuestionController@update');
Route::delete('/test/question/{question}',      'QuestionController@destroy');
Route::post('/test/question/reorder',           'QuestionController@reorder');

//LessonResultController
Route::get('/result/{lesson}',                  'LessonResultController@show');
Route::get('/result/{lesson}/pdfdiploma',       'LessonResultController@pdfdiploma');

//FeedbackController
Route::get('/feedback',                         'FeedbackController@create');
Route::post('/feedback',                        'FeedbackController@post');

//UsersControler
Route::get('/users/create',                     'UsersController@create')->middleware('permission:manage users');
Route::get('/users/impersonate/{user}',         'UsersController@impersonate')->middleware('permission:impersonate');
Route::get('/users/leaveimpersonation',         'UsersController@leaveImpersonation');
Route::get('/users/{user}',                     'UsersController@show');
Route::post('/users',                           'UsersController@store')->middleware('permission:manage users');
Route::get('/users',                            'UsersController@index')->middleware('permission:manage users');
Route::delete('/user/{user}',                   'UsersController@destroy')->middleware('permission:manage users');
Route::get('/select2users',                     'UsersController@select2');

//ActiveTimeController
Route::post('/activetime',                      'ActiveTimeController@store');

//TimeSummaryController
Route::get('/timesummary',                      'TimeSummaryController@show')->middleware('permission:export ESF report');
Route::get('/timesummaryajax/{rel_month}',      'TimeSummaryController@ajax')->middleware('permission:export ESF report');
Route::get('/timesummarywpdetails/{workplace}/{year}/{month}', 'TimeSummaryController@wpdetails')->middleware('permission:export ESF report');
Route::get('/exporttimesummary',                'TimeSummaryController@export')->middleware('permission:export ESF report');

//ProjectTimeController
Route::get('/projecttime/create',               'ProjectTimeController@create');
Route::get('/projecttime',                      'ProjectTimeController@index');
Route::post('/projecttime',                     'ProjectTimeController@store');
Route::get('/projecttime/presence_list/{project_time}', 'ProjectTimeController@presence_list');
Route::get('/projecttime/attest_from_list/{project_time}', 'ProjectTimeController@attest_from_list')->middleware('permission:manage time attests');
Route::get('/projecttime/{project_time}/edit',  'ProjectTimeController@edit');
Route::put('/projecttime/{project_time}',       'ProjectTimeController@update');
Route::get('/projecttimeajax/{workplace?}',     'ProjectTimeController@ajax');
Route::get('/projecttime/{year}/{month}',       'ProjectTimeController@show');
Route::delete('/projecttime/{project_time}',    'ProjectTimeController@destroy')->middleware('permission:manage time attests');

//TimeAttestController
Route::get('/timeattest/create',                'TimeAttestController@create')->middleware('permission:manage time attests');
Route::post('/timeattest',                      'TimeAttestController@store')->middleware('permission:manage time attests');
Route::get('/timeattestajaxuserlist/{workplace}/{year}/{month}', 'TimeAttestController@ajaxuserlist')->middleware('permission:manage time attests');
Route::get('/timeattestajaxuserdetails/{user}/{year}/{month}', 'TimeAttestController@ajaxuserdetails')->middleware('permission:manage time attests');
Route::put('/timeattest/from_list/{project_time}', 'TimeAttestController@from_list')->middleware('permission:manage time attests');

//TimeAttestLevel1Controller
Route::get('/timeattestlevel1/create',          'TimeAttestLevel1Controller@create');
Route::post('/timeattestlevel1',                'TimeAttestLevel1Controller@store');
Route::get('/manualattestxls',                  'TimeAttestLevel1Controller@manualattestxls');

//FirstLoginController
Route::get('/firstlogin',                       'FirstLoginController@show');
Route::post('storefirstloginlanguage',          'FirstLoginController@storeLanguage');
Route::post('storegdpraccept',                  'FirstLoginController@storeGdprAccept');

//SettingsController
Route::get('/settings/{user?}',                 'SettingsController@edit');
Route::post('storesettings/{user}',             'SettingsController@store');
Route::post('storelanguage',                    'SettingsController@storeLanguage');

//WorkplaceController
Route::get('/workplace/create',                 'WorkplaceController@create')->middleware('permission:add workplaces');
Route::post('/workplace',                       'WorkplaceController@store')->middleware('permission:add workplaces');
Route::get('/workplace',                        'WorkplaceController@edit')->middleware('permission:edit workplaces');
Route::put('/workplace/{workplace}',            'WorkplaceController@update')->middleware('permission:edit workplaces');
Route::get('/workplaceajax/{workplace}',        'WorkplaceController@ajax')->middleware('permission:edit workplaces');
Route::get('/workplace/{workplace}/getusers',   'WorkplaceController@getusers');
Route::delete('/workplace/{workplace}',         'WorkplaceController@destroy')->middleware('permission:add workplaces');

//SessionController
Route::get('/sessions',                         'SessionController@index')->middleware('permission:manage users');

//StatisticsController
Route::get('/statistics',                       'StatisticsController@index');
Route::get('/statistics/ajaxchart/{chartid}',   'StatisticsController@ajaxchart');
Route::get('/statistics/export',                'StatisticsController@export');

//AnnouncementsController
Route::resource('announcements',                'AnnouncementsController');

//ListController
Route::resource('lists',                        'ListController');
Route::post('/lists/lessonattach',              'ListController@lessonAttach');
Route::get('/lists/{list}/replicate',           'ListController@replicate');

//SearchController
Route::get('/select2search',                    'SearchController@select2');

//PollController
Route::get('/poll',                             'PollController@index');
Route::get('/poll/create',                      'PollController@create');
Route::post('/poll',                            'PollController@store');
Route::get('/poll/{poll}/edit',                 'PollController@edit');
Route::get('/poll/{poll}/replicate',            'PollController@replicate');
Route::get('/poll/{poll}/exportresponses',      'PollController@exportresponses');
Route::get('/poll/{poll}/{lesson?}',            'PollController@show');
Route::put('/poll/{poll}',                      'PollController@update');

//PollQuestionController
Route::get('/pollquestion/create/{poll}',       'PollQuestionController@create');
Route::post('/pollquestion',                    'PollQuestionController@store');
Route::get('/pollquestion/{question}',          'PollQuestionController@show');
Route::get('/pollquestion/{question}/edit',     'PollQuestionController@edit');
Route::put('/pollquestion/{question}',          'PollQuestionController@update');
Route::post('/pollquestion/reorder',            'PollQuestionController@reorder');
Route::delete('/pollquestion/{question}',       'PollQuestionController@destroy');

//PollResponseController
Route::post('/pollresponse',                    'PollResponseController@store');

//MassMailingController
Route::get('/massmailing/create',               'MassMailingController@create')->middleware('permission:send mass mailing');
Route::post('/massmailing',                     'MassMailingController@store')->middleware('permission:send mass mailing');

//SCORMImportController
Route::get('/scormimport/create/{track}',       'SCORMImportController@create');
Route::post('/scormimport',                     'SCORMImportController@store');

//TagController
Route::get('/tags/{tag}',                       'TagController@show');

//LogController
Route::get('/log',                              'LogController@index');
