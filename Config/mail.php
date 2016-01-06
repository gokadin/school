<?php

return [

    'driver' => 'mailgun',

    'mailgun' => [
        'domain' => env('MAIL_DOMAIN'),
        'secret' => env('MAIL_SECRET')
    ],

];