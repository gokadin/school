<?php

return [

    'mappingDriver' => 'annotation',

    'databaseDriver' => 'mysql',

    'mysql' => [
        'host' => env('DATABASE_HOST'),
        'database' => env('DATABASE_NAME'),
        'username' => env('DATABASE_USERNAME'),
        'password' => env('DATABASE_PASSWORD')
    ],

    'classes' => [
        App\Domain\Users\Teacher::class,
        App\Domain\Users\TempTeacher::class,
        App\Domain\Users\Student::class,
        App\Domain\Subscriptions\Subscription::class
    ]

];