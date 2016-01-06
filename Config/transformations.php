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
        'description' => function($o) { return $o->description(); },
        'startDate' => function($o) {
            $date = \Carbon\Carbon::parse($o->startDate());
            return $date->toDateString();
        },
        'endDate' => function($o) {
            $date = \Carbon\Carbon::parse($o->endDate());
            return $date->toDateString();
        },
        'startTime' => function($o) { return $o->startTime(); },
        'endTime' => function($o) { return $o->endTime(); },
        'isAllDay' => function($o) { return $o->isAllDay(); },
        'color' => function($o) { return $o->color(); },
        'activityId' => function($o) { return is_null($o->activity()) ? 0 : $o->activity()->getId(); },
        'studentIds' => function($o) {
            $ids = [];
            foreach ($o->lessons() as $lesson)
            {
                $ids[] = $lesson->student()->getId();
            }
            return $ids;
        }
    ]
];