<?php

$mysqlDatabase = 'jaggso5_School';
if (\Library\Config::get('env') == 'debug')
    $mysqlDatabase = 'School';
if (\Library\Config::get('testing') == 'true')
    $mysqlDatabase = 'SchoolTest';

$mysqlUsername = 'jaggso5_guiviko';
if (\Library\Config::get('env') == 'debug')
    $mysqlUsername = 'root';
if (\Library\Config::get('testing') == 'true')
    $mysqlUsername = 'root';

$mysqlPassword = 'f10ygs87';
if (\Library\Config::get('env') == 'debug')
    $mysqlPassword = 'f10ygs87';
if (\Library\Config::get('testing') == 'true')
    $mysqlPassword = 'f10ygs87';

return [
    'mysql' => [
        'driver' => 'mysql',
        'host' => 'localhost',
        'database' => $mysqlDatabase,
        'username' => $mysqlUsername,
        'password' => $mysqlPassword
    ]
];