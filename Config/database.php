<?php

use Library\Facades\Config;

$mysqlDatabase = 'jaggso5_School';
if (Config::get('env') == 'debug')
    $mysqlDatabase = 'School';
if (Config::get('testing') == 'true')
    $mysqlDatabase = 'ApplicationTest';
else if (Config::get('frameworkTesting') == 'true')
    $mysqlDatabase = 'FrameworkTest';

$mysqlUsername = 'jaggso5_guiviko';
if (Config::get('env') == 'debug')
    $mysqlUsername = 'root';
if (Config::get('testing') == 'true')
    $mysqlUsername = 'root';
else if (Config::get('frameworkTesting') == 'true')
    $mysqlUsername = 'root';

$mysqlPassword = 'f10ygs87';
if (Config::get('env') == 'debug')
    $mysqlPassword = 'f10ygs87';
if (Config::get('testing') == 'true')
    $mysqlPassword = 'f10ygs87';
else if (Config::get('frameworkTesting') == 'true')
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