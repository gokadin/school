<?php

return [

    'config' => [
        'databaseDriver' => 'mysql',
        'cacheDriver' => 'redis',
        'redisDatabase' => 15,
        'mappingDriver' => 'annotation'
    ],

    'classes' => [
        \App\Domain\Users\Teacher::class
    ]

];