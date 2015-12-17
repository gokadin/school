<?php

use Library\Facades\Router as Route;

/* FRONTEND */

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

    Route::group(['prefix' => '/student', 'as' => 'student'], function()
    {
        Route::get('/register/{id}-{code}', 'StudentController@index');
        Route::get('/not-found', 'StudentController@notFound');
        Route::post('/register', 'StudentController@register');
    });
});

/*
 * API
 */

Route::group(['namespace' => 'Api', 'prefix' => '/api', 'as' => 'api', 'middleware' => 'VerifyCsrfToken'], function() {
    Route::group(['namespace' => 'School', 'prefix' => '/school', 'as' => 'school', 'middleware' => 'VerifyAuthentication'], function() {

        Route::get('/teacher-new-students', 'StudentController@getNewStudents');

        Route::group(['namespace' => 'Teacher', 'prefix' => '/teacher', 'as' =>'teacher'], function() {
            Route::group(['prefix' => '/activities', 'as' => 'activity'], function() {
                Route::post('/', 'ActivityController@index');
                Route::delete('/{activityId}', 'ActivityController@destroy');
                Route::put('/{activityId}', 'ActivityController@update');
            });
            Route::get('/get-registration-form', 'SettingController@getRegistration');
            Route::post('/update-registration-form', 'SettingController@updateRegistrationForm');
        });
    });
});

/*
 * SCHOOL
 */

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
            Route::post('/pre-register', 'StudentController@preRegister');
            Route::get('/create', 'StudentController@create');
        });

        Route::group(['prefix' => '/settings', 'as' => 'setting'], function()
        {
            Route::get('/school-information', 'SettingController@schoolInformation');
            Route::get('/registrationForm', 'SettingController@registrationForm');
            Route::get('/preferences', 'SettingController@preferences');
        });

        Route::group(['prefix' => '/account', 'as' => 'account'], function()
        {
            Route::get('/', 'AccountController@index');
            Route::get('/personal-info', 'AccountController@personalInfo');
            Route::post('/personal-info', 'AccountController@updatePersonalInfo');
            Route::get('/password', 'AccountController@password');
            Route::post('/password', 'AccountController@updatePassword');
        });
    });
});