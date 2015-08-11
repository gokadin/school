<?php

use Library\Facades\Router as Route;

Route::get('/', function() {
    return 'hello';
});

//Route::group(['namespace' => 'Frontend', 'as' => 'frontend', 'middleware' => 'VerifyCsrfToken'], function() {
//    Route::group(['as' => 'index'], function() {
//        Route::get('/', 'IndexController@index');
//        Route::get('/features', 'IndexController@features');
//        Route::get('/testimonials', 'IndexController@testimonials');
//        Route::get('/faq', 'IndexController@faq');
//        Route::get('/contact-us', 'IndexController@contactUs');
//        Route::get('/about', 'IndexController@about');
//    });
//
//    Route::group(['prefix' => '/account', 'as' => 'account'], function() {
//        Route::get('/login', 'AccountController@index');
//        Route::post('/login', 'AccountController@login');
//        Route::get('/logout', 'AccountController@logout');
//        Route::get('/reset-password', 'AccountController@resetPassword');
//        Route::get('/signup', 'AccountController@signUp');
//        Route::post('/signup', 'AccountController@preRegisterTeacher');
//        Route::get('/signup-land', 'AccountController@signUpLand');
//        Route::get('/confirm/{id}-{code}', 'AccountController@emailConfirmation');
//        Route::post('/confirm', 'AccountController@registerTeacher');
//    });
//});