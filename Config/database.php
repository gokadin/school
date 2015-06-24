<?php

$mysqlDatabase = 'jaggso5_School';
if (\Library\Config::get('env') == 'debug')
    $mysqlDatabase = 'School';
else if (\Library\Config::get('env') == 'testing')
    $mysqlDatabase = 'SchoolTest';

$mysqlUsername = 'jaggso5_guiviko';
if (\Library\Config::get('env') == 'debug')
    $mysqlUsername = 'root';
else if (\Library\Config::get('env') == 'testing')
    $mysqlUsername = 'root';

$mysqlPassword = 'f10ygs87';
if (\Library\Config::get('env') == 'debug')
    $mysqlPassword = 'f10ygs87';
else if (\Library\Config::get('env') == 'testing')
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