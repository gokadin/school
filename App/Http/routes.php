<?php

$router->group(['namespace' => 'Api', 'prefix' => '/api', 'as' => 'api', 'middleware' => 'VerifyCsrfToken'], function($router)
{
    $router->group(['namespace' => 'Frontend', 'prefix' => '/frontend', 'as' => 'frontend'], function($router)
    {
        $router->group(['prefix' => '/account', 'as' => 'account'], function($router)
        {
            $router->post('/login', 'AccountController@login');
        });
    });

    $router->group(['namespace' => 'School', 'prefix' => '/school', 'as' => 'school', 'middleware' => 'VerifyAuthentication'], function($router)
    {
        $router->get('/currentUser', 'SchoolController@currentUser');

        $router->group(['namespace' => 'Teacher', 'prefix' => '/teacher', 'as' => 'teacher'], function($router)
        {
            $router->group(['prefix' => '/activities', 'as' => 'activity'], function($router)
            {
                $router->resource('ActivityController', ['store', 'update', 'delete']);
                $router->get('/paginate', 'ActivityController@paginate');
            });

            $router->group(['prefix' => '/events', 'as' => 'event'], function($router)
            {
                $router->get('/upcoming', 'EventController@upcomingEvents');
                $router->get('/range', 'EventController@range');
            });
        });
    });
});

//$router->group(['namespace' => 'Api', 'prefix' => '/api', 'as' => 'api', 'middleware' => 'VerifyCsrfToken'], function($router) {
//    $router->group(['namespace' => 'School', 'prefix' => '/school', 'as' => 'school', 'middleware' => 'VerifyAuthentication'], function($router) {
//
//        $router->group(['namespace' => 'Teacher', 'prefix' => '/teacher', 'as' =>'teacher'], function($router)
//        {
//            $router->get('/search/{search}', 'SearchController@index');
//
//            $router->group(['prefix' => '/activities', 'as' => 'activity'], function($router) {
//                $router->get('/', 'ActivityController@getAll');
//                $router->post('/', 'ActivityController@index');
//                $router->get('/{id}/students', 'ActivityController@students');
//                $router->delete('/{activityId}', 'ActivityController@destroy');
//                $router->put('/{activityId}', 'ActivityController@update');
//            });
//
//            $router->group(['prefix' => '/students', 'as' => 'student'], function($router) {
//                $router->get('/new-students', 'StudentController@newStudents');
//                $router->post('/', 'StudentController@index');
//                $router->post('/from-ids', 'StudentController@fromIds');
//                $router->post('/search', 'StudentController@search');
//                $router->post('/{id}/lessons/range', 'StudentController@lessonRange');
//            });
//
//            $router->group(['prefix' => '/events', 'as' => 'event'], function($router)
//            {
//                //$router->get('/upcoming-events', 'EventController@upcomingEvents');
//                $router->post('/', 'EventController@create');
//                //$router->post('/range', 'EventController@range');
//                $router->put('/change-date', 'EventController@changeDate');
//                $router->delete('/{id}', 'EventController@destroy');
//                $router->put('/{eventId}/lessons/{lessonId}/attendance', 'EventController@updateLessonAttendance');
//            });
//
//            $router->get('/get-registration-form', 'SettingController@getRegistration');
//            $router->post('/update-registration-form', 'SettingController@updateRegistrationForm');
//        });
//    });
//});


$router->catchAll(function() {
    return new \Library\Http\View('school.index');
});