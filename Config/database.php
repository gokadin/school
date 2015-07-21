<?php

$mysqlDatabase = 'jaggso5_School';
if (env('APP_ENV') == 'local')
    $mysqlDatabase = 'School';
if (env('APP_ENV') == 'testing')
    $mysqlDatabase = 'ApplicationTest';
else if (env('APP_ENV') == 'framework_testing')
    $mysqlDatabase = 'FrameworkTest';

$mysqlUsername = 'jaggso5_guiviko';
if (env('APP_ENV') == 'local')
    $mysqlUsername = 'root';
if (env('APP_ENV') == 'testing')
    $mysqlUsername = 'root';
else if (env('APP_ENV') == 'framework_testing')
    $mysqlUsername = 'root';

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