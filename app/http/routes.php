<?php

use Library\Facades\Router as Route;

Route::group(['namespace' => 'Some', 'prefix' => '/account/', 'middleware' => 'VerifyCsrfToken'], function() {
    Route::get('login', 'TestController@index');
});

Route::get('/index/{id}/{what}', 'TestController@index');