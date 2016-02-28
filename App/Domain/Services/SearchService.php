<?php

namespace App\Domain\Services;

use App\Domain\Users\Teacher;

class SearchService extends Service
{
    public function generalSearch(Teacher $teacher, string $search): array
    {
        return [
            'students' => $teacher->students()->where('firstName lastName', 'LIKE', '%'.$search.'%')
                ->sortBy('firstName', true)
                ->slice(0, 10),

            'activities' => $teacher->activities()->where('name', 'LIKE', '%'.$search.'%')
                ->sortBy('name', true)
                ->slice(0, 10)
        ];
    }
}