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

    App\Domain\Users\TempStudent::class => [
        'id' => function($o) { return $o->getId(); },
        'firstName' => function($o) { return $o->firstName(); },
        'lastName' => function($o) { return $o->lastName(); },
        'fullName' => function($o) { return $o->firstName().' '.$o->lastName(); },
        'email' => function($o) { return $o->email(); },
        'activityName' => function($o) { return $o->activity()->name(); },
        'status' => function($o) { return $o->isExpired() ? 'expired' : 'pending'; }
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
        },
        'isRecurring' => function($o) { return $o->isRecurring(); },
        'rRepeat' => function($o) { return $o->rRepeat(); },
        'rEvery' => function($o) { return $o->rEvery(); },
        'rEndDate' => function($o) { return $o->rEndDate(); },
        'rEndsNever' => function($o) { return $o->rEndsNever(); },
        'location' => function($o) { return $o->location(); },
        'visibility' => function($o) { return $o->visibility(); },
        'notifyMeBy' => function($o) { return $o->notifyMeBy(); },
        'notifyMeBefore' => function($o) { return $o->notifyMeBefore(); },
    ],

    App\Domain\Events\Lesson::class => [
        'startDate' => function($o) { return $o->event()->startDate(); },
        'endDate' => function($o) { return $o->event()->endDate(); },
        'startTime' => function($o) { return $o->event()->startTime(); },
        'endTime' => function($o) { return $o->event()->endTime(); },
    ]
];