<?php

return [

    'use' => 'sync',

    'connections' => [

        'database' => [
            'table' => 'jobs',
            'failedTable' => 'failed_jobs'
        ]

    ]

];