<?php

$router->group(['namespace' => 'Test', 'prefix' => '/test', 'as' => 'test'], function($router)
{
    $router->group(['namespace' => 'Api', 'prefix' => '/api', 'as' => 'api'], function($router)
    {
        $router->group(['namespace' => 'Frontend', 'prefix' => '/frontend', 'as' => 'frontend'], function($router)
        {
            $router->post('/login', 'AccountController@login');
        });

        $router->group(['namespace' => 'School', 'prefix' => '/school', 'as' => 'school'], function($router)
        {
            $router->group(['namespace' => 'Teacher', 'prefix' => '/teacher', 'as' => 'teacher'], function($router)
            {
                $router->group(['prefix' => '/messaging', 'as' => 'messaging'], function($router)
                {
                    $router->get('/students', 'MessagingController@students');
                });
            });
        });
    });
});

$router->group(['namespace' => 'Api', 'prefix' => '/api', 'as' => 'api', 'middleware' => 'VerifyCsrfToken'], function($router) {
    $router->group(['namespace' => 'School', 'prefix' => '/school', 'as' => 'school', 'middleware' => 'VerifyAuthentication'], function($router) {

        $router->group(['namespace' => 'Teacher', 'prefix' => '/teacher', 'as' =>'teacher'], function($router)
        {
            $router->get('/search/{search}', 'SearchController@index');

            $router->group(['prefix' => '/activities', 'as' => 'activity'], function($router) {
                $router->get('/', 'ActivityController@getAll');
                $router->post('/', 'ActivityController@index');
                $router->get('/{id}/students', 'ActivityController@students');
                $router->delete('/{activityId}', 'ActivityController@destroy');
                $router->put('/{activityId}', 'ActivityController@update');
            });

            $router->group(['prefix' => '/students', 'as' => 'student'], function($router) {
                $router->get('/new-students', 'StudentController@newStudents');
                $router->post('/', 'StudentController@index');
                $router->post('/from-ids', 'StudentController@fromIds');
                $router->post('/search', 'StudentController@search');
                $router->post('/{id}/lessons/range', 'StudentController@lessonRange');
            });

            $router->group(['prefix' => '/events', 'as' => 'event'], function($router)
            {
                $router->get('/upcoming-events', 'EventController@upcomingEvents');
                $router->post('/', 'EventController@create');
                $router->post('/range', 'EventController@range');
                $router->put('/change-date', 'EventController@changeDate');
                $router->delete('/{id}', 'EventController@destroy');
                $router->put('/{eventId}/lessons/{lessonId}/attendance', 'EventController@updateLessonAttendance');
            });

            $router->get('/get-registration-form', 'SettingController@getRegistration');
            $router->post('/update-registration-form', 'SettingController@updateRegistrationForm');
        });
    });
});

/*
 * SCHOOL
 */

$router->group([
    'namespace' => 'School',
    'prefix' => '/school',
    'as' => 'school',
    'middleware' => ['VerifyCsrfToken', 'VerifyAuthentication']
], function($router) {

    $router->group(['namespace' => 'Teacher', 'prefix' => '/teacher', 'as' => 'teacher'], function($router)
    {
        $router->group(['as' => 'index'], function ($router)
        {
            $router->get('/', 'IndexController@index');
        });

        $router->group(['prefix' => '/activities', 'as' => 'activity'], function ($router)
        {
            $router->get('/', 'ActivityController@index');
            $router->get('/create', 'ActivityController@create');
            $router->post('/create', 'ActivityController@store');
            $router->put('/edit', 'ActivityController@update');
            $router->delete('/delete', 'ActivityController@destroy');
        });

        $router->group(['prefix' => '/students', 'as' => 'student'], function ($router)
        {
            $router->resource('StudentController', ['index', 'create', 'show']);
            $router->post('/pre-register', 'StudentController@preRegister');
            $router->get('/{id}/lessons', 'StudentController@lessons');
            $router->get('/{id}/activities', 'StudentController@activities');
        });

        $router->group(['prefix' => '/calendar', 'as' => 'calendar'], function($router)
        {
            $router->get('/', 'CalendarController@index');
        });

        $router->group(['prefix' => '/settings', 'as' => 'setting'], function($router)
        {
            $router->get('/school-information', 'SettingController@schoolInformation');
            $router->get('/registrationForm', 'SettingController@registrationForm');
            $router->get('/preferences', 'SettingController@preferences');
            $router->put('/preferences', 'SettingController@updatePreferences');
        });

        $router->group(['prefix' => '/account', 'as' => 'account'], function($router)
        {
            $router->get('/', 'AccountController@index');
            $router->get('/personal-info', 'AccountController@personalInfo');
            $router->post('/personal-info', 'AccountController@updatePersonalInfo');
            $router->get('/password', 'AccountController@password');
            $router->post('/password', 'AccountController@updatePassword');
        });
    });

    $router->group(['namespace' => 'Student', 'prefix' => '/student', 'as' => 'student'], function($router)
    {
        $router->group(['as' => 'index'], function ($router)
        {
            $router->get('/', 'IndexController@index');
        });
    });
});

$router->catchAll(function($router) use ($view) {
    return new \Library\Http\View('index');
});