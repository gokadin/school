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
        App\Domain\Users\TempStudent::class,
        App\Domain\Subscriptions\Subscription::class,
        App\Domain\Common\Address::class,
        App\Domain\School\School::class,
        App\Domain\Activities\Activity::class,
        App\Domain\Setting\TeacherSettings::class,
        App\Domain\Setting\StudentSettings::class,
        App\Domain\Events\Event::class,
        App\Domain\Events\Lesson::class,
        App\Domain\Calendar\Availability::class
    ]

];