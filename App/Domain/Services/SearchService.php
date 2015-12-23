<?php

namespace App\Domain\Services;

use App\Domain\Activities\Activity;
use App\Domain\Users\Student;

class SearchService extends AuthenticatedService
{
    public function searchAllForTeacher($search)
    {
        $students = $this->user->students()->where('firstName lastName', 'LIKE', '%'.$search.'%')
            ->sortBy('firstName', true)
            ->slice(0, 10);

        $activities = $this->user->activities()->where('name', 'LIKE', '%'.$search.'%')
            ->sortBy('name', true)
            ->slice(0, 10);

        return [
            'students' => $this->transformer->of(Student::class)
                ->only(['id', 'firstName', 'lastName'])->transform($students),
            'activities' => $this->transformer->of(Activity::class)
                ->only(['id', 'name'])->transform($activities)
        ];
    }
}