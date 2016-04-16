<?php

return [

    'driver' => 'mysql',

    'mysql' => [
        'host' => env('DATABASE_HOST'),
        'database' => env('DATABASE_NAME'),
        'username' => env('DATABASE_USERNAME'),
        'password' => env('DATABASE_PASSWORD')
    ]

];