<?php

use Library\Facades\Router as Route;

Route::group(['namespace' => 'Frontend'], function() {

        Route::get('/', 'IndexController@index');
        Route::get('/features', 'IndexController@features');
        Route::get('/testimonials', 'IndexController@testimonials');
        Route::get('/faq', 'IndexController@faq');
        Route::get('/contact-us', 'IndexController@contactUs');
        Route::get('/about', 'IndexController@about');

    Route::group(['prefix' => '/account', 'as' => 'frontend.account'], function() {

        Route::get('/login', 'AccountController@index');
        Route::post('/login', 'AccountController@login');
        Route::get('/logout', 'AccountController@logout');
        Route::get('/reset-password', 'AccountController@resetPassword');
        Route::get('/signup', 'AccountController@signup');
        Route::post('/signup', 'AccountController@registerUser');
        Route::get('/signup-land', 'AccountController@signUpLand');
        Route::get('/confirm/{id}-{code}', 'AccountController@emailConfirmation');
        Route::post('/confirm', 'AccountController@completeRegistration');

    });

    Route::group(['prefix' => '/ajax'], function() {
        Route::post('/exists', 'AjaxController@exists'); // to change
    });

});
