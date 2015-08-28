<?php

return [

    'config' => [
        'databaseDriver' => 'mysql',
        'cacheDriver' => 'redis',
        'mappingDriver' => 'annotation'
    ],

    'classes' => [
        \App\Domain\Users\Teacher::class
    ]

];