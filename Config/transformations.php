<?php

return [
    App\Domain\Activities\Activity::class => [
        'id' => function($o) { return $o->getId(); },
        'name' => function($o) { return $o->name(); },
        'rate' => function($o) { return $o->rate(); },
        'period' => function($o) { return $o->period(); },
        'location' => function($o) { return $o->location(); },
        'studentCount' => function($o) { return $o->students()->count(); }
    ],

    App\Domain\Users\Student::class => [
        'id' => function($o) { return $o->getId(); },
        'firstName' => function($o) { return $o->firstName(); },
        'lastName' => function($o) { return $o->lastName(); },
        'fullName' => function($o) { return $o->firstName().' '.$o->lastName(); },
        'email' => function($o) { return $o->email(); },
        'activityName' => function($o) { return $o->activity()->name(); }
    ],

    App\Domain\Events\Event::class => [
        'id' => function($o) { return $o->getId(); },
        'title' => function($o) { return $o->title(); },
        'startDate' => function($o) {
            $date = \Carbon\Carbon::parse($o->startDate());
            return $date->toDateString();
        },
        'endDate' => function($o) {
            $date = \Carbon\Carbon::parse($o->endDate());
            return $date->toDateString();
        },
        'color' => function($o) { return $o->color(); }
    ]
];