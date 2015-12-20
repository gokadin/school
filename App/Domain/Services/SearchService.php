<?php

namespace App\Domain\Services;

use App\Domain\Transformers\ActivityTransformer;
use App\Domain\Transformers\StudentTransformer;

class SearchService extends Service
{
    /**
     * @var LoginService
     */
    private $loginService;

    /**
     * @var StudentTransformer
     */
    private $studentTransformer;

    /**
     * @var ActivityTransformer
     */
    private $activityTransformer;

    public function __construct(LoginService $loginService, StudentTransformer $studentTransformer,
                                ActivityTransformer $activityTransformer)
    {
        $this->loginService = $loginService;
        $this->studentTransformer = $studentTransformer;
        $this->activityTransformer = $activityTransformer;
    }

    public function searchAllForTeacher($search)
    {
        $user = $this->loginService->user();

        $students = $user->students()->where('firstName lastName', 'LIKE', '%'.$search.'%')
            ->sortBy('firstName', true)
            ->slice(0, 10);

        $activities = $user->activities()->where('name', 'LIKE', '%'.$search.'%')
            ->sortBy('name', true)
            ->slice(0, 10);

        return [
            'students' => $this->studentTransformer->only(['id', 'firstName', 'lastName'])
                ->transformCollection($students),
            'activities' => $this->activityTransformer->only(['id', 'name'])
                ->transformCollection($activities)
        ];
    }
}