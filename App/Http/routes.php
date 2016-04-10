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
            $router->group(['prefix' => '/search', 'as' => 'search'], function($router) {
                $router->get('/general/{search}', 'SearchController@generalSearch');
            });

            $router->group(['prefix' => '/activities', 'as' => 'activity'], function($router)
            {
                $router->resource('ActivityController', ['store', 'update', 'delete']);
                $router->get('/paginate', 'ActivityController@paginate');
                $router->get('/{id}/students', 'ActivityController@students');
            });

            $router->group(['prefix' => '/students', 'as' => 'student'], function($router)
            {
                $router->get('/paginate', 'StudentController@paginate');
                $router->get('/pending', 'StudentController@pending');
                $router->resource('StudentController', ['show']);
                $router->get('/{id}/lessons', 'StudentController@lessons');
            });

            $router->group(['prefix' => '/events', 'as' => 'event'], function($router)
            {
                $router->get('/upcoming', 'EventController@upcomingEvents');
                $router->get('/range', 'EventController@range');
                $router->patch('/{id}/{oldDate}/date', 'EventController@updateDate');
            });

            $router->group(['namespace' => 'Calendar', 'prefix' => '/calendar', 'as' => 'calendar'], function($router)
            {
                $router->group(['prefix' => '/availabilities', 'as' => 'availability'], function($router)
                {
                    $router->get('/range', 'AvailabilityController@range');
                    $router->resource('AvailabilityController', ['store', 'update', 'destroy']);
                });
            });
        });
    });
});

//$router->group(['namespace' => 'Api', 'prefix' => '/api', 'as' => 'api', 'middleware' => 'VerifyCsrfToken'], function($router) {
//    $router->group(['namespace' => 'School', 'prefix' => '/school', 'as' => 'school', 'middleware' => 'VerifyAuthentication'], function($router) {
//
//        $router->group(['namespace' => 'Teacher', 'prefix' => '/teacher', 'as' =>'teacher'], function($router)
//        {
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