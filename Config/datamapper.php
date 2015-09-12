<?php

return [

    'config' => [
        'cacheDriver' => 'redis',
        'redisDatabase' => 15,
        'mappingDriver' => 'annotation'
    ],

    'classes' => [
        App\Domain\Users\Teacher::class,
        App\Domain\Subscriptions\Subscription::class
    ]

];