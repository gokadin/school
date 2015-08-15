<?php

return [

    'config' => [
        'databaseDriver' => 'redis',
        'cacheDriver' => 'redis',
        'mappingDriver' => 'annotation'
    ],

    'classes' => [
        \App\Domain\Users\Teacher::class
    ]

];