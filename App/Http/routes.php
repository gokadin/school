<?php

use Library\Facades\Router as Route;

Route::group(['namespace' => 'Frontend', 'as' => 'frontend', 'middleware' => 'VerifyCsrfToken'], function() {
    Route::group(['as' => 'index'], function() {
        Route::get('/', 'IndexController@index');
        Route::get('/features', 'IndexController@features');
        Route::get('/testimonials', 'IndexController@testimonials');
        Route::get('/faq', 'IndexController@faq');
        Route::get('/contact-us', 'IndexController@contactUs');
        Route::get('/about', 'IndexController@about');
    });

    Route::group(['prefix' => '/account', 'as' => 'account'], function() {
        Route::get('/login', 'AccountController@index');
        Route::post('/login', 'AccountController@login');
        Route::get('/logout', 'AccountController@logout');
        Route::get('/reset-password', 'AccountController@resetPassword');
        Route::get('/signup', 'AccountController@signUp');
        Route::post('/signup', 'AccountController@preRegisterTeacher');
        Route::get('/signup-land', 'AccountController@signUpLand');
        Route::get('/confirm/{id}-{code}', 'AccountController@emailConfirmation');
        Route::post('/confirm', 'AccountController@registerTeacher');
    });
});

Route::group(['namespace' => 'Api', 'prefix' => '/api', 'as' => 'api', 'middleware' => 'VerifyCsrfToken'], function() {
    Route::group(['namespace' => 'School', 'prefix' => '/school', 'as' => 'school', 'middleware' => 'VerifyAuthentication'], function() {
        Route::post('/teacher-activities', 'ActivityController@getTeacherActivities');
        Route::get('/teacher-activities/total', 'ActivityController@getTeacherActivityCount');
    });
});

Route::group([
    'namespace' => 'School',
    'prefix' => '/school',
    'as' => 'school',
    'middleware' => ['VerifyCsrfToken', 'VerifyAuthentication']
], function() {

    Route::group(['namespace' => 'Teacher', 'prefix' => '/teacher', 'as' => 'teacher'], function()
    {
        Route::group(['as' => 'index'], function ()
        {
            Route::get('/', 'IndexController@index');
        });

        Route::group(['prefix' => '/activities', 'as' => 'activity'], function ()
        {
            Route::get('/', 'ActivityController@index');
            Route::get('/create', 'ActivityController@create');
            Route::post('/create', 'ActivityController@store');
            Route::put('/edit', 'ActivityController@update');
            Route::delete('/delete', 'ActivityController@destroy');
        });

        Route::group(['prefix' => '/students', 'as' => 'student'], function ()
        {
            Route::get('/', 'StudentController@index');
            Route::get('/create', 'StudentController@create');
            Route::post('/create', 'StudentController@store');
            Route::put('/edit', 'StudentController@update');
            Route::delete('/delete', 'StudentController@destroy');
        });
    });
});