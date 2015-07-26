<?php

return [

    'use' => env('QUEUE_DRIVER'),

    'connections' => [

        'database' => [
            'table' => 'jobs',
            'failedTable' => 'failed_jobs'
        ]

    ]

];