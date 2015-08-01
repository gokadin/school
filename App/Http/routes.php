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

Route::group(['namespace' => 'School', 'prefix' => '/school', 'as' => 'school', 'middleware' => ['VerifyCsrfToken', 'VerifyAuthentication']], function() {
    Route::group(['namespace' => 'Common', 'as' => 'common'], function() {
        Route::group(['prefix' => '/messaging', 'as' => 'messaging'], function() {
            Route::get('/', 'MessagingController@index');
            Route::post('/ajax/store', 'MessagingController@ajaxStore');
        });
    });

    Route::group(['namespace' => 'Teacher', 'prefix' => '/teacher', 'as' => 'teacher'], function() {
        Route::group(['as' => 'index'], function() {
            Route::get('/', 'IndexController@index');
        });

        Route::group(['prefix' => '/messaging', 'as' => 'messaging'], function() {
            Route::get('/', 'MessagingController@index');
            Route::post('/ajax/store', 'MessagingController@ajaxStore');
            Route::delete('/ajax/destroyConversation', 'MessagingController@destroyConversation');
        });

        Route::group(['prefix' => '/activities', 'as' => 'activity'], function() {
            Route::get('/', 'ActivityController@index');
            Route::get('/create', 'ActivityController@create');
            Route::post('/create', 'ActivityController@store');
            Route::put('/edit', 'ActivityController@update');
            Route::delete('/delete', 'ActivityController@destroy');
        });

        Route::group(['prefix' => '/students', 'as' => 'student'], function() {
            Route::get('/', 'StudentController@index');
            Route::get('/create', 'StudentController@create');
            Route::post('/create', 'StudentController@store');
            Route::put('/edit', 'StudentController@update');
            Route::delete('/delete', 'StudentController@destroy');
        });

        Route::group(['prefix' => '/calendar', 'as' => 'calendar'], function() {
            Route::get('/', 'CalendarController@index');
        });

        Route::group(['prefix' => '/payments', 'as' => 'payments'], function() {
            Route::get('/', 'PaymentController@index');
        });

        Route::group(['prefix' => '/account', 'as' => 'account'], function() {
            Route::get('/', 'AccountController@index');
            Route::put('/edit-personal-info', 'AccountController@editPersonalInfo');
            Route::put('/change-password', 'AccountController@changePassword');
            Route::put('/edit-profile-picture', 'AccountController@editProfilePicture');
            Route::get('/subscription', 'AccountController@subscription');
        });

        Route::group(['prefix' => '/ajax', 'as' => 'ajax'], function() {
            Route::post('/email-exists', 'AjaxController@emailExists');
            Route::post('/add-event', 'AjaxController@addEvent');
            Route::post('/change-event-date', 'AjaxController@changeEventDate');
        });
    });

    Route::group(['namespace' => 'Student', 'prefix' => '/student', 'as' => 'student'], function() {
        Route::group(['as' => 'index'], function() {
            Route::get('/', 'IndexController@index');
        });
    });
});